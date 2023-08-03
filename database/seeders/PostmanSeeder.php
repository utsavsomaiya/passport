<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\intro;

class PostmanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! env('POSTMAN_API_KEY')) {
            $this->command->error('Please set the postman API Key...');

            return;
        }

        $response = Http::postman()->get('/environments');

        $environmentId = null;

        if ($response->ok()) {
            $environmentId = $response->collect('environments')
                ->filter(fn ($environment): bool => $environment['name'] === 'Local')
                ->first()['id'];
        }

        if ($environmentId) {
            $response = Http::postman()->get('/environments/' . $environmentId);

            if ($response->ok()) {
                $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                $environmentData = $responseData['environment'];
                $environmentVariables = Arr::where($environmentData['values'], fn ($value): bool => $value['key'] !== 'token');

                $response = Http::postman()->put('/environments/' . $environmentId, [
                    'environment' => [
                        'values' => array_values($environmentVariables),
                    ],
                ]);

                if ($response->ok()) {
                    intro('Token removed from the Postman Environment successfully.');
                }
            }
        }
    }
}
