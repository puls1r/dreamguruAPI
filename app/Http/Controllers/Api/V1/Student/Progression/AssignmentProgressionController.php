<?php

namespace App\Http\Controllers\Api\V1\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\UserAssignment;
use App\Models\Assignment;

class AssignmentProgressionController extends Controller
{
    public function show($user_id, $assignment_id){
        $user_assignment = UserAssignment::where('user_id','=',$user_id)->where('assignment_id','=',$assignment_id)->firstOrFail();
        return response($user_assignment);
    }

    public function create(Request $request, $user_id, $assignment_id){
        $this->validate($request, [
            'status' => ['required', 'in:completed,in progress']
        ]);

        //check user
        if($user_id != Auth::id()){
            return response('forbidden' ,403);
        }
        
        //check data availablity
        $part = Assignment::findOrFail($assignment_id);
        
        $user_assignment = new UserAssignment;
        $user_assignment->user_id = $user_id;
        $user_assignment->assignment_id = $assignment_id;
        $user_assignment->score = 0;
        $user_assignment->status = $request->status;
        if(!$user_assignment->save()){
            return response('data creation failed!', 500);
        }

        return response($user_assignment);
        
    }

    public function update(Request $request, $user_id, $assignment_id){
        $this->validate($request, [
            'score' => ['required', 'numeric'],
            'status' => ['required','in:completed,in progress'],
        ]);
        
        $user_assignment = UserAssignment::where('user_id','=',$user_id)->where('assignment_id','=',$assignment_id)->firstOrFail();

        //get teacher of the course
        $teacher_id = $user_assignment->assignment->course_section->course->teacher_id;
        //check user
        if(Auth::id() != $teacher_id){
            return response('forbidden' ,403);
        }
        
        $user_assignment = UserAssignment::where('user_id','=',$user_id)->where('assignment_id','=',$assignment_id)->firstOrFail();
        $user_assignment->status = $request->status;
        $user_assignment->score = $request->score;
        if(!$user_assignment->save()){
            return response('data creation failed!', 500);
        }

        return response($user_assignment);
    }
}
