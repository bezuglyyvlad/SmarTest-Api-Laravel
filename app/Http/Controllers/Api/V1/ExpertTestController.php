<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\TestHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertTestDestroyRequest;
use App\Http\Requests\ExpertTestIndexRequest;
use App\Http\Requests\ExpertTestStoreRequest;
use App\Http\Requests\ExpertTestUpdateRequest;
use App\Http\Resources\ExpertTestCollection;
use App\Http\Resources\ExpertTestResource;
use App\Http\Resources\TestCategoryResource;
use App\Models\Answer;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ExpertTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ExpertTestIndexRequest $request
     * @return ExpertTestCollection
     */
    public function index(ExpertTestIndexRequest $request): ExpertTestCollection
    {
        $testCategoryId = (int)$request->validated()['test_category_id'];

        return new ExpertTestCollection(
            ExpertTest::where([
                'test_category_id' => $testCategoryId,
                'is_published' => 1
            ])->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpertTestStoreRequest $request
     * @return ExpertTestResource
     */
    public function store(ExpertTestStoreRequest $request): ExpertTestResource
    {
        $createdExpertTest = ExpertTest::create($request->validated());

        return new ExpertTestResource($createdExpertTest);
    }

    /**
     * Display the specified resource.
     *
     * @param ExpertTest $expertTest
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function show(ExpertTest $expertTest)
    {
        return response(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExpertTestUpdateRequest $request
     * @param ExpertTest $expertTest
     * @return ExpertTestResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(ExpertTestUpdateRequest $request, ExpertTest $expertTest): ExpertTestResource
    {
        $validatedExpertTest = $request->validated();
        $replicatedOldData = $expertTest->replicate();
        $newExpertTest = $expertTest->replicate();

        // if the data not changed, return the old resource
        if ($newExpertTest->fill($validatedExpertTest) == $replicatedOldData) {
            return new ExpertTestResource($expertTest);
        }

        $onlyIsPublishedChanged = self::onlyIsPublishedChanged($replicatedOldData, $newExpertTest);
        $activeTestIds = Test::getActiveTestIdsByExpertTest($expertTest->id);

        // deny if users passing test
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0 && !$onlyIsPublishedChanged);

        DB::transaction(function () use ($expertTest, $newExpertTest, $onlyIsPublishedChanged, $activeTestIds) {
            // deactivate old resource
            $expertTest->delete();

            // create link for history records
            $newExpertTest->modified_records_parent_id = $expertTest->id;
            $newExpertTest->save();

            // update test_category_id in Question table
            Question::where('expert_test_id', $expertTest->id)
                ->update(['expert_test_id' => $newExpertTest->id]);

            // update test_category_id in Test table if only is published changed
            if ($onlyIsPublishedChanged && $activeTestIds) {
                Test::whereIn('id', $activeTestIds)->update(['expert_test_id' => $newExpertTest->id]);
            }
        });

        return new ExpertTestResource($newExpertTest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ExpertTestDestroyRequest $request
     * @param ExpertTest $expertTest
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     * @throws \Throwable
     */
    public function destroy(ExpertTestDestroyRequest $request, ExpertTest $expertTest)
    {
        // deny if users passing test
        $activeTestIds = Test::getActiveTestIdsByExpertTest($expertTest->id);
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        DB::transaction(function () use ($expertTest) {
            $questionIds = Question::where('expert_test_id', $expertTest->id)->pluck('id');
            $answerIds = Answer::whereIn('question_id', $questionIds)->pluck('id');

            $expertTest->delete();
            Question::destroy($questionIds);
            Answer::destroy($answerIds);
        });

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ExpertTest $oldExpertTest
     * @param ExpertTest $newExpertTest
     * @return bool
     */
    private static function onlyIsPublishedChanged(ExpertTest $oldExpertTest, ExpertTest $newExpertTest): bool
    {
        $newExpertTest = array_map(
            function ($value) {
                return is_numeric($value) ? (int)$value : $value;
            },
            $newExpertTest->toArray()
        );

        $differences = array_diff_assoc($oldExpertTest->toArray(), $newExpertTest);

        return count($differences) === 1 && array_key_exists('is_published', $differences);
    }
}
