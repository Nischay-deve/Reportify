<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function privacyPolicy()
    {
        return view('static.privacy_policy');
    }

    public function termsOfService()
    {
        return view('static.terms_of_service');
    }
}
