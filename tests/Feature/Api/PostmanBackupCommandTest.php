<?php

declare(strict_types=1);

use App\Mail\PostmanBackupMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('it will get the postman backup.', function (): void {
    Http::fake([
        'api.getpostman.com/*' => Http::response([
            'collections' => [
                [
                    'id' => fake()->uuid(),
                    'name' => 'Product Xperience Manager',
                ],
            ],
            'collection' => [
                'id' => fake()->uuid(),
                'name' => 'Product Xperience Manager',
            ],
            'environments' => [
                [
                    'id' => fake()->uuid(),
                    'name' => 'Local',
                ],
                [
                    'id' => fake()->uuid(),
                    'name' => 'Production',
                ],
            ],
            'environment' => [
                'id' => fake()->uuid(),
                'name' => fake()->name(),
            ],
        ], 200),
    ]);

    Mail::fake();

    Storage::fake();

    Mail::assertNothingSent();

    $this->artisan('postman-backup')->assertExitCode(0);

    Mail::assertSent(PostmanBackupMail::class, fn (PostmanBackupMail $mail): array => $mail->attachments());
});
