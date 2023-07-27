<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
            'url' => ['required', 'url'],
        ]);

        ResetPassword::createUrlUsing(fn (User $user, string $token): string => $request->get('url') . '?token=' . $token);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
               ? Response::api($status)
               : throw ValidationException::withMessages(['email' => __($status)]);
    }
}
