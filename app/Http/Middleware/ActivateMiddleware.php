<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class ActivateMiddleware
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
        if(Auth::check() && !Auth::user()->activated){
            return redirect()->route('activate');
        }

        return $next($request);
    }
}
