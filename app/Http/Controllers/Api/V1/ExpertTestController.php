<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpertTestCollection;
use App\Models\ExpertTest;
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
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response(null, Response::HTTP_NOT_FOUND);
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
     * @param Request $request
     * @param int $id
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response(null, Response::HTTP_NOT_FOUND);
    }
}
