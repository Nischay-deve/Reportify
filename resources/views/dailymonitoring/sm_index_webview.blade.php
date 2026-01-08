<style>
    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .section {
        padding: 50px 20px;
        border-bottom: 1px solid #ddd;
    }

    .sticky-header {
        text-align: center;
        position: -webkit-sticky;
        /* For Safari */
        position: sticky;
        top: 76px;
        background: #fff;
        padding: 10px;
        z-index: 1000;
        border-bottom: 2px solid #333;
    }

    .section+.section {
        margin-top: 50px;
        /* Adds space between sections */
    }


    /* Scroll to Top Button */
    #scrollToTop {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: block; /* Hidden by default */
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 18px;
        text-align: center;
        line-height: 40px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        transition: opacity 0.3s, transform 0.3s;
    }

    #scrollToTop:hover {
        background-color: #555;
    }

    #scrollToTop:active {
        background-color: #777;
        transform: scale(0.9);
    }



    .h-100 {
        height: 100% !important;
    }

    .card-img,
    .card-img-bottom,
    .card-img-top {
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

    @mobile .positionContainer {
        position: absolute;
        top: 150px;
        width: 98%;
    }

    .card-title {
        font-size: 30px;
    }

    .card-text {
        font-size: 25px;
    }

    .card-img-top {
        object-fit: contain;
    }
    @else
        .positionContainer {
            position: absolute;
            top: 100px;
            width: 98%;
        }

    @endmobile
</style>
<div class="section" id="section1">
    <h2 class="sticky-header">{{ $selectedDate }} - SM Monitoring Report ({{$count_sm}})</h2></h2>

    <a id="sm-daily-monitoring-report"></a>
    <div class="page-content">
        <div class="container-fluid">

            {{-- PREV Next & Change Date Buttons --}}
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 text-start mt-3">
                            <a href="{{ url($website_slug . '/dmr-all-issues') }}/{{ $issue }}/{{ $prevDate }}"
                                class="btn btn-{{ config('app.color_scheme') }}">
                                << Previous Date</a>
                        </div>
                        <div class="col-md-4 text-center mt-3">
                            <span id="dateHeading" class="badge rounded-pill bg-primary"
                                style="height: 35px;padding: 10px 15px !important;font-size: 14px; cursor: pointer;">
                                <i class="fa fa-calendar-check-o mr-5" style="color: #ffffff;"></i> {{ $selectedDate }}
                                - Change Date
                            </span>
                        </div>
                        <div class="col-md-4 text-end mt-3">
                            <a href="{{ url($website_slug . '/dmr-all-issues') }}/{{ $issue }}/{{ $nextDate }}"
                                class="btn btn-{{ config('app.color_scheme') }}" id="prev">Next Date >></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row" style="position: relative;top: 30px;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="text" name="socialShareContent" id="socialShareContent" value=""
                                style="position:fixed;left:-999999px;top:-999999px;">
                                {{-- <div class="row" id="daily_monitoring">
                                <div class="col-md-12 bg-white">
                                    <div class="row">
                                        <div class="col-md-12 bg-white text-center">
                                            <h2 class="mb-3" id="dateHeading" style="cursor:pointer"><i
                                                    class="fa fa-calendar-check-o mr-5" style="color: #1d5cc9;"></i>
                                                {{ $selectedDate }} - SM Monitoring Report</h2>
                                        </div>
                                    </div>
                                </div>
                                --}}
                            </div>
                            
                            <div class="col-md-12 col-xl-12">
                                <section class="light">
                                    <div class="container-fluid py-2 mt-3">
                                        <div class="scrolling-pagination">
                           
                                @foreach ($all_items_sm_cal as $key => $item)
                                    @php
                                        /* dd($item); */
                                        $dateTime = DateTime::createFromFormat('Y-m-d', $item->publish_at);
                                        $loadingForYear = $dateTime->format('Y');
                                        // $displayItemDate = $dateTime->format('jS \o\f F Y');
                                        $displayItemDate = $dateTime->format('M d, Y');

                                        $defaultPlaceholder = config('app.url') . 'public/'. 'placeholder.png';

                                        $publish_at = \Carbon\Carbon::createFromTimestamp(strtotime($item->publish_at))->format('M d, Y');
                                        $downloadHeading = '_' . str_replace(' ', '_', strtolower($item->heading));

                                        $publicReportColor = "blue";

                                    @endphp
                                   
                                    {{-- Display content --}}
                                    <article class="postcard light {{ $publicReportColor }}">

                                        <div class="row" style="overflow: scroll; width:100%;">
                                            <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                <!-- Slider main container -->
                                                <div class="swiper">
                                                    <!-- Additional required wrapper -->
                                                    <div class="swiper-wrapper">                                                                                                                                
                                                        @php
                                                            //reportmedias
                                                            $type = "Social Media Image";
                                                            $finalScreenshot = Helper::getImageUrl($item->image);
                                                        @endphp                
                                                        <div class="swiper-slide">

                                                            <a href="javascript:void(0);"
                                                                class="pop">
                                                                <img class="postcard__img lazyload blur-up"
                                                                    src="{{ $defaultPlaceholder }}"
                                                                    data-sizes="auto"
                                                                    data-src="{{ $finalScreenshot }}"
                                                                    data-srcset="{{ $finalScreenshot }}"
                                                                    alt="{{ $type }}" />
                                                            </a>

                                                            <a class="downloadImage"
                                                                href="{{ $finalScreenshot }}"
                                                                target="_blank"
                                                                download="{{ $downloadHeading }}">
                                                                <i
                                                                    class="fas fa-download"></i>
                                                            </a>

                                                            <div class="container py-5"
                                                                style="bottom: -55px; position: absolute;">
                                                                <div
                                                                    class="text-center">
                                                                    <p
                                                                        class="btn btn-{{ config('app.color_scheme') }}">
                                                                        {{ $type }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- If we need navigation buttons -->
                                                    <div class="swiper-button-prev"></div>
                                                    <div class="swiper-button-next"></div>
                                                </div>                                                                            
                                            </div>
                                            <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                <div class="postcard__text t-dark">
                                                    <h1
                                                        class="postcard__title {{ $publicReportColor }}">
                                                        <a href="{{ $item->link }}"
                                                            target="_blank">                                                          
                                                         {{$key+1}}.   {{ ucfirst($item->heading) }}</a>
                                                    </h1>
                                                    @if (!empty($item->module_name))
                                                    <div class="postcard__subtitle fw-bold">
                                                        <i class="fas fa-folder mb-2 mr-2"></i> {{ ucfirst($item->module_name) }} @if (!empty($item->chapter_name)) -> {{ ucfirst($item->chapter_name) }} @endif
                                                    </div>
                                                    @endif
                                                    <div class="postcard__subtitle ">
                                                        <time datetime="2020-05-25 12:00:00">
                                                            <i class="fas fa-calendar-alt mr-2"></i>
                                                            {{ $publish_at }}
                                                        </time>
                                                        &nbsp;
                                                        @if (!empty($item->language_name))
                                                        <i class="fas fa-language mr-2 fsicon"></i>
                                                        {{ ucwords($item->language_name) }}
                                                        @endif

                                                        @if (!empty($item->location_name))
                                                            &nbsp; <i
                                                                class="fas fa-location-arrow mr-2"></i>
                                                            {{ $item->location_name }}
                                                            @if ($item->location_state_name)
                                                                ({{ $item->location_state_name }})
                                                            @endif
                                                        @endif

                                                        @if ($item->link)
                                                            &nbsp; <span
                                                                class="tag__item play {{ $publicReportColor }}">
                                                                <a href="{{ $item->link }}"
                                                                    target="_blank"><i
                                                                        class="fas fa-link mr-2"></i>
                                                                        @if (!empty($item->source)){{ ucwords($item->source) }}@endif</a>
                                                            </span>
                                                        @endif


                                                        
                                                    </div>

                                                    <div class="postcard__bar postcard__bar_blue"></div>

                                                    <div class="postcard__preview-txt">                                                                                
                                                            {!! trim($item->description) !!}
                                                    </div>

                                                    @if (empty($find_more) && !empty($item->module_id))
                                                        <p style="padding-top:10px;">
                                                            <span class="causale" style="margin:10px;color:#000; font-weight: 600; text-align:center;">
                                                                <a href="javascript:void(0);" onclick="findSimilar({{ $item->team_id }},{{ $item->module_id }},{{ $item->chapter_id }});" style="color:#000;"
                                                                target="_blank">
                                                                <i class="fas fa-file mr-2 fsicon"></i> Click here to find <span class="text-info fs-4 fw-bold">{{ $item->chapter_id }}</span> similar incidents
                                                            of

                                                                @if (!empty($item->module_name))
                                                                    &nbsp;{{ ucfirst($item->module_name) }}
                                                                @endif

                                                                @if (!empty($item->chapter_name))
                                                                    &nbsp;->&nbsp;{{ ucfirst($item->chapter_name) }}
                                                                @endif                                                                                                                                
                                                                <i class="fa fa-external-link-square"></i>
                                                                </a>                                                                
                                                            </span> 
                                                        </p>
                                                    @endif


                                                    <div style="text-align: right;">
                                                        @php
                                                            $seperator = "\r\n \r\n";
                                                            /* $seperator = "<br><br>"; */

                                                            $keypointDescription =  $item->description;
                                                            $socialShareContent = ucfirst($item->heading);
                                                            $socialShareContent .= $seperator.$publish_at;
                                                            $socialShareContent .= $seperator.strip_tags($keypointDescription);

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

                                    </article>
                                   
                                @endforeach
                           
                                        </div>
                                    </div>
                                </section>
                            </div>


                        </div>
                    </div>
                </div>
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

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
<script src="{{ asset('js/pages/select2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $(window).scroll(function() {
            var scrollPos = $(window).scrollTop();
            $('.section').each(function() {
                var sectionTop = $(this).offset().top;
                var sectionBottom = sectionTop + $(this).outerHeight();
                var $header = $(this).find('.sticky-header');

                if (scrollPos >= sectionTop && scrollPos < sectionBottom) {
                    $header.addClass('active');
                } else {
                    $header.removeClass('active');
                }
            });
        });

        // Show/hide the scroll to top button
        /* if ($(this).scrollTop() > 200) {
            $('#scrollToTop').fadeIn();
        } else {
            $('#scrollToTop').fadeOut();
        } */

        // Scroll to top on button click
        $('#scrollToTop').on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });

    });


    $(document).ready(function() {

        $("#selectedDate").datepicker({
            inline: true,
            dateFormat: 'dd-mm-yy',
            maxDate: "+0D",
            changeMonth: true,
            changeYear: true,
            defaultDate: '{{ $selectedDate }}',
            onSelect: function(date) {
                var url = "{{ url($website_slug . '/dmr-all-issues') }}/{{ $issue }}/" +
                date;
                window.location.href = url;
            }
        });


        $("#dateHeading").click(function() {
            $('#calendarDateModal').modal('show');
        });



        $('#shareSmReport').click(function() {
            var url = window.location.href;
            var hostname = "{{ config('app.app_domain') . $website_slug }}/"
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

                        showDialog('Report sharable link copied!', '', 'success');

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

        $(".copyBtn").on('click', function() {
            var textToCopy = $(this).data("content")
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

            showDialog('Sharable Content Copied : Heading, Date, Location, Description & Link.', '',
                'success');
        });


    });
</script>
