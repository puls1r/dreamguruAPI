<?php

namespace App\Http\Controllers\Api\V1\User\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCourse;
use App\Models\Course;
use App\Models\UserQuiz;
use App\Models\UserSectionPart;
use App\Models\UserAssignment;

class StudentCourseController extends Controller
{
    public function getStudentCourses(){
        $user_course = UserCourse::with('course.teacher.profile')
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->get();
        return $user_course;
    }

    public function getStudentCompletedCourses(){
        $user_course = UserCourse::with('course.teacher.profile')
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->get();
        return $user_course;
    }

    public function getStudentQuizzes(){
        $user_quizzes = UserQuiz::with('section_quiz.quiz.questions')
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->get();

        foreach($user_quizzes as $quiz){
            $total_question = count($quiz->section_quiz->quiz->questions);
            $quiz->total_question = $total_question;
        }

        $user_quizzes->load('section_quiz.course_section.course');

        return $user_quizzes;
    }

    public function getStudentCompletedQuizzes(){
        $user_quizzes = UserQuiz::with('section_quiz.quiz.questions', 'section_quiz.course_section.course')
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->get();
        foreach($user_quizzes as $quiz){
            $total_question = count($quiz->section_quiz->quiz->questions);
            $quiz->total_question = $total_question;
        }

        return $user_quizzes;
    }

    public function courseComplete($user_id, $course_id){
        $user_course = UserCourse::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->first();
        if($user_course->status == 'completed'){
            return response('OK');
        }

        $course = Course::with('course_sections.parts', 'course_sections.quizzes', 'course_sections.assignments')->find($course_id);

        //count total contents
        $total_contents = 0;
        foreach($course->course_sections as $section){
            $total_contents += count($section->parts);
            $total_contents += count($section->quizzes);
            $total_contents += count($section->assignments);
        }

        //get student progression
        $user_parts = 0;
        foreach($course->course_sections as $section){
            foreach($section->parts as $part){
                $part_progression = UserSectionPart::where('user_id', $user_id)
                    ->where('section_part_id', $part->id)
                    ->where('status', 'completed')
                    ->first();
                if($part_progression != null){
                    $user_parts += 1;
                }
            }
        }

        $user_quizzes = 0;
        foreach($course->course_sections as $section){
            foreach($section->quizzes as $quiz){
                $quiz_progression = UserQuiz::where('user_id', $user_id)
                    ->where('section_quiz_id', $quiz->id)
                    ->where('status', 'completed')
                    ->where('is_success', 1)
                    ->latest('created_at')
                    ->first();
                if($quiz_progression != null){
                    $user_quizzes += 1;
                }
            }
        }

        $user_assignments = 0;
        foreach($course->course_sections as $section){
            foreach($section->assignments as $assignment){
                $assignment_progression = UserAssignment::where('user_id', $user_id)
                    ->where('assignment_id', $assignment->id)
                    ->where('status', 'completed')
                    ->latest('created_at')
                    ->first();
                if($assignment_progression != null){
                    $user_assignments += 1;
                }
            }
        }

        $total_progression = $user_assignments + $user_parts + $user_quizzes;
        
        if($total_contents == $total_progression){
            $user_course = UserCourse::where('user_id', $user_id)
                ->where('course_id', $course_id)->firstOrFail();
            $user_course->status = 'completed';
            $user_course->save();

            return response('course complete!', 201);
        }

        else{
            return response('condition not satisfied', 412);
        }

        

    }
}
