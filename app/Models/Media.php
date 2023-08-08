<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Auditable;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class Media extends BaseMedia implements AuditableInterface
{
    use HasUuids;
    use Auditable;
}
