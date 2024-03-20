<?php


use App\Enums\UserRoleEnums;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [PageController::class, 'welcome'])->name('/');

// google 0-Auth login
Route::get('/login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [GoogleLoginController::class, 'redirectToGoogleCallback']);

Auth::routes(['verify' => true]);


// email verification

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
/*========================================================================================*/


//guest +++++++++++++++

//job
Route::get('/job/intro', [PageController::class, 'jobformIntro'])->name('job.intro');
Route::get('/job/listV2', [\App\Http\Controllers\JobPostController::class, 'joblist'])->name('job.listV2');
Route::get('job/{JobPost}/detail', [\App\Http\Controllers\JobPostController::class, 'jobDetail'])->name('job.detail');

//users
Route::get('users/top_pts', [PageController::class, 'TopPointsUserList'])->name('users.top_pts');
Route::get('users/teachers', [PageController::class, 'teacherLists'])->name('users.teachers');

//home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//authenticator +++++++++++++

Route::group(['middleware' => ['auth', 'verified']], function () {
    //dashboard
    Route::get('/dashboard', [\App\Http\Controllers\PageController::class, 'dashboard'])->name('dashboard');
    Route::get('user/dashboard', [\App\Http\Controllers\PageController::class, 'UserDashboard'])->name('user.dashboard');


    // Job
    Route::group(['prefix' => '/job'], function (){
        Route::get('/list', [\App\Http\Controllers\JobPostController::class, 'list'])->name('job.list')->middleware('isAdmin');
        Route::group(['middleware' => 'isAdmin'], function (){
            Route::get('/post', [\App\Http\Controllers\JobPostController::class, 'index'])->name('job.post');
            Route::post('/store', [\App\Http\Controllers\JobPostController::class, 'store'])->name('job.store');
            Route::put('/{JobPost}/update', [\App\Http\Controllers\JobPostController::class, 'update'])->name('job.update');
            Route::get('/{JobPost}', [\App\Http\Controllers\JobPostController::class, 'show'])->name('job.show');
            Route::delete('/{JobPost}', [\App\Http\Controllers\JobPostController::class, 'destroy'])->name('job.destroy');
        });
    });

    //user
    Route::get('profile/{id}', [\App\Http\Controllers\UserController::class, 'showProfile'])->name('user.profile');
    Route::post('/profile/update/', [\App\Http\Controllers\UserController::class, 'updateUserProfile'])->name('user.profile.update');
    Route::post('/profile/change/', [\App\Http\Controllers\UserController::class, 'changeUserPassword'])->name('user.password.change');
    Route::group(['prefix' => '/user', 'middleware'=>'isAdmin'], function (){

        Route::resource('/dtl', \App\Http\Controllers\UserController::class)->names([
            'index' => 'user.dtl.index',
            'create' => 'user.dtl.create',
            'store' => 'user.dtl.store',
            'show' => 'user.dtl.show',
            'edit' => 'user.dtl.edit',
            'update' => 'user.dtl.update',
            'destroy' => 'user.dtl.destroy',
        ]);
        Route::post('/dtl/pf_update/', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.dtl.pf_update');
        Route::post('/dtl/ch_pass/', [\App\Http\Controllers\UserController::class, 'changePassword'])->name('user.dtl.ch_pass');
        Route::put('/role/{id}/update',[\App\Http\Controllers\UserController::class, 'roleUpdate'])->name('role.update');
        Route::post('/role/bulkInsert', [\App\Http\Controllers\UserRoleController::class, 'bulkInsert'])->name('user.role.bulkInsert');
    });

    //category
    //အကယ်၍ page 404 error ဖြစ်ပေါ်ပါက custom route များကို resource route များပေါ်တွင်ထားရမည်။
    Route::get('/category/lst', [\App\Http\Controllers\CategoryController::class, 'listingV1'])->name('category.lst_V1')->middleware('isAdmin');
    Route::get('category/mdf/{category}', [\App\Http\Controllers\CategoryController::class, 'showV2'])->name('category.modify')->middleware('isAdmin');
    Route::resource('category', \App\Http\Controllers\CategoryController::class)->names([
        'index' => 'category.index',
        'store' => 'category.store',
        'show' => 'category.show',
        'edit' => 'category.edit',
        'update' => 'category.update',
        'destroy' => 'category.destroy',
    ])->middleware('isAdmin');

    Route::resource('sub_category', \App\Http\Controllers\SubCategoryController::class)->names([
        'index' => 'sub_category.index',
        'store' => 'sub_category.store',
        'show' => 'sub_category.show',
        'edit' => 'sub_category.edit',
        'update' => 'sub_category.update',
        'destroy' => 'sub_category.destroy',
    ])->middleware('isAdmin');

    Route::group(['middleware' => 'notStudent'], function (){
        Route::any('course/toApprove/{id}', [\App\Http\Controllers\CourseController::class, 'updateToApproveState'])->name('course.toApprove');
        Route::resource('course', \App\Http\Controllers\CourseController::class);
        Route::resource('contributor', \App\Http\Controllers\CourseContributorController::class);
        Route::get('lesson/{lesson_id}/review', [\App\Http\Controllers\LessonController::class, 'showAtAdmin'])->name('lesson.review');
        Route::any('lesson/{course_id}/createForm', [\App\Http\Controllers\LessonController::class, 'createForm'])->name('lesson.createForm');
        Route::resource('lesson', \App\Http\Controllers\LessonController::class);
    });
});


//language switching
Route::post('/language-switch', [LanguageController::class, 'languageSwitch'])->name('language.switch');



