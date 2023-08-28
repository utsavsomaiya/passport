<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Locale;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class LocaleController extends Controller
{
    public function fetch(): JsonResponse
    {
        $locales = Locale::listOfLocales();

        return Response::json(['locales' => $locales]);
    }
}
