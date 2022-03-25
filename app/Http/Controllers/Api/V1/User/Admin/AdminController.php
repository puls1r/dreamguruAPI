<?php

namespace App\Http\Controllers\Api\V1\User\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('role:admin');
    }

    public function updateUserRole(Request $request, $user_id){
        $this->validate($request, [
            'role' => ['required', 'string', 'in:admin,teacher,student']
        ]);

        $user = User::findOrFail($user_id);
        $user->role = $request->role;
        $user->save();

        return response($user);
        
    }
}
