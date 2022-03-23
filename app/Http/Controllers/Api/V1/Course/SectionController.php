<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CourseSection;
use App\Models\Section;

class SectionController extends Controller
{
    public function show($section_id){
        $section = CourseSection::findOrFail($section_id);
    
        return response($section);
    }

    public function create(Request $request, $course_id){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
        ]);

        $order = CourseSection::where('course_id', $course_id)->where('status', 'completed')->max('order') + 1;
        $section = new CourseSection;
        $section->title = $request->title;
        $section->course_id = $course_id;
        $section->order = $order;
        $section->status = 'draft';

        if(!$section->save()){
            return response('section creation failed!', 500);
        }
        
        return response($section, 201);
    }

    public function update(Request $request, $section_id){
        $this->validate($request, [
            'title' => ['string', 'max:255'],
            'status' => ['string', 'in:completed,draft,archived'],
            'order' => ['numeric'],
        ]);

        $section = CourseSection::findOrFail($section_id);
        foreach($request->input() as $field => $value){
            $section->{$field} = $request->{$field};
        }

        if(!$section->save()){
            return response('section update failed!', 500);
        }
        
        return response($section, 200);
    }
}
