<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GenerateTokenRequest;
use App\Queries\UserQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GenerateTokenController extends Controller
{
    public function __construct(
        protected UserQueries $userQueries
    ) {

    }

    public function generateToken(Request $request): array
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required'],
        ]);

        $user = $this->userQueries->findByEmail($request->email);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /* We can update the token abilities after permission enum create */
        return [
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ];
    }
}
