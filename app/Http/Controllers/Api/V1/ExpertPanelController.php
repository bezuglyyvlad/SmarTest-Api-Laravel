<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\TestHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertPanelShowRequest;
use App\Http\Requests\ExpertPanelStatisticsRequest;
use App\Http\Resources\ExpertTestResource;
use App\Http\Resources\TestCategoryResource;
use App\Http\Resources\TestResource;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExpertPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TestCategoryResource::collection(
            TestCategory::where(
                [
                    'user_id' => Auth::id(),
                    'deleted_at' => null,
                    'active_record' => 1
                ]
            )->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param ExpertPanelShowRequest $request
     * @param int $id
     * @return array
     */
    public function show(ExpertPanelShowRequest $request, int $id): array
    {
        $testCategoryModel = TestCategory
            ::setParentKeyName('parent_id');

        $testCategoryInstance = $testCategoryModel::findOrFail($id);
        $testCategories = $testCategoryInstance
            ->children()
            ->where([
                'deleted_at' => null,
                'active_record' => 1
            ])
            ->with('user')
            ->get();

        $breadcrumbs = self::getTestCategoryBreadcrumbs($testCategoryInstance);

        $parentSelect = TestCategory::where(
            [
                'user_id' => Auth::id(),
                'deleted_at' => null,
                'active_record' => 1
            ]
        )->get()->map(function (TestCategory $item) {
            return $item->descendantsAndSelf()->get();
        })->flatten()->pluck('title', 'id');

        return [
            'data' => TestCategoryResource::collection($testCategories),
            'breadcrumbs' => TestCategoryResource::collection($breadcrumbs),
            'parentSelect' => $parentSelect,
        ];
    }

    /**
     * @param int $id
     * @return AnonymousResourceCollection
     */
    public function expertTests(int $id): AnonymousResourceCollection
    {
        return ExpertTestResource::collection(
            ExpertTest::where([
                'test_category_id' => $id,
                'active_record' => 1,
                'deleted_at' => null
            ])->get()
        );
    }

    /**
     * @param ExpertPanelStatisticsRequest $request
     * @param ExpertTest $expertTest
     * @return array
     */
    public function testStatistics(ExpertPanelStatisticsRequest $request, ExpertTest $expertTest): array
    {
        $testCategoryBreadcrumbs = self::getTestCategoryBreadcrumbs(
            TestCategory::findOrFail($expertTest->test_category_id)
        );

        $expertTestHistoryRecordIds = self::getExpertTestHistoryRecordIds($expertTest->id);

        return [
            'tests' => TestResource::collection(
                Test::with('user')
                    ->whereIn('expert_test_id', $expertTestHistoryRecordIds)
                    ->get()
            ),
            'testCategoryBreadcrumbs' => TestCategoryResource::collection($testCategoryBreadcrumbs),
            'expertTestName' => $expertTest->title
        ];
    }

    /**
     * @psalm-suppress TooManyArguments
     * @psalm-suppress PossiblyInvalidMethodCall
     * @psalm-suppress InvalidArrayAccess
     * @param ExpertPanelStatisticsRequest $request
     * @param ExpertTest $expertTest
     * @return mixed
     */
    public function dataMining(ExpertPanelStatisticsRequest $request, ExpertTest $expertTest)
    {
        $expertTestHistoryRecordIds = self::getExpertTestHistoryRecordIds($expertTest->id);

        $tests = Test::with('user', 'expert_test', 'test_results.question')
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
                    $correctionCoef = $item->getScoreCorrectionCoef();
                    $maxQuestionScore = Question::BASIC_POINTS *
                        $item->test_results[$i]->question->quality_coef * $correctionCoef;
                    $questionScore = $item->test_results[$i]->score;
                    $scorePercent = round($questionScore / $maxQuestionScore, 2);
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
        return [];
    }

    /**
     * @param TestCategory $testCategoryInstance
     * @return mixed
     */
    private static function getTestCategoryBreadcrumbs(TestCategory $testCategoryInstance)
    {
        $breadcrumbs = $testCategoryInstance
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

        return $breadcrumbs;
    }

    /**
     * @param int $parentExpertTestId
     * @return array
     */
    private static function getExpertTestHistoryRecordIds(int $parentExpertTestId): array
    {
        return ExpertTest::setParentKeyName('modified_records_parent_id')
            ::findOrFail($parentExpertTestId)
            ->ancestorsAndSelf()->pluck('id')->toArray();
    }
}
