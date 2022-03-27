<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCourse;
use App\Models\SectionPart;
use App\Models\Assignment;

class HasPurchased
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $params = $request->route()->parameters();
        if(Auth::user()->role == 'student'){
            foreach($params as $param => $value){
                if($param == 'part_id'){
                    $part = SectionPart::with('course_section')->findOrFail($value);
                    $course_id = $part->course_section->course_id;
                    $owned = UserCourse::where('user_id', Auth::id())->where('course_id', $course_id)->exists();
                    
                    if(!$owned){
                        return response(['message' => "user doesn't own the course yet!"], 403);
                    }
                    return $next($request);
                }

                else if($param == 'assignment_id'){
                    $assignment = Assignment::with('course_section')->findOrFail($value);
                    $course_id = $assignment->course_section->course_id;
                    $owned = UserCourse::where('user_id', Auth::id())->where('course_id', $course_id)->exists();
                    
                    if(!$owned){
                        return response(['message' => "user doesn't own the course yet!"], 403);
                    }
                    return $next($request);
                }

                else if($param == 'discussion_id'){
                    $discussion = SectionDiscussion::with('course_section')->findOrFail($value);
                    $course_id = $discussion->course_section->course_id;
                    $owned = UserCourse::where('user_id', Auth::id())->where('course_id', $course_id)->exists();
                    
                    if(!$owned){
                        return response(['message' => "user doesn't own the course yet!"], 403);
                    }
                    return $next($request);
                }

                else if($param == 'section_quiz_id'){
                    $section_quiz = SectionQuiz::with('course_section')->findOrFail($value);
                    $course_id = $section_quiz->course_section->course_id;
                    $owned = UserCourse::where('user_id', Auth::id())->where('course_id', $course_id)->exists();
                    
                    if(!$owned){
                        return response(['message' => "user doesn't own the course yet!"], 403);
                    }
                    return $next($request);
                }
            }
        }
        
        return $next($request);
    }
}
