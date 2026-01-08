@extends('layouts.calendar')
@section('title', 'Calendar')
@section('content')

<style>
    #overlay {
        position: fixed;
        /* top:100px; */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /* background: black url(spinner.gif) center center no-repeat; */
        background-color: rgba(16, 16, 16, 0.5);
        /* opacity: .9; */
        z-index: 9999;
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
            top:100px;
        }

        .modal {
            top: 65px;
        }            

        .modal-body {
            max-height: calc(100vh - 360px);
            overflow-y: auto;
        }        
    @else
        .positionContainer {
            position: absolute;
            top: 100px;
        }

        .modal {
            top: 65px;
        }                        
    @endmobile    

    .closeButton{
        position: fixed;
        right: 40px;
        top: 83px;        
    }

    #loadMoreButton{
        z-index: 100;
        position: relative;
    }
    

    .select2-results{
        margin-top: 20px;
    }

    .toggler-icon {
        background-image: url(data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255, 255, 255, 0.5)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E);
    }
    .toggler-icon {
        display: inline-block;
        width: 1.5em;
        height: 1.5em;
        vertical-align: middle;
        content: "";
        background: no-repeat center center;
        background-size: 100% 100%;
    }

    .fc-more-cell{
        font-size: 16px;
    }

    .fc-more{
        font-weight: bold;
    }

    /* .fc-more-popover {
        overflow-y: scroll;
        max-height: 20%;
        max-width: 14%;
    }     */
</style>


<!-- Creates the bootstrap modal to show the center options -->
<div class="modal fade" id="loadCenterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <h5 class="modal-title">Show content from:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align:left; padding:20px;">
                <form class="d-flex" action="{{ config('app.app_domain').$website_slug }}" id="center-form" method="get"
                    autocomplete="off">
                    <table class="table table-striped table-hover">
                        <tbody>
                            @php
                                $websiteIdsFroSessionArr = explode(",", session()->get('website_id_calendar'));
                            @endphp
                            @foreach ($websites as $website)
                                <tr>
                                    <td>
                                        <div class="form-check form-switch form-switch-md">
                                            <input class="form-check-input" name="center_website[]"
                                                id="check_{{ $website->id }}" type="checkbox"
                                                value="{{ $website->id }}"
                                                @if (in_array($website->id, $websiteIdsFroSessionArr) ) checked @endif>
                                            <label class="form-check-label switchLabel"
                                                for="check_{{ $website->id }}">{{ $website->name }}</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-{{ config('app.color_scheme') }}"
                                        id="centerSubmitButton">Submit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="cal-page-content positionContainer">

    
    <div class="container-fluid" id="overlay">
        <div class="row">
            <div class="col-12" style="text-align: center;">
                <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                Loading...
            </div>
        </div>
    </div>

    <div class="container-fluid" id="page_content" style="display: none;">

        <div class="row">

            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @elseif(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif


            {{-- Cal --}}
            @php
                $col = 'col-md-12';
            @endphp
            <div class="{{ $col }}">
                <div class="card cardFullHeight">

                    {{-- MONTH --}}
                    <div class="card-header bg-dark text-white" id="month_cal_header">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4" style="text-align:center;">
                                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}"
                                        id="prev">
                                        << Previous</button>
                                </div>
                                <div class="col-md-4 font-awesome" style="text-align:center;">
                                    <select id="months" class="selectMonth fa">
                                        @php
                                            for ($i = 1; $i < 13; $i++) {
                                                $currentMonth = date('n');
                                                $year = date('Y');
                                                $monthNumber = sprintf('%02d', $i);
                                                $label = date('F', strtotime('01.' . $i . '.' . $year));
                                                $value = date('Y') . '-' . $monthNumber . '-01';
                                            
                                                $lastMonthNum = $i - 1;
                                                if ($lastMonthNum == 0) {
                                                    $lastMonthNum == 1;
                                                }
                                                $lastMonth = '<< ' . date('F', strtotime('01.' . $lastMonthNum . '.' . $year));
                                            
                                                $nextMonthNum = $i + 1;
                                                if ($nextMonthNum == 13) {
                                                    $nextMonthNum == 12;
                                                }
                                                $nextMonth = date('F', strtotime('01.' . $nextMonthNum . '.' . $year)) . ' >>';
                                            
                                                $selected = '';
                                                if ($i == $currentMonth) {
                                                    $selected = 'selected';
                                                }
                                                echo "<option 
                                            data-prevmonth='" .
                                                    $lastMonth .
                                                    "'
                                            data-nextmonth='" .
                                                    $nextMonth .
                                                    "'
                                            class='fa' 
                                            value='$value' " .
                                                    $selected .
                                                    '>';
                                                echo $label;
                                                // echo " &nbsp; &#xf103";
                                                echo '</option>';
                                            }
                                        @endphp
                                    </select>

                                    <i class="fas fa-angle-double-down"
                                        style="color:black;font-size:16px;position:relative;bottom:-1px;right:8%;"></i>
                                </div>
                                <div class="col-md-4" style="text-align:center;">
                                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}"
                                        id="next">Next >></button>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>

                </div>
            </div>


        </div>

        <div class="row" style="margin-top:10px;">
            <div class="card cardFullHeight">
                <div class="container-fluid">
                    <div class="row" id="daily_monitoring">
                        @include('partials.daily_monitoring')
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Creates the bootstrap modal to show the calender items -->
<div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="max-width: 100%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" name="modalLink" id="modalLink" value="">
                <input type="text" name="modalLinkFinal" id="modalLinkFinal" value=""
                    style="position:fixed;left:-999999px;top:-999999px;">

                <div class="col-3" style="text-align:center;">
                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}" id="prev_link"
                        data-prevlink="">
                        << Previous</button>
                </div>
                <div class="col-6 text-center">
                    <h4 class="modal-title" id="modalTitle"></h4>

                    <button type="button" id="share_report"
                        class="text-end fw-bold  btn btn-{{ config('app.color_scheme') }}">
                        Copy Link <i class="mdi mdi-share"></i>
                    </button>
                </div>
                <div class="col-3 text-end">
                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}" id="next_link"
                        data-nextlink="">Next >></button>
                </div>

                <button type="button" class="closeButton btn btn-{{ config('app.color_scheme') }}" data-bs-dismiss="modal"><i class="fa fa-remove mr-1"></i></button>

                
            </div>
            <div class="modal-body" style="text-align: center" id="modalBody">
                Loading...
            </div>
            <div class="modal-footer" id="footer">
                <button type="button" class="btn btn-{{ config('app.color_scheme') }}" data-bs-dismiss="modal"
                    aria-label="Close">Close <i class="fa fa-remove mr-1"></i></button>

            </div>
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



@push('styles')
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">

    <style>
        /* Hide the Day names row */
        .fc-head{
            display: none !important;    
        }

        .fc-day-top{
            float: none !important;
            text-align: center !important;
        }

        .fc td.fc-today{
            background-color: #495057 !important;            
        } 

        .fc-day-number{
            float: none !important;
            text-align: center !important;
        }

        .font-awesome .fa {
            font-family: "Font Awesome 5 Free", Open Sans;
            font-weight: 501;
        }

        @if (config('app.color_scheme') == 'pink')
            .fc-event {
                background-image: -webkit-linear-gradient(136deg, rgb(245, 190, 55) 0%, rgb(245, 190, 55) 100%);
            }

            .fc th.fc-widget-header {
                background-image: -webkit-linear-gradient(136deg, rgb(245, 190, 55) 0%, rgb(245, 190, 55) 100%);
                color: #000000;
                margin: 10px 0;
            }

        @else
            .fc-event {
                background-image: -webkit-linear-gradient(136deg, rgb(0, 0, 70) 0%, rgb(28, 181, 224) 100%);
            }

            .fc th.fc-widget-header {
                background-image: -webkit-linear-gradient(136deg, rgb(0, 0, 70) 0%, rgb(28, 181, 224) 100%);
                color: #000000;
            }

        @endif
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#overlay').hide();
            $('#page_content').show();
        });
    </script>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.3.0/fullcalendar.min.js'></script>
    <script src="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/pages/select2.min.js') }}"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" />

    <script>
        
        $(document).ready(function() {    
        
            var sKeyword = $("#search-box").select2({
                closeOnSelect: true,
                placeholder: "Search",
                tags: true,
                // tokenSeparators: [','],
                // matcher: matchCustom,
                closeOnSelect: true,
                language: {
                    inputTooShort: function(args) {
                        return "";
                    }
                },                     

            });       

            $('#search-box').on('select2:select', function (e) {
                var data = e.params.data;
                var keyword = data.text;
                if(keyword == "Report"){
                    console.log("keyword REport added. Disable createTag");

                    $("#search-box").select2({
                        createTag: function(params) {
                            return undefined;
                        },
                    });
                }

            });       
            
            $('#search-box').on('select2:unselect', function (e) {
                var data = e.params.data;
                var keyword = data.text;
            });


            @if (count($keywords) > 0)
                 $("#search-box").val([@foreach($keywords as $keyword)"{{$keyword}}",@endforeach]);                
                 $("#search-box").trigger('change.select2');                
            @endif           

            $("#daily_monitoring_button").click(function() {              
                var timer = setInterval(function() {
                    $('html, body').animate({
                        scrollTop: $("#daily_monitoring").offset().top
                    }, 1000);

                    window.clearInterval(timer);
                }, 500);
            });    

            $('#month_cal_header').show();

            /* Month related methods */
            $("#prev").click(function() {
                var selIndex = $("#months").prop('selectedIndex');
                if (selIndex != 0) {
                    $("#months").val($('#months option').eq(selIndex - 1).val());
                    $('#months').trigger('change');
                } else {
                    selIndex = 11;
                    $("#months").val($('#months option').eq(selIndex).val());
                    $('#months').trigger('change');
                }
                formatDayNumber();
            });

            $("#next").click(function() {
                var selIndex = $("#months").prop('selectedIndex');
                if (selIndex != $('#months option').length - 1) {
                    $("#months").val($('#months option').eq(selIndex + 1).val());
                    $('#months').trigger('change');
                } else {
                    selIndex = 0;
                    $("#months").val($('#months option').eq(selIndex).val());
                    $('#months').trigger('change');
                }
                formatDayNumber();
            });

            $('#months').on('change', function() {
                var selIndex = $("#months").prop('selectedIndex');
                console.log('selected_month set to ', selIndex);
                $('#selected_month').val(selIndex);
                $('#calendar').fullCalendar('gotoDate', $(this).val());
                /* $('.fc-day-header').hide(); */

                var prevMonth = $(this).find(':selected').data('prevmonth')
                $('#prev').text(prevMonth);
                /* $('#prev_modal').text(prevMonth);                 */

                var nextMonth = $(this).find(':selected').data('nextmonth');
                $('#next').text(nextMonth);
                /* $('#next_modal').text(nextMonth); */

                //******************//
                var url = "{{ config('app.app_domain') }}{{$website_slug}}/?selected_month=" + selIndex +
                    "&selected_week={{ $selected_week }}&keywords={{ implode(',',$keywords) }}";
                history.pushState({
                    page: 'ThisDateThatYear'
                }, 'ThisDateThatYear', url);

                formatDayNumber();
            });

            // page is now ready, initialize the calendar...
            $('#calendar').fullCalendar({

                // If true, the calendar will always be 6 weeks tall. If false, the calendar will have either 4, 5, or 6 weeks, depending on the month.
                fixedWeekCount: false,

                // When set to false in month view will not show prev, next month dates
                showNonCurrentDates: false,

                header: false,               

                /* agendaWeek , basicWeek, basicDay, month */
                /* defaultView: window.mobilecheck() ? "basicWeek" : "month", */
                defaultView: "month",


                // The day that each week begins. Sunday=0, Monday=1, Tuesday=2, etc.
                firstDay: 1,

                height: '100%',
                contentHeight: 1000,
                /* titleFormat: 'dddd MMMM', */
                themeSystem: 'bootstrap4',
                buttonIcons: false, // show the prev/next text
                
                eventLimit: true, // allow "more" link when too many events  
                eventLimitText: "Read more",
                
                views: {
                    month: {
                    eventLimit: 4
                    }
                },   
                             
                eventOrder: '-title',

                events: [
                    @foreach ($all_items as $item)
                        @php
                            $title = '';
                            $eventWord = 'event';
                            if ($item->totalEvents > 1) {
                                $eventWord = 'events';
                            }
                            if ($item->cal_type == 'other') {
                                $title = $item->calendar_year . ': ' . $item->totalEvents;
                            } elseif ($item->cal_type == 'document') {
                                $title = 'Report: ' . $item->totalEvents;
                            }
                            
                            if(!empty($keyword)){
                                $description = str_replace('\'','',$item->description);
                                $title .= "<br/><div style=\"padding: 5px 2px 0; text-align: left;\">".$description."</div>";
                            }  

                        @endphp 
                        {
                            title: '{!! $title !!}',
                            start: '{{ date('Y') . '-' . $item->calendar_date }}',
                            type: '{{ $item->cal_type }}',
                            date: '{{ date('Y') . '-' . $item->calendar_date }}',

                        },
                    @endforeach
                ],

                eventRender: function(event, element) {
                    element.find('span.fc-title').html(element.find('span.fc-title')
                        .text());
                },

                dayClick: function(date, jsEvent, view) {
                    dayClick(date, false, "{{ ucwords(implode(',',$keywords)) }}", "", false);
                },

                eventClick: function(event, jsEvent, view) {
                    eventClick(event, "{{ ucwords(implode(',',$keywords)) }}");
                },

                eventDidMount: function(arg) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Normalize today date to start of day for comparison

                    const eventDate = new Date(arg.event.start);
                    eventDate.setHours(0, 0, 0, 0); // Normalize event date to start of day

                    if (eventDate < today) {
                        const maxEvents = 4; // Max events visible per day
                        let dayEl = arg.el.closest('.fc-daygrid-day');

                        if (dayEl) {
                            let events = dayEl.querySelectorAll('.fc-daygrid-event-harness:not(.fc-daygrid-day-top)');
                            if (events.length > maxEvents) {
                                // Initial hide of extra events
                                events.forEach((eventEl, index) => {
                                    if (index >= maxEvents) {
                                        eventEl.style.display = 'none';
                                    }
                                });

                                // Manage more/less link
                                let moreLink = dayEl.querySelector('.more-link');
                                if (!moreLink) {
                                    moreLink = document.createElement('a');
                                    moreLink.href = '#';
                                    moreLink.textContent = `+${events.length - maxEvents} more`;
                                    moreLink.className = 'more-link';
                                    moreLink.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const isExpanded = moreLink.classList.contains('expanded');

                                        // Toggle events display
                                        events.forEach((eventEl, index) => {
                                            if (index >= maxEvents) {
                                                eventEl.style.display = isExpanded ? 'none' : '';
                                            }
                                        });

                                        // Update link text and toggle expanded class
                                        moreLink.textContent = isExpanded ? `+${events.length - maxEvents} more` : 'Show less';
                                        moreLink.classList.toggle('expanded');
                                    });

                                    dayEl.appendChild(moreLink);
                                }
                            }
                        }
                    } else {
                        // Today or future dates
                        // Show all events, no need to do anything special here
                    }
                },                

            })

            @if ($selected_month != '')
                var selectedMonth = $('#months option').eq({{ $selected_month }}).val();
                console.log('selectedMonth', selectedMonth);
                $("#months").val(selectedMonth);
                $('#months').trigger('change');
            @else
                $('#months').trigger('change');
            @endif


            $('#share_report').click(function() {
                var url = window.location.href;
                var modalLink = $('#modalLink').val();
                var finalParams = modalLink
                /* // console.log('share_report clicked');
                // console.log('hostname', hostname);
                // console.log('url', url);
                // console.log('finalParams', finalParams); */

                $.ajax({
                    type: "POST",
                    url: "{{ route('calendar.create-tdty-saved-report', $website_slug) }}",
                    data: {
                        url_params: finalParams,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        if (result.status == "success") {
                            console.log('Result Name', result.namedReport);

                            var textToCopy = "{{ config('app.app_domain') }}" + 
                                'view_link/' + result.namedReport;

                            console.log('textToCopy', textToCopy);

                            $('#modalLinkFinal').val(textToCopy);


                            // navigator clipboard api needs a secure context (https)
                            if (navigator.clipboard && window.isSecureContext) {
                                // navigator clipboard api method'
                                navigator.clipboard.writeText(textToCopy);
                            } else {

                                console.log('inaide else');

                                // text area method
                                /* let textArea = document.createElement("textarea");
                                textArea.value = textToCopy;
                                // make the textarea out of viewport
                                textArea.style.position = "fixed";
                                textArea.style.left = "-999999px";
                                textArea.style.top = "-999999px";
                                document.body.appendChild(textArea);
                                textArea.focus();
                                textArea.select(); */

                                $('#modalLinkFinal').focus();
                                $('#modalLinkFinal').select();
                                new Promise((res, rej) => {
                                    // here the magic happens
                                    document.execCommand('copy') ? res() : rej();
                                    /* textArea.remove(); */

                                    console.log('inaide Promise');
                                });
                            }

                            showDialog('Report sharable link copied!','','success');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops',
                                html: 'Not able to generate saved link.',
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

            $("#prev_link").click(function() {
                console.log("Model prev_link clicked");
                var date = $('#prev_link').attr('data-prevlink');
                dayClick(date, true, "{{ ucwords(implode(',',$keywords)) }}", "", false);
                formatDayNumber();
            });

            $("#next_link").click(function() {
                console.log("Model next_link clicked");
                var date = $('#next_link').attr('data-nextlink');
                dayClick(date, true, "{{ ucwords(implode(',',$keywords)) }}","", false);
                formatDayNumber();
            });
 

            $("#selectedDate").datepicker({
                inline: true,
                dateFormat: 'dd-mm-yy',
                maxDate: "+0D",
                changeMonth: true,
                changeYear: true,
                defaultDate: '{{$selectedDate}}',
                onSelect: function(date) {
                    var url = "{{ url($website_slug.'/daily-monitoring-report') }}/"+date;
                    window.location.href=url;
                }                
            });

            $("#dateHeading").click(function(){
                $('#calendarDateModal').modal('show');
            });
                  
            $("#calendarModal").on("hidden.bs.modal", function () {
                console.log("Modal closed");
            });     
        });

        function dayClick(date, formated = false, keyword = "", showall = "", append = false) {

            $('#overlay').show();
            console.log("loadDate inside dayClick", date);

            if (formated) {
                var clickedDate = date;
            } else {
                var clickedDate = date.format();
            }

            var eventMonth = moment(clickedDate).format('MMMM');
            var eventDate = moment(clickedDate).format('MMMM Do');
            var eventYear = moment(clickedDate).format('YYYY');

            if (keyword == "") {
               
                var title = "History of " + eventDate;

                var prevLink = moment(clickedDate).subtract(1, 'days').format('YYYY-MM-DD')
                var prevLinkDisplay = moment(clickedDate).subtract(1, 'days').format('MMMM Do');

                var nextLink = moment(clickedDate).add(1, 'days').format('YYYY-MM-DD')
                var nextLinkDisplay = moment(clickedDate).add(1, 'days').format('MMMM Do');

                $('#prev_link').html('');
                $('#prev_link').html("<< " + prevLinkDisplay);
                $('#prev_link').data('prevlink', prevLink).attr('data-prevlink', prevLink);

                $('#next_link').html('');
                $('#next_link').html(nextLinkDisplay + ' >>');
                $('#next_link').data('nextlink', nextLink).attr('data-nextlink', nextLink);

                console.log('dayClick prevLink', prevLink);
                console.log('dayClick next date', nextLink);
            } else {
                if(keyword == "Document"){
                    var title = "History of " + eventDate;
                }else{
                    var title = keyword + " " + eventDate + " - ThisDateThatYear";;
                }

                var prevLink = moment(clickedDate).subtract(1, 'months').format('YYYY-MM-01')
                var prevLinkDisplay = moment(clickedDate).subtract(1, 'months').format('MMMM');

                var nextLink = moment(clickedDate).add(1, 'months').format('YYYY-MM-01')
                var nextLinkDisplay = moment(clickedDate).add(1, 'months').format('MMMM');

                $('#prev_link').html('');
                $('#prev_link').html("<< " + prevLinkDisplay);
                $('#prev_link').data('prevlink', prevLink).attr('data-prevlink', prevLink);

                $('#next_link').html('');
                $('#next_link').html(nextLinkDisplay + ' >>');
                $('#next_link').data('nextlink', nextLink).attr('data-nextlink', nextLink);

                console.log('keyword dayClick prevLink', prevLink);
                console.log('keyword dayClick next date', nextLink);
            }

            var idTeam = this.value;

            if(!append){              
                $("#modalTitle").html('');                
                $('#modalBody').html('<p>Loading...</p>');
            }

            @php
                $showall = "";
                if(!empty(implode(',',$keywords))){
                    $showall = "yes";
                }
            @endphp

            var modalLink = "date=" + clickedDate + "&type=&eventYear=" + eventYear + "&keyword=" + "{{ implode(',',$keywords) }}" + "&showall=" + "{{$showall}}";

            $('#modalLink').val(modalLink);

            $.ajax({
                url: "{{ route('calendar.load',$website_slug) }}",
                type: "GET",
                data: {
                    date: clickedDate,
                    type: '{{ $type }}',
                    eventYear: eventYear,
                    keywords: "{{ implode(',',$keywords) }}",
                    showall: showall,
                },
                dataType: 'html',
                success: function(result) {
                    $('#overlay').hide();

                    $('#modalTitle').html(title);                    
                    if(!append){
                        $('#modalBody').html(result);
                    }else{
                        var titleContainer = '<div class="text-center"><h4 class="modal-title" id="modalTitle">'+title+'</h4></div>';
                        $('#calendarModal').find('.modal-body').append( titleContainer + result);
                    }

                    $('#calendarModal').modal('show');

                    changeTitle(title);

                    if(!append){
                        if ($('#' + eventYear).length) {
                            var timer = setInterval(function() {
                                console.log('Scroll To Element ...');
                                var timelineContainer = $('#timeline');
                                if (timelineContainer.length > 0 && showall == "") {


                                    $('#modalBody').animate({
                                        scrollTop: $('#' + eventYear).offset().top - 120
                                    }, 1500);

                                    window.clearInterval(timer);

                                    /* console.log('Done');     */
                                }
                            }, 1000);
                        }
                    }
                },
                error: function (jqXHR, exception) {
                    $('#overlay').hide();
                },
            });
        }

        function eventClick(event, keyword = "", showall = "") {
            $('#overlay').show();
            console.log('eventClick', event.type, event.title, event.date);

            var eventDate = moment(event.date).format('MMMM Do');
            var eventMonth = moment(event.date).format('MMMM');

            var title = event.title;
            const titleArray = title.split(":");
            var eventYear = titleArray[0];
            eventYear = eventYear.replace("TW", "");

            /* console.log('eventYear', eventYear); */

            if (keyword == "") {
                /* var title = "Social Media Listing: " + eventDate; */
                var title = "History of " + eventDate;
                if (event.type == "incidence_cal") {
                    /* title = "Incidence Listing: " + eventDate; */
                    title = "History of " + eventDate;
                } else if (event.type == "document") {
                    /* title = "Report Listing: " + eventDate; */
                    title = "History of " + eventDate;
                }

                var prevLink = moment(event.date).subtract(1, 'days').format('YYYY-MM-DD')
                var prevLinkDisplay = moment(event.date).subtract(1, 'days').format('MMMM Do');

                var nextLink = moment(event.date).add(1, 'days').format('YYYY-MM-DD')
                var nextLinkDisplay = moment(event.date).add(1, 'days').format('MMMM Do');

                $('#prev_link').html('');
                $('#prev_link').html("<< " + prevLinkDisplay);
                $('#prev_link').data('prevlink', prevLink).attr('data-prevlink', prevLink);

                $('#next_link').html('');
                $('#next_link').html(nextLinkDisplay + ' >>');
                $('#next_link').data('nextlink', nextLink).attr('data-nextlink', nextLink);

                console.log('eventClick prevLink', prevLink);
                console.log('eventClick next date', nextLink);

            } else {
                if(keyword == "Document"){
                    var title = "Report " + eventMonth;
                }else{
                    var title = keyword + " " + eventMonth;
                }
                

                var prevLink = moment(event.date).subtract(1, 'months').format('YYYY-MM-01')
                var prevLinkDisplay = moment(event.date).subtract(1, 'months').format('MMMM');

                var nextLink = moment(event.date).add(1, 'months').format('YYYY-MM-01')
                var nextLinkDisplay = moment(event.date).add(1, 'months').format('MMMM');

                $('#prev_link').html('');
                $('#prev_link').html("<< " + prevLinkDisplay);
                $('#prev_link').data('prevlink', prevLink).attr('data-prevlink', prevLink);

                $('#next_link').html('');
                $('#next_link').html(nextLinkDisplay + ' >>');
                $('#next_link').data('nextlink', nextLink).attr('data-nextlink', nextLink);

                console.log('keyword eventClick prevLink', prevLink);
                console.log('keyword eventClick next date', nextLink);

            }

            var idTeam = this.value;
            $("#modalTitle").html('');
            $('#modalBody').html('');
            $('#modalBody').html('<p>Loading...</p>');


            @php
                $showall = "";
                if(!empty(implode(',',$keywords))){
                    $showall = "yes";
                }
            @endphp


            var modalLink = "date=" + event.date + "&type=" + event.type + "&eventYear=" + eventYear + "&keyword=" +
                "{{ implode(',',$keywords) }}" + "&showall=" + "{{$showall}}";
                
            $('#modalLink').val(modalLink);


            $.ajax({
                url: "{{ route('calendar.load',$website_slug) }}",
                type: "GET",
                data: {
                    date: event.date,
                    type: event.type,
                    eventYear: eventYear,
                    keywords: "{{ implode(',',$keywords) }}",
                    showall: showall,
                },
                dataType: 'html',
                success: function(result) {
                    $('#overlay').hide();
                    $('#modalBody').html(result);
                    $('#modalTitle').html(title);
                    $('#calendarModal').modal('show');

                    changeTitle(title);

                    if ($('#' + eventYear).length) {
                        var timer = setInterval(function() {
                            console.log('Scroll To Element ...');
                            var timelineContainer = $('#timeline');
                            if (timelineContainer.length > 0 && showall == "") {


                                $('#modalBody').animate({
                                    scrollTop: $('#' + eventYear).offset().top - 120
                                }, 1500);

                                window.clearInterval(timer);
                                console.log('Done');
                            }
                        }, 1000);
                    }

                },
                error: function (jqXHR, exception) {
                    $('#overlay').hide();
                },
            });
        }

        @if ($calendar_date)
            var loadDate = moment("{{ $calendar_date }}", "YYYY-MM-DD").utc(true).format("YYYY-MM-DD");
            /* console.log("loadDatessss", loadDate); */
            dayClick(loadDate, true, "{{ ucwords(implode(',',$keywords)) }}", "", false);
        @elseif (implode(',',$keywords) != '' && $calendar_date == '')
            @php
                if ($selected_month != '') {
                    $calendar_date = date('Y') . '-' . ($selected_month + 1) . '-01';
                } else {
                    $calendar_date = date('Y') . '-' . date('n') . '-01';
                }
            @endphp
            var loadDate = moment("{{ $calendar_date }}", "YYYY-MM-DD").utc(true).format("YYYY-MM-DD");
            dayClick(loadDate, true, "{{ ucwords(implode(',',$keywords)) }}", "yes", false);
        @endif

        function formatDayNumber(){
            $('.fc-day-number').each(function() {
                var dataDate = $(this).closest('td').attr('data-date');              
                var dataDateFormated = '<span class="badge rounded-pill bg-primary mt-2 mb-2 p-2">'+moment(dataDate).format('Do MMM, dddd')+'</span>';
                $(this).html(dataDateFormated);
            });            
        }

        function changeTitle(newTitle) {
            $('title').html(newTitle);
        }        

        function loadMoreKeywordData(){
            console.log("loadMoreButton Model prev_link clicked");
            var date = $('#prev_link').attr('data-prevlink');
            dayClick(date, true, "{{ ucwords(implode(',',$keywords)) }}", "", true);
            formatDayNumber();                    
        }
       
      
    </script>
@endpush
