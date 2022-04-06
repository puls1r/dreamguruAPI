<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SectionPart;
use App\Models\CourseSection;
use App\Models\SectionContentOrder;

class SectionPartController extends Controller
{
    public function show($part_id){
        $part = SectionPart::find($part_id);
        
        if(!$part){                          //gunakan slug untuk mengidentifikasi model
            $part = SectionPart::where('slug', $part_id)->firstOrFail();
        }

        return response($part);
    }

    public function create(Request $request, $section_id){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'picture' => ['string', 'max:255'],
            'video' => ['string', 'max:255'],
            'status' => ['required','string', 'in:completed,draft'],
            'is_unlock' => ['required','boolean'],
            'estimated_time' => ['required','numeric'],
        ]);

        $part = new SectionPart;
        $part->course_section_id = $section_id;
        $part->title = $request->title;
        $part->text = $request->text;
        $part->picture = $request->picture;
        $part->video = $request->video;
        $part->status = $request->status;
        $part->is_unlock = $request->is_unlock;
        $part->estimated_time = $request->estimated_time;

        if(!$part->save()){
            return response('part creation failed!', 500);
        }

        $section_content_order = new SectionContentOrder;
        $section_content_order->course_section_id = $part->course_section_id;
        $section_content_order->content_id = $part->slug;
        $section_content_order->title = $part->title;
        $section_content_order->is_unlock = $part->is_unlock;
        $section_content_order->endpoint = 'parts';
        $section_content_order->order = SectionContentOrder::where('course_section_id', $part->course_section_id)->max('order') + 1;

        $section_content_order->save();
        
        return response($part);
    }

    public function update(Request $request, $part_id){
        $this->validate($request, [
            'title' => ['string', 'max:255'],
            'text' => ['string'],
            'picture' => ['string', 'max:255'],
            'video' => ['string', 'max:255'],
            'order' => ['numeric'],
            'status' => ['string', 'in:completed,draft,archived'],
            'is_unlock' => ['boolean'],
            'estimated_time' => ['numeric'],
        ]);

        $part = SectionPart::findOrFail($part_id);
        foreach($request->input() as $field => $value){
            if($field == 'order'){
                continue;
            }
            $part->{$field} = $request->{$field};
        }

        if(!$part->save()){
            return response('part update failed!', 500);
        }

        if(isset($request->order) || isset($request->title) || isset($request->is_unlock)){
            $section_content_order = SectionContentOrder::where('course_section_id', $part->course_section_id)->firstOrFail();
            isset($request->order) ? $section_content_order->order = $request->order : '';
            isset($request->title) ? $section_content_order->title = $request->title : '';
            isset($request->is_unlock) ? $section_content_order->is_unlock = $request->is_unlock : '';
            $section_content_order->save();
        }

        return response($part);
    }
}
