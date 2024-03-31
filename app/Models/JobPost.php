<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = ['title','requirements'];

    public static function getModelName(): string
    {
        return 'JobPost';
    }

    protected $guarded = [];
}
