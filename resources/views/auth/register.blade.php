@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">

            <div class="card-body">
                <h3 class="text-center m-0">
                    <a href="{{ route('home') }}" class="logo logo-admin"><img src="{{ asset('images/logo-team.jpeg') }}"
                            height="60" alt="logo" class="my-3"></a>
                </h3>

                <h4 class="font-size-18 mb-2 text-center">Register Here</h4>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group ">
                        <label for="name" class="col-form-label text-md-right">{{ __('Name') }}</label>

                        
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        
                    </div>

                    <div class="form-group ">
                        <label for="email" class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                        
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                       
                    </div>

                    <div class="form-group ">
                        <label for="password" class="col-form-label text-md-right">{{ __('Password') }}</label>

                       
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                       
                    </div>

                    <div class="form-group ">
                        <label for="password-confirm" class="col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                       
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        
                    </div>

                    <div class="form-group row mb-0" style="margin-top: 20px">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-{{config('app.color_scheme')}} form-control">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
        <div class="col-lg-8 p-0 vh-100  d-flex justify-content-center">
            <div class="accountbg d-flex align-items-center">
                <div class="account-title text-center text-white">
                    <h4 class="mt-3 text-white">Welcome To <span class="text-warning">TEAM</span> </h4>
                    <h1 class="text-white">Let's Get Started</h1>
                    
                    <div class="border w-25 mx-auto border-warning"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
