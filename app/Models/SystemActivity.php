<?php

namespace App\Models;

use App\Enums\UserRoleEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class SystemActivity extends Model
{
    use HasFactory;

    protected $fillable = ['table_name',
                            'ip_address',
                            'user_agent',
                            'user_id',
                            'short',
                            'about',
                            'target',
                            'route_name',];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public static function getData(bool $showAll, bool $adminTarget): Collection
    {
        $query = SystemActivity::orderBy('created_at', 'desc');

        if (!$showAll) {
            $query->take(10);
        }

        if ($adminTarget) {
            $query->where('target', UserRoleEnums::ADMIN->value);
        }

        return $query->get();
    }

    public static function createActivity(array $data): SystemActivity
    {
        return self::create($data);
    }


}


//to get browser name quickly
/*
 * if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
elseif (strpos($user_agent, 'Edge')) return 'Edge';
elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
elseif (strpos($user_agent, 'Safari')) return 'Safari';
elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';

return 'Other';

// Usage:

echo get_browser_name($_SERVER['HTTP_USER_AGENT']);*/
