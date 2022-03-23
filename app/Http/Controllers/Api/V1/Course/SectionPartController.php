<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SectionPart;

class SectionPartController extends Controller
{
    public function show($part_id){
        $part = SectionPart::findOrFail($part_id);

        return response($part);
    }

    public function create(Request $request, $section_id){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'picture' => ['string', 'max:255'],
            'video' => ['string', 'max:255'],
            'status' => ['required','string', 'in:completed,draft'],
            'is_unlock' => ['required','numeric', 'in:0,1'],
        ]);

        $part = new SectionPart;
        $part->course_section_id = $section_id;
        $part->title = $request->title;
        $part->text = $request->text;
        $part->picture = $request->picture;
        $part->video = $request->video;
        $part->status = $request->status;
        $part->is_unlock = $request->is_unlock;
        $part->order = 1;

        if(!$part->save()){
            return response('part creation failed!', 500);
        }
        
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
            'is_unlock' => ['numeric', 'in:0,1'],
        ]);

        $part = SectionPart::findOrFail($part_id);
        foreach($request->input() as $field => $value){
            $part->{$field} = $request->{$field};
        }

        if(!$part->save()){
            return response('part update failed!', 500);
        }
        
        return response($part);
    }
}
