<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fillable = ['us_ex','pts_ex'];

    protected $casts = [
        'us_ex' => 'integer','pts_ex' => 'integer'
    ];
}
