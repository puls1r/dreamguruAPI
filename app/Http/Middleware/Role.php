<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role1, $role2=NULL, $role3=NULL)
    {
        if(Auth::user()->role == $role1){
            return $next($request);
        }

        else if($role2 != NULL)
            if(Auth::user()->role == $role2){
                return $next($request);
            }
            
        else if($role3 != NULL)
            if(Auth::user()->role == $role3){
                return $next($request);
            }

        return response(['message' => 'forbidden'], 403);
    }
}
