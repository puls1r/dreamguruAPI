<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnerOrAdmin
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
        // $teacher_id = $request->route('teacher_id');
        // if(auth()->user()->id == $teacher_id || auth()->user()->role == 'admin'){
        //     return $next($request);
        // }

        // return response(['message' => 'forbidden'], 403);
        return $next($request);

    }
}
