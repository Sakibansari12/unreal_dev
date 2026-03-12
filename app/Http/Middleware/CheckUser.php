<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        $loggedInUser = Auth::guard('admin')->user();

        if (!$loggedInUser) {
            return redirect()->route('admin.login');
        }

        if ($loggedInUser->role_id == 1) {
            return $next($request);
        }

        if ($loggedInUser->role_id == 7) {
            $adminId = $request->route('id');
            
            if (!$adminId) {
                return $next($request);
            }

            $targetAdmin = Admin::find($adminId);
            
            if (!$targetAdmin) {
                return redirect()->route('pms.dashboard')->with('error', 'User not found.');
            }

            if ($targetAdmin->parent_user_id != $loggedInUser->id) {
                return redirect()->route('pms.dashboard');
            }
        }

        return $next($request);
    }
}