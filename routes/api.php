<?php

use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\UserControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/user',UserControllerApi::class);
Route::get('/users/{id}/avatar',[UserControllerApi::class, 'getAvatar']);
//Route::post('/language/switch', [LanguageController::class, ''])->name('language.switch');
