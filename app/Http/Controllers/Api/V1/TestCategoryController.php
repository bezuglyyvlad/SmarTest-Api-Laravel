<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestCategoryCollection;
use App\Http\Resources\TestCategoryResource;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return TestCategoryCollection
     */
    public function index(): TestCategoryCollection
    {
        /** @phpstan-ignore-next-line */
        return new TestCategoryCollection(TestCategory::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param TestCategory $testCategory
     * @return TestCategoryResource
     */
    public function show(TestCategory $testCategory): TestCategoryResource
    {
        return new TestCategoryResource($testCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TestCategory $testCategory
     * @return void
     */
    public function update(Request $request, TestCategory $testCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        //
    }
}
