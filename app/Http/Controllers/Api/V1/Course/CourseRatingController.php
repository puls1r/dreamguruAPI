<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\UserRating;

class CourseRatingController extends Controller
{
    public function getCourseRating($course_id){
        $ratings = UserRating::with('user')->where('course_id', $course_id)->get();
        return response($ratings);
    }

    public function create(Request $request, $course_id){
        $this->validate($request, [
            'comment' => ['string', 'max:60'],
            'rating' => ['required', 'numeric', 'between:1,5'],
        ]);

        $course = Course::findOrFail($course_id);
        $user = User::findOrFail(Auth::id());

        $user->rates()->attach([$course->id => [
            'comment' => $request->comment,
            'rating' => $request->rating
            ]
        ]);

        return response('rating has been posted', 201);

    }
}
