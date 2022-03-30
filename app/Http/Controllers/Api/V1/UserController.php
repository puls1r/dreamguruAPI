<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserProfile;
use App\Http\Resources\UserResource;
use App\Rules\MatchOldPassword;

class UserController extends Controller
{
    public function show(){
        $user = User::with('profile')->where('id', Auth::id())->first();

        return (new UserResource($user));
    }

    public function updateProfile(Request $request){
        
        $this->validate($request, [
            'name' => ['string', 'max:100'],
            'phone_number' => [ 'between:10,16', Rule::unique(UserProfile::class)->ignore(Auth::id(), 'user_id')],
            'date_of_birth' => ['date'],
            'gender' => ['string', 'in:male,female'],
            'address' => ['string', 'max:255'],
            'avatar' => ['file', 'max:1024', 'mimes:jpg,jpeg,png'],
        ]);

        $user_profile = UserProfile::where('user_id', Auth::id())->first();
        foreach($request->input() as $field => $value){
            $user_profile->{$field} = $request->{$field};   
        }

        if(!$user_profile->save()){
            return response('data saving failed', 500);
        }
        else{
            return response('profile updated sucessfully!', 200);
        }

    }

    public function updateAccountSecurity(Request $request){

        $this->validate($request, [
            'username' => ['string', 'max:25', Rule::unique(User::class),],
            'email' => ['string', 'email', 'max:255', Rule::unique(User::class),],
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['string', 'min:8'],
            'new_confirm_password' => ['string','same:new_password'],
        ],
        [
            'new_confirm_password.same' => "password confirmation doesn't match!",
        ]);

        $user = User::where('id', Auth::id())->first();
        $request->email ? ($user->email = $request->email) : '';
        $request->username ? ($user->username = $request->username) : '';
        $request->new_password ? ($user->password = bcrypt($request->new_password)) : '';

        if(!$user->save()){
            return response('data saving failed', 500);
        }
        else{
            return response('security updated sucessfully!', 200);
        }
    }
}
