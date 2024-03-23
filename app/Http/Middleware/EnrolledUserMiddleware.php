<?php

namespace App\Http\Middleware;

use App\Models\CourseEnrollUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnrolledUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $courseId = $request->route('course_id'); // Assuming course_id is the parameter in your route

        // Check if the current user is enrolled in the course
        $enrollment = CourseEnrollUser::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->exists();

        if (!$enrollment) {
            // User is not enrolled, redirect them to another route or show an error
            //return redirect()->route('not_enrolled_route');
            // Alternatively, you can abort with a 403 Forbidden error
            abort(403);
        }

        // User is enrolled, allow them to access the route
        return $next($request);
    }
}
