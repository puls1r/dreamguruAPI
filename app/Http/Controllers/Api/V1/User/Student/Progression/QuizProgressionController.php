<?php

namespace App\Http\Controllers\Api\V1\User\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\UserQuiz;
use App\Models\SectionQuiz;
use App\Models\UserQuizQuestion;
use App\Models\UserQuizQuestionAnswer;


class QuizProgressionController extends Controller
{
    public function index($user_id){
        $user_quiz = UserQuiz::where('user_id', '=', $user_id)->get();
        return response($user_quiz);
    }

    public function show($user_id, $user_quiz_id){
        $user_quiz = UserQuiz::where('user_id', '=', $user_id)->where('id', '=', $user_quiz_id)->firstOrFail();
        return response($user_quiz);
    }

    public function showUsingSectionQuiz($user_id, $section_quiz_id){
        $user_quiz = UserQuiz::with('user_quiz_questions.user_quiz_question_answers')->where('user_id' , $user_id)->where('section_quiz_id', $section_quiz_id)->latest('created_at')->firstOrFail();
        return response($user_quiz);
    }

    public function create($section_quiz_id){
        //get the latest quiz attempt
        $latest_user_quiz = UserQuiz::where('user_id', Auth::id())->where('section_quiz_id', $section_quiz_id)->latest('created_at')->first();
        
        //first time doing quiz
        if($latest_user_quiz == null){
            $user_quiz = new UserQuiz;
            $user_quiz->section_quiz_id = $section_quiz_id;
            $user_quiz->user_id = Auth::id();
            $user_quiz->user_score = 0;
            $user_quiz->time_spent = 0;
            $user_quiz->attempts = 1;
            $user_quiz->is_success = 0;
            $user_quiz->status = 'in_progress';            
        }

        else{
            //check max attempt
            $max_attempt = SectionQuiz::where('id', $section_quiz_id)->first()->max_attempt;

            //infinite tries
            if($max_attempt == 0){
                $user_quiz = new UserQuiz;
                $user_quiz->section_quiz_id = $section_quiz_id;
                $user_quiz->user_id = Auth::id();
                $user_quiz->user_score = 0;
                $user_quiz->time_spent = 0;
                $user_quiz->attempts = $latest_user_quiz->attempts + 1;
                $user_quiz->is_success = 0;
                $user_quiz->status = 'in_progress';

                $user_quiz->save();
                return response($user_quiz);
            }

            if(($latest_user_quiz->attempts + 1) <= $max_attempt){
                $user_quiz = new UserQuiz;
                $user_quiz->section_quiz_id = $section_quiz_id;
                $user_quiz->user_id = Auth::id();
                $user_quiz->user_score = 0;
                $user_quiz->time_spent = 0;
                $user_quiz->attempts = $latest_user_quiz->attempts + 1;
                $user_quiz->is_success = 0;
                $user_quiz->status = 'in_progress';
            }
            else{
                return response('max attempt has been reached!', 403);
            }
        }

        $user_quiz->save();
        return response($user_quiz);
    }

    public function storeAnswer(Request $request, $user_id, $user_quiz_id){
        //check user
        if($user_id != Auth::id()){
            return response('forbidden', 403);
        }

        $user_quiz = UserQuiz::findOrFail($user_quiz_id);
        $user_quiz->time_spent = $request->time_spent;
        $user_quiz->save();

        $user_quiz_question = UserQuizQuestion::where('user_quiz_id', $user_quiz_id)->where('question_id', $request->question_id)->first();
        
        if(!$user_quiz_question){
            $user_quiz_question = new UserQuizQuestion;
            $user_quiz_question->user_quiz_id = $user_quiz_id;
            $user_quiz_question->question_id = $request->question_id;
            $user_quiz_question->point = 0;
            $user_quiz_question->is_true = 0;

            $user_quiz_question->save();
            
            foreach($request->answers as $answer){
                $user_quiz_answer = new UserQuizQuestionAnswer;
                $user_quiz_answer->user_quiz_question_id = $user_quiz_question->id;
                $user_quiz_answer->answer = $answer['answer'];
                $user_quiz_answer->save();
            }
        }
        else{
            $user_quiz_answers = UserQuizQuestionAnswer::where('user_quiz_question_id', $user_quiz_question->id)->get();
            
            foreach($user_quiz_answers as $answer){
                $answer->delete();
            }

            foreach($request->answers as $answer){
                $user_quiz_answer = new UserQuizQuestionAnswer;
                $user_quiz_answer->user_quiz_question_id = $user_quiz_question->id;
                $user_quiz_answer->answer = $answer['answer'];
                $user_quiz_answer->save();
            }
        }
        
        return response('progress saved!', 201);
    }

    public function finishQuiz(Request $request, $user_id, $user_quiz_id){
        //check user
        if($user_id != Auth::id()){
            return response('forbidden', 403);
        }

        $user_quiz = UserQuiz::findOrFail($user_quiz_id);

        $user_quiz->status = 'completed';
        $user_quiz->time_spent = $request->time_spent;
        $user_quiz->save();
        $user_quiz->refresh();
        $user_quiz->load('user_quiz_questions.user_quiz_question_answers');
        
        $total_points = 0;
        //kalkulasi score
        foreach($user_quiz->user_quiz_questions as $user_quiz_question){
            foreach($user_quiz_question->user_quiz_question_answers as $quiz){
                $user_answers_arr[] = $quiz['answer'];
            }

            $question = Question::with(['answers' => function($query){
                $query->where('is_true',1);
            }])
            ->find($user_quiz_question->question_id);
            
            foreach($question->answers as $answer){
                $answer_keys_arr[] = $answer['answer'];
            }

            $diff1 = array_diff($user_answers_arr, $answer_keys_arr);
            $diff2 = array_diff($answer_keys_arr ,$user_answers_arr);

            if(count($diff1) == 0 && count($diff2) == 0){
                $user_quiz_question->point = $question->point;
                $user_quiz_question->is_true = 1;
                $total_points += $question->point;
            }
        }

        $user_quiz->user_score = $total_points;
        $section_quiz = SectionQuiz::where('id', $user_quiz->section_quiz_id)->first();
        if($user_quiz->user_score >=  $section_quiz->point_requirement){
            $user_quiz->is_success = 1;
        }
        $user_quiz->save();
        return response($user_quiz);
    }

    public function pauseQuiz(Request $request, $user_quiz_id){
        //check user
        if($user_id != Auth::id()){
            return response('forbidden', 403);
        }

        $user_quiz = UserQuiz::findOrFail($user_quiz_id);
        $user_quiz->attempt += 1;
        $user_quiz->time_spent = $request->time_spent;

        $user_quiz->save();
        return response('progress saved!');
    }
}
