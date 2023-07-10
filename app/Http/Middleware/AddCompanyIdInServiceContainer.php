<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCompanyIdInServiceContainer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $personalAccessToken = PersonalAccessToken::findToken($request->bearerToken());

        abort_if(! $personalAccessToken, Response::HTTP_FORBIDDEN);

        app()->bind('company_id', fn () => $personalAccessToken->company_id);

        return $next($request);
    }
}
