<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserVerifyEmail;
use App\Models\Team;
use App\Models\UserAssociation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use App\Services\UserEmailService;
use DateTime;
use Illuminate\Support\Str;
use App\Http\Requests\StoreRegisterRequest;
use Illuminate\Support\Facades\Session;
use App\Traits\HasFormBuilderTraits;


class RegisterController extends Controller
{
    use HasFormBuilderTraits;

    public function signUp(StoreRegisterRequest $request)
    {
        DB::connection('thisdatethatyear');

        $params = $request->all();

        $user = new User([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => bcrypt($params['password']),
            'status' => User::STATUS_PENDING,
            'user_type' => User::TYPE_EXTERNAL,
            'parent_id' => 0,
        ]);

        $user->save();

         //Assign role = Basic User
         $user->roles()->sync(config('app.basic_user_role_id'));

        $birthday = null;
        if(!empty(request('birthday'))){
            $fromdate = DateTime::createFromFormat('d-m-Y', request('birthday'));
            $birthday = $fromdate->format('Y-m-d');
        }

        $slug = Str::slug($params['name'], '_').'_'.$user->id;

        $details = new UserProfile([
            'user_id' => $user->id,
            'name' => $params['name'],
            'team_id' => 0,
            'place_id' => $params['place_id'],            
            'mobile' => $params['mobile'],
            'dob' => $birthday,
            'slug' => $slug,
            'occupation' => $params['occupation'],      
            'moreinfo' => $params['moreinfo'],      
            'reference' => $params['reference'],    
            'reference_mobile' => $params['reference_mobile'],    
        ]);

        if ($details->save()) {
            $token = Str::random(40);
            $user->verifyEmail()->create([ 'token' => $token ]);                    
            UserEmailService::sendVerificationEmail($user, $token);
        }

        return redirect()->route('calendar.index', session()->get('website_slug') ,['id','register']);        
    }


    protected function isValidRequest($email, $token)
    {
        $this->validData = null;

        if (!empty($email) && !empty($token)) {
            $user = User::where('email', $email)->first();
            if (isset($user) && $user->id > 0) {
                if (empty($user->email_verification)) {

                    $verify = UserVerifyEmail::where('token', $token)->first();
                    if ($verify) {

                        $this->validData = (object)[
                            'user' => $user,
                            'delete' => true
                        ];
                        return true;
                    }
                } else {

                    $this->validData = (object)[
                        'user' => $user,
                        'delete' => false
                    ];
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Handle a email verification request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(Request $request, $token)
    {
        $verified = $this->isValidRequest($request->email, $token);

        if ($verified) {
            if (property_exists($this->validData, "delete") && $this->validData->delete) {
                try {
                    UserVerifyEmail::where('token', $token)->delete();
                } catch (\Exception $e) {
                    //Log::error('ERROR ON verifyEmail / $this->validData->broker->deleteToken($this->validData->user)');
                }

                $this->validData->user->email_verification = Carbon::now()->getTimestamp();
                $this->validData->user->status = User::STATUS_APPROVED;
                $this->validData->user->save();        
            }
        }

        if ($verified) {
            if (property_exists($this->validData, "delete")) {
                // return redirect('/')->with('success', 'That\'s it! You\'re now one step close to login to ThisDateThatYear.com. We will notify you once your account is approverd by the admin!');

                return redirect('/viewLogin')->with('success', 'That\'s it! You\'re can now login to ThisDateThatYear.com');
            }else{
                return redirect('/email-verification')->with('error', 'Failed to verify your email. Please try again.');
            }
          
        } else {
            return redirect('/email-verification')->with('error', 'Failed to verify your email. Please try again.');
        }
    }    

/**
     * Email verification view
     *
     * @param Request $request
     * @return view content
     */
    public function showEmailVerificationForm()
    {
        return view('auth.verify');
    }

    /**
     * Process email verification
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function processEmailVerification(Request $request)
    {
        $model = $request->user();

        if (empty($model->email_verification)) {
            Session::flash('alert-class', 'alert-warning');
            Session::flash('message', 'Please follow the below instruction and get your email verified.');

            return redirect('/email-verification');
        } else {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'Your email has been verified.');

            return redirect('/');
        }
    }

    /**
     * Resend email verification
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function resendEmailVerification(Request $request)
    {
        $email = $request->email;
        // dd($email);

        $user = User::where('email',$email)->first();
        
        if($user){

            UserVerifyEmail::where('user_id', $user->id)->delete();

            $token = Str::random(40);
            $user->verifyEmail()->create([ 'token' => $token ]);
    
            UserEmailService::sendVerificationEmail($user, $token);
    
            $message = 'Email with a new verification link has been sent to ' . $user->email;
            return redirect('/email-verification')->with('success', $message);
        }else{

            $message = 'No user wfound with email ' . $email.". Please try again!";
            return redirect('/email-verification')->with('error', $message);            
        }
     
    }    
}
