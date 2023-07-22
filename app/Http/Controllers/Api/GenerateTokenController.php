<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GenerateTokenRequest;

class GenerateTokenController extends Controller
{
    /**
     * Generate token using Laravel Sanctum
     *
     * @return array<string, string>
     */
    public function generateToken(GenerateTokenRequest $request): array
    {
        $validatedData = $request->validated();

        return [
            'token' => $validatedData['user']->createToken($request->get('device_name', 'Frontend'), $request->company_id)->plainTextToken,
        ];
    }
}
