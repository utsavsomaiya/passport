<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Queries\ProductQueries;

class ProductController extends Controller
{
    public function __construct(
        protected ProductQueries $productQueries
    ) {

    }
}
