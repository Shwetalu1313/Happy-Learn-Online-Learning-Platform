<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fillable = ['us_ex','pts_ex'];

    public static function getModelName(): string
    {
        return 'CurrencyExchange';
    }

    protected $casts = [
        'us_ex' => 'integer','pts_ex' => 'integer'
    ];

    public static function getUSD(){
        $exchange =  CurrencyExchange::first();
        return $exchange->us_ex;
    }

    public static function getPts(){
        $exchange =  CurrencyExchange::first();
        return $exchange->pts_ex;
    }
}
