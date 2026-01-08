<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CheckRememberToken
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() && Cookie::has('remember_token')) {
            try {
                $userId = decrypt(Cookie::get('remember_token'));
                Auth::loginUsingId($userId);
            } catch (\Exception $e) {
                Cookie::queue(Cookie::forget('remember_token'));
            }
        }

        return $next($request);
    }
} 