@extends('layouts.calendar')
@section('title', 'Calendar')
@section('content')

<style>
   
   /* .displayHidden{
    display: none !important;
   } */

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
    /* @mobile
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
    @endmobile     */

    .positionContainer {
        position: absolute;
        top: 100px;
    }

    .modal {
        top: 65px;
    }               

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

        <div class="row" style="margin-top:10px;">
            <div class="card cardFullHeight">
                <div class="row" id="daily_monitoring">
                    @include('partials.daily_monitoring')
                </div>
            </div>
        </div>

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
                    {{-- @mobile displayHidden @endif --}}
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

        .select2-dropdown{
            z-index: 10000 !important;
        }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/pages/select2.min.js') }}"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" />

    <script>
        var viewForDevice = "dayGridMonth";
        /* @mobile
            viewForDevice = "dayGridDay";                
        @endif */


        var CalendarApp = (function() {
            var calendarInstance;

            function initCalendar() {
                var calendarEl = document.getElementById('calendar');
                calendarInstance = new FullCalendar.Calendar(calendarEl, {
                    // your calendar options
                    initialView: viewForDevice,

                     /* @mobile
                        headerToolbar: {
                            left: '',
                            center: 'prev,next today',
                            right: '',
                        },
                        dayHeaders: true,
                        dayHeaderFormat: { weekday: 'long', month: 'numeric', day: 'numeric', omitCommas: true },

                    @else 
                        headerToolbar: false,
                        dayHeaders: false,
                     @endif    */

                     headerToolbar: false,
                     dayHeaders: false,

                    // If true, the calendar will always be 6 weeks tall. If false, the calendar will have either 4, 5, or 6 weeks, depending on the month.
                    fixedWeekCount: false,

                    // When set to false in month view will not show prev, next month dates
                    showNonCurrentDates: false,

                    // The day that each week begins. Sunday=0, Monday=1, Tuesday=2, etc.
                    firstDay: 1,

                    themeSystem: 'bootstrap4',
                    buttonIcons: false, // show the prev/next text

                    height: 'auto',  // Set height to 'auto' to automatically adjust based on events

                    eventOrder: '-title',

                    eventDidMount: function(arg) {
                        handleEventDisplay(arg);
                    },

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

                    eventContent: function(arg) { // Updated from 'eventRender'
                        let spanEl = document.createElement('span');
                        spanEl.innerText = arg.event.title;
                        spanEl.className = "fc-title";

                        let arrayOfDomNodes = [ spanEl ];
                        return { domNodes: arrayOfDomNodes };
                    },
                    
           
               

                    datesSet: function(dateInfo) {
  var calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  if (!calendarEl.dataset.delegateAttached) {
    calendarEl.dataset.delegateAttached = '1';

    function scrollToYearInDialog(year) {
      var section = document.getElementById(year) || document.getElementById('year-' + year);
      if (!section) return false;

      var container = section.closest('.modal-body, .dialog-content, .year-dialog-content');
      var offsetUp = 70; // ⬅️ always leave some space above (adjust this as needed)

      if (!container) {
        // fallback to window scroll
        var y = section.getBoundingClientRect().top + window.scrollY - offsetUp;
        window.scrollTo({ top: y, behavior: 'smooth' });
        return true;
      }

      // container scroll
      var sectionRect = section.getBoundingClientRect();
      var containerRect = container.getBoundingClientRect();
      var topRelative = sectionRect.top - containerRect.top + container.scrollTop;

      var finalTop = Math.max(0, topRelative - offsetUp);

      try {
        container.scrollTo({ top: finalTop, behavior: 'smooth' });
      } catch (e) {
        container.scrollTop = finalTop;
      }

      // highlight effect
      section.classList.add('calendar-highlight');
      setTimeout(() => section.classList.remove('calendar-highlight'), 1500);

      return true;
    }

    function waitForSectionAndScroll(year, attempt) {
      attempt = attempt || 0;
      var ok = scrollToYearInDialog(year);
      if (!ok && attempt < 30) {
        setTimeout(() => waitForSectionAndScroll(year, attempt + 1), 120);
      }
    }

    calendarEl.addEventListener('click', function(event) {
      var numEl = event.target.closest('.fc-daygrid-day-number');
      if (!numEl) return;
      var dayEl = numEl.closest('.fc-daygrid-day');
      if (!dayEl) return;
      var date = dayEl.getAttribute('data-date');
      if (!date) return;

      handleDateClick(date);
      openYearCalendar();

      var year = date.split('-')[0];
      setTimeout(() => waitForSectionAndScroll(year, 0), 150);

      event.stopPropagation();
    }, true);
  }
},







                    eventClick: function(info) {
                        // event click logic

                        /*console.log('info: ', info);
                        console.log('Event: ' , info.event);
                        */

                        // change the border color just for fun
                        info.el.style.borderColor = 'red';

                        
                        var eventDate = moment(info.event.start).format("YYYY-MM-DD");  
                        console.log(eventDate);


                        var event = {type:info.event.extendedProps.type, title:info.event.title, date:eventDate};
                        /* console.log('Event: ' , event); */

                        eventClick(event, "{{ ucwords(implode(',',$keywords)) }}");
                    }

                });
                calendarInstance.render();

                setTimeout(() => {
                    calendarInstance.today();  // Navigates the calendar to today's date                    
                }, 100);            
                
            }

            
            function handleEventDisplay(arg) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const eventDate = new Date(arg.event.start);
                eventDate.setHours(0, 0, 0, 0);

                if (eventDate < today) {
                    const maxEvents = 4;
                    let dayEl = arg.el.closest('.fc-daygrid-day-frame');
                    if (dayEl) {
                        manageMoreLessLinks(dayEl, arg);
                    }
                }
            }

            function manageMoreLessLinks(dayEl, arg) {
                let events = dayEl.querySelectorAll('.fc-daygrid-event-harness:not(.fc-daygrid-day-top)');
                const maxEvents = 4;
                if (events.length > maxEvents) {
                    events.forEach((eventEl, index) => {
                        eventEl.style.display = index < maxEvents ? '' : 'none';
                    });

                    let moreLink = dayEl.querySelector('.more-link');
                    if (!moreLink) {
                        moreLink = document.createElement('a');
                        moreLink.href = '#';
                        moreLink.textContent = `+${events.length - maxEvents} more`;
                        moreLink.className = 'more-link';
                        moreLink.addEventListener('click', function(e) {
                            e.preventDefault();
                            toggleEventVisibility(e, events, maxEvents);
                        });
                        dayEl.appendChild(moreLink);
                    }
                }
            }

            function toggleEventVisibility(e, events, maxEvents) {
                e.stopPropagation();
                const isExpanded = e.target.classList.contains('expanded');
                events.forEach((eventEl, index) => {
                    if (index >= maxEvents) {
                        eventEl.style.display = isExpanded ? 'none' : '';
                    }
                });
                e.target.textContent = isExpanded ? `+${events.length - maxEvents} more` : 'Show less';
                e.target.classList.toggle('expanded');
            }


            function handleDateClick(date) {
                console.log("Date clicked: ", date);
                // Add your custom date click handling logic here
                dayClick(date, false, "{{ ucwords(implode(',',$keywords)) }}", "", false);
            }

            return {
                getCalendar: function() {
                    if (!calendarInstance) {
                        initCalendar();
                    }
                    return calendarInstance;
                }
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            CalendarApp.getCalendar(); // Initializes and renders the calendar
        });
        
        $(document).ready(function() {    
        
            var sKeyword = $("#search-box").select2({
                closeOnSelect: true,
                placeholder: "Search",
                tags: true,
                // tokenSeparators: [','],
                // matcher: matchCustom,
                closeOnSelect: true,
                /* minimumInputLength: 2, // Only show options after typing 2 characters */
                /* templateResult: function(data) {
                    console.log("data", data);
                    // Always show the "Report" option
                    if (data.text === 'Report') {
                        return data.text;
                    }
                    
                    // Only show other options when searching
                    if (data.element && $(data.element).hasClass('search-item')) {
                        return data.loading ? 'Searching...' : data.text;
                    }
                    
                    return null;
                }, */
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
                    console.log("keyword Report added. Disable createTag");

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
               
                var calendar = CalendarApp.getCalendar(); // Access the initialized calendar
                calendar.gotoDate($(this).val());

                var prevMonth = $(this).find(':selected').data('prevmonth')
                $('#prev').text(prevMonth);

                var nextMonth = $(this).find(':selected').data('nextmonth');
                $('#next').text(nextMonth);

                //******************//
                var url = "{{ config('app.app_domain') }}{{$website_slug}}/?selected_month=" + selIndex +
                    "&selected_week={{ $selected_week }}&keywords={{ implode(',',$keywords) }}";
                history.pushState({
                    page: 'ThisDateThatYear'
                }, 'ThisDateThatYear', url);

                formatDayNumber();
            });

           
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
                /* var clickedDate = date.format(); */
                var clickedDate = date;
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
                    var title = "History of " + eventMonth;
                }else{
                    var title = keyword + " " + eventMonth + " - ThisDateThatYear";;
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
            //fc-col-header-cell
            console.log("insie formatDayNumber");            

            /* @mobile
               
            @else */
                setTimeout(() => {
                    $('.fc-daygrid-day-top a').each(function() {
                    // Extract the date from the 'aria-label' attribute
                    var dateFromLabel = $(this).attr('aria-label');
                    /* console.log("dateFromLabel", dateFromLabel); */
                    
                    // Format the date using Moment.js
                    var formattedDate = moment(dateFromLabel, "MMMM D, YYYY").format('Do MMM, dddd');
                    
                    // Update the text of the 'a' tag
                    var newText = '<span class="badge rounded-pill bg-primary mt-2 mb-2 p-2 date-number">'+formattedDate+'</span>'
                    $(this).html(newText);
                }); 
                }, 100);            
            /* @endif     */
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
