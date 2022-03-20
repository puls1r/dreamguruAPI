<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\SectionDiscussion;
use App\Models\DiscussionReply;

class SectionDiscussionController extends Controller
{
    public function show($discussion_id){
        $discussion = SectionDiscussion::findOrFail($discussion_id);
        return response($discussion);
    }

    public function create(Request $request, $section_id){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $discussion = new SectionDiscussion;
        $discussion->course_section_id = $section_id;
        $discussion->user_id = Auth::id();
        $discussion->title = $request->title;
        $discussion->message = $request->message;
        $discussion->status = 'open';

        if(!$discussion->save()){
            return response('discussion creation failed!', 500);
        }
        
        return response($discussion);
    }

    public function update(Request $request, $discussion_id){
        $this->validate($request, [
            'message' => ['string'],
            'status' => ['string', 'in:solved,open,archived'],
        ]);

        $discussion = SectionDiscussion::findOrFail($discussion_id);
        if(Auth::id() != $discussion->user_id){
            return response(['message' => 'forbidden'], 403);
        }

        foreach($request->input() as $field => $value){
            $discussion->{$field} = $request->{$field};
        }

        if(!$discussion->save()){
            return response('discussion creation failed!', 500);
        }
        
        return response($discussion);
    }

    public function createReply(Request $request, $discussion_id){
        $this->validate($request, [
            'message' => ['required', 'string'],
            'message_parent_id' => ['numeric', 'exists:discussion_replies,id'],
        ]);

        $reply = new DiscussionReply;
        $reply->section_discussion_id = $discussion_id;
        $reply->user_id = Auth::id();
        if(isset($request->message_parent_id)){
            $reply->discussion_message_parent_id = $request->message_parent_id;
        }
        $reply->message = $request->message;
        $reply->status = 'posted';

        if(!$reply->save()){
            return response('reply creation failed!', 500);
        }
        
        return response($reply);
    }

    public function updateReply(Request $request, $reply_id){
        $this->validate($request, [
            'message' => ['string'],
        ]);

        $reply = DiscussionReply::findOrFail($reply_id);

        //check reply owner
        $reply->section_discussion_id = $discussion;
        if(Auth::id() != $reply->user_id){
            return response(['message' => 'forbidden'], 403);
        }

        $reply->message = $request->message;

        if(!$reply->save()){
            return response('reply update failed!', 500);
        }
        
        return response($reply);
    }

    public function updateReplyToAnswer($reply_id){
        $reply = DiscussionReply::findOrFail($reply_id);
        $discussion = $reply->section_discussion;
        //check status discussion
        if($discussion->status == 'solved' || $discussion->status == 'archived'){
            return response(['message' => 'forbidden'], 403);
        }

        //check discussion owner
        if($discussion->user_id != Auth::id()){
            return response(['message' => 'forbidden'], 403);
        }

        $reply->status = 'answer';
        $discussion->status = 'solved';

        if(!$reply->save() || !$discussion->save()){
            return response('update failed!', 500);
        }
        
        return response($reply);
    }

    public function deleteReply($reply_id){
        $reply = DiscussionReply::findOrFail($reply_id);

        //check reply owner
        if(Auth::id() != $reply->user_id){
            return response(['message' => 'forbidden'], 403);
        }

        //check reply status
        if($reply->status == 'answer' || $reply->status == 'deleted'){
            return response(['message' => 'forbidden'], 403);
        }

        $reply->status = 'deleted';

        if(!$reply->save()){
            return response('update failed!', 500);
        }
        
        return response($reply);
    }
}
