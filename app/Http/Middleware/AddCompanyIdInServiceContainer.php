<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Facades\Laravel\Passport\PersonalAccessTokenFactory;
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
        /** @var string $bearerToken */
        $bearerToken = $request->bearerToken();

        $token = PersonalAccessTokenFactory::findAccessToken(['access_token' => $bearerToken]);

        abort_if(! $token->exists, Response::HTTP_FORBIDDEN, 'Unauthenticated.');

        abort_if(! $token->company_id, Response::HTTP_FORBIDDEN, 'Please set the company before making this request.');

        app()->bind('company_id', fn () => $token->company_id);

        return $next($request);
    }
}
