<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    use HasUuids;

    /**
     * @see https://laravel-auditing.com/guide/community/troubleshooting.html#attribute-accessors-and-modifiers-are-not-applied-to-softdeleted-models
     *
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo()->withTrashed();
    }
}
