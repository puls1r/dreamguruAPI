<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', App\Http\Controllers\Api\V1\Auth\RegisterController::class)->middleware('guest');
        Route::post('/login', App\Http\Controllers\Api\V1\Auth\LoginController::class)->middleware('guest')->name('login');
        Route::post('/logout', App\Http\Controllers\Api\V1\Auth\LogoutController::class)->middleware('auth:sanctum');
        
    });
    
    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\V1\CategoryController::class, 'getCategories']);
        Route::post('/', [App\Http\Controllers\Api\V1\CategoryController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::get('/{category_id}', [App\Http\Controllers\Api\V1\CategoryController::class, 'show']);
        Route::put('/{category_id}', [App\Http\Controllers\Api\V1\CategoryController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::delete('/{category_id}', [App\Http\Controllers\Api\V1\CategoryController::class, 'delete'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        
    });

    Route::prefix('students')->group(function(){
        Route::prefix('/{user_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\User\Student\StudentController::class, 'show']);
            Route::prefix('/courses')->group(function(){
                Route::get('/', [App\Http\Controllers\Api\V1\User\Student\Progression\StudentCourseController::class, 'getStudentCourses'])->middleware('auth:sanctum');
            });
            Route::prefix('/parts')->group(function(){
                Route::get('/{part_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\PartProgressionController::class, 'show']);
                Route::post('/{part_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\PartProgressionController::class, 'create'])->middleware('auth:sanctum');
                Route::put('/{part_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\PartProgressionController::class, 'update'])->middleware('auth:sanctum');
            });
            Route::prefix('/assignments')->group(function(){
                Route::get('/{assignment_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\AssignmentProgressionController::class, 'show']);
                Route::post('/{assignment_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\AssignmentProgressionController::class, 'create'])->middleware('auth:sanctum');
                Route::put('/{assignment_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\AssignmentProgressionController::class, 'update'])->middleware('auth:sanctum');
            });
            Route::prefix('/quizzes')->group(function(){
                Route::get('/', [App\Http\Controllers\Api\V1\User\Student\Progression\QuizProgressionController::class, 'index'])->middleware('auth:sanctum');
                Route::get('/{quiz_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\QuizProgressionController::class, 'show'])->middleware('auth:sanctum');
                Route::post('/{quiz_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\QuizProgressionController::class, 'create'])->middleware('auth:sanctum');
                Route::put('/{quiz_id}', [App\Http\Controllers\Api\V1\User\Student\Progression\QuizProgressionController::class, 'update'])->middleware('auth:sanctum');
                Route::post('/{quiz_id}/answers', [App\Http\Controllers\Api\V1\User\Student\Progression\QuizProgressionController::class, 'storeAnswer'])->middleware('auth:sanctum');
            });
        });
    });
 
    Route::prefix('teachers')->group(function(){
        Route::get('/', [App\Http\Controllers\Api\V1\User\Teacher\TeacherController::class, 'index']);
        Route::prefix('/{teacher_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\User\Teacher\TeacherController::class, 'show']);
            Route::prefix('/courses')->group(function(){
                Route::get('/', [App\Http\Controllers\Api\V1\User\Teacher\TeacherController::class, 'getCourses']);
                Route::get('/draft', [App\Http\Controllers\Api\V1\User\Teacher\TeacherController::class, 'getDraftCourses'])->middleware(['auth:sanctum', 'role:teacher,admin']);
                Route::get('/archived', [App\Http\Controllers\Api\V1\User\Teacher\TeacherController::class, 'getArchivedCourses'])->middleware(['auth:sanctum', 'role:teacher,admin']);
                
            });
        });
    });
    
    Route::prefix('users')->group(function (){
        Route::get('/self', [App\Http\Controllers\Api\V1\UserController::class, 'show'])->middleware(['auth:sanctum']);
        Route::put('/self/security', [App\Http\Controllers\Api\V1\UserController::class, 'updateAccountSecurity'])->middleware(['auth:sanctum']);
        Route::put('/self/profile', [App\Http\Controllers\Api\V1\UserController::class, 'updateProfile'])->middleware(['auth:sanctum']);
    });

    Route::prefix('courses')->group(function(){
        Route::get('/', [App\Http\Controllers\Api\V1\Course\CourseController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Api\V1\Course\CourseController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::prefix('/{course_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Course\CourseController::class, 'show']);
            Route::put('/', [App\Http\Controllers\Api\V1\Course\CourseController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/sections', [App\Http\Controllers\Api\V1\Course\SectionController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/rate', [App\Http\Controllers\Api\V1\Course\CourseRatingController::class, 'create'])->middleware(['auth:sanctum']);
            Route::get('/rating', [App\Http\Controllers\Api\V1\Course\CourseRatingController::class, 'getCourseRating']);
        });
    });

    Route::prefix('sections')->group(function (){
        Route::prefix('/{section_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Course\SectionController::class, 'show']);
            Route::put('/', [App\Http\Controllers\Api\V1\Course\SectionController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/parts', [App\Http\Controllers\Api\V1\Course\SectionPartController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/assignments', [App\Http\Controllers\Api\V1\Course\AssignmentController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/discussions', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/quizzes', [App\Http\Controllers\Api\V1\Course\SectionQuizController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::get('/order', [App\Http\Controllers\Api\V1\Course\SectionController::class, 'getContentOrder'])->middleware(['auth:sanctum']);
        });
    });

    Route::prefix('parts')->group(function (){
        Route::prefix('/{part_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Course\SectionPartController::class, 'show'])->middleware(['auth:sanctum', 'purchased']);
            Route::put('/', [App\Http\Controllers\Api\V1\Course\SectionPartController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        });
    });

    Route::prefix('assignments')->group(function (){
        Route::prefix('/{assignment_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Course\AssignmentController::class, 'show']);
            Route::put('/', [App\Http\Controllers\Api\V1\Course\AssignmentController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        });
    });

    Route::prefix('discussions')->group(function (){
        Route::prefix('/{discussion_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'show']);
            Route::put('/', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'update'])->middleware('auth:sanctum');
            Route::post('/replies', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'createReply'])->middleware('auth:sanctum');
        });
    });

    Route::prefix('replies')->group(function (){
        Route::prefix('/{reply_id}')->group(function(){
            Route::put('/', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'updateReply'])->middleware('auth:sanctum');
            Route::delete('/', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'deleteReply'])->middleware('auth:sanctum');
            Route::put('/answer', [App\Http\Controllers\Api\V1\Course\SectionDiscussionController::class, 'updateReplyToAnswer'])->middleware('auth:sanctum');
        });
    });

    Route::prefix('quizzes')->group(function (){
        Route::post('/', [App\Http\Controllers\Api\V1\Quiz\QuizController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::prefix('/{quiz_id}')->group(function(){
            Route::get('/', [App\Http\Controllers\Api\V1\Quiz\QuizController::class, 'show'])->middleware('auth:sanctum');
            Route::put('/', [App\Http\Controllers\Api\V1\Quiz\QuizController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::post('/questions', [App\Http\Controllers\Api\V1\Quiz\QuestionController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
            Route::delete('/questions/{question_id}', [App\Http\Controllers\Api\V1\Quiz\QuestionController::class, 'detachQuestionFromQuiz'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        });
    });

    Route::prefix('questions')->group(function (){
        Route::get('/{question_id}', [App\Http\Controllers\Api\V1\Quiz\QuestionController::class, 'show'])->middleware('auth:sanctum');
        Route::put('/{question_id}', [App\Http\Controllers\Api\V1\Quiz\QuestionController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::delete('/{question_id}/answers/{answer_id}', [App\Http\Controllers\Api\V1\Quiz\QuestionController::class, 'deleteAnswer'])->middleware(['auth:sanctum', 'role:teacher,admin']);
    });

    Route::prefix('section_quizzes')->group(function (){
        Route::get('/{section_quiz_id}', [App\Http\Controllers\Api\V1\Course\SectionQuizController::class, 'show'])->middleware('auth:sanctum');
        Route::put('/{section_quiz_id}', [App\Http\Controllers\Api\V1\Course\SectionQuizController::class, 'update'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::delete('/{section_quiz_id}', [App\Http\Controllers\Api\V1\Course\SectionQuizController::class, 'delete'])->middleware(['auth:sanctum', 'role:teacher,admin']);
        Route::post('/{section_quiz_id}/start', [App\Http\Controllers\Api\V1\Student\Progression\QuizProgressionController::class, 'create'])->middleware(['auth:sanctum', 'role:teacher,admin']);
    });

    Route::prefix('admin')->group(function(){
        Route::put('/users/{user_id}/role', [App\Http\Controllers\Api\V1\User\Admin\AdminController::class, 'updateUserRole'])->middleware(['auth:sanctum', 'role:admin']);
    });

    //payment
    Route::post('/charge', App\Http\Controllers\Api\V1\Payment\ChargeController::class)->middleware('auth:sanctum');

    //Xendit Callback
    Route::post('/callback/xendit/ewallet/status', [App\Http\Controllers\Api\V1\Callback\XenditCallbackController::class, 'eWalletPaymentStatus'])->middleware('xendit');
    Route::post('/callback/xendit/retail/status', [App\Http\Controllers\Api\V1\Callback\XenditCallbackController::class, 'retailPaymentStatus'])->middleware('xendit');

    //Midtrans Callback
    Route::post('/callback/midtrans/payment/status', [App\Http\Controllers\Api\V1\Callback\MidtransCallbackController::class, 'paymentStatus']);
    Route::post('/callback/midtrans/card/checkout/status', [App\Http\Controllers\Api\V1\Callback\MidtransCallbackController::class, 'cardPaymentStatus']);
});
