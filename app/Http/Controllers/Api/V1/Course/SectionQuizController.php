<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SectionQuiz;
use App\Models\CourseSection;
use App\Models\SectionContentOrder;

class SectionQuizController extends Controller
{
    public function show($section_quiz_id){
        $section_quiz = SectionQuiz::with('quiz')->findOrFail($section_quiz_id);
        if(!$section_quiz){                          //gunakan slug untuk mengidentifikasi model
            $section_quiz = SectionQuiz::where('slug', $section_quiz_id)->first();
        }

        return response($section_quiz);
    }

    public function create(Request $request, $section_id){
        $this->validate($request, [
            'quiz_id' => ['required', 'exists:quizzes,id'],
            'title' => ['required', 'string'],
        ]);

        $section = CourseSection::findOrFail($section_id);
        $section_quiz = new SectionQuiz;
        $section_quiz->course_section_id = $request->section_id;
        $section_quiz->quiz_id = $request->quiz_id;
        $section_quiz->title = $request->title;
        $section_quiz->status = 'draft';

        if(!$section_quiz->save()){
            return response('section_quiz creation failed!', 500);
        }

        $section_content_order = new SectionContentOrder;
        $section_content_order->course_section_id = $section_quiz->course_section_id;
        $section_content_order->content_id = $section_quiz->slug;
        $section_content_order->title = $section_quiz->title;
        $section_content_order->endpoint = 'section_quizzes';
        $section_content_order->order = SectionContentOrder::where('course_section_id', $section_quiz->course_section_id)->max('order') + 1;

        $section_content_order->save();
        
        return response($section_quiz, 201);
    }

    public function update(Request $request, $section_quiz_id){
        $this->validate($request, [
            'quiz_id' => ['exists:quizzes'],
            'title' => ['string'],
            'order' => ['numeric'],
            'status' => ['in:draft,completed'],
        ]);

        //check apakah sudah ada user yang mengerjakan quiz
        $section_quiz = SectionQuiz::findOrFail($section_quiz_id);
        if($section_quiz->user_quizzes()->count() > 0){
            return response('resource is currently in use and cannot be modified, contact the administrator for help', 403);
        }

        foreach($request->input() as $field => $value){
            $section_quiz->{$field} = $request->{$field};
        }

        if(!$section_quiz->save()){
            return response('section_quiz update failed!', 500);
        }

        if(isset($request->order)){
            $section_content_order = SectionContentOrder::findOrFail('course_section_id', $section_quiz->course_section_id);
            $section_content_order->order = $request->order;
            $section_content_order->title = $request->title;
    
            $section_content_order->save();
        }
        
        return response($section_quiz);
    }

    public function delete($section_quiz_id){
        $section_quiz = SectionQuiz::findOrFail($section_quiz_id);
        $section_quiz->status = 'archived';
        $section_quiz->save();

        return response('section quiz archived!');
    }
}
