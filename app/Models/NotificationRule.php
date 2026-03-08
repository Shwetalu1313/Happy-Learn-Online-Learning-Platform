<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_key',
        'label',
        'description',
        'is_enabled',
        'channels',
        'template_title',
        'template_subject',
        'template_line',
        'template_action_text',
        'template_end',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'channels' => 'array',
    ];
}
