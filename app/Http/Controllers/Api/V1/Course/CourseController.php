<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseCollection;
use App\Rules\IsTeacher;
use App\Models\Course;
use App\Models\UserCourse;

class CourseController extends Controller
{
    protected $placeholder_img = 'default_background.jpg';

    public function index(){
        $courses = Course::with('teacher.profile')->get();
        return response(new CourseCollection($courses));
    }

    public function show($course_id){
        $course = Course::with('teacher.profile', 'course_sections.section_content_orders', 'category')->where('id', $course_id)->firstOrFail();
        if($course->status == 'draft'){
            return response('course is not yet available!', 403);
        }
        
        $course->total_students = UserCourse::where('course_id', $course_id)->count();

        return response(new CourseResource($course));
    }

    public function create(Request $request){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255', Rule::unique(Course::class)],
            'price' => ['required', 'numeric'],
            'desc' => ['required', 'string'],
            'level' => ['required', 'string', 'in:Beginner,Intermediate,Advanced'],
            'estimated_time' => ['required','date_format:H:i'],
            'language' => ['required', 'string', 'max:25'],
            'category_id' => ['required', 'exists:categories,id'],
            'teacher_id' => ['bail','required', 'exists:users,id', new IsTeacher],
        ]);


        $course = new Course;
        $course->category_id = $request->category_id;
        $course->teacher_id = $request->teacher_id;
        $course->title = $request->title;
        $course->price = $request->price;
        $course->desc = $request->desc;
        $course->level = $request->level;
        $course->language = $request->language;
        $course->estimated_time = $request->estimated_time;
        $course->hero_background = $this->placeholder_img;
        $course->thumbnail = $this->placeholder_img;
        $course->trailer = NULL;
        $course->status = 'draft';
        $course->discount_price = 0;
        $course->is_on_discount = 0;

        if(!$course->save()){
            return response('course creation failed!', 500);
        }
        
        return response(new CourseResource($course), 201);
    }

    public function update(Request $request, $course_id){
        // check if user is admin
        if(!Auth::user()->role == 'admin'){
            //check if user is owner
            if(Auth::id() != Course::findOrFail($course_id)->teacher_id){
                return response('forbidden',403);
            }
        }

        $this->validate($request, [
            'title' => ['string', 'max:255', Rule::unique(Course::class)->ignore($course_id, 'id')],
            'price' => ['numeric'],
            'desc' => ['string'],
            'level' => ['string', 'in:Beginner,Intermediate,Advanced'],
            'estimated_time' => ['date_format:H:i:s,H:i'],
            'language' => ['string', 'max:25'],
            'category_id' => ['exists:categories,id'],
            'trailer' => ['string', 'max:255'],
            'thumbnail' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'hero_background' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status' => ['string', 'in:draft,archived,completed'],
            'is_on_discount' => ['numeric', 'in:0,1'],
            'discount_price' => ['numeric'],
        ]);

        $course = Course::findOrFail($course_id);
        foreach($request->input() as $field => $value){
            $course->{$field} = $request->{$field};
        }

        if(!$course->save()){
            return response('course update failed!', 500);
        }
        
        return response(new CourseResource($course), 201);
    }
}
