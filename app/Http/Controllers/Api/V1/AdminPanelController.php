<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPanelIndexRequest;
use App\Http\Resources\TestCategoryCollection;
use App\Http\Resources\TestCategoryResource;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdminPanelIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(AdminPanelIndexRequest $request): AnonymousResourceCollection
    {
        $testCategoryModel = TestCategory::setParentKeyName('parent_id');
        $testCategories = $testCategoryModel->tree(0);
        return TestCategoryResource::collection($testCategories->with('user')->where([
            'deleted_at' => null,
            'active_record' => 1
        ])->get());
    }
}
