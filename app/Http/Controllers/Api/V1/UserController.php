<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['store']);
        $this->middleware(function (Request $request, Closure $next) {
            /** @phpstan-ignore-next-line */
            if ($request->user->id !== Auth::id()) {
                return response(['message' => "You don't have enough permission"], Response::HTTP_FORBIDDEN);
            }
            return $next($request);
        })->only(['show', 'update', 'destroy']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserStoreRequest $request
     * @return UserResource
     */
    public function store(UserStoreRequest $request): UserResource
    {
        /** @phpstan-ignore-next-line */
        $created_user = User::create($request->validated());

        return new UserResource($created_user);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserStoreRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UserStoreRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $tokenId = $request->user()->token()->id;

        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $tokenRepository->revokeAccessToken($tokenId);

        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
