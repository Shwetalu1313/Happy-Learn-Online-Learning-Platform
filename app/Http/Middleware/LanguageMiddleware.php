<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = session('language'); //session value is stored in the var.
        // if (! in_array($language, ['en', 'mm'])) {
        //     abort(400);
        // }
        App::setlocale($language); // set the selected language
        
        Log::info("Language set to" . $language);
        return $next($request);
    }
}
