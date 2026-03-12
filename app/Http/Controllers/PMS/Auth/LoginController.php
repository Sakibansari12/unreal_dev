<?php

namespace App\Http\Controllers\PMS\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use Illuminate\Support\Facades\Password;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;



class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('pms.dashboard');
        }
        return view('pms.auth.login'); 
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
   
            $user = Auth::guard('admin')->user();
            if ($user->status == 0) {
  
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }


            Session::put('last_activity', time());
            session(['login_admin' => $user->id]);
            
            session()->flash('success', 'You have logged in successfully!');
            
            $intendedUrl = Session::get('intended_url');
            Session::forget('intended_url');
            
           // return redirect()->to($intendedUrl ?? route('pms.dashboard'));
           
            if($user->role_id == 8){
                return redirect()->to($intendedUrl ?? route('pms.report.sale-report'));
            }else{
               return redirect()->to($intendedUrl ?? route('pms.dashboard'));
            }
           
           
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }



    public function forgotPassword()
    {
        return view('pms.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $email = $request->email;
        $token = Str::random(64);

        
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Mail::to($email)->send(new ForgotPasswordEmail($token, $email));

        return back()->with('success', 'A password reset link has been sent to your Email.');
    }
    
    public function showResetForm(Request $request, $token)
    {
        
        $tokenData = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $token)
        ->first();

        if (!$tokenData) {
            // Token is invalid or already used, show 404
            abort(404);
        }
        
        return view('pms.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }
    
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'errors' => [
                    'password' => [$errors->first('password')],
                    'password_confirmation' => $errors->first('password_confirmation') ? [$errors->first('password_confirmation')] : []
                ]
            ], 422);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['errors' => ['token' => ['Invalid or expired token.']]], 422);
        }

        $admin = Admin::where('email', $request->email)->first();
        $admin->password = bcrypt($request->password);
        $admin->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => 'Password has been reset.']);
    }


    public function logout(Request $request){
        Auth::guard('admin')->logout();
        // Session::flush();
        $request->session()->forget('login_admin');
        $request->session()->regenerateToken();
        return redirect()->route('pms.login')->with('success', 'You have been logged out.');
    }
    
    public function changepassword(){
        return view('pms.auth.changepassword');
    }

    public function changepasswordsubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ], [
            'password.required' => 'The new password field is required.',
            'password.confirmed' => 'The confirm password must match the new password.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password_confirmation.required' => 'The confirm password field is required.',
        ]);
    
        $admin = Auth::guard('admin')->user();
        $admin->password = Hash::make($request->password);
        $admin->save();
    
        Auth::guard('admin')->logout();
    
        session()->flash('success', 'Password changed successfully. Please login again.');
        return redirect()->route('pms.login');
    }
}