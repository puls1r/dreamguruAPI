<?php

namespace App\Http\Controllers\Api\V1\User\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\UserCourse;
use App\Http\Resources\UserResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseCollection;

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
        foreach($courses as $course){
            $course->total_students = UserCourse::where('course_id', $course->id)->count();
            $course->students_on_progress = UserCourse::where('course_id', $course->id)->where('status','in_progress')->count();
            $course->students_completed = UserCourse::where('course_id', $course->id)->where('status','completed')->count();
        }
        return response(new CourseCollection($courses));
    }

    public function getDraftCourses($teacher_id){
        $courses = Course::with('teacher')->where('teacher_id', '=', $teacher_id)->where('status', '=', 'draft')->get();
        return response($courses);
    }

    public function getArchivedCourses($teacher_id){
        $courses = Course::with('teacher', 'students')->where('teacher_id', '=', $teacher_id)->where('status', '=', 'archived')->get();
        foreach($courses as $course){
            $course->total_students = UserCourse::where('course_id', $course->id)->count();
            $course->students_on_progress = UserCourse::where('course_id', $course->id)->where('status','in_progress')->count();
            $course->students_completed = UserCourse::where('course_id', $course->id)->where('status','completed')->count();
        }
        return response(new CourseCollection($courses));
    }

    public function showTeacherCourse($teacher_id, $course_id){
        // check if user is admin
        if(!Auth::user()->role == 'admin'){
            //check if user is owner
            if(Auth::id() != Course::find($course_id)->teacher_id){
                return response('forbidden',403);
            }
        }

        $course = Course::with(['teacher.profile', 
        'course_sections' => function($q){
            $q->where('status', '!=', 'archived')->orderBy('order');
        }, 
        'course_sections.section_content_orders' => function($q){
            $q->orderBy('order');
        }, 
        'category',
        'students'])->where('id', '=', $course_id)->where('teacher_id', '=', $teacher_id)->firstOrFail();
       
        $course->total_students = UserCourse::where('course_id', $course_id)->count();
        $course->students_on_progress = UserCourse::where('course_id', $course->id)->where('status','in_progress')->count();
        $course->students_completed = UserCourse::where('course_id', $course->id)->where('status','completed')->count();
        return response(new CourseResource($course));
    }
}
