                
               <div class="col-md-12 bg-white">
                    <div class="row">
                        <div class="col-md-4 bg-white text-start mt-3 mb-3">
                            <a href="{{ url($website_slug.'/daily-monitoring-report') }}/{{ $prevDate }}" class="btn btn-{{ config('app.color_scheme') }}"><< Previous Date</a>
                        </div>
                        <div class="col-md-4 bg-white text-center mt-3 mb-3">
                            <span id="dateHeading" class="badge rounded-pill bg-primary" style="height: 35px;padding: 10px 15px !important;font-size: 14px; cursor: pointer;">
                                <i class="fa fa-calendar-check-o mr-5"  style="color: #ffffff;"></i> {{$selectedDate}} - Monitoring Report
                            </span>
                        </div>
                        <div class="col-md-4 bg-white text-end mt-3 mb-3">                            
                            <a href="{{ url($website_slug.'/daily-monitoring-report') }}/{{ $nextDate }}" class="btn btn-{{ config('app.color_scheme') }}">Next Date >></a>
                        </div>                                                
                    </div>    
                </div>

                @php
                    // if ($imageSize == '100') {
                    //     $desktopCol = "col-4"; // 3 cards per row
                    // } elseif ($imageSize == '75') {
                    //     $desktopCol = "col-3"; // 4 cards per row
                    // } elseif ($imageSize == '60') {
                    //     $desktopCol = "col-five"; // Custom class for 5 cards per row
                    // } elseif ($imageSize == '50' || $imageSize == '25') {
                    //     $desktopCol = "col-2"; // 6 cards per row
                    // }
                    $desktopCol = "col-five"; // Custom class for 5 cards per row
                @endphp


                <div class="col-md-12 bg-white">
                    <div class="d-flex justify-content-center mt-50 mb-50">

                        <div class="row">
                            {{-- Social Media Monitoring --}}
                            @php
                            $app_domain = config('app.app_domain').$website_slug;
                            
                            $selectedDateArr = explode("-", $selectedDate);
                            // $monitoringDate = $selectedDate;
                            $monitoringDate = $selectedDateArr[0]."-".$selectedDateArr[1]."-".$selectedDateArr[2];
                            $monitoringYear = $selectedDateArr[2];

                            if(empty($monitoringYear)){
                                $monitoringYear = date("Y");
                            }

                            if($all_sm_cals['0']->totalReports > 0)   {
                                // $viewDailyPopupUrl = $app_domain."sm-daily-monitoring-report/".$monitoringDate;
                                $viewDailyPopupUrl = $app_domain."/dmr-all-issues/sm-daily-monitoring-report/".$monitoringDate;
                                $target = 'target="_blank"';
                            }else{
                                $viewDailyPopupUrl = "javascript:void(zeroPopup('Social Media Monitoring Report','".$selectedDate."'))";
                                $target = '';
                            }                                
                            @endphp    
                            <div class="@mobile col-md-6 @else {{$desktopCol}} @endmobile mt-2">
                                <div class="card">
                                    <a href="{{$viewDailyPopupUrl}}" {{$target}}>
                                        <img  
                                        class="card-img-top"
                                        alt="Social Media Monitoring Report"
                                        src="{{ URL::to('public/images/social_media_monitoring.jpg') }}" />
                                    </a>                                
                                    <div class="card-body bg-light">
                                        <h5 class="card-title text-center">
                                            <a href="{{$viewDailyPopupUrl}}"   {{$target}}  class="text-default mb-2">
                                                Social Media
                                            </a>                                        
                                        </h5>
                                        <p class="card-text text-center">{{$all_sm_cals['0']->totalReports}} Reports</p>
                                    </div>
                                </div>
                            </div>  

                            {{-- Categories --}}
                            @foreach ($all_issues as $key => $issue)
                                @php
                                    // $domain = $app_domain."/dmr/";
                                    $domain = $app_domain."/dmr-all-issues/";
                                    
                                    $monitoringDate = $selectedDate;

                                    if($issue->totalReports > 0){
                                        $viewReportUrl = $domain.$issue->slug."/".$monitoringDate;
                                        $target = 'target="_blank"';
                                    }else{
                                        $viewReportUrl = "javascript:void(zeroPopup('".ucwords(strtolower($issue->name))."','".$selectedDate."'))";
                                        $target = '';
                                    }

                                    
                                @endphp
                                <div class="@mobile col-md-6 @else {{$desktopCol}} @endmobile mt-2">
                                    <div class="card">
                                        <a href="{{$viewReportUrl}}"  {{$target}}>
                                            @if ($issue->id)
                                                @if (file_exists(public_path().'/images/issues/' . $issue->id . '.jpg'))
                                                    <img  class="card-img-top"
                                                        alt=""
                                                        src="{{ URL::to('public/images/issues/' . $issue->id . '.jpg') }}" />
                                                @else

                                                    <img  class="card-img-top"
                                                        alt=""
                                                        src="{{ URL::to('public/images/issues/0.jpg') }}" />
                                                @endif

                                            @else
                                                <img  class="card-img-top"
                                                    alt="" src="{{ URL::to('public/images/issues/0.jpg') }}" />
                                            @endif
                                        </a>                              
                                        <div class="card-body bg-light">
                                            <h5 class="card-title text-center">
                                                <a href="{{$viewReportUrl}}"   {{$target}}  class="text-default mb-2">
                                                    {{ ucwords(strtolower($issue->name)) }} {{file_exists(URL::to('public/images/issues/' . $issue->id . '.jpg'))}}
                                                </a>                                       
                                            </h5>
                                            <p class="card-text text-center">{{$issue->totalReports}} Reports</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Today in History     --}}
                            @php
                            $app_domain = config('app.app_domain').$website_slug."/";
                            
                            $selectedDateArr = explode("-", $selectedDate);
                            // $monitoringDate = $selectedDateArr[2]."-".$selectedDateArr[1]."-".$selectedDateArr[0];
                            $monitoringDate = $selectedDateArr[0]."-".$selectedDateArr[1]."-".$selectedDateArr[2];
                            $monitoringYear = $selectedDateArr[2];

                            
                            $dailyPopupDateArr = explode("-", $dailyPopupDate);
                            $monitoringdailyPopupDate = $dailyPopupDateArr[2]."-".$dailyPopupDateArr[1]."-".$dailyPopupDateArr[0];
                            $monitoringdailyPopupDateYear = $dailyPopupDateArr[2];


                            if(empty($monitoringdailyPopupDateYear)){
                                $monitoringdailyPopupDateYear = date("Y");
                            }

                            if($all_count_popup > 0){    
                                // $viewDailyPopupUrl = $app_domain."?date=".$monitoringdailyPopupDate."&type=&eventYear=".$monitoringdailyPopupDateYear."&keyword=";

                                $viewDailyPopupUrl = $app_domain."dmr-all-issues/history-report/".$monitoringDate;
                                $target = 'target="_blank"';
                            }else{
                                $viewDailyPopupUrl = "javascript:void(zeroPopup('History ','".$dailyPopupDate."'))";
                                $target = '';
                            }                                  
                            
                            @endphp    
                                                        
                            <div class="@mobile col-md-6 @else {{$desktopCol}} @endmobile mt-2">
                                <div class="card">
                                    <a href="{{$viewDailyPopupUrl}}"  {{$target}}>
                                        <img  class="card-img-top"
                                        alt="History of {{$dailyPopupDate}}"
                                        src="{{ URL::to('public/images/daily_popup.jpg') }}" />
                                    </a>                               
                                    <div class="card-body bg-light">
                                        <h5 class="card-title text-center">
                                            <a href="{{$viewDailyPopupUrl}}"  {{$target}}  class="text-default mb-2">
                                                Today in History
                                            </a>                                      
                                        </h5>
                                        <p class="card-text text-center">{{$all_count_popup}} Reports</p>
                                    </div>
                                </div>
                            </div>                                   

                        </div>
                    </div>
                </div>

<style>
    /* Custom class for 5 equal columns */
    .col-five {
        flex: 0 0 20%;
        max-width: 20%;
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }

    /* Make it responsive */
    @media (max-width: 768px) {
        .col-five {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>