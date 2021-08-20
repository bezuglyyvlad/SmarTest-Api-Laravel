<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $grant_type
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $grant_type)
    {
        $client = DB::table('oauth_clients')->where('id', 2)->first();

        if (!$client) {
            return response(null, Response::HTTP_NOT_FOUND);
        }

        $request->merge([
            'grant_type' => $grant_type,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => ''
        ]);

        return $next($request);
    }
}
