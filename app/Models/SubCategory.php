<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','img_path','category_id'];
    protected $guarded = [];
    protected $table = 'sub_categories';
    protected $hidden = ['id'];

    public static function getModelName(): string
    {
        return 'SubCategory';
    }

    public function getImgPathAttribute($value): string
    {
        if (! is_string($value) || trim($value) === '' || trim($value) === '0') {
            return 'cate/sub_cate/sample.jpg';
        }

        return str_replace('\\', '/', trim($value));
    }


    public function category(): BelongsTo {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function courses(): HasMany{
        return $this->hasMany(Course::class,'sub_category_id','id');
    }
}
