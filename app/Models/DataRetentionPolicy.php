<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRetentionPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_key',
        'keep_days',
        'archive_grace_days',
        'exclude_unread_notifications',
        'is_enabled',
        'updated_by',
    ];

    protected $casts = [
        'keep_days' => 'integer',
        'archive_grace_days' => 'integer',
        'exclude_unread_notifications' => 'boolean',
        'is_enabled' => 'boolean',
        'updated_by' => 'integer',
    ];
}
