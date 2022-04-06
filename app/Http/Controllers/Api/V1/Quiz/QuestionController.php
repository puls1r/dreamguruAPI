<?php

namespace App\Http\Controllers\Api\V1\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Question;
use App\Models\Answer;

class QuestionController extends Controller
{
    public function show($question_id){
        $question = Question::with('answers')->findOrFail($question_id);
        return response($question);
    }

    public function create(Request $request, $quiz_id){
        $this->validate($request, [
            'question' => ['required', 'string', 'max:255'],
            'question_type' => ['required', 'string', 'in:single_answer,multiple_answer'],
            'point' => ['required', 'numeric'],
            'picture' => ['string'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.answer' => ['required', 'string'],
            'answers.*.is_true' => ['required', 'boolean'],
        ]);

        $question = new Question;
        $question->question = $request->question;
        $question->question_type = $request->question_type;
        $question->point = $request->point;
        $question->picture = $request->picture;

        $question->save();
        $question->quizzes()->attach([$quiz_id => ['order'=>'1']]);

        foreach($request->answers as $data){
            $answer = new Answer;
            $answer->answer = $data['answer'];
            $answer->is_true = $data['is_true'];
            $answer->question()->associate($question);
            $answer->save();
        }

        return response($question->load('answers'), 201);
    }

    public function update(Request $request, $question_id){
        $this->validate($request, [
            'question' => ['required', 'string', 'max:255'],
            'question_type' => ['required', 'string', 'in:single_answer,multiple_answer'],
            'point' => ['required', 'numeric'],
            'picture' => ['string'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.answer' => ['required', 'string'],
            'answers.*.is_true' => ['required', 'numeric', 'in:0,1'],
        ]);

        $question = Question::findOrFail($question_id);
        $question->question = $request->question;
        $question->question_type = $request->question_type;
        $question->point = $request->point;
        $question->picture = ($request->picture != NULL) ? $request->picture : '';

        $question->save();

        foreach($request->answers as $data){
            if(isset($data['answer_id'])){
                $answer = Answer::where('id', '=', $data['answer_id'])->where('question_id', '=', $question->id)->firstOrFail();
                $answer->answer = $data['answer'];
                $answer->is_true = $data['is_true'];
                $answer->save();
            }
            else{
                $answer = new Answer;
                $answer->answer = $data['answer'];
                $answer->is_true = $data['is_true'];
                $answer->question()->associate($question);
                $answer->save();
            }
        }

        return response($question->load('answers'), 201);
    }

    public function deleteAnswer($question_id, $answer_id){
        $answer = Answer::where('id', '=', $answer_id)->where('question_id', '=', $question_id)->firstOrFail();
        $answer->delete();

        return response('answer deleted');
    }

    public function detachQuestionFromQuiz($quiz_id, $question_id){
        $question = Question::findOrFail($question_id);
        $question->quizzes()->detach($quiz_id);

        return response('question detached');
    }
}
