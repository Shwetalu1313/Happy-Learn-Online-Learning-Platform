<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['title','description', 'image', 'courseType', 'fees', 'state', 'createdUser_id', 'approvedUser_id', 'sub_category_id'];

    public function creator(): BelongsTo{
        return $this->belongsTo(User::class,'createdUser_id','id');
    }

    public function approver(): BelongsTo{
        return $this->belongsTo(User::class,'approvedUser_id','id');
    }

    public function sub_category(): BelongsTo{
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }
}
