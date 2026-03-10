<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPurgeArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_key',
        'source_pk',
        'payload',
        'source_created_at',
        'archived_at',
        'purge_after_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'source_created_at' => 'datetime',
        'archived_at' => 'datetime',
        'purge_after_at' => 'datetime',
    ];
}
