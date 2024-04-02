<?php


use App\Enums\UserRoleEnums;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\CurrencyExchangeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\CourseContributorController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CommentController;


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
Route::get('/job/listV2', [JobPostController::class, 'joblist'])->name('job.listV2');
Route::get('job/{JobPost}/detail', [JobPostController::class, 'jobDetail'])->name('job.detail');

//users
Route::get('users/top_pts', [PageController::class, 'TopPointsUserList'])->name('users.top_pts');
Route::get('users/teachers', [PageController::class, 'teacherLists'])->name('users.teachers');

//course
Route::get('course/{course_id}/enroll', [PageController::class, 'CourseEnroll'])->name('course.enroll');
Route::get('/list/learner', [PageController::class, 'showCourses'])->name('course.list.learners');

//home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//authenticator +++++++++++++

Route::group(['middleware' => ['auth', 'verified']], function () {
    //dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('user/dashboard', [PageController::class, 'UserDashboard'])->name('user.dashboard');


    // Job
    Route::group(['prefix' => '/job'], function (){
        Route::get('/list', [JobPostController::class, 'list'])->name('job.list')->middleware('isAdmin');
        Route::group(['middleware' => 'isAdmin'], function (){
            Route::get('/post', [JobPostController::class, 'index'])->name('job.post');
            Route::post('/store', [JobPostController::class, 'store'])->name('job.store');
            Route::put('/{JobPost}/update', [JobPostController::class, 'update'])->name('job.update');
            Route::get('/{JobPost}', [JobPostController::class, 'show'])->name('job.show');
            Route::delete('/{JobPost}', [JobPostController::class, 'destroy'])->name('job.destroy');
        });
    });

    //user
    Route::get('profile/{id}', [UserController::class, 'showProfile'])->name('user.profile');
    Route::post('/profile/update/', [UserController::class, 'updateUserProfile'])->name('user.profile.update');
    Route::post('/profile/change/', [UserController::class, 'changeUserPassword'])->name('user.password.change');
    Route::group(['prefix' => '/user', 'middleware'=>'isAdmin'], function (){

        Route::resource('/dtl', UserController::class)->names([
            'index' => 'user.dtl.index',
            'create' => 'user.dtl.create',
            'store' => 'user.dtl.store',
            'show' => 'user.dtl.show',
            'edit' => 'user.dtl.edit',
            'update' => 'user.dtl.update',
            'destroy' => 'user.dtl.destroy',
        ]);
        Route::post('/dtl/pf_update/', [UserController::class, 'updateProfile'])->name('user.dtl.pf_update');
        Route::post('/dtl/ch_pass/', [UserController::class, 'changePassword'])->name('user.dtl.ch_pass');
        Route::put('/role/{id}/update',[UserController::class, 'roleUpdate'])->name('role.update');
        Route::post('/role/bulkInsert', [\App\Http\Controllers\UserRoleController::class, 'bulkInsert'])->name('user.role.bulkInsert');
    });

    //category
    //အကယ်၍ page 404 error ဖြစ်ပေါ်ပါက custom route များကို resource route များပေါ်တွင်ထားရမည်။
    Route::get('/category/lst', [CategoryController::class, 'listingV1'])->name('category.lst_V1')->middleware('isAdmin');
    Route::get('category/mdf/{category}', [CategoryController::class, 'showV2'])->name('category.modify')->middleware('isAdmin');
    Route::resource('category', CategoryController::class)->names([
        'index' => 'category.index',
        'store' => 'category.store',
        'show' => 'category.show',
        'edit' => 'category.edit',
        'update' => 'category.update',
        'destroy' => 'category.destroy',
    ])->middleware('isAdmin');

    Route::resource('sub_category', SubCategoryController::class)->names([
        'index' => 'sub_category.index',
        'store' => 'sub_category.store',
        'show' => 'sub_category.show',
        'edit' => 'sub_category.edit',
        'update' => 'sub_category.update',
        'destroy' => 'sub_category.destroy',
    ])->middleware('isAdmin');

    Route::group(['middleware' => 'notStudent'], function (){
        Route::any('course/toApprove/{id}', [CourseController::class, 'updateToApproveState'])->name('course.toApprove');
        Route::resource('course', CourseController::class);
        Route::resource('contributor', CourseContributorController::class);
        Route::get('lesson/{lesson_id}/review', [LessonController::class, 'showAtAdmin'])->name('lesson.review');
        Route::any('lesson/{course_id}/createForm', [LessonController::class, 'createForm'])->name('lesson.createForm');
        Route::resource('lesson', LessonController::class);
        Route::post('exercise/restore/{exercise_id}',[ExerciseController::class, 'restore'])->name('exercise.restore');
        Route::delete('exercise/force_del/{exercise_id}',[ExerciseController::class, 'forceDelete'])->name('exercise.force_del');
        Route::get('question/{exercise_id}/form', [ExerciseController::class, 'showQuestionCreateForm'])->name('question.show.form');
        Route::resource('exercise', ExerciseController::class);
        Route::put('question/{question}/{exercise_id}', [QuestionController::class, 'updateQuestion'])->name('question.updateQuestion');
        Route::post('question/{exercise}', [QuestionController::class, 'storeQuestion'])->name('question.storeQuestion');
        Route::resource('question', QuestionController::class);
        Route::get('enroll/list', [CourseEnrollController::class, 'ListPage'])->name('enroll.list');
    });

    Route::group(['middleware' => 'isAdmin', 'prefix' => 'exchange'], function() {
        Route::get('edit', [CurrencyExchangeController::class, 'edit'])->name('exchange.edit');
        Route::put('usUpdate', [CurrencyExchangeController::class, 'updateUSDollar'])->name('usUpdate');
        Route::put('ptsUpdate', [CurrencyExchangeController::class, 'updatePts'])->name('ptsUpdate');
    });

    //enroll course
    Route::group(['prefix' => 'course'], function (){
        Route::get('{course_id}/detail', [PageController::class, 'courseDetail'])->name('course.detail')->middleware('enrolled');
        Route::post('ptsPayment', [CourseEnrollController::class, 'PtsPayment'])->name('course.ptsPayment');
        Route::post('cardPayment', [CourseEnrollController::class, 'cardPayment'])->name('course.cardPayment');
        Route::post('freePayment', [CourseEnrollController::class, 'FreePayment'])->name('course.freePayment');
        Route::delete('enroll/delete', [CourseEnrollController::class, 'deleteEnroll'])->name('enroll.delete');
    });

    //Exercises
    Route::group(['prefix' => 'exercise'], function (){
        Route::get('list/{id}', [ExerciseController::class, 'showExerciseList'])->name('exercise.list');
        Route::get('{exercise}/questions_learner_form', [ExerciseController::class, 'showToLearners'])->name('exercise.questions_learner_form');
        Route::post('{exercise}/submit_answer', [ExerciseController::class, 'submitAnswers'])->name('exercise.submit');
        Route::get('/{exercise}/answer-form', [ExerciseController::class, 'answerForm'])->name('exercise.answer_form');
    });

    //Forum
    Route::group(['prefix' => 'forum'], function (){
        Route::get('{lesson}', [ForumController::class, 'showForumList'])->name('forums');
        Route::post('', [ForumController::class, 'store'])->name('forums.store');
        Route::delete('{forum}', [ForumController::class, 'destroy'])->name('forums.destroy');

        Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/{comment}/comments', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    //Activities
    Route::get('activities/log', [PageController::class, 'showAllActivities'])->name('activities')->middleware('isAdmin');

});


//language switching
Route::post('/language-switch', [LanguageController::class, 'languageSwitch'])->name('language.switch');

Route::get('/fetch-data', function () {
    try {
        // Fetch XSRF token from external server
        $response = Http::get('http://forex.cbm.gov.mm/api/latest/token');
        $token = $response->json()['token'];

        // Fetch data using the obtained token
        $dataResponse = Http::withHeaders([
            'X-XSRF-TOKEN' => $token,
        ])->get('http://forex.cbm.gov.mm/api/latest');

        return $dataResponse->json();
    } catch (Exception $e) {
        // Log the error
        \Log::error('Error fetching data: ' . $e->getMessage());

        // Return an error response
        return response()->json(['error' => 'Error fetching data'], 500);
    }
});


