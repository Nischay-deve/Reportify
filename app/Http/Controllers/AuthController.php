<?php

namespace App\Http\Controllers;

use App\Models\ReferredBy;
use App\Models\TdtyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function login(Request $request, $slug)
    {
        // dd(session()->get('website_id'));
        // dd($request->all());
        $website_id=0;

        $website = DB::connection('setfacts')->table('websites')->where('domain', $slug)->first();

        if($website){
            $website_id = $website->id;
        }
        // dd($website_id);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $mode = $request->input('mode');
        // dd($mode );

        if ($mode == "login") {
            $email = $request->input('email');
            $password = $request->input('password');

            // Check the users table for a matching user
            $userSql = "select * from tdty_users where email = '" . $email . "' and (status = 'Approved' OR status = 'Auto Approved')"; 
            
            if (config('app.allow_mail_sending')) {       
                $userSql .= " AND email_verified = 1 ";
            }

            $userSql .= " and deleted_at IS NULL AND (website_id = '" . $website_id . "' OR is_global_user = 1)  limit 1";

            // dd($userSql);
            $user = DB::connection('setfacts')->select($userSql);
            if ($user && Hash::check($password, $user[0]->password)) {

                // Convert the DB user to an Authenticatable user
                $authUser = new TdtyUser((array)$user[0]);

                // // Log in using the user's ID
                Auth::loginUsingId($authUser->id);

                // Set remember me cookie for 30 days
                Cookie::queue('remember_token', encrypt(auth()->user()->id), 43200); // 30 days in minutes

                // return redirect()->route('calendar.index', ['slug' => $slug]);
                // Redirect to intended route
                return redirect()->intended(route('calendar.index', ['slug' => $slug]));
            } else {
                if (config('app.allow_mail_sending')) {       
                    //check email and password where email_verified = 0
                    // Check the users table for a matching user
                    $userSql = "select * from tdty_users where email = '" . $email . "' and (status = 'Approved' OR status = 'Auto Approved') 
                    AND email_verified = 0 and deleted_at IS NULL AND (website_id = '" . $website_id . "' OR is_global_user = 1)  limit 1";
                    $user = DB::connection('setfacts')->select($userSql);
                    if ($user && Hash::check($password, $user[0]->password)) {
                        return redirect()->back()->with(
                            'error',
                            'Email verification pending, please click on the verification link sent on registered email. <a href="' . route('resend.verify.email.form', ['slug' => $slug,'email' => $email]) . '" style="color:rgb(0, 2, 105); text-decoration: underline;">Click here</a> to send verification link again.'
                        );
                    }
                }
            }

            return redirect()->back()->with('error', 'This email is not registered with us. Please use another email or signup!');
        } else if ($mode == "signup") {

            // Generate a unique verification code
            $verificationCode = Str::random(64);

            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');
            $mobile_number = $request->input('mobile_number');
            $referred_by_name = $request->input('referred_by_name');
            $referred_by_mobile_number = $request->input('referred_by_mobile_number');

            // Check if email already exists
            $userSql = "SELECT * FROM tdty_users WHERE email = '" . $email . "' LIMIT 1";
            $user = DB::connection('setfacts')->select($userSql);

            if ($user) {
                // Email already exists
                return redirect()->back()->with('error', $email.' Email already exists. Please login instead of signup!');
            }

            // Email doesn't exist, proceed to register
            $status = 'Pending';
            $referred_by_id = 0;

            if ($referred_by_name && $referred_by_mobile_number) {
                $ReferredByData = ReferredBy::where('mobile_number', $referred_by_mobile_number)
                    ->where('website_id', $website_id)
                    ->first();

                if ($ReferredByData) {
                    $status = 'Auto Approved';
                    $referred_by_id = $ReferredByData->id;
                }
            }

            // Register the new user
            $newUser = TdtyUser::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'mobile_number' => $mobile_number,
                'status' => $status,
                'email_verified' => 0,
                'access_level_id' => $request->access_level_id ?? null,
                'website_id' => $website_id,
                'referred_by_name' => $referred_by_name,
                'referred_by_mobile_number' => $referred_by_mobile_number,
                'referred_by_id' => $referred_by_id,
                'verification_code' => $verificationCode,
            ]);

            // Let's not send verification code if ref number is incorrect
            if ($status == 'Auto Approved') {
                // // Send verification email
                EmailService::sendUserVerificationEmail($newUser, $slug);
            }

            // Log in the user after registration
            $loginSql = "SELECT * FROM tdty_users WHERE email = '" . $email . "' AND (status = 'Approved' OR status = 'Auto Approved')";
            
            if (config('app.allow_mail_sending')) {       
                $loginSql .= " AND email_verified = 1 ";
            }
            
            $loginSql .= " AND deleted_at IS NULL AND (website_id = '" . $website_id . "' OR is_global_user = 1)  LIMIT 1";

            $user = DB::connection('setfacts')->select($loginSql);

            if ($user && Hash::check($password, $user[0]->password)) {

                // Convert the DB user to an Authenticatable user
                $authUser = new TdtyUser((array)$user[0]);

                // // Log in using the user's ID
                Auth::loginUsingId($authUser->id);

                // Set remember me cookie for 30 days
                Cookie::queue('remember_token', encrypt(auth()->user()->id), 43200); // 30 days in minutes

                // return redirect()->route('calendar.index', ['slug' => $slug]);
                // Redirect to intended route
                return redirect()->intended(route('calendar.index', ['slug' => $slug]));
            }

            $message = "Registration done!";
            if (config('app.allow_mail_sending')) {       
                if ($status == 'Auto Approved') {
                    $message = 'Please verify your email to login. Please look for Verification link in spam too!';
                } else {
                    // $message = 'Please contact Admin for further access, meanwhile please verify your email by clicking on the verification link sent to your email id.';
                    $message = 'Please contact Admin for further access';
                }
            }else{
                if ($status == 'Auto Approved') {
                    $message = 'Registration successful, please login.';
                } else {
                    $message = 'Request received. Your request will be reviewed soon.';
                }
            }

            return back()->with('success', $message);
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout($slug)
    {
        // dd($slug);
        Auth::logout();
        Cookie::queue(Cookie::forget('remember_token'));
        return redirect()->route('login', ['slug' => $slug]);
    }

    public function showChangePasswordForm($slug)
    {
        return view('auth.passwords.change', compact('slug'));
    }


    public function showConvertOldUrlForm($slug)
    {
        return view('auth.convert_url', compact('slug'));
    }

    public function changePassword(Request $request, $slug)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Get the logged-in user's email
        $userEmail = Auth::user()->email;

        // Fetch user from tdty_users table
        $user = DB::connection('setfacts')->table('tdty_users')->where('email', $userEmail)->first();

        if (!$user) {
            return back()->with('error', 'User not found. Please try again!');
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The current password is incorrect. Please try again!');
        }

        // Update the password in tdty_users table
        DB::connection('setfacts')->table('tdty_users')->where('email', $userEmail)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('calendar.index', ['slug' => $slug])->with('success', 'Password changed successfully.');
    }


    public function verifyEmail($slug, $code)
    {
        $user = DB::connection('setfacts')->table('tdty_users')->where('verification_code', $code)->first();

        if (!$user) {
            return redirect()->route('login', ['slug' => $slug])->with('error', 'Invalid verification link.');
        }

        DB::connection('setfacts')->table('tdty_users')->where('id', $user->id)->update([
            'email_verified' => 1,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('login', ['slug' => $slug])->with('success', 'Email verified successfully. You can now log in.');
    }


    // Forgotpassword
    public function showForgotPasswordForm($slug)
    {
        return view('auth.passwords.forgot', compact('slug'));
    }

    public function sendResetPasswordEmail(Request $request, $slug)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'No user found with this email.');
        }
        
        //check if email not verified send verification email   
        if ($user->email_verified == 0) {
    
            // Generate a unique verification code
            $verificationCode = Str::random(64);

            // Update the verificationCode in tdty_users table
            DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->update([
                'verification_code' => $verificationCode,
            ]);

                
            // // Send verification email
            if (config('app.allow_mail_sending')) {            
                EmailService::sendUserVerificationEmail($user, $slug);
            }

        }   


        $token = Str::random(64);
        DB::connection('setfacts')->table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // // Send Reset Password Email 
        if (config('app.allow_mail_sending')) {
            EmailService::sendResetPasswordEmail($user, $slug, $token);
        }

        return back()->with('success', 'Password reset link sent to your email.');
    }

    public function showResetPasswordForm($slug, $token)
    {
        return view('auth.passwords.reset', compact('slug', 'token'));
    }

    public function resetPassword(Request $request, $slug)
    {
        // dd($request->all());

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $reset = DB::connection('setfacts')->table('password_resets')->where('token', $request->token)->first();
        if (!$reset || $reset->email !== $request->email) {
            return back()->with('error', 'Invalid token or email. Please try again!');
        }

        DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::connection('setfacts')->table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login', ['slug' => $slug])->with('success', 'Password reset successfully.');
    }

    // Forgotpassword   
    public function resedVerifyEmailForm($slug, Request $request)
    {
        $email = $request->email;
        return view('auth.passwords.resendverify', compact('slug', 'email'));
    }

    public function resedVerifyEmail(Request $request, $slug)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'No user found with this email.');
        }

        $user = DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->where('email_verified', 1)->first();
        if ($user) {
            return back()->with('error', 'This email is already verified!');
        }

        // Generate a unique verification code
        $verificationCode = Str::random(64);

        // Update the verificationCode in tdty_users table
        DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->update([
            'verification_code' => $verificationCode,
        ]);

        $user = DB::connection('setfacts')->table('tdty_users')->where('email', $request->email)->first();

        // // Send verification email
        if (config('app.allow_mail_sending')) {            
            EmailService::sendUserVerificationEmail($user, $slug);
        }
        return back()->with('success', 'Please verify your email id by clicking on link sent to your registered email.');
    }
}
