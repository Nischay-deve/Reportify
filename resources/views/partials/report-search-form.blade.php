
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
</style>
@php
// dump($params);
@endphp
<div class="table-responsive">
    <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
        <div class="row">
            @if($mode == "download")
            <form target="_blank" action="{{ route('report.pdfview', $website_slug,[ 'downloadWord' => 'word']) }}" method="get" autocomplete="off" id="downloadForm">
            @endif
                <div class="row mb-3 input-daterange">

                    <div class="col-md-12">
                        <div class="mb-3">
                            @if($mode == "download")
                            <h1 class="search_heading_{{config('app.color_scheme')}}">Search</h1>
                            @elseif($mode == "faq")
                            {{-- <h1 class="search_heading_{{config('app.color_scheme')}}">Faq Report</h1> --}}
                            @endif
                        </div>
                    </div>

                    
                    {{-- Issues --}}
                    <div class="col-md-12">
                        <div class="mb-3">                 
                            <label for="team_name" class="form-label">Issues</label>  <br/> 
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                                @foreach($team as $key=> $teams)
                                <input value="{{$teams->id}}" type="radio" class="btn-check team_name" name="team_name" id="btnradio{{$teams->id}}" autocomplete="off" @if($mode == "faq" && isset($params->team_name) && ($teams->id==$params->team_name)) checked @endif>
                                <label class="btn btn-outline-primary" for="btnradio{{$teams->id}}">{{$teams->name}} <br>({{$teams->total_reports}} Reports)</label>
                                @endforeach
                                
                                
                            </div>                    
                                                   

                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="module" class="form-label">Category</label>
                            <select class="form-select" name="module[]" id="module"  multiple autocomplete="off" style="height:200px;">
                                <option value="">All Categories</option>
                                @if($mode == "faq" && !empty($selectedmodules))
                                    @foreach ($selectedmodules as $modules)
                                        <option value="{{ $modules['id'] }}"
                                            @if (in_array($modules['id'], $moduleIds)) selected @endif>
                                            {{ $modules['name'] }}</option>
                                    @endforeach                                
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Sub-Category --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="chapter_id" class="form-label">Sub-Category</label>
                            <select class="form-select" name="chapter_id[]" id="chapter_id"  multiple autocomplete="off" style="height:200px;">
                                <option value="">All Sub-Categories</option>
                                @if($mode == "faq" && !empty($selectedChapters))
                                    @foreach ($selectedChapters as $chapters)
                                        <option value="{{ $chapters['id'] }}"
                                            @if (in_array($chapters['id'], $chapterIds)) selected @endif>
                                            {{ $chapters['name'] }}</option>
                                    @endforeach                                
                                @endif                                
                            </select>
                        </div>
                    </div>            
                    
                    {{-- Start Date --}}
                    <div class="col-md-6" @if($mode == "faq") style="display:none;" @endif>
                        <div class="mb-3">
                            <label for="from_date" class="form-label">Start Date
                                <span class="label" id="from_date_reports"></span>
                            </label>
                            <input id="from_date" name="from_date" class="form-control" placeholder="All Dates" @if($mode == "faq" && isset($params->from_date)) value="{{$params->from_date}}" @endif>
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-6" @if($mode == "faq") style="display:none;" @endif>
                        <div class="mb-3">
                            <label for="to_date" class="form-label">End Date
                                <span class="label" id="to_date_reports"></span>
                            </label>
                            <input id="to_date" name="to_date" class="form-control" placeholder="All Dates"  @if($mode == "faq" && isset($params->to_date)) value="{{$params->to_date}}" @endif>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-6"  id="location_container">
                        <div class="mb-3">
                            <label for="location_id" class="form-label">State</label><br>
                            <select class="form-select" name="location_id[]" id="location_id"  multiple autocomplete="off" style="width: 100%">
                                <option value="">All location-worldwide</option>
                                @foreach($locations as $key=> $location)
                                    <option value="{{$location->id}}" @if ($mode == "faq" &&  isset($locationIds) && in_array($location->id, $locationIds)) selected @endif>{{$location->name}} ({{$location->total_reports}} Reports)</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    
                    <div class="col-md-4" style="display:none;" id="location_state_container">
                        <div class="mb-3">
                            <label for="location_state_id" class="form-label">State District</label>
                            @php
                            $locationExisting = old('location_state_id');
                            @endphp                                           
                            <select class="form-select" name="location_state_id[]" id="location_state_id"   multiple autocomplete="off" style="width: 100%">
                                <option value="">All location-state</option>
                            </select>
                            @error('location_state_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>                                            

                    <div class="col-md-6"   id="language_container">
                        <div class="mb-3">
                            <label for="language_id" class="form-label">Select language</label><br>
                            <select name="language_id[]" class="form-control" id="language_id"   multiple autocomplete="off" style="width: 100%">
                                <option value="">All languages</option>
                                @foreach($languages as $key=> $language)
                                    <option value="{{$language->language_id}}"  @if ($mode == "faq" &&  isset($languageIds) && in_array($language->language_id, $languageIds)) selected @endif>{{$language->language_name}} ({{$language->total_reports}} Reports)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>         
                    

                </div>

                {{-- Rename document start --}}
                <div class="row mb-3" @if($mode == "faq") style="display:none;" @endif>
                    <div class="col-md-12">
                        <a class="me-1 btn btn-{{config('app.color_scheme')}}" data-bs-toggle="collapse" href="#renameDocumentContainer" role="button" aria-expanded="false" aria-controls="renameDocumentContainer">
                            Rename document
                        </a>     

                        <a class="btn btn-{{config('app.color_scheme')}}" data-bs-toggle="collapse" href="#advancedSearchContainer" role="button" aria-expanded="false" aria-controls="advancedSearchContainer">
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
                                        <label for="heading" class="form-label">Rename Report Name</label>
                                        <input type="text" class="form-control" name="download_file_name" placeholder="Enter Report Name" @if($mode == "faq" && isset($params->download_file_name)) value="{{$params->download_file_name}}" @endif>
                                    </div>
                                </div>
        
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="heading" class="form-label">Replace Report Heading With</label>
                                        <input type="text" class="form-control" name="download_file_heading" placeholder="Enter report file heading" @if($mode == "faq" && isset($params->download_file_heading)) value="{{$params->download_file_heading}}" @endif>
                                    </div>
                                </div>                                        
                                
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="heading" class="form-label">Add new Description to file</label>
                                        <textarea class="form-control" name="download_file_title" placeholder="Enter report file description" rows="3">@if($mode == "faq" && isset($params->download_file_heading)){{$params->download_file_heading}}@endif</textarea>
                                    </div>
                                </div>                                              
                            </div>                          
                        </div>
                    </div>                    
                  
                </div>
                {{-- Rename document End --}}


                {{-- Advanced Search Start --}}   
                <div class="collapse" id="advancedSearchContainer" @if($mode == "faq") style="display:none;" @endif>

                    <div class="card text-dark bg-light mb-3">
                        <div class="card-header fs-5 fw-bold">Advance Search</div>
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="heading" class="form-label">Tag keyword</label>
                                        <textarea class="form-check" placeholder="Enter Tag (one per line)"  id="tags" name="tags" rows="3" style="width:100%">@if($mode == "faq" && isset($params->tags)){{$params->tags}}@endif</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <input type="hidden" name="view_reports" value="yes" id="view_reports">
                                <input type="hidden" name="report_type" value="word">
        
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input mb-2 mr-3 fs-5" value="1" id="first_time" type="checkbox" name="first_time" @if($mode == "faq" && isset($params->first_time ) && $params->first_time == 1) checked @endif>
                                        <label class="form-check-label" for="first_time">First Time <br><span class="fw-bold label" id="first_time_reports"></span></label>
                                    </div>                                         
                                </div>
                                <div class="col-md-3">                                
                                    <div class="form-check form-switch">
                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1" id="calendar_date" type="checkbox" name="calendar_date" @if($mode == "faq" && isset($params->calendar_date ) && $params->calendar_date == 1) checked @endif>
                                        <label class="form-check-label" for="calendar_date">Calendar Date
                                            <br><span class="label fw-bold" id="calendar_date_reports"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">        
                                    <div class="form-check form-switch">
                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1" id="fir_documents" type="checkbox" name="fir_documents" @if($mode == "faq" && isset($params->fir_documents ) && $params->fir_documents == 1) checked @endif>

                                        <label class="form-check-label" for="fir_documents">FIR/Documents
                                            <br><span class="label fw-bold" id="fir_documents_reports"></span>
                                        </label>
                                    </div>         
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input  mb-2 mr-3 fs-5" value="1" id="followup" type="checkbox" name="followup" @if($mode == "faq" && isset($params->followup ) && $params->followup == 1) checked @endif>

                                        <label class="form-check-label" for="followup">Followup
                                            <br><span class="label fw-bold" id="followup_reports"></span>
                                        </label>
                                    </div>                                                                      
                                </div>
                            </div>                            

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="fs-6 text fw-bold ">Order of news</p>
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="asc" type="radio" name="news_order_by_order" id="news_order_by_order_asc">
                                            <label class="form-check-label" for="news_order_by_order">
                                                Oldest NEWS on Top
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input"  value="desc" type="radio" name="news_order_by_order" id="news_order_by_order_desc" checked>
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
                                            <input class="form-check-input" value="all" type="radio" name="report_from" id="report_from_all"   @if($mode == "faq" && isset($params->report_from ) && $params->report_from == "all") checked @endif>
                                            <label class="form-check-label" for="report_from_all">
                                                All Location
                                            </label>
                                        </div>                                                    
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="within_bharat" type="radio" name="report_from" id="report_from_within_bharat"  @if($mode == "faq" && isset($params->report_from ) && $params->report_from == "within_bharat") checked @endif>
                                            <label class="form-check-label" for="report_from_within_bharat">
                                                Within Bharat
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input"  value="outside_bharat" type="radio" name="report_from" id="report_from_outside_bharat"  @if($mode == "faq" && isset($params->report_from ) && $params->report_from == "outside_bharat") checked @endif>
                                            <label class="form-check-label" for="report_from_outside_bharat">
                                                Outside Bharat
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>                            

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="fs-6 fw-bold ">Download Report For <br><span class="label" id="total_reports"></span> </p>
                                    <div class="mb-3">
                                   

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="daily_report" id="daily_report" type="radio" name="report_data_type" checked>
                                            <label class="form-check-label" for="report_data_type">
                                                Reports
                                            </label>
                                        </div>   
        
                                        @if($role_id == 2)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" value="work_report" id="work_report" type="radio" name="report_data_type">
                                                <label class="form-check-label" for="report_data_type">
                                                    Work Report
                                                </label>
                                            </div>                                                    
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" value="table_report" id="table_report" type="radio" name="report_data_type">
                                                <label class="form-check-label" for="report_data_type">
                                                    Table report
                                                </label>                                                       
                                            </div>     
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" value="hashtag_report" id="hashtag_report" type="radio" name="report_data_type">
                                                <label class="form-check-label" for="report_data_type">
                                                    Hashtag Report
                                                </label>
                                            </div>                                                                                                                                                                                  
                                        @endif
        
                                        
                                           
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="custom_report" id="custom_report" type="radio" name="report_data_type">
                                            <label class="form-check-label" for="report_data_type">
                                                Custom Report
                                            </label>
                                        </div>                                                      
                                        
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="upload_date_report" id="upload_date_report" type="radio" name="report_data_type">
                                            <label class="form-check-label" for="report_data_type">
                                                Upload date report
                                            </label>
                                        </div>                                                              
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" value="document_report" id="document_report" type="radio" name="report_data_type">
                                            <label class="form-check-label" for="report_data_type">
                                                Document report
                                            </label>                                                       
                                        </div>   
                                                                              
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3"   id="table_report_container" style="display:none; margin-left:25px;">                                                              
                                <div class="col-md-12">
                                    <div class="mb-3" >
                                        <label for="table_report_state_order" class="form-label">State Order By</label>
                                        <select name="table_report_state_order" class="form-select" id="table_report_state_order">
                                            <option value="state_asc">State ASC</option>
                                            <option value="state_desc">State DESC</option>
                                            <option value="strength_asc">Strength ASC</option>
                                            <option value="strength_desc">Strength DESC</option>
                                        </select>

                                        <label for="table_report_state_min_cases" class="form-label">Minimum Cases</label>
                                        <input id="table_report_state_min_cases" name="table_report_state_min_cases" class="form-control" placeholder="Minimum cases reported?" value="5">
                                    </div> 
                                </div>
                            </div>                                 
                            
                            <div class="row mb-3"  id="hashtags_container" style="display:none; margin-left:25px;">                                                              
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea class="form-check" placeholder="Enter Hashtag"  id="hashtags" name="hashtags"  style="width:100%;" rows="5"></textarea>
                                    </div>       
                                </div>
                            </div>                     

                            <div class="row mb-3"   id="document_report_container" style="display:none; margin-left:25px;">                                                              
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="document_report_tags" class="form-label">Document Tag</label>
                                        <select name="document_report_tags" class="form-select" id="document_report_tags"  autocomplete="off" style="width: 100%">
                                            <option value="">No tag Selected</option>
                                        </select>
                                    </div>                                         
                                </div>
                            </div>
        
                            <div class="row mb-3 custom_report_options" id="custom_report_options" style="display: none;">
                                <div class="col-md-12">
                                    <p>Please select what to include:</p>
                                    <input value="custom_report_index" type="checkbox" name="custom_report_index" class="btn-check" id="custom_report_index" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_index">Index ( Category Names )</label>
        
                                    <input value="custom_report_chapter" type="checkbox" name="custom_report_chapter" class="btn-check" id="custom_report_chapter" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_chapter">Sub-Category Names</label>
        
                                    <input value="custom_report_headline" type="checkbox" name="custom_report_headline" class="btn-check" id="custom_report_headline" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_headline">Headline</label>
        
                                    <input value="custom_report_link_display" type="checkbox" name="custom_report_link_display" class="btn-check" id="custom_report_link_display" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_link_display">News URL</label>
        
                                    <input value="custom_report_link" type="checkbox" name="custom_report_link" class="btn-check" id="custom_report_link" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_link">News URL as Hyperlinks</label>
        
                                    <input value="custom_report_front_page_screenshots" type="checkbox" name="custom_report_front_page_screenshots" class="btn-check" id="custom_report_front_page_screenshots" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_front_page_screenshots">Front page screenshot of News link</label>
        
                                    <input value="custom_report_full_news_screenshots" type="checkbox" name="custom_report_full_news_screenshots" class="btn-check" id="custom_report_full_news_screenshots" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_full_news_screenshots">Full News Screenshot link</label>
        
                                    <input value="custom_report_source" type="checkbox" name="custom_report_source" class="btn-check" id="custom_report_source" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_source">Source Name</label>
        
                                    <input value="custom_report_keypoints" type="checkbox" name="custom_report_keypoints" class="btn-check" id="custom_report_keypoints" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_keypoints">Keypoints</label>
        
                                    <input value="custom_report_videolinks" type="checkbox" name="custom_report_videolinks" class="btn-check" id="custom_report_videolinks" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_videolinks">Related Video links</label>
        
                                    <input value="custom_report_imagesources" type="checkbox" name="custom_report_imagesources" class="btn-check" id="custom_report_imagesources" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_imagesources">Related Image links</label>
                                    
                                    <input value="custom_report_featuredimages" type="checkbox" name="custom_report_featuredimages" class="btn-check" id="custom_report_featuredimages" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_featuredimages">Related Extra images</label>
        
                                    {{-- Jan 2023 --}}
                                    <input value="custom_report_date" type="checkbox" name="custom_report_date" class="btn-check" id="custom_report_date" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_date">Date</label>
        
                                    <input value="custom_report_tags" type="checkbox" name="custom_report_tags" class="btn-check" id="custom_report_tags" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_tags">Tags</label>
        
                                    <input value="custom_report_fir" type="checkbox" name="custom_report_fir" class="btn-check" id="custom_report_fir" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_fir">FIR</label>                            
        
                                    <input value="custom_report_documents" type="checkbox" name="custom_report_documents" class="btn-check" id="custom_report_documents" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_documents">Documents</label>                            
                                    
                                    <input value="custom_report_languages" type="checkbox" name="custom_report_languages" class="btn-check" id="custom_report_languages" autocomplete="off">
                                    <label style="margin-right:2px;margin-bottom:5px;" class="btn btn-outline-{{config('app.color_scheme')}}" for="custom_report_languages">Language</label>                                                        
                                </div>
                            </div>                            

                            <div class="row mb-3">                                                              
                                <div class="col-md-12">
                                    <p class="fs-6 text fw-bold ">Group By</p>

                                    <div class="row">                                                              
                                        <div class="col-md-4">

                                            <div class="form-check form-switch">
                                                <input class="form-check-input  mb-2 mr-3 fs-5 value="1" id="group_by_language" type="checkbox" name="group_by_language"   @if($mode == "faq" && isset($params->group_by_language ) && $params->group_by_language == 1) checked @endif>

                                                <label class="form-check-label " for="group_by_language">Language
                                                </label>
                                        
                                            </div>    
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input  mb-2 mr-3 fs-5" value="1" id="group_by_location" type="checkbox" name="group_by_location"  @if($mode == "faq" && isset($params->group_by_location ) && $params->group_by_location == 1) checked @endif>                    

                                                <label class="form-check-label" for="group_by_location">
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
                @if($mode == "download")
                <div class="row">
                    <div class="col-md-4">
                        &nbsp;
                    </div>
                    <div class="col-md-4" style="margin-top: 26px;">
                        <button id="filter" name="filter" class="btn btn-{{config('app.color_scheme')}} form-control"><i
                            class="mdi mdi-download"></i> View Report</button>
                    </div>
                    <div class="col-md-4"  style="margin-top: 26px;">
                        <a class="btn btn-secondary" href=" {{route("report.pdf", $website_slug)}}">
                            <i class="mdi mdi-eraser"></i> Clear
                        </a>
                    </div>                                            
                </div>
                @endif

            @if($mode == "download")
            </form>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

<script>
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
        if ( typeof $(data.element).data('lookup') !== 'undefined' && $(data.element).data('lookup').toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
            return data;
        }

        // Return `null` if the term should not be displayed
        return null;
    }
    
    $(document).ready(function () {

        // $('#from_date').prop('required', false);
        // $('#to_date').prop('required', false);
        // $('#team_name').prop('required', true);

        var c1 = $("#module").select2({
            width: '100%', // need to override the changed default
            closeOnSelect: false,
            placeholder: "All Categories",
            tags: false,
            tokenSeparators: [','],
            matcher: matchCustom,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        }); 
        
        var c2 = $("#chapter_id").select2({
            width: '100%', // need to override the changed default
            closeOnSelect: false,
            placeholder: "All Sub-Categories",
            tags: false,
            tokenSeparators: [','],
            matcher: matchCustom,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        });               

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

       
        $( "#from_date" ).datepicker(
            { 
                dateFormat: 'dd-mm-yy', 
                maxDate: "+0D", 
                changeMonth: true, 
                changeYear: true,
                onClose: function (selectedDate) {

                    if(selectedDate){
                        var idTeam = $('#team_name').val();

                        // var idModule = $('#module').val();
                        let idModules="";
                        var idModuleArr = $('#module').select2("val");
                        if(idModuleArr){
                            idModules = idModuleArr.toString();            
                        }                        
                        
                        // var idChapter = $('#chapter_id').val();
                        let idChapters="";
                        var idChapterArr = $('#chapter_id').select2("val");
                        if(idChapterArr){
                            idChapters = idChapterArr.toString();            
                        }                                                   
                        
                        let idLocations="";
                        var idLocationArr = $('#location_id').select2("val");
                        if(idLocationArr){
                            idLocations = idLocationArr.toString();            
                        }

                        let idLocationStates="";
                        var idLocationStateArr = $('#location_state_id').select2("val");
                        if(idLocationStateArr){
                            idLocationStates = idLocationStateArr.toString();            
                        }                    

                        let idLanguages="";
                        var idLanguageArr = $('#language_id').select2("val");
                        if(idLanguageArr){
                            idLanguages = idLanguageArr.toString();            
                        }

                        var from_user = $('#from_user').val();                        

                        var to_dateVal = $('#to_date').val();

                        console.log('from_date', selectedDate);
                        console.log('to_date', to_dateVal);

                        $.ajax({
                            url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                            // type: "GET",                
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
                                mode: 'use_date',                            
                                _token: '{{csrf_token()}}'
                            },
                            dataType: 'json',
                            success: function (result) {

                                // update date_reports
                                $.each(result.date_reports, function (key, value) {
                                    $("#from_date_reports").html('('+value.total_reports + ' Reports)');
                                    $("#to_date_reports").html('('+value.total_reports + ' Reports)');
                                });                               

                                // update first_time_reports
                                $.each(result.first_time_reports, function (key, value) {
                                    $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                                });   

                                // update followup_reports
                                $.each(result.followup_reports, function (key, value) {
                                    $("#followup_reports").html('('+value.total_reports + ' Reports)');
                                });   


                                // calendar_date_reports
                                $.each(result.calendar_date_reports, function (key, value) {
                                    $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                                });   

                                // fir_documents_reports
                                $.each(result.fir_documents_reports, function (key, value) {
                                    $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                                });                                   

                                // total report
                                $.each(result.total_reports, function (key, value) {
                                    $("#total_reports").html('('+value.total_reports + ' Reports)');
                                });       
                                
                                //update users
                                $('#from_user').html('<option value="">Select User</option>');
                                $.each(result.users, function (key, value) {
                                    $("#from_user").append('<option value="' + value
                                    .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                                });                                 


                            }
                        });       
                    }                 
                }                 
            }
        );

        $( "#to_date" ).datepicker(
            { 
                dateFormat: 'dd-mm-yy', 
                maxDate: "+0D", 
                changeMonth: true, 
                changeYear: true,
                onClose: function (selectedDate) {

                    if(selectedDate){

                        var idTeam = $('#team_name').val();

                        // var idModule = $('#module').val();
                        // var idChapter = $('#chapter_id').val();

                        // var idModule = $('#module').val();
                        let idModules="";
                        var idModuleArr = $('#module').select2("val");
                        if(idModuleArr){
                            idModules = idModuleArr.toString();            
                        }                        
                        
                        // var idChapter = $('#chapter_id').val();
                        let idChapters="";
                        var idChapterArr = $('#chapter_id').select2("val");
                        if(idChapterArr){
                            idChapters = idChapterArr.toString();            
                        } 


                        let idLocations="";
                        var idLocationArr = $('#location_id').select2("val");
                        if(idLocationArr){
                            idLocations = idLocationArr.toString();            
                        }

                        let idLocationStates="";
                        var idLocationStateArr = $('#location_state_id').select2("val");
                        if(idLocationStateArr){
                            idLocationStates = idLocationStateArr.toString();            
                        }    

                        let idLanguages="";
                        var idLanguageArr = $('#language_id').select2("val");
                        if(idLanguageArr){
                            idLanguages = idLanguageArr.toString();            
                        }

                        var from_user = $('#from_user').val();        

                        var from_dateVal = $('#from_date').val();

                        console.log('from_date', from_dateVal);
                        console.log('to_date', selectedDate);                    

                        $.ajax({
                            url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                            // type: "GET",                
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
                                mode: 'use_date',                            
                                _token: '{{csrf_token()}}'
                            },
                            dataType: 'json',
                            success: function (result) {

                                // update date_reports
                                $.each(result.date_reports, function (key, value) {
                                    $("#from_date_reports").html('('+value.total_reports + ' Reports)');
                                    $("#to_date_reports").html('('+value.total_reports + ' Reports)');
                                });                               

                                // update first_time_reports
                                $.each(result.first_time_reports, function (key, value) {
                                    $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                                });   

                                // update followup_reports
                                $.each(result.followup_reports, function (key, value) {
                                    $("#followup_reports").html('('+value.total_reports + ' Reports)');
                                });   


                                // calendar_date_reports
                                $.each(result.calendar_date_reports, function (key, value) {
                                    $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                                });   

                                // fir_documents_reports
                                $.each(result.fir_documents_reports, function (key, value) {
                                    $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                                });                                

                                // total report
                                $.each(result.total_reports, function (key, value) {
                                    $("#total_reports").html('('+value.total_reports + ' Reports)');
                                });            
                                
                                
                                //update users
                                $('#from_user').html('<option value="">Select User</option>');
                                $.each(result.users, function (key, value) {
                                    $("#from_user").append('<option value="' + value
                                    .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                                });                                 

                                
                            }
                        });       
                        
                    }
                }                 
            }
        );
      
        $('#overlay').hide();
        $('#page_content').show();

        // Issues
        // $('#team_name').on('change', function () {
        $('input[name="team_name"]').click(function() {  
            
            $('#overlay').show();

            var idTeam = this.value;

            console.log('idTeam', idTeam);

            // $("#module").html('');
            // $('#module').html('<option value="">All Categories</option>');

            // $("#chapter_id").html('');
            // $('#chapter_id').html('<option value="">All Sub-Categories</option>');

            $("#module").html('');
            $('#module').select2({
                placeholder: "All Categories"
            });
            
            $("#chapter_id").html('');
            $('#chapter_id').select2({
                placeholder: "All Sub-Categories"
            });             

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

            $('#first_time_reports').empty();
            $('#followup_reports').empty();
            $('#calendar_date_reports').empty();
            $('#fir_documents_reports').empty();
            $('#total_reports').empty();           

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');

            if(idTeam){

                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if(selectedReportType == "daily_report") {
                    $('#from_date').val('');
                    $('#to_date').val('');
                }      
                
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        team_id: idTeam,
                        mode: 'modules',
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#module').html('<option value="">All Category</option>');
                        $.each(result.modules, function (key, value) {
                            $("#module").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });


                        // update locations
                        $('#location_id').html('<option value="">All State</option>');
                        $.each(result.locations, function (key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        }); 

                        // update languages
                        $('#language_id').html('<option value="">All Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        });                 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });                           

                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });     

                        // update document tags
                        if(result.documentTags != undefined){
                            $.each(result.documentTags, function (key, value) {
                                $("#document_report_tags").append('<option value="' + value
                                    .id + '">' + value.tag + '</option>');
                            });   
                        }
                         
                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });                        

                        $('#overlay').hide();
                    }
                });
            }else {
                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if(selectedReportType == "daily_report") {
                    setDefaultDate();
                }      
                $('#overlay').hide();
            }            
        });

        // Category
        $('#module').on('change', function () {
            
            $('#overlay').show();

            // var idModule = this.value;

            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
                        

            // $("#chapter_id").html('');
            // $('#chapter_id').html('<option value="">All Sub-Categories</option>');

            $("#chapter_id").html('');
            $('#chapter_id').select2({
                placeholder: "All Sub-Categories"
            });               

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

             
            if(idModules){    

                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if(selectedReportType == "daily_report") {
                    $('#from_date').val('');
                    $('#to_date').val('');
                }    


                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        module_ids: idModules,
                        mode: 'chapters',
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#chapter_id').html('<option value="">Select Sub-Category</option>');
                        $.each(result.chapters, function (key, value) {
                            $("#chapter_id").append('<option value="' + value
                            .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });

                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function (key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        }); 

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        }); 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });     

                         $('#overlay').hide();                     
                    }
                });
            } else {
                var idTeam = $('#team_name').val();
                $('#team_name').val(idTeam); // Select the option with a value of '1'
                $('#team_name').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }
            
        });

        // Sub Category
        $('#chapter_id').on('change', function () {
            
            $('#overlay').show();

            // var idChapter = this.value;

            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
            }             

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

   
            if(idChapters){      

                //clear dates if report_type is daily_report
                var selectedReportType = $('input[name="report_data_type"]:checked').val();
                if(selectedReportType == "daily_report") {
                    $('#from_date').val('');
                    $('#to_date').val('');
                }    
                                
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        chapter_ids: idChapters,
                        mode: 'sub-category',
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                    
                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function (key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        }); 

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        }); 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        }); 

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });          

                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                        }); 

                        $('#overlay').hide();                         
                    }
                });
            } else {
                var idModule = $('#module').val();
                $('#module').val(idModule); // Select the option with a value of '1'
                $('#module').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }
            
        });        
        
        // Location
        $('#location_id').on('change', function () {
            
            $('#overlay').show();

            var idLocationArr = $('#location_id').select2("val");
            let idLocations = "";

            var idTeam = $('#team_name').val();
            // var idModule = $('#module').val();
            // var idChapter = $('#chapter_id').val();

            // var idModule = $('#module').val();
            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
            
            // var idChapter = $('#chapter_id').val();
            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
            }    

            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });  

            if(idLocationArr){
                idLocations = idLocationArr.toString();        
            }            

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if(idLocations){
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        location_ids: idLocations,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        mode: 'location',
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {

                        if(result.location_state.length > 0){
                            $('#location_container').removeClass('col-md-6').addClass( "col-md-4" );
                            $('#language_container').removeClass('col-md-6').addClass( "col-md-4" );
                            
                            
                            $('#location_state_container').css('display', '');
                            $('#location_state_id').html('<option value="">Select State District</option>');
                            $.each(result.location_state, function (key, value) {
                                $("#location_state_id").append('<option value="' + value
                                    .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                            });                        
                        } else {
                            $('#location_state_container').css('display', 'none');
                            $('#location_container').removeClass('col-md-4').addClass( "col-md-6" );
                            $('#location_container').removeClass('col-md-6').addClass( "col-md-6" );

                            $('#language_container').removeClass('col-md-4').addClass( "col-md-6" );
                            $('#language_container').removeClass('col-md-6').addClass( "col-md-6" );

                        }   



                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        }); 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        }); 

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });                           

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });    
                        
                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
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
        $('#location_state_id').on('change', function () {

            $('#overlay').show();

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";

            var idLocationArr = $('#location_id').select2("val");
            let idLocations = "";

            var idTeam = $('#team_name').val();
            // var idModule = $('#module').val();
            // var idChapter = $('#chapter_id').val();

            // var idModule = $('#module').val();
            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
            
            // var idChapter = $('#chapter_id').val();
            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
            }  

            $("#language_id").html('');
            $('#language_id').select2({
                placeholder: "All Languages"
            });  

            if(idLocationArr){
                idLocations = idLocationArr.toString();        
            } 
            
            if(idLocationStateArr){
                idLocationStates = idLocationStateArr.toString();        
            }             

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if(idLocationStates){
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        location_ids: idLocations,
                        location_state_ids: idLocationStates,
                        team_id: idTeam,
                        module_ids: idModules,
                        chapter_ids: idChapters,
                        mode: 'location_state',
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        }); 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        }); 
                        
                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });                         

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });                 
                        
                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
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
                $('#location_container').removeClass('col-md-4').addClass( "col-md-6" );
                $('#location_container').removeClass('col-md-6').addClass( "col-md-6" );

                $('#language_container').removeClass('col-md-4').addClass( "col-md-6" );
                $('#language_container').removeClass('col-md-6').addClass( "col-md-6" );

                $('#location_id').trigger("change");
                $('#overlay').hide();
            }

            
        });        

        // Language
        $('#language_id').on('change', function () {


            // var idLanguage = this.value;

            let idLanguages="";
            var idLanguageArr = $('#language_id').select2("val");
            if(idLanguageArr){
                idLanguages = idLanguageArr.toString();            
            }            

            let idLocations = "";
            var idLocationArr = $('#location_id').select2("val");
            if(idLocationArr){
                idLocations = idLocationArr.toString();        
            }

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";
            if(idLocationStateArr){
                idLocationStates = idLocationStateArr.toString();        
            }       


            var idTeam = $('#team_name').val();
            // var idModule = $('#module').val();
            // var idChapter = $('#chapter_id').val();

            // var idModule = $('#module').val();
            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
            
            // var idChapter = $('#chapter_id').val();
            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
            }   

            $("#from_user").html('');
            $('#from_user').html('<option value="">All Users</option>');


            if(idLanguages){
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
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
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });                           

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });                 
                        
                        //update users
                        $('#from_user').html('<option value="">Select User</option>');
                        $.each(result.users, function (key, value) {
                            $("#from_user").append('<option value="' + value
                            .id + '">' + value.name +  ' [ '+value.email+' ] ' +  ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });     
                        
                    }
                });
            }

        });

        // From_user
        $('#from_user').on('change', function () {


            var from_user = $('#from_user').val();

            var idTeam = $('#team_name').val();

            let idLanguages="";
            var idLanguageArr = $('#language_id').select2("val");
            if(idLanguageArr){
                idLanguages = idLanguageArr.toString();            
            }            

            let idLocations = "";
            var idLocationArr = $('#location_id').select2("val");
            if(idLocationArr){
                idLocations = idLocationArr.toString();        
            }

            var idLocationStateArr = $('#location_state_id').select2("val");
            let idLocationStates = "";
            if(idLocationStateArr){
                idLocationStates = idLocationStateArr.toString();        
            }       
            
            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
            
            // var idChapter = $('#chapter_id').val();
            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
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

            if(from_user){
                $.ajax({
                    url: "{{route('report.api.fetch-download-options', $website_slug)}}",
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
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {

                        // update locations
                        $('#location_id').html('<option value="">Select State</option>');
                        $.each(result.locations, function (key, value) {
                            $("#location_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });
                        $('#location_id').select2({
                            placeholder: "All States"
                        }); 

                        // update languages
                        $('#language_id').html('<option value="">Select Language</option>');
                        $.each(result.languages, function (key, value) {
                            $("#language_id").append('<option value="' + value
                                .id + '">' + value.name + ' ( '+value.total_reports + ' Reports )' + '</option>');
                        });   
                        $('#language_id').select2({
                            placeholder: "All Languages"
                        }); 

                        // update first_time_reports
                        $.each(result.first_time_reports, function (key, value) {
                            $("#first_time_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // update followup_reports
                        $.each(result.followup_reports, function (key, value) {
                            $("#followup_reports").html('('+value.total_reports + ' Reports)');
                        });                           

                        // calendar_date_reports
                        $.each(result.calendar_date_reports, function (key, value) {
                            $("#calendar_date_reports").html('('+value.total_reports + ' Reports)');
                        });   

                        // fir_documents_reports
                        $.each(result.fir_documents_reports, function (key, value) {
                            $("#fir_documents_reports").html('('+value.total_reports + ' Reports)');
                        });   


                        // total report
                        $.each(result.total_reports, function (key, value) {
                            $("#total_reports").html('('+value.total_reports + ' Reports)');
                        });   
                        
                    }
                });
            }

        });        

        @if($mode == "faq" && isset($params->team_name))
            //trigger team selection
            // $("#btnradio{{$params->team_name}}").attr('checked', true).trigger('click');
        @endif

        $('input[name="report_data_type"]').click(function() {            
           
            if($(this).attr('id') == 'daily_report') {
                
                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();                                           
                
                // $('#tags_container').hide();
                // $('#tags').hide();   
                
                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();     
                
                $('#table_report_container').hide();
                

            }else if($(this).attr('id') == 'hashtag_report') {

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

            } else if($(this).attr('id') == 'custom_report') {

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
            else if($(this).attr('id') == 'document_report') {

                // $('#tags_container').hide();
                // $('#tags').hide();

                $('#hashtags_container').hide();
                $('#hashtags').hide();      
                $('#team_name').prop('required', false);                
                $('#custom_report_options').hide();

                $('#document_report_container').show();
                $('#document_report_tags').show();         
                
                $('#table_report_container').hide();
               
            }else if($(this).attr('id') == 'hashtag_report') {
                                
                // $('#hashtags').prop('required', false);   
                $('#hashtags_container').hide();
                $('#hashtags').hide();            

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();                                   

            }else if($(this).attr('id') == 'work_report') {
                
                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();      

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();     
                
                $('#table_report_container').hide();
                

            }else if($(this).attr('id') == 'upload_date_report') {
                
                // $('#hashtags').prop('required', false);     
                $('#hashtags_container').hide();
                $('#hashtags').hide();                      

                // $('#tags_container').hide();
                // $('#tags').hide();   

                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();     
                
                $('#table_report_container').hide();
                
            }else if($(this).attr('id') == 'table_report') {

                // $('#tags_container').hide();
                // $('#tags').hide();

                $('#hashtags_container').hide();
                $('#hashtags').hide();      
                $('#team_name').prop('required', false);                
                $('#custom_report_options').hide();

                $('#document_report_container').hide();
                $('#document_report_tags').hide();         

                $('#table_report_container').show();

            }

            if( 
                $(this).attr('id') == 'hashtag_report'
            ){
                $('#view_reports').val("");         
            } else {
                $('#view_reports').val("yes");         
            }       
        });

        $("#filter").click(function() {
            var selectedReportType = $('input[name="report_data_type"]:checked').val();

            var selectedReportTypeFormated = selectedReportType.replace(/\_/g, ' ');

            var fromDate = $( "#from_date" ).val();
            var toDate = $( "#to_date" ).val();
            var idTeam = $('#team_name').val();
            var selectedTeamText = $('#team_name option:selected').text();
            var selectedModuleText = $('#module option:selected').text();
            var selectedChapterText = $('#chapter_id option:selected').text();

            var idTeam = $('#team_name').val();
            // var idModule = $('#module').val();
            // var idChapter = $('#chapter_id').val();

            // var idModule = $('#module').val();
            let idModules="";
            var idModuleArr = $('#module').select2("val");
            if(idModuleArr){
                idModules = idModuleArr.toString();            
            }                        
            
            // var idChapter = $('#chapter_id').val();
            let idChapters="";
            var idChapterArr = $('#chapter_id').select2("val");
            if(idChapterArr){
                idChapters = idChapterArr.toString();            
            } 

            // console.log('selectedTeamText', idTeam, selectedTeamText);
            // console.log('selectedModuleText', idModule, selectedModuleText);
            // console.log('selectedChapterText', idChapter, selectedChapterText);

            var finalMsgText='';
            if(idChapters != ''){
                finalMsgText = selectedChapterText;
            }else if(idModules != ''){
                finalMsgText = selectedModuleText;
            } else {
                finalMsgText = selectedTeamText;
            }

            if(selectedReportType == 'tag_report'){
                const myArray = finalMsgText.split(" (");
                finalMsgText = myArray[0];
            }

            if((fromDate == '' || toDate == '') && idTeam == '') {  
                event.preventDefault();       
                Swal.fire({
                    icon: 'error',
                    title: 'Oops',
                    html: 'Please select dates or an Issue to download '+selectedReportTypeFormated,
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                       
                    } else if (result.isDenied) {
                        
                    }
                }) 
            } else if((fromDate == '' || toDate == '') && idTeam != '') {     

                event.preventDefault();       
                Swal.fire({
                    icon: 'success',
                    title: 'Note',
                    html: 'No date is selected, this will download '+selectedReportTypeFormated+' for '+finalMsgText,                    
                    showCancelButton: true,
                    confirmButtonColor: '#2DA5D1',
                    confirmButtonText: 'OK',
                    cancelButtonText: "No, cancel it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // $('form#downloadForm').submit();
                        
                        var datastring = $("form#downloadForm").serialize();
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

        function checkDownloadReportCount(datastring){
            var datastringUpdated = datastring + "&mode=getcount";
            $.ajax({
            type: "GET",
            url: "{{ route('report.pdfview', $website_slug,[ 'downloadWord' => 'word']) }}",
            data: datastringUpdated,
            success: function(result) {
                //  alert('Data send');
                if(result > 0){
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

        setDefaultDate();

        function setDefaultDate(){

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
    });
</script>




