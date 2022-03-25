<?php

namespace App\Http\Controllers\Api\V1\User\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show($id){
        //check user accessing
        if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin' || Auth::id() == $id){
            $user = User::where('id', $id)->with('profile')->first();
            return (new UserResource($user));
        }

        return response(['message' => 'Forbidden'], 403);
    }
}
