<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Assignment;


class AssignmentController extends Controller
{
    public function show($assignment_id){
        $assignment = Assignment::findOrFail($assignment_id);

        return response($assignment);
    }

    public function create(Request $request, $section_id){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'instruction' => ['required', 'string'],
            'picture' => ['string', 'max:255'],
            'status' => ['required','string', 'in:completed,draft'],
        ]);

        $assignment = new Assignment;
        $assignment->course_section_id = $section_id;
        $assignment->title = $request->title;
        $assignment->instruction = $request->instruction;
        $assignment->picture = $request->picture;
        $assignment->status = $request->status;
        $assignment->order = 1;

        if(!$assignment->save()){
            return response('assignment creation failed!', 500);
        }
        
        return response($assignment);
    }

    public function update(Request $request, $assignment_id){
        $this->validate($request, [
            'title' => ['string', 'max:255'],
            'instruction' => ['string'],
            'picture' => ['string', 'max:255'],
            'order' => ['numeric'],
            'status' => ['string', 'in:completed,draft,archived'],
        ]);

        $assignment = Assignment::findOrFail($assignment_id);
        foreach($request->input() as $field => $value){
            $assignment->{$field} = $request->{$field};
        }

        if(!$assignment->save()){
            return response('assignment update failed!', 500);
        }
        
        return response($assignment);
    }
}
