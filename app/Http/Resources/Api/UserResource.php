<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource;

        /** @var string $bearerToken */
        $bearerToken = $request->bearerToken();

        [$id, $token] = explode('|', $bearerToken, 2);

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'username' => $user->username,
            'roles' => $user->roles->pluck('name')->toArray(),
            'token_last_used_at' => $user->tokens->find($id)?->last_used_at?->displayFormat(),
            'created_at' => $user->created_at?->displayFormat(),
        ];
    }
}
