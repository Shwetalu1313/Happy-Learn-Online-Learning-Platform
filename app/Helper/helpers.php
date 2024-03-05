<?php

use Illuminate\Support\Facades\Route;

/*
 *
 * This function will match current route name vs route name you insert.
 *
 * usage
 *
 * {{is_Active_route('home')}}
 *
 *
 * */
if (!function_exists('is_active_route')){
    function is_active_route($routeNames): string
    {
        if (!is_array($routeNames)) {
            $routeNames = [$routeNames];
        }

        foreach ($routeNames as $routeName) {
            if (Route::currentRouteName() === $routeName) {
                return 'active';
            }
        }

        return '';
    }
}


if (!function_exists('is_active_route_collapse_show')){
    function is_active_route_collapse_show($routeNames): string
    {
        if (!is_array($routeNames)) {
            $routeNames = [$routeNames];
        }

        foreach ($routeNames as $routeName) {
            if (Route::currentRouteName() === $routeName) {
                return 'show';
            }
        }

        return '';
    }
}

if (!function_exists('is_active_route_val')){
    /**
     * Return the String Value if this is a current route;
     * @param $routeNames
     * @param string $what_ever_val
     * @return string
     */
    function is_active_route_val($routeNames, string $firstvalue, string $nextvalue): string{
        if (!is_array($routeNames)) {
            $routeNames = [$routeNames];
        }

        foreach ($routeNames as $routeName) {
            if (Route::currentRouteName() === $routeName) {
                return $firstvalue;
            }
        }

        return $nextvalue;
    }
}

