<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    use AuthenticatesUsers {
        login as protected traitLogin;
        logout as public traitLogout;
    }


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    ////////////////////////////////////////////////
    // custom
    ////////////////////////////////////////////////

    /**
     * @override
     */
    public function logout(Request $request)
    {
        $authenticatedUser = $this->guard()->user();
        return $this->traitLogout($request);
    }

    /**
     * @override
     */
    protected function authenticated(Request $request, User $user)
    {
    }

    /**
     * @override
     */
    protected function validateLogin(Request $request)
    {
        return true;
    }

    /**
     * @override
     */
    public function login(LoginRequest $request)
    {
        return $this->traitLogin($request);
    }    
}
