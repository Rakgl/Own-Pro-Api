<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'general_settings';

    protected $fillable = [
        'key',
        'name',
        'value',
        'type',
        'group',
        'description',
    ];

    protected $casts = [
        // No specific casts needed by default, but you could add them
        // if you have specific value types like 'array' or 'boolean'
    ];
}
