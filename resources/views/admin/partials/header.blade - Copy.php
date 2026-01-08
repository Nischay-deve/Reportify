<header id="page-topbar" class="bg-{{config('app.color_scheme')}}">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                

                <a href="{{ route('report.create') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo-sm.png') }}" alt="" height="22" />
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/logo-team.jpeg') }}" alt="" height="50" />
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="mdi mdi-menu"></i>
            </button>

        </div>

        <!-- Search input -->
        {{-- <div class="search-wrap" id="search-wrap">
            <div class="search-bar">
                <input class="search-input form-control" placeholder="Search" />
                <a href="#" class="close-search toggle-search" data-target="#search-wrap">
                    <i class="mdi mdi-close-circle"></i>
                </a>
            </div>
        </div> --}}

        <div class="d-flex">            

            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username" />
                                <button class="btn btn-{{config('app.color_scheme')}}" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- full-screen -->
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen"></i>
                </button>
            </div>
            
        </div>
    </div>
</header>
