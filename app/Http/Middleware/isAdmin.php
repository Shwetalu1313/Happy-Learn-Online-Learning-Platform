<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnums;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class   isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->value === UserRoleEnums::ADMIN->value) {
            return $next($request);
        }

        abort(403);
    }

}
