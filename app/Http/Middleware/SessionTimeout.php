<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next){
        if (Auth::guard('admin')->check()) {
            $lastActivity = Session::get('last_activity');

            if ($lastActivity && (time() - $lastActivity > 15000)) { // 2 minutes = 120 seconds
                Session::put('intended_url', $request->fullUrl());
                
                Auth::guard('admin')->logout();
                Session::flush();

                Session::put('intended_url', $request->fullUrl());
                return redirect()->route('pms.login')->withErrors(['session_expired' => 'Session expired due to inactivity.']);
            }

            Session::put('last_activity', time());
        }

        return $next($request);
    }
}