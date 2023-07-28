<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        ResetPassword::createUrlUsing(fn (User $user, string $token): string => $request->get('url') . '?token=' . $token);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
               ? Response::api($status)
               : throw ValidationException::withMessages(['email' => __($status)]);
    }
}
