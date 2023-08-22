<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\PostmanBackupMail;
use Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Storage;

class PostmanCollectionBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postman-collection-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to the developer with the latest Postman collection.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = Http::postman()->get('/collections');

        if ($response->ok()) {
            $collectionId = $response->collect('collections')
                ->filter(fn (array $collection): bool => isset($collection['name']) && $collection['name'] === config('app.name'))
                ->pluck('id')
                ->first();

            $response = Http::postman()->get('/collections/' . $collectionId);

            if ($response->ok()) {
                $collection = $response->json('collection');

                $now = now()->format('Y-m-d_H:i:s_A');

                $collection = json_encode($collection, JSON_PRETTY_PRINT);

                file_put_contents(Storage::path($filename = 'public/collection_' . $now . '.json'), $collection);

                Mail::to(env('DEVELOPER_EMAIL', 'utsav@freshbits.in'))->send(new PostmanBackupMail($filename));

                Storage::delete($filename);
            }
        }

        Log::error('Postman Response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        exit(1);
    }
}
