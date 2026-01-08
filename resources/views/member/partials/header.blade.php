<!-- ============= COMPONENT ============== -->
<nav class="navbar navbar-light navbar-expand-lg bg-white">
    <div class="container-fluid">

        <a href="{{ route('calendar.index', $website_slug) }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('images/logo.jpg') }}" alt="" height="50" />
            </span>
            <span class="logo-lg">
                <img src="{{ asset('images/logo.jpg') }}" alt="" height="50" />
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main_nav">
            {{-- Right side --}}
            <ul class="navbar-nav ms-auto">
                @if (Auth::user())
                <li class="nav-item dropdown">
                    <a href="javascript: void(0);" class="nav-link dropdown-toggle active"
                        data-bs-toggle="dropdown">
                        <i class="mdi mdi-account"></i>
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end fade-down">
                        @if ($role_id == config("app.admin_user_role_id"))
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}"
                                class=" waves-effect">
                                <i class="mdi mdi-lock-reset"></i>
                                Admin Dashboard
                            </a>
                        </li>                        
                        @endif
                        <li>
                            <a class="dropdown-item" href="{{ route('member.users.resetpassword') }}"
                                class=" waves-effect">
                                <i class="mdi mdi-lock-reset"></i>
                                Reset Password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" class="waves-effect">
                                <i class="mdi mdi-logout"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
                @else
                    <li class="nav-item  active">
                        <a href="{{ route('otp.login') }}" class="nav-link">
                            <button type="button" class="btn btn-outline-warning text-dark"><i class="mdi mdi-login"></i> Login / Register</button>                            
                        </a>
                    </li>                
                @endif
            </ul>            
        </div>

    </div> <!-- container-fluid.// -->
</nav>
<!-- ============= COMPONENT END// ============== -->
