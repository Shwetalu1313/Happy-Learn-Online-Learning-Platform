<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRoleEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Illuminate\Auth\Passwords\CanResetPassword; //note:: this is important when you want to enable reset password method.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','phone','birthdate','points','avatar','role','about','password'];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRoleEnums::class,
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'createdUser_id')
            ->orWhere('approvedUser_id', $this->id);
    }

    public function contributor(): HasMany {
        return $this->hasMany(CourseContributor::class);
    }

    public function lessons(): HasMany {
        return $this->hasMany(Lesson::class,'creator_id');
    }

    public static function students() {
        return User::where('role', UserRoleEnums::STUDENT->value);
    }

}
