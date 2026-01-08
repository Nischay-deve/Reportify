<style>
    .invalid-feedback {
        display: block !important;
    }

    .select2-container--default .select2-selection--single {
        height: 35px !important;
        border: 1px solid #ced4da;
    }

    .select2-container .select2-selection--multiple {
        min-height: 35px !important;
    }

    .column-5 {
        width: 50%;
        float: left;
        /* Force width to take into account border size */
        box-sizing: border-box;
    }

    .border-light {
        border: 1px solid black;
    }

    .categoryHeading {
        width: 100%;
        text-align: center;
        background-color: aliceblue;
    }

    .cat_checkbox {
        text-align: left;
        width: 93%;
    }

    #cat_container {
        text-align: left;
        /* width: 100%;         */
    }

    #sub_cat_container {
        text-align: left;
        /* width: 100%;         */
    }


    .dm_report_icon {
        position: relative;
        top: 2px;
        margin-left: 4px;
        font-size: 20px;
    }
</style>
@php
// dump($params);
$app_domain = config('app.app_domain');
$domain = $app_domain . $website_slug . '/';
@endphp



<div class="row">
    <form target="_blank" action="{{ route('report.pdfview', $website_slug, ['downloadWord' => 'word']) }}" method="get"
        autocomplete="off" id="downloadForm">

        <div class="row mb-3">


            <div class="col-md-12">
                <div class="card cardFullHeight">
                    <div class="card-body">

                        {{-- Issues --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{-- Issues --}}
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="team_name" class="form-label">Issues</label> <br />
                                        <div role="group" aria-label="Basic radio toggle button group">
                                            @foreach ($team as $key => $teams)
                                            @php
                                            $viewReportUrl = $domain . $teams->slug;
                                            @endphp
                                            <input value="{{ $teams->id }}" type="checkbox"
                                                @if ($teams->id == $defaultTeamId) checked @endif
                                            class="btn-check team_name team_checkbox" name="team_name[]"
                                            data-slug="{{ $teams->slug }}" id="btnradio{{ $teams->id }}"
                                            autocomplete="off">
                                            <label class="btn btn-outline-primary"
                                                for="btnradio{{ $teams->id }}">{{ $teams->name }}
                                                <br>({{ $teams->total_reports }} Reports)
                                                {{-- <a class="dm_report_icon" href="{{$viewReportUrl}}" target="_blank"><i class="mdi mdi-search-web"></i></a> --}}
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3" style="display: none;" id="cat_subcat_row">
                            {{-- Category + Sub Category  --}}

                            <div class="col-md-8" id="cat_subcat_container">
                                <div class="row">
                                    {{-- Category --}}
                                    <div class="col-md-6 column-5 border-light" id="cat_container">
                                        <label for="module" class="form-label categoryHeading">
                                            Category - <span id="cat_count">{{ count($categoriesData) }}</span>
                                        </label>
                                        <div id="category-btn-group">
                                            @foreach ($categoriesData as $key => $category)
                                            <input value="{{ $category->id }}" type="radio"
                                                @if ($category->id == $defaultModuleId) checked @endif
                                            class="btn-check category_checkbox" name="module[]"
                                            id="cb{{ $category->id }}" autocomplete="off">
                                            <label class="btn btn-outline-primary cat_checkbox"
                                                for="cb{{ $category->id }}">{{ $key + 1 }}.
                                                {{ $category->name }} ({{ $category->total_reports }}
                                                Reports)</label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Sub-Category --}}
                                    <div class="col-md-6 column-5 border-light" id="sub_cat_container">
                                        <label for="chapter_id" class="form-label categoryHeading">
                                            Sub-Category - <span
                                                id="subcat_count">{{ count($subCategoriesData) }}</span>
                                        </label>
                                        <div id="sub-category-btn-group">
                                            @foreach ($subCategoriesData as $key => $subCategory)
                                            <input value="{{ $subCategory->id }}" type="radio"
                                                class="btn-check sub_category_checkbox" name="chapter_id[]"
                                                id="scb{{ $subCategory->id }}" autocomplete="off">
                                            <label class="btn btn-outline-primary cat_checkbox"
                                                for="scb{{ $subCategory->id }}">{{ $key + 1 }}.
                                                {{ $subCategory->name }} ({{ $subCategory->total_reports }}
                                                Reports)</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">

                                    {{-- REport Type --}}
                                    <div class="col-md-12" id="location_container">
                                        <div class="mb-3">
                                            <label for="show_report_type" class="form-label">Report Type</label><br>
                                            <select class="form-select" name="show_report_type" id="show_report_type"
                                                autocomplete="off" style="width: 100%">
                                                <option value="">All</option>
                                                <option value="News">News Reports</option>
                                                <option value="Social Media">Social Media Reports</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Filter Type Radio Buttons --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Filter Type</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="filter_type"
                                                    id="filter_date" value="date" checked>
                                                <label class="form-check-label" for="filter_date">Date Range</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="filter_type"
                                                    id="filter_year" value="year">
                                                <label class="form-check-label" for="filter_year">Year Range</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="date_range_fields" class="row">
                                        {{-- Start Date --}}
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="from_date" class="form-label">Start Date
                                                    <span class="label" id="from_date_reports"></span>
                                                </label>
                                                <input id="from_date" name="from_date" class="form-control"
                                                    placeholder="All Dates">
                                            </div>
                                        </div>

                                        {{-- End Date --}}
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="to_date" class="form-label">End Date
                                                    <span class="label" id="to_date_reports"></span>
                                                </label>
                                                <input id="to_date" name="to_date" class="form-control"
                                                    placeholder="All Dates">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Year Range Inputs (initially hidden) --}}
                                    <div id="year_range_fields" class="row d-none">
                                        {{-- From Year --}}
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="from_year" class="form-label">From Year
                                                    <span class="label" id="from_year_dummy"></span>
                                                </label>
                                                <input id="from_year" name="from_year" class="form-control"
                                                    placeholder="From Year">
                                            </div>
                                        </div>

                                        {{-- To Year --}}
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="to_year" class="form-label">To Year
                                                    <span class="label" id="to_year_dummy"></span>
                                                </label>
                                                <input id="to_year" name="to_year" class="form-control"
                                                    placeholder="To Year">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- State --}}
                                    <div class="col-md-12" id="location_container">
                                        <div class="mb-3">
                                            <label for="location_id" class="form-label">State</label><br>
                                            <select class="form-select" name="location_id[]" id="location_id"
                                                multiple autocomplete="off" style="width: 100%">
                                                <option value="">All location-worldwide</option>
                                                @foreach ($locations as $key => $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}
                                                    ({{ $location->total_reports }} Reports)
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- District --}}
                                    <div class="col-md-12" style="display:none;" id="location_state_container">
                                        <div class="mb-3">
                                            <label for="location_state_id" class="form-label">State District</label>
                                            @php
                                            $locationExisting = old('location_state_id');
                                            @endphp
                                            <select class="form-select" name="location_state_id[]"
                                                id="location_state_id" multiple autocomplete="off"
                                                style="width: 100%">
                                                <option value="">All location-state</option>
                                            </select>
                                            @error('location_state_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- language --}}
                                    <div class="col-md-12" id="language_container">
                                        <div class="mb-3">
                                            <label for="language_id" class="form-label">Select language</label><br>
                                            <select name="language_id[]" class="form-control" id="language_id"
                                                multiple autocomplete="off" style="width: 100%">
                                                <option value="">All languages</option>
                                                @foreach ($languages as $key => $language)
                                                <option value="{{ $language->language_id }}">
                                                    {{ $language->language_name }}
                                                    ({{ $language->total_reports }} Reports)
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if ($role_id == 2)
                                    {{-- Web Admin --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="from_user" class="form-label">By User</label>
                                            <select class="form-select" name="from_user" id="from_user"
                                                aria-label="select example">
                                                <option value="">All Users</option>
                                                @foreach ($users as $key => $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                    [{{ $user->email }}] ({{ $user->total_reports }} Reports)
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('user')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                {{-- Rename document start --}}
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <a class="me-1 btn btn-{{ config('app.color_scheme') }}"
                                            data-bs-toggle="collapse" href="#renameDocumentContainer" role="button"
                                            aria-expanded="false" aria-controls="renameDocumentContainer">
                                            Rename document
                                        </a>

                                        <a class="btn btn-{{ config('app.color_scheme') }}" data-bs-toggle="collapse"
                                            href="#advancedSearchContainer" role="button" aria-expanded="false"
                                            aria-controls="advancedSearchContainer">
                                            Advanced Search
                                        </a>
                                    </div>
                                </div>

                                <div class="collapse" id="renameDocumentContainer">

                                    <div class="card text-dark bg-light mb-3">
                                        <div class="card-header fs-5 fw-bold">Rename Document</div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="heading" class="form-label">Rename Report
                                                            Name</label>
                                                        <input type="text" class="form-control"
                                                            name="download_file_name" placeholder="Enter Report Name">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="heading" class="form-label">Replace Report
                                                            Heading
                                                            With</label>
                                                        <input type="text" class="form-control"
                                                            name="download_file_heading"
                                                            placeholder="Enter report file heading">
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="heading" class="form-label">Add new Description
                                                            to
                                                            file</label>
                                                        <textarea class="form-control" name="download_file_title" placeholder="Enter report file description"
                                                            rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                {{-- Rename document End --}}


                                {{-- Advanced Search Start --}}
                                <div class="collapse" id="advancedSearchContainer">

                                    <div class="card text-dark bg-light mb-3">
                                        <div class="card-header fs-5 fw-bold">Advance Search</div>
                                        <div class="card-body">

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="heading" class="form-label">Tag keyword</label>
                                                        <textarea class="form-check" placeholder="Enter Tag (one per line)" id="tags" name="tags" rows="3"
                                                            style="width:100%"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <input type="hidden" name="view_reports" value="yes"
                                                    id="view_reports">
                                                <input type="hidden" name="report_type" value="word">

                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input mb-2 mr-3 fs-5" value="1"
                                                            id="first_time" type="checkbox" name="first_time">
                                                        <label class="form-check-label" for="first_time">First Time
                                                            <br><span class="fw-bold label"
                                                                id="first_time_reports"></span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1"
                                                            id="calendar_date" type="checkbox" name="calendar_date">
                                                        <label class="form-check-label" for="calendar_date">Calendar
                                                            Date
                                                            <br><span class="label fw-bold"
                                                                id="calendar_date_reports"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1"
                                                            id="fir_documents" type="checkbox" name="fir_documents">

                                                        <label class="form-check-label"
                                                            for="fir_documents">FIR/Documents
                                                            <br><span class="label fw-bold"
                                                                id="fir_documents_reports"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1"
                                                            id="followup" type="checkbox" name="followup">

                                                        <label class="form-check-label" for="followup">Followup
                                                            <br><span class="label fw-bold"
                                                                id="followup_reports"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <p class="fs-6 text fw-bold ">Order of news</p>
                                                    <div class="mb-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="asc"
                                                                type="radio" name="news_order_by_order"
                                                                id="news_order_by_order_asc">
                                                            <label class="form-check-label" for="news_order_by_order">
                                                                Oldest NEWS on Top
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="desc"
                                                                type="radio" name="news_order_by_order"
                                                                id="news_order_by_order_desc" checked>
                                                            <label class="form-check-label" for="news_order_by_order">
                                                                Latest NEWS on Top
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <p class="fs-6 text fw-bold ">Reports from</p>
                                                    <div class="mb-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="all"
                                                                type="radio" name="report_from"
                                                                id="report_from_all">
                                                            <label class="form-check-label" for="report_from_all">
                                                                All Location
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="within_bharat"
                                                                type="radio" name="report_from"
                                                                id="report_from_within_bharat">
                                                            <label class="form-check-label"
                                                                for="report_from_within_bharat">
                                                                Within Bharat
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="outside_bharat"
                                                                type="radio" name="report_from"
                                                                id="report_from_outside_bharat">
                                                            <label class="form-check-label"
                                                                for="report_from_outside_bharat">
                                                                Outside Bharat
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <p class="fs-6 fw-bold ">Download Report For <br><span
                                                            class="label" id="total_reports"></span> </p>
                                                    <div class="mb-3">


                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="daily_report"
                                                                id="daily_report" type="radio"
                                                                name="report_data_type" checked>
                                                            <label class="form-check-label" for="report_data_type">
                                                                Reports
                                                            </label>
                                                        </div>

                                                        @if ($role_id == 2)
                                                        {{-- <div class="form-check form-check-inline">
                                                                <input class="form-check-input" value="work_report"
                                                                    id="work_report" type="radio"
                                                                    name="report_data_type">
                                                                <label class="form-check-label" for="report_data_type">
                                                                    Work Report
                                                                </label>
                                                            </div> --}}
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="table_report"
                                                                id="table_report" type="radio"
                                                                name="report_data_type">
                                                            <label class="form-check-label"
                                                                for="report_data_type">
                                                                Table report
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="hashtag_report"
                                                                id="hashtag_report" type="radio"
                                                                name="report_data_type">
                                                            <label class="form-check-label"
                                                                for="report_data_type">
                                                                Hashtag Report
                                                            </label>
                                                        </div>
                                                        @endif



                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="custom_report"
                                                                id="custom_report" type="radio"
                                                                name="report_data_type">
                                                            <label class="form-check-label" for="report_data_type">
                                                                Custom Report
                                                            </label>
                                                        </div>

                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="upload_date_report"
                                                                id="upload_date_report" type="radio"
                                                                name="report_data_type">
                                                            <label class="form-check-label" for="report_data_type">
                                                                Upload date report
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="document_report"
                                                                id="document_report" type="radio"
                                                                name="report_data_type">
                                                            <label class="form-check-label" for="report_data_type">
                                                                Document report
                                                            </label>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" id="table_report_container"
                                                style="display:none; margin-left:25px;">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="table_report_state_order" class="form-label">State
                                                            Order
                                                            By</label>
                                                        <select name="table_report_state_order" class="form-select"
                                                            id="table_report_state_order">
                                                            <option value="state_asc">State ASC</option>
                                                            <option value="state_desc">State DESC</option>
                                                            <option value="strength_asc">Strength ASC</option>
                                                            <option value="strength_desc">Strength DESC</option>
                                                        </select>

                                                        <label for="table_report_state_min_cases"
                                                            class="form-label">Minimum
                                                            Cases</label>
                                                        <input id="table_report_state_min_cases"
                                                            name="table_report_state_min_cases" class="form-control"
                                                            placeholder="Minimum cases reported?" value="5">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" id="hashtags_container"
                                                style="display:none; margin-left:25px;">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <textarea class="form-check" placeholder="Enter Hashtag" id="hashtags" name="hashtags" style="width:100%;"
                                                            rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" id="document_report_container"
                                                style="display:none; margin-left:25px;">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="document_report_tags" class="form-label">Document
                                                            Tag</label>
                                                        <select name="document_report_tags" class="form-select"
                                                            id="document_report_tags" autocomplete="off"
                                                            style="width: 100%">
                                                            <option value="">No tag Selected</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3 custom_report_options" id="custom_report_options"
                                                style="display: none;">
                                                <div class="col-md-12">
                                                    <p>Please select what to include:</p>
                                                    <input value="custom_report_index" type="checkbox"
                                                        name="custom_report_index" class="btn-check"
                                                        id="custom_report_index" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_index">Index ( Category Names )</label>

                                                    <input value="custom_report_chapter" type="checkbox"
                                                        name="custom_report_chapter" class="btn-check"
                                                        id="custom_report_chapter" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_chapter">Sub-Category Names</label>

                                                    <input value="custom_report_headline" type="checkbox"
                                                        name="custom_report_headline" class="btn-check"
                                                        id="custom_report_headline" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_headline">Headline</label>

                                                    <input value="custom_report_link_display" type="checkbox"
                                                        name="custom_report_link_display" class="btn-check"
                                                        id="custom_report_link_display" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_link_display">News URL</label>

                                                    <input value="custom_report_link" type="checkbox"
                                                        name="custom_report_link" class="btn-check"
                                                        id="custom_report_link" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_link">News URL as Hyperlinks</label>

                                                    <input value="custom_report_front_page_screenshots"
                                                        type="checkbox" name="custom_report_front_page_screenshots"
                                                        class="btn-check" id="custom_report_front_page_screenshots"
                                                        autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_front_page_screenshots">Front page
                                                        screenshot of
                                                        News
                                                        link</label>

                                                    <input value="custom_report_full_news_screenshots" type="checkbox"
                                                        name="custom_report_full_news_screenshots" class="btn-check"
                                                        id="custom_report_full_news_screenshots" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_full_news_screenshots">Full News Screenshot
                                                        link</label>

                                                    <input value="custom_report_source" type="checkbox"
                                                        name="custom_report_source" class="btn-check"
                                                        id="custom_report_source" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_source">Source Name</label>

                                                    <input value="custom_report_keypoints" type="checkbox"
                                                        name="custom_report_keypoints" class="btn-check"
                                                        id="custom_report_keypoints" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_keypoints">Keypoints</label>

                                                    <input value="custom_report_videolinks" type="checkbox"
                                                        name="custom_report_videolinks" class="btn-check"
                                                        id="custom_report_videolinks" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_videolinks">Related Video links</label>

                                                    <input value="custom_report_imagesources" type="checkbox"
                                                        name="custom_report_imagesources" class="btn-check"
                                                        id="custom_report_imagesources" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_imagesources">Related Image links</label>

                                                    <input value="custom_report_featuredimages" type="checkbox"
                                                        name="custom_report_featuredimages" class="btn-check"
                                                        id="custom_report_featuredimages" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_featuredimages">Related Extra images</label>

                                                    {{-- Jan 2023 --}}
                                                    <input value="custom_report_date" type="checkbox"
                                                        name="custom_report_date" class="btn-check"
                                                        id="custom_report_date" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_date">Date</label>

                                                    <input value="custom_report_tags" type="checkbox"
                                                        name="custom_report_tags" class="btn-check"
                                                        id="custom_report_tags" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_tags">Tags</label>

                                                    <input value="custom_report_fir" type="checkbox"
                                                        name="custom_report_fir" class="btn-check"
                                                        id="custom_report_fir" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_fir">FIR</label>

                                                    <input value="custom_report_documents" type="checkbox"
                                                        name="custom_report_documents" class="btn-check"
                                                        id="custom_report_documents" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_documents">Documents</label>

                                                    <input value="custom_report_languages" type="checkbox"
                                                        name="custom_report_languages" class="btn-check"
                                                        id="custom_report_languages" autocomplete="off">
                                                    <label style="margin-right:2px;margin-bottom:5px;"
                                                        class="btn btn-outline-{{ config('app.color_scheme') }}"
                                                        for="custom_report_languages">Language</label>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <p class="fs-6 text fw-bold ">Group By</p>

                                                    <div class="row">
                                                        <div class="col-md-4">

                                                            <div class="form-check form-switch">
                                                                <input
                                                                    class="form-check-input  mb-2 mr-3 fs-5 value=" 1"
                                                                    id="group_by_language" type="checkbox"
                                                                    name="group_by_language">

                                                                <label class="form-check-label "
                                                                    for="group_by_language">Language
                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input  mb-2 mr-3 fs-5"
                                                                    value="1" id="group_by_location"
                                                                    type="checkbox" name="group_by_location">

                                                                <label class="form-check-label"
                                                                    for="group_by_location">
                                                                    Location
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>



                                </div>
                                {{-- Advanced Search End --}}

                                {{-- View Report Button --}}
                                <div class="row">
                                    <div class="col-md-6" style="margin-top: 26px;">
                                        <button id="filter" name="filter"
                                            class="btn btn-{{ config('app.color_scheme') }} form-control"><i
                                                class="mdi mdi-download"></i> View Report</button>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 26px;">
                                        <a class="btn btn-secondary"
                                            href=" {{ route('report.pdf', $website_slug) }}">
                                            <i class="mdi mdi-eraser"></i> Clear All Filters
                                        </a>
                                    </div>
                                </div>

                                <div class="row" style="display: none" id="clear_category_container">
                                    <div class="col-md-4" style="margin-top: 26px;">
                                        <a class="btn btn-secondary" href="javascript:void(0);"
                                            onclick="clearCategories();">
                                            <i class="mdi mdi-eraser"></i> Clear Categories
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

<script>
    function clearCategories() {
        $('input[name="team_name[]"]').removeAttr("checked");
        $('input[name="module[]"]').removeAttr("checked");
        $('input[name="chapter_id[]"]').removeAttr("checked");
    }

    function formatDate(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        const month = months[date.getMonth()];
        const day = date.getDate();
        const year = date.getFullYear();

        return month + ' ' + day + ', ' + year;
    }

    function matchCustom(params, data) {
        // If there are no search terms, return all of the data
        if ($.trim(params.term) === '') {
            return data;
        }

        // Do not display the item if there is no 'text' property
        if (typeof data.text === 'undefined') {
            return null;
        }

        // `params.term` should be the term that is used for searching
        // `data.text` is the text that is displayed for the data object
        if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
            return data;
        }

        // custom search using lookup data
        if (typeof $(data.element).data('lookup') !== 'undefined' && $(data.element).data('lookup').toUpperCase()
            .indexOf(params.term.toUpperCase()) == 0) {
            return data;
        }

        // Return `null` if the term should not be displayed
        return null;
    }

    $(document).ready(function() {

        $('#overlay').hide();
        $('#page_content').show();

        ///////////////////////////////////
        // location_id dropdown
        ///////////////////////////////////
        var s1 = $("#location_id").select2({
            width: '100%', // need to override the changed default
            closeOnSelect: false,
            placeholder: "All States",
            tags: true,
            tokenSeparators: [','],
            // minimumInputLength: 1,
            matcher: matchCustom,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        });

        var s2 = $("#location_state_id").select2({
            width: '100%', // need to override the changed default
            closeOnSelect: false,
            placeholder: "All State District",
            tags: true,
            tokenSeparators: [','],
            // minimumInputLength: 1,
            matcher: matchCustom,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        });

        var s3 = $("#language_id").select2({
            closeOnSelect: false,
            placeholder: "All Languages",
            tags: false,
            tokenSeparators: [','],
            matcher: matchCustom,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        });


        $("#from_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: "+0D",
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {

                if (selectedDate) {
                    var idTeam = getSelectedIdsByClass("team_checkbox");
                    let idModules = getSelectedIdsByClass("category_checkbox");
                    let idChapters = getSelectedIdsByClass("sub_category_checkbox");

                    let idLocations = "";
                    var idLocationArr = $('#location_id').select2("val");
                    if (idLocationArr) {
                        idLocations = idLocationArr.toString();
                    }

                    let idLocationStates = "";
                    var idLocationStateArr = $('#location_state_id').select2("val");
                    if (idLocationStateArr) {
                        idLocationStates = idLocationStateArr.toString();
                    }

                    let idLanguages = "";
                    var idLanguageArr = $('#language_id').select2("val");
                    if (idLanguageArr) {
                        idLanguages = idLanguageArr.toString();
                    }

                    var from_user = $('#from_user').val();
                    var to_dateVal = $('#to_date').val();
                    var show_report_type = $('#show_report_type').val(); // 👈 NEW

                    $.ajax({
                        url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                        type: "POST",
                        data: {
                            team_id: idTeam,
                            module_ids: idModules,
                            chapter_ids: idChapters,
                            location_ids: idLocations,
                            location_state_ids: idLocationStates,
                            language_ids: idLanguages,
                            from_date: selectedDate,
                            to_date: to_dateVal,
                            from_user: from_user,
                            show_report_type: show_report_type, // 👈 NEW
                            mode: 'use_date',
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {

                            // update date_reports
                            $.each(result.date_reports, function(key, value) {
                                $("#from_date_reports").html('(' + value.total_reports + ' Reports)');
                                $("#to_date_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // update first_time_reports
                            $.each(result.first_time_reports, function(key, value) {
                                $("#first_time_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // update followup_reports
                            $.each(result.followup_reports, function(key, value) {
                                $("#followup_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // calendar_date_reports
                            $.each(result.calendar_date_reports, function(key, value) {
                                $("#calendar_date_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // fir_documents_reports
                            $.each(result.fir_documents_reports, function(key, value) {
                                $("#fir_documents_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // total report
                            $.each(result.total_reports, function(key, value) {
                                $("#total_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            //update users
                            $('#from_user').html('<option value="">Select User</option>');
                            $.each(result.users, function(key, value) {
                                $("#from_user").append(
                                    '<option value="' + value.id + '">' +
                                    value.name + ' [ ' + value.email + ' ] ( ' +
                                    value.total_reports + ' Reports )</option>'
                                );
                            });
                        }
                    });
                }
            }
        });

        $("#to_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: "+0D",
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {

                if (selectedDate) {

                    var idTeam = getSelectedIdsByClass("team_checkbox");
                    let idModules = getSelectedIdsByClass("category_checkbox");
                    let idChapters = getSelectedIdsByClass("sub_category_checkbox");

                    let idLocations = "";
                    var idLocationArr = $('#location_id').select2("val");
                    if (idLocationArr) {
                        idLocations = idLocationArr.toString();
                    }

                    let idLocationStates = "";
                    var idLocationStateArr = $('#location_state_id').select2("val");
                    if (idLocationStateArr) {
                        idLocationStates = idLocationStateArr.toString();
                    }

                    let idLanguages = "";
                    var idLanguageArr = $('#language_id').select2("val");
                    if (idLanguageArr) {
                        idLanguages = idLanguageArr.toString();
                    }

                    var from_user = $('#from_user').val();
                    var from_dateVal = $('#from_date').val();
                    var show_report_type = $('#show_report_type').val(); // 👈 NEW

                    $.ajax({
                        url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                        type: "POST",
                        data: {
                            team_id: idTeam,
                            module_ids: idModules,
                            chapter_ids: idChapters,
                            location_ids: idLocations,
                            location_state_ids: idLocationStates,
                            language_ids: idLanguages,
                            from_date: from_dateVal,
                            to_date: selectedDate,
                            from_user: from_user,
                            show_report_type: show_report_type, // 👈 NEW
                            mode: 'use_date',
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {

                            // update date_reports
                            $.each(result.date_reports, function(key, value) {
                                $("#from_date_reports").html('(' + value.total_reports + ' Reports)');
                                $("#to_date_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // update first_time_reports
                            $.each(result.first_time_reports, function(key, value) {
                                $("#first_time_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // update followup_reports
                            $.each(result.followup_reports, function(key, value) {
                                $("#followup_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // calendar_date_reports
                            $.each(result.calendar_date_reports, function(key, value) {
                                $("#calendar_date_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // fir_documents_reports
                            $.each(result.fir_documents_reports, function(key, value) {
                                $("#fir_documents_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            // total report
                            $.each(result.total_reports, function(key, value) {
                                $("#total_reports").html('(' + value.total_reports + ' Reports)');
                            });

                            //update users
                            $('#from_user').html('<option value="">Select User</option>');
                            $.each(result.users, function(key, value) {
                                $("#from_user").append(
                                    '<option value="' + value.id + '">' +
                                    value.name + ' [ ' + value.email + ' ] ( ' +
                                    value.total_reports + ' Reports )</option>'
                                );
                            });
                        }
                    });
                }
            }
        });


        // Issues
        // $('#team_name').on('change', function () {
        // ===============================
        // TEAM SELECTION CLICK HANDLER
        // ===============================
        $('input[name="team_name[]"]').on("click", function() {

            console.log("Team checkbox clicked");

            $("#overlay").show();

            // Get all selected team IDs as comma-separated string
            let idTeam = getSelectedIdsByClass("team_checkbox");
            console.log("Selected Teams:", idTeam);

            // Reset UI (Categories & Subcategories)
            $("#category-btn-group").html('');
            $("#sub-category-btn-group").html('');
            $("#subcat_count").text('0'); // ensure subcategory count is reset

            // Reset dropdowns
            $("#location_id").html('');
            $("#location_state_id").html('');
            $("#language_id").html('');
            $("#from_user").html('<option value="">All Users</option>');

            $("#location_state_container").hide();

            $('#location_id').select2({
                placeholder: "All States"
            });
            $('#location_state_id').select2({
                placeholder: "All Districts"
            });
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            // Reset counters
            $('#first_time_reports').empty();
            $('#followup_reports').empty();
            $('#calendar_date_reports').empty();
            $('#fir_documents_reports').empty();
            $('#total_reports').empty();

            // If no team selected → stop
            if (!idTeam) {
                $("#overlay").hide();
                return;
            }

            // Fetch ONLY CATEGORIES (modules)
            $.ajax({
                url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                type: "POST",
                data: {
                    team_id: idTeam,
                    mode: "modules",
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",

                success: function(result) {
                    console.log("Modules API Result:", result);

                    // =======================================================
                    // RENDER ONLY CATEGORY (MODULES) — NO SUBCATEGORY HERE ❗
                    // =======================================================
                    let container = $("#category-btn-group");
                    container.html("");

                    $.each(result.modules, function(index, item) {

                        // Checkbox
                        container.append(`
                    <input type="checkbox" 
                        class="btn-check category_checkbox" 
                        name="module[]" 
                        id="module_${item.id}" 
                        value="${item.id}">
                `);

                        // Label
                        container.append(`
                    <label class="btn btn-outline-primary cat_checkbox" 
                        for="module_${item.id}">
                        ${(index + 1)}. ${item.name}
                        (${item.total_reports} Reports)
                        <br>
                        <small>Last update: ${formatDate(item.latest_published_at)}</small>
                    </label>
                `);
                    });

                    $("#cat_count").text(result.modules.length);

                    // IMPORTANT: DO NOT LOAD SUBCATEGORIES HERE ❌
                    // So we skip result.chapters completely

                    // =======================================================
                    // UPDATE LOCATIONS
                    // =======================================================
                    $("#location_id").html('<option value="">All States</option>');
                    $.each(result.locations, function(i, loc) {
                        $("#location_id").append(`
                    <option value="${loc.id}">
                        ${loc.name} (${loc.total_reports})
                    </option>
                `);
                    });

                    // =======================================================
                    // UPDATE LANGUAGES
                    // =======================================================
                    $("#language_id").html('<option value="">All Languages</option>');
                    $.each(result.languages, function(i, lang) {
                        $("#language_id").append(`
                    <option value="${lang.id}">
                        ${lang.name} (${lang.total_reports})
                    </option>
                `);
                    });

                    // =======================================================
                    // UPDATE COUNTS
                    // =======================================================
                    if (result.total_reports?.[0]) {
                        $("#total_reports").text(result.total_reports[0].total_reports);
                    }
                    if (result.first_time_reports?.[0]) {
                        $("#first_time_reports").text(result.first_time_reports[0].total_reports);
                    }
                    if (result.followup_reports?.[0]) {
                        $("#followup_reports").text(result.followup_reports[0].total_reports);
                    }
                    if (result.calendar_date_reports?.[0]) {
                        $("#calendar_date_reports").text(result.calendar_date_reports[0].total_reports);
                    }
                    if (result.fir_documents_reports?.[0]) {
                        $("#fir_documents_reports").text(result.fir_documents_reports[0].total_reports);
                    }

                    // =======================================================
                    // UPDATE USERS
                    // =======================================================
                    $.each(result.users, function(i, user) {
                        $("#from_user").append(`
                    <option value="${user.id}">
                        ${user.name} [${user.email}] (${user.total_reports})
                    </option>
                `);
                    });

                    // Show the section
                    $("#cat_subcat_row").show();

                    $("#overlay").hide();
                },

                error: function(xhr) {
                    console.error("Error:", xhr.responseText);
                    $("#overlay").hide();
                }
            });
        });



        // Category
        // $('#module').on('change', function() {
        $('body').on('click', '.category_checkbox', function() {

            // console.log("inside category_checkbox");

            var idTeam = getSelectedIdsByClass("team_checkbox");
            console.log("inside category_checkbox ", idTeam);


            let idModules = getSelectedIdsByClass("category_checkbox");
            // console.log('idModules', idModules);   

            $('#overlay').show();

            // clear sub category container
            $("#sub-category-btn-group").html('');


            $("#location_id").html('');
            $('#location_id').select2({
                placeholder: "All States"
            });

            $("#location_state_id").html('');
            $('#location_state_container').css('display', 'none');
            $('#location_state_id').select2({
                placeholder: "All State Districts"
            });

            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if (idModules) {

                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    $('#from_date').val('');
                    $('#to_date').val('');
                }


                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        module_ids: idModules,
                        mode: 'chapters',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        // $('#chapter_id').html(
                        //     '<option value="">Select Sub-Category</option>');
                        // $.each(result.chapters, function(key, value) {
                        //     $("#chapter_id").append('<option value="' + value
                        //         .id + '">' + value.name + ' ( ' + value
                        //         .total_reports + ' Reports )' + '</option>');
                        // });

                        var container = $('#sub-category-btn-group');
                        var inputs = container.find('input');
                        $.each(result.chapters, function(key, value) {
                            var id = key + 1;
                            $('<input />', {
                                type: 'checkbox',
                                class: 'btn-check sub_category_checkbox',
                                id: 'scb' + value.id,
                                name: 'chapter_id[]',
                                value: value.id
                            }).appendTo(container);
                            $('<label />', {
                                'for': 'scb' + value.id,
                                class: 'btn btn-outline-primary cat_checkbox',
                                html: id + '. ' + value.name + '&nbsp;' +
                                    '( ' + value.total_reports +
                                    ' Reports )' +
                                    '<br> [ Last news added on: ' +
                                    formatDate(value.latest_published_at) +
                                    ' ]'
                            }).appendTo(container);
                            // $('<br /><br />').appendTo(container);

                            var spanIcon = '<i class="mdi mdi-search-web"></i>';
                            //chapter_id
                            // var hrefUrl = '{{ config('app.app_domain') . $website_slug }}/pdfview?fromPage=dmr&team_name='+idTeam+'&chapter_id[]='+value.id+'&from_date=&to_date=&from_user=&download_file_name=&download_file_heading=&download_file_title=&tags=&view_reports=yes&report_type=word&news_order_by_order=desc&report_data_type=daily_report&table_report_state_order=state_asc&table_report_state_min_cases=5&hashtags=&document_report_tags='

                            var hrefUrl =
                                '{{ config('
                            app.app_domain ') . $website_slug }}/' +
                                value.team_slug + "/" + value.module_slug + "/" +
                                value.chapter_slug;

                            // $('<a />', { 'for': 'sp_cb'+id, class:'dm_report_icon', html: spanIcon , href:hrefUrl, target:'_blank'  }).appendTo(container);

                        });

                        $("#subcat_count").html(result.chapters.length);

                        // $("#sub_cat_container").show();

                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function(key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        });

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function(key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#from_user").append('<option value="' + value
                                .id + '">' + value.name + ' [ ' + value.email +
                                ' ] ' + ' ( ' + value.total_reports +
                                ' Reports )' + '</option>');
                        });

                        $('#overlay').hide();
                    }
                });
            } else {
                $('#overlay').hide();
                $("#subcat_count").html(0);
            }

        });

        // Sub Category
        // $('#chapter_id').on('change', function() {
        $('body').on('click', '.sub_category_checkbox', function() {

            // console.log("inside sub_category_checkbox");
            $('#overlay').show();

            let idChapters = getSelectedIdsByClass("sub_category_checkbox");
            console.log('idChapters', idChapters);

            $("#location_id").html('');
            $('#location_id').select2({
                placeholder: "All States"
            });

            $("#location_state_id").html('');
            $('#location_state_container').css('display', 'none');
            $('#location_state_id').select2({
                placeholder: "All State Districts"
            });

            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if (idChapters) {

                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    $('#from_date').val('');
                    $('#to_date').val('');
                }

                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        chapter_ids: idChapters,
                        mode: 'sub-category',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function(key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        });

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function(key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#from_user").append('<option value="' + value
                                .id + '">' + value.name + ' [ ' + value.email +
                                ' ] ' + ' ( ' + value.total_reports +
                                ' Reports )' + '</option>');
                        });

                        $('#overlay').hide();
                    }
                });
            } else {
                // var idModule = $('#module').val();
                // $('#module').val(idModule); // Select the option with a value of '1'
                // $('#module').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }

        });

        // Location
        $('#location_id').on('change', function() {

            $('#overlay').show();

            var idLocationArr = $('#location_id').select2("val");
            let idLocations = "";

            var idTeam = getSelectedIdsByClass("team_checkbox");
            let idModules = getSelectedIdsByClass("category_checkbox");
            let idChapters = getSelectedIdsByClass("sub_category_checkbox");


            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            if (idLocationArr) {
                idLocations = idLocationArr.toString();
            }

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if (idLocations) {
                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        location_ids: idLocations,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        mode: 'location',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        if (result.location_state.length > 0) {
                            $('#location_container').removeClass('col-md-6').addClass(
                                "col-md-4");
                            $('#language_container').removeClass('col-md-6').addClass(
                                "col-md-4");


                            $('#location_state_container').css('display', '');
                            $('#location_state_id').html(
                                '<option value="">Select State District</option>');
                            $.each(result.location_state, function(key, value) {
                                $("#location_state_id").append('<option value="' +
                                    value
                                    .id + '">' + value.name + ' ( ' + value
                                    .total_reports + ' Reports )' + '</option>');
                            });
                        } else {
                            $('#location_state_container').css('display', 'none');
                            $('#location_container').removeClass('col-md-4').addClass(
                                "col-md-6");
                            $('#location_container').removeClass('col-md-6').addClass(
                                "col-md-6");

                            $('#language_container').removeClass('col-md-4').addClass(
                                "col-md-6");
                            $('#language_container').removeClass('col-md-6').addClass(
                                "col-md-6");

                        }



                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function(key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#from_user").append('<option value="' + value
                                .id + '">' + value.name + ' [ ' + value.email +
                                ' ] ' + ' ( ' + value.total_reports +
                                ' Reports )' + '</option>');
                        });

                        $('#overlay').hide();
                    }
                });
            } else {
                var idChapter = $('#chapter_id').val();
                $('#chapter_id').val(idChapter); // Select the option with a value of '1'
                $('#chapter_id').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }


        });

        // State District
        $('#location_state_id').on('change', function() {

            $('#overlay').show();

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";

            var idLocationArr = $('#location_id').select2("val");
            let idLocations = "";

            var idTeam = getSelectedIdsByClass("team_checkbox");
            let idModules = getSelectedIdsByClass("category_checkbox");
            let idChapters = getSelectedIdsByClass("sub_category_checkbox");


            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            if (idLocationArr) {
                idLocations = idLocationArr.toString();
            }

            if (idLocationStateArr) {
                idLocationStates = idLocationStateArr.toString();
            }

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if (idLocationStates) {
                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        location_ids: idLocations,
                        location_state_ids: idLocationStates,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        mode: 'location_state',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function(key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#from_user").append('<option value="' + value
                                .id + '">' + value.name + ' [ ' + value.email +
                                ' ] ' + ' ( ' + value.total_reports +
                                ' Reports )' + '</option>');
                        });

                        $('#overlay').hide();
                    }
                });
            } else {

                $('#location_state_container').css('display', 'none');

                var idlanguage = $('#language_id').val();
                $('#language_id').val(idlanguage); // Select the option with a value of '1'
                $('#language_id').trigger('change'); // Notify any JS components that the value changed

                $('#location_state_container').css('display', 'none');
                $('#location_container').removeClass('col-md-4').addClass("col-md-6");
                $('#location_container').removeClass('col-md-6').addClass("col-md-6");

                $('#language_container').removeClass('col-md-4').addClass("col-md-6");
                $('#language_container').removeClass('col-md-6').addClass("col-md-6");

                $('#location_id').trigger("change");
                $('#overlay').hide();
            }


        });

        // Language
        $('#language_id').on('change', function() {


            // var idLanguage = this.value;

            let idLanguages = "";
            var idLanguageArr = $('#language_id').select2("val");
            if (idLanguageArr) {
                idLanguages = idLanguageArr.toString();
            }

            let idLocations = "";
            var idLocationArr = $('#location_id').select2("val");
            if (idLocationArr) {
                idLocations = idLocationArr.toString();
            }

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";
            if (idLocationStateArr) {
                idLocationStates = idLocationStateArr.toString();
            }


            var idTeam = getSelectedIdsByClass("team_checkbox");
            let idModules = getSelectedIdsByClass("category_checkbox");
            let idChapters = getSelectedIdsByClass("sub_category_checkbox");


            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if (idLanguages) {
                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        location_ids: idLocations,
                        location_state_ids: idLocationStates,
                        language_ids: idLanguages,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        mode: 'language',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function(key, value) {
                            $("#from_user").append('<option value="' + value
                                .id + '">' + value.name + ' [ ' + value.email +
                                ' ] ' + ' ( ' + value.total_reports +
                                ' Reports )' + '</option>');
                        });

                    }
                });
            }

        });

        // From_user
        $('#from_user').on('change', function() {

            $('#clear_category_container').show();

            var from_user = $('#from_user').val();

            var idTeam = getSelectedIdsByClass("team_checkbox");
            let idModules = getSelectedIdsByClass("category_checkbox");
            let idChapters = getSelectedIdsByClass("sub_category_checkbox");

            let idLanguages = "";
            var idLanguageArr = $('#language_id').select2("val");
            if (idLanguageArr) {
                idLanguages = idLanguageArr.toString();
            }

            let idLocations = "";
            var idLocationArr = $('#location_id').select2("val");
            if (idLocationArr) {
                idLocations = idLocationArr.toString();
            }

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";
            if (idLocationStateArr) {
                idLocationStates = idLocationStateArr.toString();
            }


            $("#location_id").html('');
            $('#location_id').select2({
                placeholder: "All States"
            });


            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });

            var from_dateVal = $('#from_date').val();
            var to_dateVal = $('#to_date').val();

            if (from_user) {
                $.ajax({
                    url: "{{ route('report.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        from_user: from_user,
                        location_ids: idLocations,
                        location_state_ids: idLocationStates,
                        language_ids: idLanguages,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        from_date: from_dateVal,
                        to_date: to_dateVal,
                        mode: 'from_user',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function(key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        });

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function(key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( ' + value
                                .total_reports + ' Reports )' + '</option>');
                        });
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });

                        // update first_time_reports
                        $.each(result.first_time_reports, function(key, value) {
                            $("#first_time_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // update followup_reports
                        $.each(result.followup_reports, function(key, value) {
                            $("#followup_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function(key, value) {
                            $("#calendar_date_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function(key, value) {
                            $("#fir_documents_reports").html('(' + value
                                .total_reports + ' Reports)');
                        });


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                    }
                });
            }

        });

        $('input[name="report_data_type"]').click(function() {

            if ($(this).attr('id') == 'daily_report') {

                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').hide();
                $('#clear_category_container').hide();


            } else if ($(this).attr('id') == 'hashtag_report') {

                // $('#hashtags').prop('required', true);
                $('#hashtags_container').show();
                $('#hashtags').show();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#team_name').prop('required', false);
                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').hide();
                $('#clear_category_container').hide();

            } else if ($(this).attr('id') == 'custom_report') {

                $('#team_name').prop('required', false);

                // $('#hashtags').prop('required', false); 
                $('#hashtags_container').hide();
                $('#hashtags').hide();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').show();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').hide();
                $('#clear_category_container').hide();

            }
            // else if($(this).attr('id') == 'tag_report') {

            //     $('#tags_container').show();
            //     $('#tags').show();

            //     $('#hashtags_container').hide();
            //     $('#hashtags').hide();      

            //     $('#team_name').prop('required', false);                
            //     $('#custom_report_options').hide();

            //     $('#document_report_container').hide();
            //     $('#document_report_tags').hide();        

            //     $('#table_report_container').hide();

            // }
            else if ($(this).attr('id') == 'document_report') {

                // $('#tags_container').hide();
                // $('#tags').hide();

                $('#hashtags_container').hide();
                $('#hashtags').hide();
                $('#team_name').prop('required', false);
                $('#custom_report_options').hide();

                $('#document_report_container').show();
                $('#document_report_tags').show();

                $('#table_report_container').hide();
                $('#clear_category_container').hide();

            } else if ($(this).attr('id') == 'hashtag_report') {

                // $('#hashtags').prop('required', false);   
                $('#hashtags_container').hide();
                $('#hashtags').hide();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();
                $('#clear_category_container').hide();

            } else if ($(this).attr('id') == 'work_report') {

                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').hide();

                $('#clear_category_container').show();

            } else if ($(this).attr('id') == 'upload_date_report') {

                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').hide();
                $('#clear_category_container').hide();

            } else if ($(this).attr('id') == 'table_report') {

                // $('#tags_container').hide();
                // $('#tags').hide();

                $('#hashtags_container').hide();
                $('#hashtags').hide();
                $('#team_name').prop('required', false);
                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();

                $('#table_report_container').show();
                $('#clear_category_container').hide();

            }

            if (
                $(this).attr('id') == 'hashtag_report'
            ) {
                $('#view_reports').val("");
            } else {
                $('#view_reports').val("yes");
            }
        });

        $("#filter").click(function() {
            var selectedReportType = $('input[name="report_data_type"]:checked').val();


            var selectedReportTypeFormated = selectedReportType.replace(/\_/g, ' ');

            var fromDate = $("#from_date").val();
            var from_year = $("#from_year").val();
            var to_year = $("#to_year").val();
            var toDate = $("#to_date").val();
            var idTeam = getSelectedIdsByClass("team_checkbox");
            let idModules = getSelectedIdsByClass("category_checkbox");
            let idChapters = getSelectedIdsByClass("sub_category_checkbox");

            let selectedTeamText = $('label[for="btnradio' + idTeam + '"]').text();

            // let selectedModuleText = $('label[for="cb' + idModules + '"]').text();
            // let selectedChapterText = $('label[for="scb' + idChapters + '"]').text();

            // Handle multiple selected modules
            let selectedModuleIds = idModules.split(','); // Split by comma
            let selectedModuleTextArray = selectedModuleIds.map(function(id) {
                let fullText = $('label[for="cb' + id + '"]').text();
                return fullText.replace(/^\d+\.\s*/, ''); // Remove the serial number and dot
            });
            let selectedModuleText = selectedModuleTextArray.join(' and ');

            // Handle multiple selected chapters
            let selectedChapterIds = idChapters.split(','); // Split by comma
            let selectedChapterTextArray = selectedChapterIds.map(function(id) {
                let fullText = $('label[for="scb' + id + '"]').text();
                return fullText.replace(/^\d+\.\s*/, ''); // Remove the serial number and dot
            });
            let selectedChapterText = selectedChapterTextArray.join(' and ');



            console.log("filter idTeam", idTeam);
            console.log("filter idModules", idModules);
            console.log("filter idChapters", idChapters);
            console.log("filter selectedTeamText", selectedTeamText);
            console.log("filter selectedModuleText", selectedModuleText);
            console.log("filter selectedChapterText", selectedChapterText);

            console.log("finalMsgText", finalMsgText);




            var finalMsgText = '';
            if (idChapters != '') {
                finalMsgText = selectedChapterText;
            } else if (idModules != '') {
                finalMsgText = selectedModuleText;
            } else {
                finalMsgText = selectedTeamText;
            }

            if (selectedReportType == 'tag_report') {
                const myArray = finalMsgText.split(" (");
                finalMsgText = myArray[0];
            }

            if ((fromDate == '' || toDate == '') && idTeam == '') {

                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops',
                    html: 'Please select dates or an Issue to download ' +
                        selectedReportTypeFormated,
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {

                    } else if (result.isDenied) {

                    }
                })
            } else if ((fromDate == '' && toDate == '') && idTeam != '' && from_year == '' && to_year == '') {

                event.preventDefault();
                Swal.fire({
                    icon: 'success',
                    title: 'Note',
                    html: 'No date is selected, this will download Base Report of ' +
                        finalMsgText,
                    showCancelButton: true,
                    confirmButtonColor: '#2DA5D1',
                    confirmButtonText: 'OK',
                    cancelButtonText: "No, cancel it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // $('form#downloadForm').submit();

                        var datastring = $("form#downloadForm").serialize();
                        console.log("Serialized Data:", datastring);
                        checkDownloadReportCount(datastring);


                    } else if (result.isDenied) {
                        return false;
                    }
                })
            } else {
                event.preventDefault();

                var datastring = $("form#downloadForm").serialize();

                checkDownloadReportCount(datastring);
            }
        });

        function checkDownloadReportCount(datastring) {
            var datastringUpdated = datastring + "&mode=getcount";
            console.log(datastringUpdated);
            $.ajax({
                type: "GET",
                url: "{{ route('report.pdfview', $website_slug, ['downloadWord' => 'word']) }}",
                data: datastringUpdated,
                success: function(result) {
                    //  alert('Data send');
                    if (result > 0) {
                        // console.log('Download result', result);
                        $('form#downloadForm').submit();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            html: 'No reports found for selected filter attributes',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            } else if (result.isDenied) {

                            }
                        })
                    }
                }
            });
        }

        function setDefaultDate() {

            var date = new Date();
            console.log('date', date);

            var date = new Date(Date.now() - 864e5);
            console.log('date', date);

            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            if (month < 10) month = "0" + month;
            if (day < 10) day = "0" + day;
            var yesterday = day + "-" + month + "-" + year;
            document.getElementById("from_date").value = yesterday;
            document.getElementById("to_date").value = yesterday;
            console.log('yesterday', yesterday);

        }

        function getSelectedIdsByClass(className) {
            console.log('getSelectedIdsByClass className', className);
            let idSelected = "";
            let elementChecked = "." + className + ":checkbox:checked";

            var checkedVals = $(elementChecked).map(function() {
                return this.value;
            }).get();
            idSelected = checkedVals.join(",");

            console.log('idSelected', idSelected);
            return idSelected;
        }
    });


    document.addEventListener("DOMContentLoaded", function() {
        const dateFields = document.getElementById("date_range_fields");
        const yearFields = document.getElementById("year_range_fields");



        document.querySelectorAll('input[name="filter_type"]').forEach((radio) => {
            radio.addEventListener("change", function() {
                if (this.value === "date") {
                    dateFields.classList.remove("d-none");
                    yearFields.classList.add("d-none");
                    $('[name="from_year"], [name="to_year"]').val('');

                } else {
                    yearFields.classList.remove("d-none");
                    dateFields.classList.add("d-none");
                    $('[name="from_date"], [name="to_date"]').val('');
                }
            });
        });
    });

    $("#from_year").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: "+0D",
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate) {
            console.log(selectedDate);
        }
    });

    $("#to_year").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: "+0D",
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate) {
            console.log(selectedDate);
        }
    });
</script>