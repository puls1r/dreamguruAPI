<?php

namespace App\Http\Controllers\Api\V1\User\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Http\Resources\UserResource;

class TeacherController extends Controller
{
    public function index(){
        $teachers = User::where('role', 'teacher')->with('profile')->get();
        return $teachers;
    }

    public function show($teacher_id){
        $user = User::with('teacher')->where('id', '=', $teacher_id)->where('role', '=', 'teacher')->with('profile')->firstOrFail();
        return (new UserResource($user));
    }

    public function getCourses($teacher_id){
        $courses = Course::with('teacher', 'students')->where('teacher_id', '=', $teacher_id)->where('status', '=', 'completed')->get();
        return response($courses);
    }

    public function getDraftCourses($teacher_id){
        $courses = Course::with('teacher')->where('teacher_id', '=', $teacher_id)->where('status', '=', 'draft')->get();
        return response($courses);
    }

    public function getArchivedCourses($teacher_id){
        $courses = Course::with('teacher', 'students')->where('teacher_id', '=', $teacher_id)->where('status', '=', 'archived')->get();
        return response($courses);
    }
}
