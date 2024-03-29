<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\TestHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestNextQuestionRequest;
use App\Http\Requests\TestStoreRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\PrivateAnswerResource;
use App\Http\Resources\PrivateTestResultResource;
use App\Http\Resources\TestCollection;
use App\Http\Resources\TestControllerResultResource;
use App\Http\Resources\TestResource;
use App\Http\Resources\TestResultResource;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return TestCollection
     */
    public function index(Request $request): TestCollection
    {
        $perPage = $request->perPage ? (int)$request->perPage : 10;
        return new TestCollection(
            Test::whereIn('id', TestHelper::getUserTestIds())
                ->orderByDesc('id')
                ->with('expert_test', 'test_category')
                ->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TestStoreRequest $request
     * @return array
     * @throws \Throwable
     */
    public function store(TestStoreRequest $request): array
    {
        $expert_test_id = $request->validated()['expert_test_id'];

        DB::beginTransaction();
        try {
            $newTest = TestHelper::startNewTest($expert_test_id);
            $coefRange = Question::selectCoefRange(0);
            ['test_result' => $newQuestion, 'answers' => $answers] =
                TestHelper::generateNewQuestion($expert_test_id, $newTest, 1, $coefRange);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'test' => new TestResource($newTest),
            'question' => new TestResultResource($newQuestion),
            'answers' => AnswerResource::collection($answers)
        ];
    }

    /**
     * Display the specified resource.
     *
     * @psalm-suppress TooManyArguments
     * @param int $id
     * @return array|Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $test = Test::with('expert_test', 'test_category')->findOrFail($id);
        if (($test instanceof Test) && $test->user_id === Auth::id() && !$test->testIsFinished()) {
            ['test_tesult' => $test_tesult, 'answers' => $answers] = TestHelper
                ::getLastQuestionAndAnswers($test->id, ['id', 'text']);
            return [
                'test' => new TestResource($test),
                'question' => new TestResultResource($test_tesult),
                'answers' => AnswerResource::collection($answers)
            ];
        } else {
            return response(['message' => "Forbidden"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @return array
     */
    public function rating(): array
    {
        $userTestIds = TestHelper::getUserTestIds();
        $tests = Test::whereIn('id', $userTestIds)->get();
        // rating by category
        $basicTestCategories = TestHelper::getBasicTestCategories();
        $ratingByCategory = collect();
        foreach ($basicTestCategories as &$item) {
            $expertTestIds = TestHelper::getExpertTestIds($item->id);
            $score = Test::whereIn('id', $userTestIds)
                ->whereIn('expert_test_id', $expertTestIds)
                ->avg('score');
            if (!is_null($score)) {
                $item->score = round($score, 2);
                $ratingByCategory->add($item);
            }
        }
        // rating
        $rating = $ratingByCategory->avg('score');
        // chart data
        $chartData = $tests->filter(function (Test $value) {
            return Carbon::now()->subMonth()
                ->lte(Carbon::createFromFormat('c', $value->start_date));
        })->pluck('score')->map(function (float $item) {
            return round($item, 2);
        });
        return ['rating' => $rating, 'ratingByCategory' => $ratingByCategory, 'chartData' => $chartData];
    }

    /**
     * @param TestNextQuestionRequest $request
     * @return array
     * @throws \Throwable
     */
    public function nextQuestion(TestNextQuestionRequest $request): array
    {
        $test_id = $request->test_id;
        $userAnswer = $request->answer;

        $newQuestion = null;
        $newAnswers = null;

        DB::beginTransaction();
        try {
            ['test_tesult' => $test_result, 'lastQuestion' => $lastQuestion, 'answers' => $answers] =
                TestHelper::getLastQuestionAndAnswers($test_id);
            $test = Test::where('id', $test_id)->first();
            TestHelper::updateTestStatistics($test_result, $answers, $userAnswer, $lastQuestion, $test);
            if ($test->max_score < 100) {
                // select range of coefficient new question
                $coefRange = Question::selectCoefRange($test->score);
                ['test_result' => $newQuestion, 'answers' => $newAnswers] = TestHelper::generateNewQuestion(
                    $test->expert_test_id,
                    $test,
                    $test_result->serial_number + 1,
                    $coefRange
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($newQuestion && $newAnswers) {
            return ['question' => new TestResultResource($newQuestion), 'answers' => AnswerResource::collection($newAnswers)];
        }
        return [];
    }

    /**
     * @param Request $request
     * @return array|Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function result(Request $request)
    {
        $test = Test::where(['id' => $request->test_id, 'user_id' => Auth::id()])
            ->with('expert_test', 'test_category')
            ->first();
        if ($test && $test->testIsFinished()) {
            return [
                'test' => new TestResource($test),
                'questions' => TestControllerResultResource::collection(
                    TestResult::where('test_id', $test->id)->with('question')
                        ->get()
                )
            ];
        } else {
            return response(['message' => "Forbidden"], Response::HTTP_FORBIDDEN);
        }
    }
}
