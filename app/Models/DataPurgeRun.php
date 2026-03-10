<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPurgeRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'run_type',
        'status',
        'triggered_by',
        'summary',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'triggered_by' => 'integer',
        'summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
