@extends('layouts.calendar')
@section('title', $selectedDate.' - Social Media Daily Monitoring Report')
@section('content')

<style>
.h-100 {
    height: 100%!important;
}   
.card-img, .card-img-bottom, .card-img-top {
     height: 250px;
}
.card-footer {
    background-color: #d4e3f3;
}

div#social-links {
    margin: 0 auto;
    /* max-width: 500px; */
}
div#social-links ul li {
    display: inline-block;
}          
div#social-links ul li a {
    padding: 10px;
    /* border: 1px solid #ccc; */
    margin: 1px;
    font-size: 20px;
    color: #222;
    /* background-color: #ccc; */
}
@mobile
    .positionContainer {        
        position: absolute; 
        top:150px;
        width:98%;
    }

    .card-title{
        font-size: 30px;
    }

    .card-text{
        font-size: 25px;
    }    

    .card-img-top {
        object-fit: contain;
    }
@else
    .positionContainer {
        position: absolute;
        top: 100px;
        width:98%;
    }
               
@endmobile 
</style>  

    <div class="cal-page-content positionContainer">

        <input type="text" name="socialShareContent" id="socialShareContent" value="" style="position:fixed;left:-999999px;top:-999999px;">                                        

        <div class="container-fluid" id="overlay">
            <div class="row">
                <div class="col-12" style="text-align: center;">
                    <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                    Loading...
                </div>
            </div>
        </div>

        <div class="container-fluid" id="page_content" style="display: none;">

            <div class="row" id="daily_monitoring">
                <div class="col-md-12 bg-white">
                    <div class="row">
                        <div class="col-md-4 bg-white text-start mt-3 mb-3">
                            <a href="{{ url($website_slug.'/sm-daily-monitoring-report') }}/{{ $prevDate }}" class="btn btn-{{ config('app.color_scheme') }}"><< Previous Date</a>
                        </div>
                        <div class="col-md-4 bg-white text-center">
                            <h2 class="mt-3 mb-3" id="dateHeading" style="cursor:pointer"><i class="fa fa-calendar-check-o mr-5"  style="color: #1d5cc9;"></i> {{$selectedDate}} - SM Monitoring Report</h2>
                        </div>
                        <div class="col-md-4 bg-white text-end mt-3 mb-3">     
                            
                            <div  style="float: left;">
                                <button type="button" class="btn btn-{{ config('app.color_scheme') }}" id="shareSmReport">
                                    <i class="fas fa-share" style="font-size: 18px; margin-right:5px;"></i> Share
                                </button>                 
                            </div>

                            <div style="float: left; margin-left:10px;">
                                <form action="{{ route('download-sm-report', $website_slug) }}" method="get" target="_blank" autocomplete="off" id="downloadSmReport">
                                    <input type="hidden" name="date" value="{{$selectedDate}}">
                                    <input type="hidden" id="download_report_type" name="download_report_type" value="pdf">
                                </form>

                              
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-{{ config('app.color_scheme') }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Download <i class="fas fa-download"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">     
                                            <li><a class="dropdown-item" href="javascript:void(0)" id="download_word"><i class="mdi mdi-download"></i> Download Word Report</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" id="download_googledoc"><i class="mdi mdi-download"></i> Download Google Doc Report</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" id="download_pdf"><i class="mdi mdi-download"></i> Download Pdf Report</a></li>
                                        </ul>
                                    </div>
                                </div>


                            </div>
                             

                            <a href="{{ url($website_slug.'/sm-daily-monitoring-report') }}/{{ $nextDate }}" class="btn btn-{{ config('app.color_scheme') }}" id="prev">Next Date >></a>
                        </div>                                                
                    </div>    

                   

                </div>                
            </div>

            <div class="row @mobile row-cols-1 @else row-cols-4 @endmobile  g-4">

                @foreach ($all_items_sm_cal as $key => $item)
                    @php
                        $dateTime = DateTime::createFromFormat('Y-m-d', $item->date);
                        $loadingForYear = $dateTime->format('Y');
                        // $displayItemDate = $dateTime->format('jS \o\f F Y');
                        $displayItemDate = $dateTime->format('M d, Y');
                    @endphp                   

                    <div class="col">
                        <div class="card h-100">                            

                                @if ($item->image)
                                @php
                                    $hashtag = $item->heading;
                                    $finalScreenshot = Helper::getImageUrl($item->image);
                                @endphp           
                                
                                @if(!empty($item->link))
                                    <a href="{{ $item->link }}" target="_blank"
                                        class="img-responsive">
                                        <img class="card-img-top" src="{{ $finalScreenshot }}"
                                            alt="{{ $item->hashtag }}" />
                                    </a>         
                                @else
                                    <img class="card-img-top" src="{{ $finalScreenshot }}" alt="{{ $item->hashtag }}" />
                                @endif                                    
                                
                            @else
                                @if($item->team_id)    
                                @if(file_exists(URL::to('public/images/teams/' . $item->team_id.'.jpg')))
                                    <img class="card-img-top" src="{{ URL::to('public/images/teams/' . $item->team_id.'.jpg') }}" />
                                @else
                                    <img class="card-img-top" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                                @endif
                                @else
                                    <img class="card-img-top" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                                @endif                    
                            @endif


                            <div class="card-body">
                                <h5 class="card-title">
                                    @if(!empty($item->link))
                                       <a href="{{ $item->link }}" target="_blank" class="importo">{{ ucfirst($item->hashtag) }}</a>
                                    @else
                                        {{ ucfirst($item->hashtag) }}
                                    @endif                                    
                                </h5>
                                <p class="card-text">
                                    {!! $item->description !!}
                                </p>
                                
                            </div>

                           
                            <div class="card-footer">      
                                
                                <div class="row">
                                    <div class="col-6 text-start" style="margin:0; padding:0;">
                                        <a class="btn btn-link fs-5 fw-bold" href="{{ $item->link }}" target="_blank"  data-ripple-color="hsl(0, 0%, 67%)">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            <span class="d-none d-md-inline-block">
                                                {{ $displayItemDate }}
                                            </span>
                                        </a>                                        
                                    </div>
                                    <div class="col-6 text-end">
                                        @php
                                            $seperator = "####";
                                            /* $seperator = "<br><br>"; */
                                            $socialShareContent = ucfirst($item->hashtag);
                                            $socialShareContent .= $seperator.$displayItemDate;

                                            // First, decode all HTML entities to their applicable characters
                                            $decodedContent = html_entity_decode($item->description, ENT_QUOTES | ENT_HTML5);

                                            $socialShareContent .= $seperator.strip_tags($decodedContent);
                                            $socialShareContent .= $seperator.strip_tags($item->link);

                                            /* echo $socialShareContent; */
                                        @endphp
                                       
                                        <div class="copyBtn" data-content="{{$socialShareContent}}">
                                            <button type="button" class="btn btn-{{ config('app.color_scheme') }} copyBtnText">
                                                <i class="fas fa-share" style="font-size: 18px; margin-right:5px;"></i> Share
                                            </button>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </div>

    <!-- Creates the bootstrap modal to show the calender -->
    <div class="modal fade" id="calendarDateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center">
                    <h5 class="modal-title">Select Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="margin-left: auto; margin-right: auto;">
                    <div id="selectedDate"></div>
                </div>
                <div class="modal-footer" id="footer">
                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}" data-bs-dismiss="modal"
                        aria-label="Close">Close <i class="fa fa-remove mr-1"></i></button>            
                </div>                            
            </div>
        </div>
    </div>      
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#overlay').hide();
            $('#page_content').show();
        });
    </script>

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/select2.min.js') }}"></script>

    <script>

        $(document).ready(function() {

            

            $('#shareSmReport').click(function() {
                var url = window.location.href;
                var hostname = "{{ config('app.app_domain').$website_slug }}/"
                var finalParams = url.replace(hostname, '');
                console.log('share_report clicked');
                console.log('hostname', hostname);
                console.log('url', url);
                console.log('finalParams', finalParams);

                $.ajax({
                    type: "POST",
                    url: "{{ route('report.createsavedreport', $website_slug) }}",
                    data: {
                        url_params: finalParams,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        if (result.status == "success") {
                            console.log('Result Name', result.namedReport);

                            var textToCopy = "{{ config('app.app_domain') }}" +
                                'view_report/' + result.namedReport;

                            // navigator clipboard api needs a secure context (https)
                            if (navigator.clipboard && window.isSecureContext) {
                                // navigator clipboard api method'
                                navigator.clipboard.writeText(textToCopy);
                            } else {
                                // text area method
                                let textArea = document.createElement("textarea");
                                textArea.value = textToCopy;
                                // make the textarea out of viewport
                                textArea.style.position = "fixed";
                                textArea.style.left = "-999999px";
                                textArea.style.top = "-999999px";
                                document.body.appendChild(textArea);
                                textArea.focus();
                                textArea.select();
                                new Promise((res, rej) => {
                                    // here the magic happens
                                    document.execCommand('copy') ? res() : rej();
                                    textArea.remove();
                                });
                            }

                            showDialog('Report sharable link copied!','','success');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops',
                                html: 'Not able to generate saved report.',
                                confirmButtonText: 'Ok',
                            }).then((result) => {
                                if (result.isConfirmed) {

                                } else if (result.isDenied) {

                                }
                            })
                        }
                    }
                });

            });
            

            $('#download_word').click(function() {
                $('#download_report_type').val('word');
                $("#downloadSmReport").submit();
            });
            $('#download_pdf').click(function() {
                $('#download_report_type').val('pdf');
                $("#downloadSmReport").submit();
            });
            $('#download_googledoc').click(function() {
                $('#download_report_type').val('googledoc');
                $("#downloadSmReport").submit();
            });            

            $(".copyBtn").on('click', function() {
                var textToCopy =  $(this).data("content") 
                console.log("textToCopy", textToCopy);

                $('#socialShareContent').val(textToCopy);

                // navigator clipboard api needs a secure context (https)
                if (navigator.clipboard && window.isSecureContext) {

                    console.log("in if");

                    // navigator clipboard api method'
                    navigator.clipboard.writeText(textToCopy);
                    
                } else {

                    console.log("in else");

                    $('#socialShareContent').focus();
                    $('#socialShareContent').select();

                    new Promise((res, rej) => {
                        // here the magic happens
                        document.execCommand('copy') ? res() : rej();
                    });
                }

                showDialog('Sharable Content Copied : Heading, Date, Location, Description & Link.','','success');
            });            

          

            $("#selectedDate").datepicker({
                inline: true,
                dateFormat: 'dd-mm-yy',
                maxDate: "+0D",
                changeMonth: true,
                changeYear: true,
                defaultDate: '{{$selectedDate}}',
                onSelect: function(date) {
                    var url = "{{ url($website_slug.'/sm-daily-monitoring-report') }}/"+date;
                    window.location.href=url;
                }                
            });

            $("#dateHeading").click(function(){
                $('#calendarDateModal').modal('show');
            });
            
        });
    </script>
@endpush
