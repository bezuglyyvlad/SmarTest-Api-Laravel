<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\TestHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertTestDestroyRequest;
use App\Http\Requests\ExpertTestStoreRequest;
use App\Http\Requests\ExpertTestUpdateRequest;
use App\Http\Resources\ExpertTestCollection;
use App\Http\Resources\ExpertTestResource;
use App\Http\Resources\TestCategoryResource;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpertTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ExpertTestCollection
     */
    public function index(Request $request): ExpertTestCollection
    {
        return new ExpertTestCollection(
            ExpertTest::where([
                'test_category_id' => $request->test_category_id,
                'is_published' => 1,
                'active_record' => 1,
                'deleted_at' => null
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
     * @param int $id
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function show(int $id)
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
     */
    public function update(ExpertTestUpdateRequest $request, ExpertTest $expertTest)
    {
        $validatedExpertTest = $request->validated();
        $replicatedOldData = $expertTest->replicate();
        $newExpertTest = $expertTest->replicate();

        // if the data not changed, return the old resource
        if ($newExpertTest->fill($validatedExpertTest) == $replicatedOldData) {
            return new ExpertTestResource($expertTest);
        }

        $onlyIsPublishedChanged = self::onlyIsPublishedChanged($replicatedOldData, $newExpertTest);
        $activeTestId = ExpertTestUpdateRequest::validateExpertTestId($expertTest->id, $onlyIsPublishedChanged);

        // deactivate old resource
        $expertTest->active_record = false;
        $expertTest->is_published = false;
        $expertTest->save();

        // create link for history records
        $newExpertTest->modified_records_parent_id = $expertTest->id;
        $newExpertTest->save();

        // update test_category_id in Question table
        Question::where('expert_test_id', $expertTest->id)
            ->update(['expert_test_id' => $newExpertTest->id]);

        // update test_category_id in Test table if only is published changed
        if ($onlyIsPublishedChanged) {
            Test::whereIn('id', $activeTestId)->update(['expert_test_id' => $newExpertTest->id]);
        }

        return new ExpertTestResource($newExpertTest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ExpertTestDestroyRequest $request
     * @param ExpertTest $expertTest
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(ExpertTestDestroyRequest $request, ExpertTest $expertTest)
    {
        $expertTest->deleted_at = Carbon::now();
        $expertTest->active_record = false;
        $expertTest->is_published = false;
        $expertTest->save();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ExpertTest $oldExpertTest
     * @param ExpertTest $newExpertTest
     * @return bool
     */
    public static function onlyIsPublishedChanged(ExpertTest $oldExpertTest, ExpertTest $newExpertTest): bool
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
