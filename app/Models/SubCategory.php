<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','img_path','category_id'];
    protected $guarded = [];
    protected $table = 'sub_categories';
    protected $hidden = ['id'];


    public function category(): BelongsTo {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
