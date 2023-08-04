<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Queries\ProductBundleQueries;

class ProductBundleController extends Controller
{
    public function __construct(
        protected ProductBundleQueries $productBundleQueries
    ) {

    }

    public function create()
    {
        
    }

    public function update()
    {

    }
}
