<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckPublicAccess
{
    public function handle(Request $request, Closure $next)
    {
        $setting = DB::connection('setfacts')
            ->table('settings')
            ->where('key', 'public_access')
            ->first();

        $publicAccess = $setting ? (bool)$setting->value : false;


        if ($publicAccess) {
            return $next($request);
        }

        return $this->handleAuthCheck($request, $next);
    }

    protected function handleAuthCheck($request, $next)
    {
        if (!auth()->check() && !$request->cookie('remember_token')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            }
            return redirect()->route('login', ['slug' => session()->get('website_slug')]);
        }

        return $next($request);
    }
}
