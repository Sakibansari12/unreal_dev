<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\TblHome;

class CheckPropertyOwnership
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('admin')->user();
        $propertyId = $request->route('id'); // 'id' in URL

        if ($user->role_id == 7 && $propertyId) {
            $property = TblHome::find($propertyId);
            if (!$property || $property->parent_user_id != $user->id) {
                 return redirect()->route('pms.dashboard');
            }
        }

        return $next($request);
    }
}
