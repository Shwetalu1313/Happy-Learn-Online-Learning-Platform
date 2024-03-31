<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','img_path'];
    protected $guarded = [];
    protected $table = 'categories';
    protected $hidden = ['id'];

    public static function getModelName(): string
    {
        return 'Category';
    }

    public function sub_categories(): HasMany{
        return $this->hasMany(SubCategory::class);

    }
}
