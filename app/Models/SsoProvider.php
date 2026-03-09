<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_key',
        'display_name',
        'driver',
        'client_id',
        'client_secret',
        'redirect_uri',
        'scopes',
        'tenant',
        'icon_class',
        'sort_order',
        'is_enabled',
    ];

    protected $hidden = [
        'client_secret',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
        'scopes' => 'array',
        'client_secret' => 'encrypted',
    ];
}
