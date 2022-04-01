<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Assignment;
use App\Models\SectionContentOrder;


class AssignmentController extends Controller
{
    public function show($assignment_id){
        $assignment = Assignment::find($assignment_id);
        if(!$assignment){                          //gunakan slug untuk mengidentifikasi model
            $assignment = Assignment::where('slug', $assignment)->firstOrFail();
        }

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

        if(!$assignment->save()){
            return response('assignment creation failed!', 500);
        }

        $section_content_order = new SectionContentOrder;
        $section_content_order->course_section_id = $assignment->course_section_id;
        $section_content_order->content_id = $assignment->slug;
        $section_content_order->title = $assignment->title;
        $section_content_order->is_unlock = $assignment->is_unlock;
        $section_content_order->endpoint = 'assignments';
        $section_content_order->order = SectionContentOrder::where('course_section_id', $assignment->course_section_id)->max('order') + 1;

        $section_content_order->save();
        
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

        if(isset($request->order) || isset($request->title) || isset($request->is_unlock)){
            $section_content_order = SectionContentOrder::findOrFail('course_section_id', $assignment->course_section_id);
            isset($request->order) ? $section_content_order->order = $request->order : '';
            isset($request->title) ? $section_content_order->title = $request->title : '';
            isset($request->is_unlock) ? $section_content_order->is_unlock = $request->is_unlock : '';
            $section_content_order->save();
        }
       
        return response($assignment);
    }
}
