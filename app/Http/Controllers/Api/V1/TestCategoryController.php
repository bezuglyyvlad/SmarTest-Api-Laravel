<?php
// phpcs:ignoreFile

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestCategoryDestroyRequest;
use App\Http\Requests\TestCategoryStoreRequest;
use App\Http\Requests\TestCategoryUpdateRequest;
use App\Http\Resources\TestCategoryCollection;
use App\Http\Resources\TestCategoryResource;
use App\Models\ExpertTest;
use App\Models\TestCategory;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class TestCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return TestCategoryCollection
     */
    public function index(Request $request): TestCategoryCollection
    {
        $testCategoryModel = TestCategory::setParentKeyName('parent_id');

        [
            'breadcrumbs' => $breadcrumbs,
            'testCategories' => $testCategories
        ] = TestCategoryController::getTestCategoriesWithBreadcrumbs(
            $request->test_category_id,
            $testCategoryModel
        );

        return (new TestCategoryCollection($testCategories->where([
            'deleted_at' => null,
            'active_record' => 1
        ])->paginate()))->additional(
            ['breadcrumbs' => $breadcrumbs]
        );
    }

    /**
     * @param int|null $parent_test_category_id
     * @param TestCategory $testCategoryModel
     * @return array
     */
    private static function getTestCategoriesWithBreadcrumbs(
        ?int         $parent_test_category_id,
        TestCategory $testCategoryModel
    ): array {
        $breadcrumbs = [];

        // tree() use for root resources
        if ($parent_test_category_id) {
            $testCategoryInstance = $testCategoryModel::findOrFail($parent_test_category_id);
            $breadcrumbs = array_reverse($testCategoryInstance
                ->ancestorsAndSelf()
                ->get(['id', 'title'])->toArray());
            $testCategories = $testCategoryInstance->children();
        } else {
            $testCategories = $testCategoryModel->tree(0);
        }

        return ['breadcrumbs' => $breadcrumbs, 'testCategories' => $testCategories];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TestCategoryStoreRequest $request
     * @return TestCategoryResource
     */
    public function store(TestCategoryStoreRequest $request): TestCategoryResource
    {
        $validatedTestCategory = $request->validated();
        // assign expert role if user_email exists
        if (array_key_exists('user_email', $validatedTestCategory)) {
            $user = User::where('email', $validatedTestCategory['user_email'])->first();
            $user->assignRole(User::getExpertRole());
            $validatedTestCategory['user_id'] = $user->id;
        }

        $createdTestCategory = TestCategory::create($validatedTestCategory);

        return new TestCategoryResource($createdTestCategory);
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
     * Creates a new resource as an update to the old one for the record update history
     *
     * @param TestCategoryUpdateRequest $request
     * @param TestCategory $testCategory
     * @return TestCategoryResource
     */
    public function update(TestCategoryUpdateRequest $request, TestCategory $testCategory): TestCategoryResource
    {
        $validatedTestCategory = $request->validated();
        $newTestCategory = $testCategory->replicate();
        // remove expert role for old user expert
        if ($newTestCategory->user_id) {
            $oldExpert = User::find($newTestCategory->user_id);
            $oldExpert->removeExpertRole();
        }
        // by default user_id is null for new resource
        $validatedTestCategory['user_id'] = null;
        // assign expert role if user_email exists
        if (array_key_exists('user_email', $validatedTestCategory)) {
            $newExpert = User::where('email', $validatedTestCategory['user_email'])->first();
            $newExpert->assignRole(User::getExpertRole());
            $validatedTestCategory['user_id'] = $newExpert->id;
        }

        // if the data not changed, return the old resource
        if ($newTestCategory->fill($validatedTestCategory) == $testCategory->replicate()) {
            return new TestCategoryResource($testCategory);
        }

        // deactivate old resource
        $testCategory->active_record = false;
        $testCategory->save();

        // create link for history records
        $newTestCategory->modified_records_parent_id = $testCategory->id;
        $newTestCategory->save();

        // update test_category_id in linked tables
        ExpertTest::where('test_category_id', $testCategory->id)
            ->update(['test_category_id' => $newTestCategory->id]);

        return new TestCategoryResource($newTestCategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TestCategoryDestroyRequest $request
     * @param TestCategory $testCategory
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(TestCategoryDestroyRequest $request, TestCategory $testCategory)
    {
        $testCategory->deleted_at = Carbon::now();
        $testCategory->active_record = false;
        $testCategory->save();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
