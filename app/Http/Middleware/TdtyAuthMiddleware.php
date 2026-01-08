<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TdtyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // if (!empty(session()->get('website_slug'))) {
        //     $slug = session()->get('website_slug');
        // } else {
        //     $slug = "team3";
        // }
        
        // // dd(Auth::user());

        // if (!Auth::check()) {
        //     return redirect()->route('login', $slug);
        // }

        // return $next($request);

        $slug = $request->route('slug');

        if (!Auth::check()) {
            return redirect()->route('login', ['slug' => $slug]);
        }
    
        return $next($request);        
    }
}
