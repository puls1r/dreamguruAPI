<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Xendit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $token)
    {
        //verify token API
        if($token == 'ZtlQeavg374UAjH7GUpukIDByJ467oBTIDHxj9L2GmWs5RKt'){
            return $next($request);
        }
        
        else{
            abort(403);
        }
    }
}
