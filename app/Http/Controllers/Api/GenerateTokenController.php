<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckCredentialsRequest;

class GenerateTokenController extends Controller
{
    /**
     * Generate token using Laravel Sanctum
     *
     * @return array<string, string>
     */
    public function generateToken(CheckCredentialsRequest $request): array
    {
        $validatedData = $request->validated();

        $token = $validatedData['user']->createToken($request->get('device_name', 'Frontend'))->plainTextToken;

        return ['token' => $token];
    }
}
