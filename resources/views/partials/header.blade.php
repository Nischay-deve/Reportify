@php
$isHomePage = false;
if (Route::getCurrentRoute()->getActionName() == 'App\Http\Controllers\CalendarController@index') {
$isHomePage = true;
}
@endphp

@mobile
<div style="background-color: #e3f2fd; position:fixed; top:0px; z-index: 5000; width:100%;">
    <div style="margin:16px;">
        <a href="{{ route('calendar.index', $website_slug) }}" class="navbar-brand">
            <img src="{{ asset('images/tdty_logo_sm.png') }}" alt="TDTY Logo" />
        </a>

        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContent"
            style="text-align: right;float: right;">
            <span><i class="fa fa-bars" aria-hidden="true"></i></span>
        </button>
    </div>
</div>

<div class="collapse" id="collapseContent" style="position: absolute; top: 10px; z-index: 6000; left: 125px; width:75%; ">

    <div class="row">
        <div class="col-6">
            <div class="btn-group ms-3 me-3" role="group"
                aria-label="TDTY Action Group" style="position: relative; top:8px;">
                {{-- <a class="border border-primary btn text-white btn-rounded  mb-3 text-nowrap me-1"
                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                        href="{{ route('dailyMonitoringReport', $website_slug) }}" role="button">
                <i class="mdi mdi-calendar-today"></i> Daily Monitoring Report
                </a> --}}

                <a class="border border-primary btn text-white btn-rounded mb-3 text-nowrap me-1"
                    style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                    href="{{ route('report.pdf', $website_slug) }}" role="button">
                    <i class="mdi mdi-search-web"></i> Search Database
                </a>

                {{-- <a class="border border-primary btn text-white btn-rounded mb-3 text-nowrap me-1"
                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                        href="{{ route('smmonitoring.pdf', $website_slug) }}" role="button">
                <i class="mdi mdi-search-web"></i> Search Social Monitoring Database
                </a> --}}

                @if (count($websites) > 1)
                <span id="loadCenterButton"
                    class="border border-primary btn text-white btn-rounded  mb-3 text-nowrap  me-1"
                    style="background: linear-gradient(40deg,#45cafc,#303f9f) !important; cursor: pointer;">
                    <i class="mdi mdi-checkbox-multiple-marked"></i> Filter
                </span>
                @endif
            </div>
        </div>

        <div class="col-6" style="position: relative; left: 45px; ">
            @if ($isHomePage)
            <form class="d-flex" action="{{ config('app.app_domain') . $website_slug }}" id="search-form"
                method="get" autocomplete="off">
                <div class="input-group search-box mb-3" style="max-width: 100%; position: relative; top:6px;">

                    {{-- <select name="keywords[]" class="form-control" id="search-box" autocomplete="off"
                                style="width: 100%;" multiple>
                                <option value="" disabled>Search</option>
                                @foreach ($keywords as $keyword)
                                    @if (isset($keyword) &&
                                            $keyword != 'Document' &&
                                            $keyword != 'Church' &&
                                            $keyword != 'Islam' &&
                                            $keyword != 'Rising Bharat' &&
                                            $keyword != 'Women Empowerment' &&
                                            $keyword != 'Temple' &&
                                            $keyword != 'China')
                                        <option value="{{ $keyword }}">{{ $keyword }}</option>
                    @endif
                    @endforeach
                    <option value="Document">Report</option>
                    <option value="Church">Church</option>
                    <option value="Islam">Islam</option>
                    <option value="Rising Bharat">Rising Bharat</option>
                    <option value="Women Empowerment">Women Empowerment</option>
                    <option value="Temple">Temple</option>
                    <option value="China">China</option>
                    </select> --}}

                    <select name="keywords[]" class="form-control" id="search-box" autocomplete="off"
                        style="width: 100%;" multiple>
                        <option value="" disabled>Search</option>
                        <option value="Document">Report</option>
                        @foreach($all_items_search as $searchItem)
                        <option value="{{$searchItem->title}}">{{ucwords(strtolower($searchItem->title))}}</option>
                        @endforeach
                    </select>





                    <input type="hidden" name="selected_month" id="selected_month"
                        value="@if (isset($selected_month)) {{ $selected_month }} @endif">
                    <input type="hidden" name="selected_week" id="selected_week"
                        value="@if (isset($selected_week)) {{ $selected_week }} @endif">

                    @if (isset($keyword))
                    <button type="button" class="reset-btn" id="reset-btn" onClick="reload();">X</button>
                    @endif

                    <button type="submit" class="search-btn" id="search-btn"></button>
                </div>
            </form>
            @endif

        </div>
    </div>

    <div class="row">
        <div class="col-12" style="position: relative; top: 8px; text-align: center;">
            <div id="google_translate_element" style="min-width: 200px;"></div>
        </div>
    </div>




</div>
@else
<nav class="navbar navbar-expand-lg navbar-light"
    style="background-color: #e3f2fd; position:fixed; top:0px; z-index: 5000; width:100%;">
    <div class="container-fluid">

        <a href="{{ route('calendar.index', $website_slug) }}" class="navbar-brand">
            <img src="{{ asset('images/tdty_logo_sm.png') }}" alt="TDTY Logo" />
        </a>

        <div class="collapse navbar-collapse show @if (!$isHomePage) justify-content-between @else justify-content-between @endif"
            id="collapseContent">

            <div class="@if ($isHomePage) btn-group @endif ms-3 me-3" role="group"
                aria-label="TDTY Action Group" style="position: relative; top:8px;">
                {{-- <a class="border border-primary btn text-white btn-rounded  mb-3 text-nowrap me-1"
                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                        href="{{ route('dailyMonitoringReport', $website_slug) }}" role="button">
                <i class="mdi mdi-calendar-today"></i> Daily Monitoring Report
                </a> --}}

                <a class="border border-primary btn text-white btn-rounded mb-3 text-nowrap me-1"
                    style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                    href="{{ route('report.pdf', $website_slug) }}" role="button">
                    <i class="mdi mdi-search-web"></i> Search Database
                </a>

                {{-- <a class="border border-primary btn text-white btn-rounded mb-3 text-nowrap me-1"
                    style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;"
                    href="{{ route('smmonitoring.pdf', $website_slug) }}" role="button">
                <i class="mdi mdi-search-web"></i> Search Social Monitoring Database
                </a> --}}

                @if (count($websites) > 1)
                <span id="loadCenterButton"
                    class="border border-primary btn text-white btn-rounded  mb-3 text-nowrap  me-1"
                    style="background: linear-gradient(40deg,#45cafc,#303f9f) !important; cursor: pointer;">
                    <i class="mdi mdi-checkbox-multiple-marked"></i> Filter
                </span>
                @endif
            </div>

            <div class="ms-auto me-3" style="position: relative; top: 8px;">
                <div id="google_translate_element" style="min-width: 200px;"></div>
            </div>

            @if ($isHomePage)
            <form class="d-flex" action="{{ config('app.app_domain') . $website_slug }}" id="search-form"
                method="get" autocomplete="off">
                <div class="input-group search-box mb-3" style="max-width: 90%; position: relative; top:8px;">

                    <select name="keywords[]" class="form-control" id="search-box" autocomplete="off"
                        style="width: 100%;" multiple>
                        <option value="" disabled>Search</option>
                        <option value="Document">Report</option>
                        @foreach($all_items_search as $searchItem)
                        <option value="{{$searchItem->title}}">{{ucwords(strtolower($searchItem->title))}}</option>
                        @endforeach
                    </select>


                    <input type="hidden" name="selected_month" id="selected_month"
                        value="@if (isset($selected_month)) {{ $selected_month }} @endif">
                    <input type="hidden" name="selected_week" id="selected_week"
                        value="@if (isset($selected_week)) {{ $selected_week }} @endif">

                    @if (isset($keyword))
                    <button type="button" class="reset-btn" id="reset-btn" onClick="reload();">X</button>
                    @endif

                    <button type="submit" class="search-btn" id="search-btn"></button>
                </div>
            </form>
            @endif

        </div>

        {{-- Right side --}}
        @php
        $setting = DB::connection('setfacts')
        ->table('settings')
        ->where('key', 'public_access')
        ->first();

        $publicAccess = $setting ? (bool)$setting->value : false;
        @endphp

        @if(!$publicAccess)
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a href="javascript: void(0);" class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <i class="mdi mdi-account"></i>
                    @if (Auth::check())
                    {{ Auth::user()->name }}
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end fade-down">
                    <li>
                        <a class="dropdown-item" href="{{ route('password.change', ['slug' => $website_slug]) }}">
                            <i class="mdi mdi-lock-reset"></i> Change Password
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('convert.old.url', ['slug' => $website_slug]) }}">
                            <i class="mdi mdi-link"></i> Convert Old Url
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout', ['slug' => $website_slug]) }}" class="waves-effect">
                            <i class="mdi mdi-logout"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        @endif
    </div>
</nav>


@endmobile