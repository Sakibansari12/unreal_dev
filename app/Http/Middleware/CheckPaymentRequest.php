<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyBooking;

class CheckPaymentRequest
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        $paymentRequestId = $request->route('property_booking_id'); // 'id' in URL
        if ($user->role_id == 7 && $paymentRequestId) {
            $booking = PropertyBooking::find($paymentRequestId);
            if (!$booking || $booking->parent_user_id != $user->id) {
                 return redirect()->route('pms.dashboard');
            }
        }

        return $next($request);
    }
}
