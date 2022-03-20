<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'username' => [ 'required', 'string', 'max:25', Rule::unique(User::class),],
            'email' => [ 'required', 'string', 'email', 'max:255', Rule::unique(User::class),],
            'phone_number' => [ 'required', 'size:11', Rule::unique(UserProfile::class),],
            'password' => ['required','confirmed','min:8'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string'],
        ]);

        $data = [
            'affiliate_id' => NULL,
            'username' => $request->username,
            'email' => $request->email,
            'role' => 'student',
            'referral_code' => Str::random(8),
            'password' => bcrypt($request->password),
        ];
        $user = new User($data);
        $user->role = 'student';
        $user->save();

        $user_profile = new UserProfile([
            'name' => $request->name,
            'address' => NULL,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'avatar' => NULL,
        ]);
        
        $user->profile()->save($user_profile);

        $token = $user->createToken('dream_guru_token');

        return (new UserResource($user->load('profile')))->additional([
            'token' => $token->plainTextToken,
        ]);
    }
}
