<?php

use App\Http\Controllers\Api\V1\ExpertTestController;
use App\Http\Controllers\Api\V1\TestCategoryController;
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
Route::apiResource('users', UserController::class)->except('index');

Route::apiResource('test-categories', TestCategoryController::class)->middleware(['auth:api']);

Route::apiResource('expert-tests', ExpertTestController::class)->middleware(['auth:api']);
