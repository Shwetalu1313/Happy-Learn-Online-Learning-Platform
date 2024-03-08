<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = ['title','description', 'image', 'courseType', 'fees', 'state', 'createdUser_id', 'approvedUser_id', 'sub_category_id'];
}
