<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Http\Requests\StoreSubscriberRequest;
use DateTime;


class SubscriberController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SubscriberController
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function store(StoreSubscriberRequest $request)
    {
        //  dd($request->all());
        $fromdate = DateTime::createFromFormat('d-m-Y', request('birthday'));
        $birthday = $fromdate->format('Y-m-d');

        $subscriber = Subscriber::create([
            'name' => request('name'),
            'email' => request('email'),
            'place_id' => request('place_id'),
            'mobile' => request('mobile'),
            'occupation' => request('occupation'),
            'birthday' => $birthday,
            'moreinfo' => request('moreinfo')
        ]);

        $subscriber = $subscriber->save();

        return redirect()->route('user.home',['id','register']);        
    }
}
