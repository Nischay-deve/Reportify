@extends('layouts.app_empty')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center" style="color: #fff;background-color: #FF5500;border-color: #FF5500;"><h4>{{ __('OTP Login') }}</h4></div>

            <div class="card-body">

                @if (session('error'))
                <div class="alert alert-danger" role="alert"> {{session('error')}} 
                </div>
                @endif

                <form method="POST" action="{{ route('otp.generate') }}">
                    @csrf

                    <div class="row mb-3">
                        @if(config('app.otp_login_mode') == 'mobile')
                            <label for="mobile" class="col-md-4 col-form-label text-md-end">{{ __('Mobile No') }}</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" autofocus placeholder="Enter Your Registered Mobile Number">

                                @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @else
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter Your Registered Email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                        
                        @endif
                    </div>



                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-{{ config('app.color_scheme') }}">
                                {{ __('Generate OTP') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection