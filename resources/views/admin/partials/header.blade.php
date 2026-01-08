<!-- ============= COMPONENT ============== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-{{ config('app.color_scheme') }}">
    <div class="container-fluid">

        <div class="loginLogoContainer">
            <a href="{{ route('calendar.index', $website_slug) }}" class="logo logo-admin"><img
                src="{{ asset('images/logo.jpg') }}" height="60" alt="logo"></a>
        </div>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main_nav" style="margin-left:50px;">
            <ul class="navbar-nav">
                @php
                    $classDashboardActive = 'active';
                    $classIndexManagementActive = '';
                    if (Route::currentRouteName() == 'admin.review.index'  || Route::currentRouteName() == 'admin.users.index') {
                        $classDashboardActive = '';
                        $classIndexManagementActive = 'active';                    
                    } else {
                        $classDashboardActive = 'active';
                        $classIndexManagementActive = '';
                    }
                @endphp


                @if ($role_id == config("app.admin_user_role_id"))
                    {{-- Admin Only --}}

                    {{-- Management --}}
                    <li class="nav-item dropdown">

                        <a href="javascript: void(0);" class="nav-link dropdown-toggle {{ $classIndexManagementActive }}"
                        data-bs-toggle="dropdown">
                        <i class="mdi mdi-content-duplicate"></i> Management
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end fade-down">
                            <li>
                               
                                    <a class="dropdown-item disabled" href="#"><i class="mdi mdi-checkerboard"></i> Reviews
                                        Management</a>
                                    <a class="dropdown-item" href="{{ route('admin.review.index') }}">Reviews</a>
                            </li>
                            <li>
                                    
                                    <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                    <a class="dropdown-item disabled" href="#"><i class="mdi mdi-account-key"></i>
                                        Admin Management</a>
                                    <a class="dropdown-item" href="{{ route('admin.admin_users.index') }}">Admin
                                        Users</a>
                                    <a class="dropdown-item" href="{{ route('admin.admin_users.alllog_index') }}">Admin
                                        Activity Log</a>
                            </li>
        
                                 <li>   <div class="dropdown-divider"></div>
                                 </li>

                                 <li>
                                    <a class="dropdown-item disabled" href="#"><i class="mdi mdi-account"></i> User
                                        Management</a>
                                    <a class="dropdown-item" href="{{ route('admin.roles.index') }}">Roles</a>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">Internal Users</a>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.users.index', ['mode' => 'external']) }}">External Users</a>
                                 </li>
                                   
                               
                            </li>
                          
                        </ul>                        

                     


                       
                    </li>

                @endif

            </ul>

            {{-- Right side --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a href="javascript: void(0);" class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <i class="mdi mdi-account"></i>
                        {{ $user['name'] }} ( {{ $role_name }} )
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end fade-down">
                        <li>
                            <a class="dropdown-item" href="{{ route('member.users.resetpassword') }}"
                                class=" waves-effect">
                                <i class="mdi mdi-lock-reset"></i>
                                Reset Password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" class="waves-effect">
                                <i class="mdi mdi-logout"></i>

                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div> <!-- navbar-collapse.// -->
    </div> <!-- container-fluid.// -->
</nav>
<!-- ============= COMPONENT END// ============== -->
