<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Image;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertPanelBreadcrumbsRequest;
use App\Http\Requests\ExpertPanelImportRequest;
use App\Http\Requests\ExpertPanelTestCategoryComponentsRequest;
use App\Http\Requests\ExpertPanelGetByExpertTestRequest;
use App\Http\Requests\ExpertPanelStoreQuestionRequest;
use App\Http\Resources\ExpertTestResource;
use App\Http\Resources\PrivateExportQuestionResource;
use App\Http\Resources\PrivateQuestionResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\TestCategoryResource;
use App\Http\Resources\TestResource;
use App\Models\Answer;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExpertPanelController extends Controller
{
    /**
     * Display a listing of the basic test categories.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TestCategoryResource::collection(
            TestCategory::where(
                [
                    'user_id' => Auth::id()
                ]
            )->whereNull(['parent_id'])->orderByDesc('updated_at')->get()
        );
    }

    /**
     * Display test categories.
     *
     * @param ExpertPanelTestCategoryComponentsRequest $request
     * @return AnonymousResourceCollection
     */
    public function testCategories(ExpertPanelTestCategoryComponentsRequest $request): AnonymousResourceCollection
    {
        $testCategories = TestCategory
            ::setParentKeyName('parent_id')
            ::findOrFail($request->test_category_id)
            ->children()
            ->with('user')
            ->orderByDesc('updated_at')
            ->get();

        return TestCategoryResource::collection($testCategories);
    }

    /**
     * @param ExpertPanelTestCategoryComponentsRequest $request
     * @return AnonymousResourceCollection
     */
    public function expertTests(ExpertPanelTestCategoryComponentsRequest $request): AnonymousResourceCollection
    {
        $testCategoryId = $request->test_category_id;

        return ExpertTestResource::collection(
            ExpertTest
                ::where([
                    'test_category_id' => $testCategoryId
                ])
                ->orderByDesc('updated_at')
                ->get()
        );
    }

    /**
     * @param ExpertPanelGetByExpertTestRequest $request
     * @param ExpertTest $expertTest
     * @return array
     */
    public function testStatistics(ExpertPanelGetByExpertTestRequest $request, ExpertTest $expertTest): array
    {
        $expertTestHistoryRecordIds = ExpertTest::getExpertTestHistoryRecordIds($expertTest->id);

        return [
            'tests' => TestResource::collection(
                Test
                    ::with('user')
                    ->whereIn('expert_test_id', $expertTestHistoryRecordIds)
                    ->get()
            )
        ];
    }

    /**
     * @psalm-suppress TooManyArguments
     * @psalm-suppress PossiblyInvalidMethodCall
     * @psalm-suppress InvalidArrayAccess
     * @param ExpertPanelGetByExpertTestRequest $request
     * @param ExpertTest $expertTest
     * @return array|string
     */
    public function dataMining(ExpertPanelGetByExpertTestRequest $request, ExpertTest $expertTest)
    {
        $expertTestHistoryRecordIds = ExpertTest::getExpertTestHistoryRecordIds($expertTest->id);

        $tests = Test::with('user', 'expert_test', 'test_results')
            ->whereIn('expert_test_id', $expertTestHistoryRecordIds)
            ->get()
            ->filter(function (Test $test) {
                return $test->testIsFinished();
            })->values();

        if (!$tests->isEmpty()) {
            $numberOfTestPassesByEmail = $tests->groupBy('user.email')->map->count();

            $dataMining = $tests->each(function (Test $item) use ($numberOfTestPassesByEmail) {
                $item->expert_test_title = $item->expert_test->title;

                $item->user_email = $item->user->email;

                for ($i = 0; $i < count($item->test_results); $i++) {
                    $maxQuestionScore = $item->test_results[$i]->max_score;
                    $questionScore = $item->test_results[$i]->score;
                    $scorePercent = round($questionScore / $maxQuestionScore * 100);
                    $item['Q' . $item->test_results[$i]->question_id] = $scorePercent;
                }

                $item->score = round($item->score, 2);
                $item->number_of_test_passes = $numberOfTestPassesByEmail[$item->user->email];

                unset($item->user);
                unset($item->expert_test);
                unset($item->test_results);
                unset($item->user_id);
                unset($item->expert_test_id);
                unset($item->test_category_id);
                unset($item->max_score);
            })
                ->sortByDesc('score')
                ->unique('user_email')
                ->sortKeys();
            $splitCount = intdiv($dataMining->count(), 250);
            $dataMining = $dataMining
                ->split($splitCount)
                ->map(function (Collection $item) {
                    return json_encode($item, JSON_UNESCAPED_UNICODE);
                })->toArray();

            $dataMining['count'] = $splitCount;

            // run python script
            $process = new Process(
                ['python', 'PythonScripts/dataMining.py'],
                null,
                $dataMining
            );
            $process->run();

            // error handling
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output_data = json_decode($process->getOutput(), true);
            return array_map(function (string $i) {
                $result = json_decode($i, true);
                if (json_last_error() === 0) {
                    return $result;
                }
                return $i;
            }, $output_data);
        }
        return json_encode((object)[]);
    }

    /**
     * Index questions for expert panel
     * @param ExpertPanelGetByExpertTestRequest $request
     * @param ExpertTest $expertTest
     * @return AnonymousResourceCollection
     */
    public function questions(ExpertPanelGetByExpertTestRequest $request, ExpertTest $expertTest): AnonymousResourceCollection
    {
        return PrivateQuestionResource::collection(
            Question
                ::where([
                    'expert_test_id' => $expertTest->id
                ])
                ->orderByDesc('updated_at')
                ->get()
                ->append('condComplexity')
        );
    }

    /**
     * Store question
     * @param ExpertPanelStoreQuestionRequest $request
     * @return PrivateQuestionResource
     */
    public function question(ExpertPanelStoreQuestionRequest $request): PrivateQuestionResource
    {
        $validatedData = $request->validated();

        $question = new Question();
        $question->fill($validatedData);
        $question->quality_coef = $question->getQualityCoefByFuzzyLogic();

        $answers = $validatedData['answers'];

        $imageFileName = Image::saveImage('question', $request->file('image'));
        $question->image = $imageFileName;

        self::saveQuestionWithAnswers($question, $answers, $imageFileName);

        return new PrivateQuestionResource($question->load('answers'));
    }

    /**
     * @param ExpertPanelImportRequest $request
     * @return AnonymousResourceCollection
     * @throws ValidationException
     * @throws \Throwable
     */
    public function importQuestions(ExpertPanelImportRequest $request): AnonymousResourceCollection
    {
        $xmlString = file_get_contents($request->file('importFile'));
        $xmlString = str_replace(["\n", "\r", "\t"], '', $xmlString);
        $xmlString = trim(str_replace('"', "'", $xmlString));

        try {
            $simpleXml = simplexml_load_string($xmlString);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'importFile' => ['Помилка парсингу файла']
            ]);
        }

        $questions = self::validateImportQuestionsXml($simpleXml);

        // if import one question transform {text: ...} to [{text: ...}]
        if (!is_array(array_values($questions)[0])) {
            $questions = [$questions];
        }

        $questionStoreRules = (new ExpertPanelStoreQuestionRequest)->rules();
        DB::transaction(function () use ($questions, $request, $questionStoreRules) {
            foreach ($questions as $item) {
                $item['expert_test_id'] = $request->expert_test_id;
                // validate question
                Validator::make($item, $questionStoreRules)->validate();

                // save question with answers
                $question = new Question();
                $question->fill($item);
                $question->quality_coef = $question->getQualityCoefByFuzzyLogic();

                $answers = $item['answers'];

                self::saveQuestionWithAnswers($question, $answers);
            }
        });

        return PrivateQuestionResource::collection(
            Question
                ::where([
                    'expert_test_id' => $request->expert_test_id
                ])
                ->orderByDesc('updated_at')
                ->get()
                ->append('condComplexity')
        );
    }

    public function exportQuestions(ExpertPanelGetByExpertTestRequest $request, ExpertTest $expertTest)
    {
        $questionsWithAnswers = json_decode(
            PrivateExportQuestionResource::collection(
                Question
                    ::where('expert_test_id', $expertTest->id)
                    ->with('answers')
                    ->get()
            )->toJson(),
            true
        );

        return response()->xml(['question' => $questionsWithAnswers], 200, [], 'root', 'utf-8');
    }

    /**
     * @param ExpertPanelBreadcrumbsRequest $request
     * @return array
     */
    public function breadcrumbs(ExpertPanelBreadcrumbsRequest $request): array
    {
        $breadcrumbs = TestCategory
            ::setParentKeyName('parent_id')
            ::findOrFail($request->test_category_id)
            ->ancestorsAndSelf()
            ->with('user')
            ->get()
            ->reverse()->values();

        foreach ($breadcrumbs as $key => $value) {
            if ($value->user_id !== Auth::id()) {
                $breadcrumbs->forget($key);
            } else {
                break;
            }
        }

        $expertTestName = $request->expert_test_id ? ExpertTest::findOrFail($request->expert_test_id)->title : '';

        return [
            'breadcrumbs' => TestCategoryResource::collection($breadcrumbs),
            'expertTestName' => $expertTestName,
        ];
    }

    private static function saveQuestionWithAnswers($question, $answers, $imageFileName = null)
    {
        DB::transaction(function () use ($question, $answers, $imageFileName) {
            try {
                $question->save();
            } catch (\Exception $e) {
                Image::deleteImage('question', $imageFileName);
                throw $e;
            }

            $answerModels = [];
            foreach ($answers as $item) {
                $answerModels[] = new Answer($item);
            }
            $question->answers()->saveMany($answerModels);
        });
    }

    /**
     * @param $simpleXml
     * @return array
     * @throws ValidationException
     */
    private static function validateImportQuestionsXml($simpleXml): array
    {
        if (!stripos($simpleXml->asXML(), 'encoding="utf-8"')) {
            throw ValidationException::withMessages([
                'importFile' => ['Вкажіть кодування utf-8']
            ]);
        }

        $json = json_encode($simpleXml);
        $data = json_decode($json, true);

        if (!array_key_exists('question', $data)) {
            throw ValidationException::withMessages([
                'importFile' => ['Відсутній атрибут question в XML файлі']
            ]);
        } else if (count($data['question']) > 300) {
            throw ValidationException::withMessages([
                'importFile' => ['Неможливо імпортувати більше ніж 300 питань']
            ]);
        }

        return $data['question'];
    }
}
