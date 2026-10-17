<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable("name", "data", "last_activated")]
class Preset extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'last_activated' => 'datetime',
        ];
    }
}
