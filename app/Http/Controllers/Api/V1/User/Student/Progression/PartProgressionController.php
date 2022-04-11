<?php

namespace App\Http\Controllers\Api\V1\User\Student\Progression;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\UserSectionPart;
use App\Models\SectionPart;

class PartProgressionController extends Controller
{
    public function show($user_id, $part_id){
        $user_section_part = UserSectionPart::where('user_id','=',$user_id)->where('section_part_id','=',$part_id)->firstOrFail();
        return response($user_section_part);
    }

    public function create(Request $request, $user_id, $part_id){
        $this->validate($request, [
            'status' => ['required', 'in:completed,in progress']
        ]);

        //check user
        if($user_id != Auth::id()){
            return response('forbidden' ,403);
        }
        
        //check data availablity
        $part = SectionPart::findOrFail($part_id);

        //check if progression is created
        $user_section_part = UserSectionPart::where('user_id','=',$user_id)->where('section_part_id','=',$part_id)->first();
        if($user_section_part != null){
            return response($user_section_part);
        }
        
        $user_section_part = new UserSectionPart;
        $user_section_part->user_id = $user_id;
        $user_section_part->section_part_id = $part_id;
        $user_section_part->status = $request->status;
        if(!$user_section_part->save()){
            return response('data creation failed!', 500);
        }

        return response($user_section_part);
        
    }

    public function update(Request $request, $user_id, $part_id){
        $this->validate($request, [
            'status' => ['required', 'in:completed,in progress']
        ]);

        //check user
        if($user_id != Auth::id()){
            return response('forbidden' ,403);
        }
        
        $user_section_part = UserSectionPart::where('user_id','=',$user_id)->where('section_part_id','=',$part_id)->firstOrFail();
        $user_section_part->status = $request->status;
        if(!$user_section_part->save()){
            return response('data creation failed!', 500);
        }

        return response($user_section_part);
    }
}
