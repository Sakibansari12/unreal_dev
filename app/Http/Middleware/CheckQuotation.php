<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingQuotation;

class CheckQuotation
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('admin')->user();
        $bookingQuotationId = $request->route('id'); // 'id' in URL

        if ($user->role_id == 7 && $bookingQuotationId) {
            $quotation = BookingQuotation::find($bookingQuotationId);
            if (!$quotation || $quotation->parent_user_id != $user->id) {
                 return redirect()->route('pms.dashboard');
            }
        }

        return $next($request);
    }
}
