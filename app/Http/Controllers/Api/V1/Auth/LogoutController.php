<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!auth()->user()->tokens()->delete()){
            return [
                'message' => 'Logout Failed'
            ];
        }
        
        return [
            'message' => 'Logout Successful'
        ];
    }
}
