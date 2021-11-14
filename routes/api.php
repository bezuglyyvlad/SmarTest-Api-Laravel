<?php

use App\Http\Controllers\Api\V1\AdminPanelController;
use App\Http\Controllers\Api\V1\ExpertPanelController;
use App\Http\Controllers\Api\V1\ExpertTestController;
use App\Http\Controllers\Api\V1\TestCategoryController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('oauth/token', [AccessTokenController::class, 'issueToken'])
    ->middleware(['oauth:password']);
Route::post('oauth/token/refresh', [AccessTokenController::class, 'issueToken'])
    ->middleware(['oauth:refresh_token']);

Route::post('users/logout', [UserController::class, 'logout']);
Route::apiResource('users', UserController::class);

Route::apiResource('test-categories', TestCategoryController::class)->middleware(['auth:api']);

Route::apiResource('expert-tests', ExpertTestController::class)->middleware(['auth:api']);

Route::prefix('tests')->middleware(['auth:api'])->group(function () {
    Route::post('/nextQuestion', [TestController::class, 'nextQuestion']);
    Route::get('/result', [TestController::class, 'result']);
    Route::get('/rating', [TestController::class, 'rating']);
    Route::get('/{test}', [TestController::class, 'show']);
    Route::post('/', [TestController::class, 'store']);
    Route::get('/', [TestController::class, 'index']);
});

Route::get('admin-panels', [AdminPanelController::class, 'index'])->middleware(['auth:api']);

Route::prefix('expert-panels')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ExpertPanelController::class, 'index']);
    Route::get('/{test_category_id}', [ExpertPanelController::class, 'show']);
    Route::get('/expertTests/{test_category_id}', [ExpertPanelController::class, 'expertTests']);
    Route::get('/testStatistics/{expert_test}', [ExpertPanelController::class, 'testStatistics']);
    Route::get('/dataMining/{expert_test}', [ExpertPanelController::class, 'dataMining']);
});
