@extends('layouts.app_empty')

@section('content')
<div class="card">
    <div class="card-header">{{ __('Verify Your Email Address') }}</div>

    <div class="card-body">
        @if (session('resent'))
            
        @endif

        @if(session()->has('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            </div>
        </div>
        @endif

        @if(session()->has('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            </div>
        </div>
        @endif                
        
        
        <h3>Resend verification email!</h3>
        <form action="{{ url($website_slug.'/resend-email-verification') }}" method="POST" class="form-horizontal">
            @csrf

            <div class="form-group">
                <label for="email"
                    class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                <input id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required
                    autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>
            
            <div class="form-group row mb-0" style="margin-top: 20px;">
                <div class="col-md-12 ">
                    <button type="submit"
                    class="btn btn-{{ config('app.color_scheme') }} btn-block mb-3"
                    id="registerButton">Send Verification Email</button>
                </div>
            </div>            

        </form>
    </div>
</div>
@endsection
