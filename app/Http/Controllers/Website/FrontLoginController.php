<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserRegister;
use App\Mail\ForgotPassword;
use App\Http\Controllers\Controller;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\DB;
class FrontLoginController extends Controller
{
    // Login page
    public function index()
    {
        if (Auth::guard('webusers')->check()) {
            return redirect('/');
        }
        return view('website.login.login');
    }

    // Signup page
    public function signup()
    {
        if (Auth::guard('webusers')->check()) {
            return redirect('/');
        }
        return view('website.login.signup');
    }

    // Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'first_name' => 'required',
            //'last_name'  => 'required',
            'first_name' => 'required|regex:/^[A-Za-z]+$/',
            'last_name'  => 'required|regex:/^[A-Za-z]+$/',
            'email'      => 'required|email|unique:users,email',
            'mobile'     => 'required|min:6|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'message' => $validator->errors()
            ]);
        }

        $passwordPlain = Str::random(8);

        $user = User::create([
            'name'        => $request->first_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'country_code' => $request->country_code,
            'mobile_no'   => $request->mobile,
            'role'        => 'Customer',
            'password'    => Hash::make($passwordPlain),
        ]);

        Mail::to($user->email)->send(
            new UserRegister($passwordPlain, $user->email, $user->name)
        );

        return response()->json([
            'code' => 200,
            'message' => 'Registration successful! Password sent to your email.',
        ]);
    }

    // Login submit
    public function userlogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'message' => $validator->errors()
            ]);
        }

        if (Auth::guard('webusers')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {

            $user = Auth::guard('webusers')->user();

            if ($user->status == 0) {
                Auth::guard('webusers')->logout();
                return response()->json([
                    'code' => 400,
                    'message' => 'Your account is inactive.'
                ]);
            }

            session(['login_webusers' => $user->id]);

            return response()->json([
                'code' => 200,
                'message' => 'Login successful',
                'url' => $request->uri ?? ''
            ]);
        }

        return response()->json([
            'code' => 400,
            'message' => 'Invalid credentials'
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('webusers')->logout();
        // session()->flush();
        $request->session()->forget('login_webusers');
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Forgot password
    public function forgotpassword(Request $request){   
        $uri = $request->query('uri');
        session(['slug_uri' => $uri]);
        return view('website.login.forgot-password',['pagename' => 'index.php']);
    }


    public function submitforgotpassword(Request $request){

        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validate->fails()) {
            return response()->json(['code' => 401, 'message' => $validate->errors()->toArray()]);
        } else {
            // $uniqid = Str::random(9);
            $customerData = User::where('email', $request->email)->where('role', 'Customer')->first();
            if($customerData){
                // Generate a new password
                $newPassword = Str::random(8);
                $customerData->password = hash::make($newPassword);
                $customerData->save();
                // Send the email with the new password
                Mail::to($customerData->email)->send(new ForgotPassword($newPassword, $customerData->email, $customerData->name));
                return response()->json(['code' => 200,'message' => 'We have sent a new password on your email. Please check and proceed further.','url' => $request->uri ?? '']);
            }
            else{
                return response()->json(['code' => 400,'message' => "Email Id doesn't exists"]);
            }
        }

       
    }

    public function myaccount(Request $request)
    {   
        if(checkAuth()){
            $user = User::where('id', Auth::guard('webusers')->id())->first();
            $state_id = DB::table('states')->where('state_name',  $user->state)->first();

            $id =  $user->id;
            $statesData = DB::table('states')->get();
            $cityQuery = DB::table('cities');

            $cityQuery->when(!empty($state_id), function ($q) use ($id, $state_id) {
                return $q->where('state_id', $state_id->id);
            });
            $citiesData = $cityQuery->orderBy('city_name')->get();
            return view('website.login.my-account',['pagename' => 'index.php'],compact('user','statesData','citiesData'));
          //  return view('websitefrontend.login.my-account',['pagename' => 'index.php'],compact('user'));
        }else{
            return redirect()->route('index');
        }
    }
    
    public function GetCity($id){
        //dd($id);
        $states = DB::table('states')->where('state_name',  $id)->first();
        $city = DB::table('cities')->where('state_id', $states->id)->get();
        return response()->json([
            'status' => true,
            'city' => $city,
            'message' => "state in city",
        ], 201);
    }

    public function profilesubmit(Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
           // 'state' => 'required',
           // 'city' => 'required',
           
            'state' => 'required_if:country_name,India',
            'city' => 'required_if:country_name,India',
            'state_input' => 'required_unless:country_name,India',
            'city_input' => 'required_unless:country_name,India',
           
           
            'zipcode' => 'required',
            'address' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['code' => 401, 'message' => $validate->errors()->toArray()]);
        } else {
            
            $state = $request->country_name === "India" ? $request->state : $request->state_input;
           $city = $request->country_name === "India" ? $request->city : $request->city_input;
           
           
            $data = [
                'name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'mobile_no' => $request->mobile,
                //'state' => $request->state,
               // 'city' => $request->city,
               
               'country_name' => $request->country_name,
                'state' => $state,
                'city' => $city,
               
                'zipcode' => $request->zipcode,
                'address' => $request->address,
            ];

            $categorySubmit = User::where('id',Auth::guard('webusers')->id())->update($data);

            if ($categorySubmit) {
                return response()->json(['code' => 200, 'message' => 'Profile updated successfully']);
            } else {
                return response()->json(['code' => 400, 'message' => 'Something went wrong']);
            }
        }
    }

    public function mybooking(Request $request)
    {   
        if(checkAuth()){
            $user = User::where('id', Auth::guard('webusers')->id())->first();
            $booking = PropertyBooking::with('homeUnit')->where('user_id', Auth::guard('webusers')->id())->get();
        //    dd($booking);
            return view('website.login.my-bookings',['pagename' => 'index.php'],compact('user','booking'));
        }else{
            return redirect()->route('index');
        }
    }

    public function changepassword(Request $request){
        if(checkAuth()){
            $user = User::where('id', Auth::guard('webusers')->id())->first();
            return view('website.login.change-password',['pagename' => 'index.php'],compact('user'));
        }else{
            return redirect()->route('index');
        }
    }

    public function submitpassword(Request $request){
        
        $validate = Validator::make($request->all(), [
            // 'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validate->fails()) {
            return response()->json(['code' => 401, 'message' => $validate->errors()->toArray()]);
        } else {
                
            $user = User::where('id', Auth::guard('webusers')->id())->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            // Compare old password
            // if (!Hash::check($request->old_password, $user->password)) {
            //     return response()->json(['code' => 400, 'message' => "Old password doesn't match"]);
            // }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            Auth::guard('webusers')->logout();
            $request->session()->forget('login_webusers');
            $request->session()->regenerateToken();

            return response()->json(['code' => 200, 'message' => 'Password changed successfully']);
            
        }

    }
}
