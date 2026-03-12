<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth{

    public function handle($request, Closure $next){

        if (!Auth::guard('admin')->check()) {
            return redirect()->route('pms.login');
        }

        return $next($request);
    }
}
