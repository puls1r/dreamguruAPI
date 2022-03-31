<?php

namespace App\Http\Controllers\Api\V1\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function index(){
        $quizzes = Quiz::with('questions')->where('user_id', Auth::id())->get();
        foreach($quizzes as $quiz){
            $total_question = count($quiz->questions);
            $quiz->total_question = $total_question;
        }

        return response($quizzes);
    }

    public function show($quiz_id)
    {
        $quiz = Quiz::with('questions.answers', 'user')->findOrFail($quiz_id);

        foreach ($quiz->questions as $question) {
            $question->pivot->order;
        }
        return response($quiz);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'time_limit' => ['required', 'numeric'],
        ]);

        $quiz = new Quiz;
        $quiz->title = $request->title;
        $quiz->time_limit = $request->time_limit;
        $quiz->status = 'draft';
        $quiz->user_id = Auth::id();

        if(!$quiz->save()){
            return response('quiz creation failed!', 500);
        }
        
        return response($quiz, 201);
    }

    public function update(Request $request, $quiz_id)
    {
        $this->validate($request, [
            'title' => ['string', 'max:255'],
            'time_limit' => ['numeric'],
            'status' => ['string', 'in:draft,completed,archived'],
        ]);

        $quiz = Quiz::findOrFail($quiz_id);
        foreach($request->input() as $field => $value){
            $quiz->{$field} = $request->{$field};
        }

        if(!$quiz->save()){
            return response('quiz update failed!', 500);
        }
        
        return response($quiz);
    }

    
}
