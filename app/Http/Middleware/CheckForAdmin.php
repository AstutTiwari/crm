<?php

namespace App\Http\Middleware;

use Auth;
use Session;
use Closure;

class CheckForAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if(Auth::check() && Auth::user()->hasRole("Admin"))
        {
            return $next($request);
        }
        return redirect(route('login'));
    }
}
