<?php

namespace App\Http\Controllers\Api\V1\User\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCourse;
use App\Models\UserQuiz;

class StudentCourseController extends Controller
{
    public function getStudentCourses(){
        $user_course = UserCourse::where('user_id', Auth::id())->where('status', 'in_progress')->get();
        return $user_course;
    }

    public function getStudentCompletedCourses(){
        $user_course = UserCourse::where('user_id', Auth::id())->where('status', 'completed')->get();
        return $user_course;
    }

    public function getStudentQuizzes(){
        $user_quizzes = UserQuiz::where('user_id', Auth::id())->where('status', 'in_progress')->get();
        return $user_quizzes;
    }

    public function getStudentCompletedQuizzes(){
        $user_quizzes = UserQuiz::where('user_id', Auth::id())->where('status', 'completed')->get();
        return $user_quizzes;
    }
}
