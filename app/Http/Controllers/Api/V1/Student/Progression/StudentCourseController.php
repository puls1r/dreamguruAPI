<?php

namespace App\Http\Controllers\Api\V1\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCourse;

class StudentCourseController extends Controller
{
    public function getStudentCourses(){
        $user_course = UserCourse::where('user_id', Auth::id())->get();
        return $user_course;
    }
}
