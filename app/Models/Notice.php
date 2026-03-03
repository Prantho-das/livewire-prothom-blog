<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'body',
        'type',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }
}
