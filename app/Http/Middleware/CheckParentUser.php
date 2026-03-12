<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckParentUser
{
   public function handle($request, Closure $next)
   {
        $user = Auth::guard('admin')->user();
        if (!$user || $user->parent_user_id != 1) {
             return redirect()->route('pms.dashboard');
        }

        return $next($request);
    }
}
