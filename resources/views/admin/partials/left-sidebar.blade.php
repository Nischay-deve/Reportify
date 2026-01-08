<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <h4 class="menu-title">
                    {{$user['name']}}
                    <p style="margin-top:5px;">{{$role_name}}</p>
                </h4>

                @if($role_id ==  config('app.viewer_user_role_id'))
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-file-document"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            {{-- <li><a href="{{ route('report.public') }}">Public Reports</a></li> --}}
                            <li><a href="{{ route('report.pdf', $website_slug) }}">Download Report</a></li>
                        </ul>
                    </li>                 
                @else
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-file-document"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">

                            <li><a href="{{ route('report.my-report') }}">My Reports</a></li>
                                                        
                            <li><a href="{{ route('report.create') }}">Add Report</a></li>

                            @if($role_id !=  config('app.basic_user_role_id'))
                                <li><a href="{{ route('report.index') }}">Edit Report</a></li>                    
                            @endif


                            @if($role_id == 1)    
                                <li><a href="{{ route('report.work_report_index') }}">Work Reports</a></li>                        
                            @endif

                            {{-- <li><a href="{{ route('report.public') }}">Public Reports</a></li> --}}

                            <li><a href="{{ route('report.pdf', $website_slug) }}">Download Report</a></li>
                        </ul>
                    </li>
                @endif

               

                @if($role_id == 1)
                    {{-- Admin Only --}}
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-checkerboard"></i>

                            <span>Index Management</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.team.index') }}">Issues</a></li>
                            <li><a href="{{ route('admin.modules.index') }}">Categories</a></li>
                            <li><a href="{{ route('admin.chapters.index') }}">Sub Categories</a></li>
                            <li><a href="{{ route('admin.reallotment.index') }}">Re-Allotment</a></li>
                            <li><a href="{{ route('admin.location.index') }}">States</a></li>
                            <li><a href="{{ route('admin.locationstate.index') }}">State Districts</a></li>
                            <li><a href="{{ route('admin.language.index') }}">Languages</a></li>
                            <li><a href="{{ route('reportsource.index') }}">Sources</a></li>
                        </ul>
                    </li>  
                    
                    @if($isMasterAdmin == 1)
                    {{-- Master Admin only --}}
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-account"></i>

                            <span>Admin Management</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.admin_users.index') }}">Admin Users</a></li>
                            <li><a href="{{ route('admin.admin_users.alllog_index') }}">Admin Activity Log</a></li>
                        </ul>
                    </li>                         
                    @endif

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-account"></i>

                            <span>User Management</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                            <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                        </ul>
                    </li>
                @endif


                <li>
                    <a href="{{ route('admin.users.resetpassword') }}" class=" waves-effect">
                        <i class="mdi mdi-lock-reset"></i>
                        <span>Reset Password</span>
                    </a>
                </li>                
                <li>
                    <a href="{{ route('logout') }}" class="waves-effect">
                        <i class="mdi mdi-logout"></i>

                        <span>Logout</span>
                    </a>
                </li>



            </ul>
        </div>

    </div>
</div>
