<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\PostmanBackupMail;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mail;

class PostmanBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postman-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to the developer with the latest Postman collection and environments.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $collectionFileName = 'public/pxm-collection.json';
        $localEnvironmentFileName = 'public/pxm-local-environment.json';
        $productionEnvironmentFileName = 'public/pxm-production-environment.json';

        $files = [$collectionFileName, $localEnvironmentFileName, $productionEnvironmentFileName];

        $collectionsResponse = $this->generateCollection($collectionFileName);
        $environmentsResponse = $this->fetchEnvironments();
        if ($environmentsResponse->ok()) {
            [$localId, $productionId] = $environmentsResponse->collect('environments')
                ->filter(fn ($environment): bool => isset($environment['name']) && in_array($environment['name'], ['Local', 'Production']))
                ->pluck('id');

            $this->generateLocalEnvironment($localId, $localEnvironmentFileName);
            $this->generateProductionEnvironment($productionId, $productionEnvironmentFileName);
        }

        if ($collectionsResponse->ok() && $environmentsResponse->ok()) {
            Mail::to(env('DEVELOPER_EMAIL', 'utsav@freshbits.in'))->send(new PostmanBackupMail($files));
            Storage::delete($files);

            exit(0);
        }

        Log::error('Postman collections Response', [
            'status' => $collectionsResponse->status(),
            'body' => $collectionsResponse->body(),
        ]);

        Log::error('Postman environments Response', [
            'status' => $environmentsResponse->status(),
            'body' => $environmentsResponse->body(),
        ]);

        exit(1);
    }

    private function generateCollection(string $collectionFileName): Response
    {
        [$response, $duration] = Benchmark::value(fn () => Http::postman()->get('/collections'));

        if ($duration > 1500) {
            Log::warning('The Postman API is running slowly when attempting to fetch all collections.');
        }

        if ($response->ok()) {
            $collectionId = $response->collect('collections')
                ->filter(fn (array $collection): bool => isset($collection['name']) && $collection['name'] === config('app.name'))
                ->pluck('id')
                ->first();

            [$collectionResponse, $fetchCollectionDuration] = Benchmark::value(fn () => Http::postman()->get('/collections/' . $collectionId));

            if ($fetchCollectionDuration > 4000) {
                Log::warning('The Postman API is running slowly when attempting to fetch pxm collection.');
            }

            if ($collectionResponse->ok()) {
                $collection = $collectionResponse->json('collection');

                /** @var string $collection */
                $collection = json_encode($collection, JSON_PRETTY_PRINT);

                Storage::put($collectionFileName, $collection);
            } else {
                Log::error('Postman collection response', [
                    'status' => $collectionResponse->status(),
                    'collection_id' => $collectionId,
                    'body' => $collectionResponse->body(),
                ]);

                exit(1);
            }
        }

        return $response;
    }

    private function fetchEnvironments(): Response
    {
        [$response, $duration] = Benchmark::value(fn () => Http::postman()->get('/environments'));

        if ($duration > 1500) {
            Log::warning('The Postman API is running slowly when attempting to fetch all environments.');
        }

        if ($response->failed()) {
            exit(1);
        }

        return $response;
    }

    private function generateLocalEnvironment(string $localId, string $localEnvironmentFileName): void
    {
        [$response, $duration] = Benchmark::value(fn () => Http::postman()->get('/environments/' . $localId));

        if ($duration > 2500) {
            Log::warning('The Postman API is running slowly when attempting to fetch local environment.');
        }

        if ($response->ok()) {
            $localEnvironment = $response->json('environment');

            /** @var string $localEnvironment */
            $localEnvironment = json_encode($localEnvironment, JSON_PRETTY_PRINT);

            Storage::put($localEnvironmentFileName, $localEnvironment);
        } else {
            Log::error('Postman environment response', [
                'status' => $response->status(),
                'environment_id' => $localId,
                'environment_name' => 'Local',
                'body' => $response->body(),
            ]);

            exit(1);
        }
    }

    private function generateProductionEnvironment(string $productionId, string $productionEnvironmentFileName): void
    {
        [$response, $duration] = Benchmark::value(fn () => Http::postman()->get('/environments/' . $productionId));

        if ($duration > 2500) {
            Log::warning('The Postman API is running slowly when attempting to fetch production environment.');
        }

        if ($response->ok()) {
            $productionEnvironment = $response->json('environment');

            /** @var string $productionEnvironment */
            $productionEnvironment = json_encode($productionEnvironment, JSON_PRETTY_PRINT);

            Storage::put($productionEnvironmentFileName, $productionEnvironment);
        } else {
            Log::error('Postman environment response', [
                'status' => $response->status(),
                'environment_id' => $productionId,
                'environment_name' => 'Production',
                'body' => $response->body(),
            ]);

            exit(1);
        }
    }
}
