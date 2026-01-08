@extends($viewLayout)
@section('title', $reportTitle)

@section('content')

    @include('report_public_css')
    <style>
        @mobile .swiper {
            height: 100%;
            max-height: 550px;
        }

        .positionContainer {
            position: absolute;
            top: 65px;
            width: 98%;
        }
        @else
            .swiper {
                width: 500px;
                height: 100%;
                max-height: 550px;
                max-width: 100%;
            }

            .positionContainer {
                position: absolute;
                top: 20px;
                width: 98%;
            }
        @endmobile
    </style>

    <input type="text" name="socialShareContent" id="socialShareContent" value=""
        style="position:fixed;left:-999999px;top:-999999px;">

    <div class="page-content positionContainer">

        <div class="container-fluid" id="overlay">
            <div class="row">
                <div class="col-12" style="text-align: center;">
                    <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                    Loading...
                </div>
            </div>
        </div>

        <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off"
            id="find_more_downloadForm" target="_blank">
            <input type="hidden" name="team_name[]" id="find_more_team_id" value="">
            <input type="hidden" name="module[]" id="find_more_module_id" value="">
            <input type="hidden" name="chapter_id[]" id="find_more_chapter_id" value="">
            <input type="hidden" name="find_more[]" id="find_more" value="yes">
            @php
                foreach ($_GET as $name => $value) {
                    if (
                        $name !== 'team_name' &&
                        $name !== 'module[]' &&
                        $name !== 'chapter[]' &&
                        $name != 'from_date' &&
                        $name != 'to_date' &&
                        $name != 'from_user' &&
                        $name != 'report_from'
                    ) {
                        if (is_array($value)) {
                            // foreach($value as $key => $keyValue){
                            //     $name = htmlspecialchars($name);
                            //     $keyValue = htmlspecialchars($keyValue);
                            //     echo '<input type="hidden" name="' . $name . '[]" value="' . $keyValue . '">';
                            //     break;
                            // }
                        } else {
                            $name = htmlspecialchars($name);
                            $value = htmlspecialchars($value);
                            echo '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '">';
                        }
                    }
                }
            @endphp
        </form>


        <div class="container-fluid" id="page_content" style="display: none;">

            <!-- Share Report -->
            <div class="row">
                <div class="col-md-4"></div>

                <div class="col-md-4  fixed-button-share">
                    <button type="button" id="share_report" class="btn btn-{{ config('app.color_scheme') }}">
                        Share Report <i class="mdi mdi-share"></i>
                    </button>
                </div>

                <div class="col-md-4 fixed-button">
                    <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off"
                        id="downloadForm">
                        @php
                            foreach ($_GET as $name => $value) {
                                if ($name !== 'view_reports') {
                                    if (is_array($value)) {
                                        foreach ($value as $key => $keyValue) {
                                            $name = htmlspecialchars($name);
                                            $keyValue = htmlspecialchars($keyValue);
                                            echo '<input type="hidden" name="' .
                                                $name .
                                                '[]" value="' .
                                                $keyValue .
                                                '">';
                                        }
                                    } else {
                                        $name = htmlspecialchars($name);
                                        $value = htmlspecialchars($value);

                                        if ($name == 'report_type') {
                                            $divId = 'download_report_type';
                                        } else {
                                            $divId = $name;
                                        }
                                        echo '<input type="hidden" id="' .
                                            $divId .
                                            '" name="' .
                                            $name .
                                            '" value="' .
                                            $value .
                                            '">';
                                    }
                                }
                            }
                        @endphp


                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button"
                                    class="btn btn-{{ config('app.color_scheme') }} dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Download Report<i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" href="#word"><i class="mdi mdi-download"></i> Download Word
                                        Report</a>
                                    <a class="dropdown-item" href="#pdf"><i class="mdi mdi-download"></i> Download Pdf
                                        Report</a>
                                    <a class="dropdown-item" href="#googledoc"><i class="mdi mdi-download"></i> Download
                                        Google Doc Report</a>
                                    <a class="dropdown-item" href="#excel"><i class="mdi mdi-download"></i> Download
                                        Excel Report</a>
                                        @if($calendar_date==1)
                                        <a class="dropdown-item" href="#pdf_desc"><i class="mdi mdi-download"></i> Download PDF Description</a>
                                        @endif

                                        @if($zihad==1)
                                        <a class="dropdown-item" href="#lovezihad"><i class="mdi mdi-download"></i> Download Love Zihad Report</a>
                                        @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

            <div class="row" style="position: relative;top: 50px;">

                <div class="col-md-12 col-xl-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive">
                                <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">

                                        @php
                                            $total_news = 0;
                                        @endphp
                                        @foreach ($dataAllTeamReport as $key => $teamData)
                                            @php
                                                $total_news = $total_news + $teamData['team_count'];
                                            @endphp
                                        @endforeach

                                        <table class="tableStyleHeading" cellspacing="0" cellpadding="2"
                                            style="text-align: center;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; font-size: 15px;font-weight:bold">
                                                        {{ $reportTitle }}</td>
                                                </tr>
                                                @if ($reportHeading)
                                                    <tr>
                                                        <td style="text-align: center; font-weight:bold">
                                                            {{ $reportHeading }}</td>
                                                    </tr>
                                                @endif
                                                @if ($reportDescription)
                                                    <tr>
                                                        <td style="text-align: center">{{ $reportDescription }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>


                                        {{-- TEAM HEADING INDEX --}}
                                        @if ($show_index)
                                            <table class="tableStyle" border="1" cellspacing="0" cellpadding="2"
                                                width="100%" style="border: 1px solid black;">
                                                <tbody>
                                                    @foreach ($dataAllTeamReport as $key => $teamData)
                                                        <!-- Team Header Row -->
                                                        <tr>
                                                            <td colspan="2"
                                                                style="background-color: #FFC000; color: #000000; text-align: center;"
                                                                width="70%">
                                                                {{ $teamData['team']['name'] }}
                                                            </td>
                                                            <td style="background-color: #d9e2f3; color: #000000; text-align: center;"
                                                                width="20%">
                                                                News Count ({{ $teamData['team_count'] }})
                                                            </td>
                                                        </tr>

                                                        <!-- Loop through chapters for each team -->
                                                        @foreach (collect($data)->where('team_id', $teamData['team_id']) as $chapterData)
                                                            <tr>
                                                                <!-- Chapter index -->
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="10%">
                                                                    {{ $loop->iteration }}
                                                                </td>

                                                                <!-- Chapter Name -->
                                                                <td style="border: 1px solid #000000; text-align: left;"
                                                                    width="70%">
                                                                    @if (isset($chapterData['chapter']['id']))
                                                                        {{ $moduleNameArr[$chapterData['chapter']['id']] }}
                                                                        ->
                                                                        {{ $chapterData['chapter']['name'] }}
                                                                    @endif
                                                                </td>

                                                                <!-- Chapter Count -->
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="20%">
                                                                    {{ $chapterData['chapter_count'] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif


                                        @php
                                            $showEndReport = false;
                                        @endphp

                                        <div class="col-md-12 col-xl-12">
                                            <section class="light">
                                                <div class="container-fluid py-2 mt-3">
                                                    <div class="scrolling-pagination">
                                                        @foreach ($keyNews as $key => $reports)
                                                            @php
                                                                //exit();
                                                                //dd($reports);

                                                                if ($keyNews->hasMorePages() == '') {
                                                                    $showEndReport = true;
                                                                }

                                                                if ($reports['website_id'] == '1') {
                                                                    $prefix = config('app.app_domain_info') . 'public/';
                                                                } else {
                                                                    $prefix =
                                                                        config('app.app_domain_issue') . 'public/';
                                                                }

                                                                $defaultPlaceholder =
                                                                    config('app.url') . 'public/' . 'placeholder.png';

                                                                $created_at = \Carbon\Carbon::createFromTimestamp(
                                                                    strtotime($reports['created_at']),
                                                                )->format('M d, Y');
                                                                $publish_at = \Carbon\Carbon::createFromTimestamp(
                                                                    strtotime($reports['publish_at']),
                                                                )->format('M d, Y');
                                                                $downloadHeading =
                                                                    '_' .
                                                                    str_replace(
                                                                        ' ',
                                                                        '_',
                                                                        strtolower($reports['heading']),
                                                                    );
                                                            @endphp

                                                            <article class="postcard light {{ $publicReportColor }}">

                                                                <div class="row" style="overflow: scroll; width:100%;">
                                                                <div @mobile class="col-12" @else
                                                                    class="col-6" @endmobile>
                                                                    <!-- Slider main container -->
                                                                    <div class="swiper">
                                                                        <!-- Additional required wrapper -->
                                                                        <div class="swiper-wrapper">
                                                                            <!-- screenshots Slides -->
                                                                            @foreach ($reports['screenshot'] as $screenshot)
                                                                                @if ($screenshot['screenshot'] != '')
                                                                                    @php
                                                                                        $type =
                                                                                            $screenshot[
                                                                                                'screenshot_type'
                                                                                            ];
                                                                                        $type = str_replace(
                                                                                            '_',
                                                                                            ' ',
                                                                                            $type,
                                                                                        );
                                                                                        $type =
                                                                                            ucwords($type) .
                                                                                            ' Screenshot';

                                                                                        $finalScreenshot = '';

                                                                                        if (
                                                                                            $screenshot['screenshot'] !=
                                                                                            ''
                                                                                        ) {
                                                                                            $finalScreenshot = Helper::getImageUrl(
                                                                                                $screenshot[
                                                                                                    'screenshot'
                                                                                                ],
                                                                                            );
                                                                                        } else {
                                                                                            $finalScreenshot =
                                                                                                $prefix .
                                                                                                'placeholder.png';
                                                                                        }
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
                                                                                            download="{{ $screenshot['screenshot_type'] }}{{ $downloadHeading }}">
                                                                                            <i
                                                                                                class="fas fa-download"></i>
                                                                                        </a>

                                                                                        <div class="container py-5"
                                                                                            style="bottom: -55px; position: absolute;">
                                                                                            <div class="text-center">
                                                                                                <p
                                                                                                    class="btn btn-{{ config('app.color_scheme') }}">
                                                                                                    {{ $type }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach

                                                                            <!-- featuredimage Slides -->
                                                                            @foreach ($reports['feateredimage'] as $key => $featured_image)
                                                                                @if ($featured_image['featured_image'] != '')
                                                                                    @php
                                                                                        $type = ucwords(
                                                                                            'Featured Image',
                                                                                        );

                                                                                        $finalsource = '';

                                                                                        if (
                                                                                            $featured_image[
                                                                                                'featured_image'
                                                                                            ] != ''
                                                                                        ) {
                                                                                            $finalsource = Helper::getImageUrl(
                                                                                                $featured_image[
                                                                                                    'featured_image'
                                                                                                ],
                                                                                            );
                                                                                        } else {
                                                                                            $finalsource =
                                                                                                $prefix .
                                                                                                'placeholder.png';
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="swiper-slide">

                                                                                        <a href="javascript:void(0);"
                                                                                            class="pop">
                                                                                            <img class="postcard__img lazyload blur-up"
                                                                                                src="{{ $defaultPlaceholder }}"
                                                                                                data-sizes="auto"
                                                                                                data-src="{{ $finalsource }}"
                                                                                                data-srcset="{{ $finalsource }}"
                                                                                                alt="{{ $type }}" />
                                                                                        </a>

                                                                                        <a class="downloadImage"
                                                                                            href="{{ $finalsource }}"
                                                                                            target="_blank"
                                                                                            download="{{ $type }}{{ $downloadHeading }}">
                                                                                            <i
                                                                                                class="fas fa-download"></i>
                                                                                        </a>

                                                                                        <div class="container py-5"
                                                                                            style="bottom: -55px; position: absolute;">
                                                                                            <div class="text-center">
                                                                                                <p
                                                                                                    class="btn btn-{{ config('app.color_scheme') }}">
                                                                                                    {{ $type }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach


                                                                            <!-- Document Slides -->
                                                                            @foreach ($reports['document'] as $key => $document)
                                                                                @if ($document['document'] != '')
                                                                                    @php
                                                                                        $type =
                                                                                            $document['document_type'];
                                                                                        $type = str_replace(
                                                                                            '_',
                                                                                            ' ',
                                                                                            $type,
                                                                                        );
                                                                                        $type = ucwords($type);

                                                                                        if (
                                                                                            !empty(
                                                                                                $document[
                                                                                                    'document_name'
                                                                                                ]
                                                                                            )
                                                                                        ) {
                                                                                            $type =
                                                                                                $document[
                                                                                                    'document_name'
                                                                                                ];
                                                                                        }

                                                                                        $source = $document['document'];
                                                                                        $extension = '';

                                                                                        $finalsource = '';

                                                                                        if (
                                                                                            $document['document'] != ''
                                                                                        ) {
                                                                                            $finalsource = Helper::getImageUrl(
                                                                                                $document['document'],
                                                                                            );
                                                                                        } else {
                                                                                            $finalsource =
                                                                                                $prefix .
                                                                                                'placeholder.png';
                                                                                        }

                                                                                        $displayIcon =
                                                                                            $prefix . 'placeholder.png';
                                                                                        if ($extension == 'pdf') {
                                                                                            $displayIcon =
                                                                                                $prefix .
                                                                                                'placeholder_pdf.png';
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="swiper-slide">
                                                                                        <a href="javascript:void(0);"
                                                                                            class="pop">
                                                                                            <img class="postcard__img lazyload blur-up"
                                                                                                src="{{ $defaultPlaceholder }}"
                                                                                                data-sizes="auto"
                                                                                                data-src="{{ $displayIcon }}"
                                                                                                data-srcset="{{ $displayIcon }}"
                                                                                                alt="{{ $type }}" />
                                                                                        </a>

                                                                                        <a class="downloadImage"
                                                                                            href="{{ $prefix . $finalsource }}"
                                                                                            target="_blank"
                                                                                            download="{{ $document['document_type'] }}{{ $downloadHeading }}">
                                                                                            <i
                                                                                                class="fas fa-download"></i>
                                                                                        </a>

                                                                                        <div class="container py-5"
                                                                                            style="bottom: -55px; position: absolute;">
                                                                                            <div class="text-center">
                                                                                                <p
                                                                                                    class="btn btn-{{ config('app.color_scheme') }}">
                                                                                                    {{ $type }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach

                                                                        </div>

                                                                        <!-- If we need navigation buttons -->
                                                                        <div class="swiper-button-prev"></div>
                                                                        <div class="swiper-button-next"></div>


                                                                    </div>
                                                                </div>
                                                            <div @mobile class="col-12" @else
                                                                class="col-6" @endmobile>
                                                                <div class="postcard__text t-dark">
                                                                    <h1
                                                                        class="postcard__title {{ $publicReportColor }}">
                                                                        <a href="{{ $reports['link'] }}"
                                                                            target="_blank">{{ $reports['serial_number'] }}.
                                                                            {{ ucfirst($reports['heading']) }}</a>
                                                                    </h1>
                                                                    <div class="postcard__subtitle fw-bold">
                                                                        <i class="fas fa-folder mb-2 mr-2"></i>
                                                                        {{ ucfirst($reports['module']['name']) }}
                                                                        ->
                                                                        {{ ucfirst($reports['chapter']['name']) }}
                                                                    </div>
                                                                    <div class="postcard__subtitle ">
                                                                        <time datetime="2020-05-25 12:00:00">
                                                                            <i
                                                                                class="fas fa-calendar-alt mr-2"></i>
                                                                            {{ $publish_at }}
                                                                        </time>
                                                                        &nbsp;
                                                                        <i class="fas fa-language mr-2 fsicon"></i>
                                                                        {{ ucwords($reports['language_name']) }}

                                                                        @if ($reports['location_name'])
                                                                            &nbsp; <i
                                                                                class="fas fa-location-arrow mr-2"></i>
                                                                            {{ $reports['location_name'] }}
                                                                            @if ($reports['location_state_name'])
                                                                                ({{ $reports['location_state_name'] }})
                                                                            @endif
                                                                        @endif

                                                                        @if ($reports['link'])
                                                                            &nbsp; <span
                                                                                class="tag__item play {{ $publicReportColor }}">
                                                                                <a href="{{ $reports['link'] }}"
                                                                                    target="_blank"><i
                                                                                        class="fas fa-link mr-2"></i>
                                                                                    {{ ucwords($reports['source']) }}</a>
                                                                            </span>
                                                                        @endif


                                                                        @if (count($reports['followup']) > 0)
                                                                            <br>
                                                                            @foreach ($reports['followup'] as $key => $followup)
                                                                                <a href="{{ $followup['followup_link'] }}"
                                                                                    target="_blank">
                                                                                    <i
                                                                                        class="fas fa-link mr-2"></i>
                                                                                    Related News
                                                                                    {{ $key + 1 }}
                                                                                </a> |
                                                                            @endforeach
                                                                        @endif
                                                                    </div>

                                                                    <div
                                                                        class="postcard__bar {{ $reports['report_type'] == 'News' ? 'postcard__bar_red' : 'postcard__bar_blue' }}">
                                                                    </div>

                                                                    <div class="postcard__preview-txt">
                                                                        <ol>
                                                                            @foreach ($reports['keypoint'] as $key => $keypoint)
                                                                                <li>{{ trim($keypoint['keypoint']) }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ol>
                                                                    </div>

                                                                    @if (empty($find_more))
                                                                        <p style="padding-top:10px;">
                                                                            <span class="causale"
                                                                                style="margin:10px;color:#000; font-weight: 600; text-align:center;">
                                                                                <a href="javascript:void(0);"
                                                                                    onclick="findSimilar({{ $reports['team_id'] }},{{ $reports['module_id'] }},{{ $reports['chapter_id'] }});"
                                                                                    style="color:#000;"
                                                                                >
                                                                                    <i
                                                                                        class="fas fa-file mr-2 fsicon"></i>
                                                                                    Click here to find <span
                                                                                        class="text-info fs-4 fw-bold">{{ $chapterReportData[$reports['chapter_id']] }}</span>
                                                                                    similar incidents
                                                                                    of

                                                                                    @if (!empty($reports['module']['name']))
                                                                                        &nbsp;{{ ucfirst($reports['module']['name']) }}
                                                                                    @endif

                                                                                    @if (!empty($reports['chapter']['name']))
                                                                                        &nbsp;->&nbsp;{{ ucfirst($reports['chapter']['name']) }}
                                                                                    @endif
                                                                                    <i
                                                                                        class="fa fa-external-link-square"></i>
                                                                                </a>
                                                                            </span>
                                                                        </p>
                                                                    @endif

                                                                    <div style="text-align: right;">
                                                                      @php
    $seperator = "\r\n \r\n"; // WhatsApp-friendly new lines

    $keypointDescription = [];
    foreach ($reports['keypoint'] as $key => $keypoint) {
        $keypointDescription[] = trim($keypoint['keypoint']);
    }

    $socialShareContent  = ucfirst($reports['heading']);
    $socialShareContent .= $seperator . $publish_at; // Date
    $socialShareContent .= $seperator . ($reports['chapter']['name'] ?? ''); // Category
    $socialShareContent .= $seperator . $reports['location_name']; // Location
    $socialShareContent .= $seperator . ($reports['link'] ?? ''); // Link
    $socialShareContent .= $seperator . strip_tags(implode("\r\n", $keypointDescription)); // Keypoints
@endphp

<div class="copyBtn" data-content="{{ $socialShareContent }}">
    <button type="button" class="btn btn-{{ config('app.color_scheme') }} copyBtnText">
        <i class="fas fa-share" style="font-size: 18px; margin-right:5px;"></i>
        Share
    </button>
</div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </article>
                                                @endforeach

                                                @if ($showEndReport == true)
                                                    <div class="row">
                                                        <div class="col-md-12 col-xl-12 text-center mt-5">
                                                            <div class="btn btn-{{ config('app.color_scheme') }}">
                                                                Report End
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($keyNews)
                                                    {{ $keyNews->links() }}
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                </div>


                                @if ($fromPage == 'dmr')
                                    <div class="row">
                                        <div class="col-md-12 col-xl-12 text-left mt-5">
                                            <h5>
                                                Other Issues
                                            </h5>
                                            <div id="sub-category-btn-group">


                                                @foreach ($all_issues as $key => $issue)
                                                    @if ($issue->id != $team_name)
                                                        @php
                                                            $app_domain_info = config('app.app_domain_info');
                                                            $app_domain_issue = config('app.app_domain_issue');
                                                            $app_domain =
                                                                config('app.app_domain') . $website_slug . '/';
                                                            $domain = $app_domain;
                                                            $monitoringDate = $selectedDate;
                                                            $viewReportUrl = $domain . config('app.view_report_url');
                                                            $viewReportUrl = str_replace(
                                                                '##ISSUE_ID##',
                                                                $issue->id,
                                                                $viewReportUrl,
                                                            );
                                                            $viewReportUrl = str_replace(
                                                                '##START_DATE##',
                                                                $monitoringDate,
                                                                $viewReportUrl,
                                                            );
                                                            $viewReportUrl = str_replace(
                                                                '##END_DATE##',
                                                                $monitoringDate,
                                                                $viewReportUrl,
                                                            );
                                                        @endphp
                                                        <a href="{{ $viewReportUrl }}" class="find_more">
                                                            <label
                                                                class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                                style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">
                                                                {{ ucwords(strtolower($issue->name)) }}
                                                                ({{ $issue->totalReports }} Reports)
                                                            </label>
                                                        </a>
                                                    @endif
                                                @endforeach

                                                {{-- daily Popup     --}}
                                                @php
                                                    $app_domain = config('app.app_domain') . $website_slug . '/';

                                                    $selectedDateArr = explode('-', $selectedDate);
                                                    $monitoringDate =
                                                        $selectedDateArr[2] .
                                                        '-' .
                                                        $selectedDateArr[1] .
                                                        '-' .
                                                        $selectedDateArr[0];
                                                    $monitoringYear = $selectedDateArr[2];

                                                    $dailyPopupDateArr = explode('-', $dailyPopupDate);
                                                    $monitoringdailyPopupDate =
                                                        $dailyPopupDateArr[2] .
                                                        '-' .
                                                        $dailyPopupDateArr[1] .
                                                        '-' .
                                                        $dailyPopupDateArr[0];
                                                    $monitoringdailyPopupDateYear = $dailyPopupDateArr[2];

                                                    if (empty($monitoringdailyPopupDateYear)) {
                                                        $monitoringdailyPopupDateYear = date('Y');
                                                    }

                                                    $viewDailyPopupUrl =
                                                        $app_domain .
                                                        '?date=' .
                                                        $monitoringdailyPopupDate .
                                                        '&type=&eventYear=' .
                                                        $monitoringdailyPopupDateYear .
                                                        '&keyword=';
                                                @endphp
                                                <a href="{{ $viewDailyPopupUrl }}" class="find_more">
                                                    <label
                                                        class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">History
                                                        of {{ $dailyPopupDate }} ({{ $all_count_popup }}
                                                        Reports)</label>
                                                </a>

                                                {{-- Social Media Monitoring --}}
                                                @php
                                                    $app_domain = config('app.app_domain') . $website_slug . '/';

                                                    $selectedDateArr = explode('-', $selectedDate);
                                                    // $monitoringDate = $selectedDate;
                                                    $monitoringDate =
                                                        $selectedDateArr[0] .
                                                        '-' .
                                                        $selectedDateArr[1] .
                                                        '-' .
                                                        $selectedDateArr[2];
                                                    $monitoringYear = $selectedDateArr[2];

                                                    if (empty($monitoringYear)) {
                                                        $monitoringYear = date('Y');
                                                    }

                                                    // $viewDailyPopupUrl = $app_domain."?date=".$monitoringDate."&type=sm_cal&eventYear=".$monitoringYear."&keyword=";
                                                    $viewDailyPopupUrl =
                                                        $app_domain . 'sm-daily-monitoring-report/' . $monitoringDate;
                                                @endphp
                                                <a href="{{ $viewDailyPopupUrl }}" target="_blank"
                                                    class="find_more">
                                                    <label
                                                        class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">Social
                                                        Media Monitoring Report
                                                        ({{ $all_sm_cals['0']->totalReports }} Reports)</label>
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                @endif



                            </div>



                        </div>
                    </div>

                </div>



            </div>

        </div>

    </div>

</div>

</div>

<!-- Creates the bootstrap modal where the image will appear -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" style="color: black;">Preview</h4>
        </div>
        <div class="modal-body" style="text-align: center">
            <img src="" id="imagepreview" style="width: 100%;">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>


<script>
    $('ul.pagination').hide();
    $(function() {
        $('.scrolling-pagination').jscroll({
            loadingHtml: '<center><img src="{{ asset('public/images/loading.svg') }}" alt="Loading" /> <b>Loading...</b></center> ',
            autoTrigger: true,
            padding: 20,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.scrolling-pagination',
            callback: function() {
                $('ul.pagination').remove();
            }
        });
    });

    var swiper = "";

    function findSimilar(team_id, module_id, chapter_id) {
        $('#find_more_team_id').val(team_id);
        $('#find_more_module_id').val(module_id);
        $('#find_more_chapter_id').val(chapter_id);

        $("#find_more_downloadForm").submit();
    }

    $(document).ready(function() {

    $(".copyBtn").on('click', function () {
    var textToCopy = $(this).data("content");

    // Normalize
    let lines = textToCopy.replace(/\r\n/g, "\n").split("\n").map(line => line.trim()).filter(line => line);

    let heading  = lines[0] || "";
    let date     = lines[1] || "";
    let category = lines[2] || "";
    let location = lines[3] || "";
    let link     = lines[4] || "";
    let keypoints = lines.slice(5);

    const nl = "\n\n"; // double newline for WhatsApp spacing

    let numberedKeypoints = keypoints.map((item, idx) => `${idx + 1}. ${item}`).join(nl);

    let finalMessage =
        `*Heading:* ${heading}${nl}` +
        `*Date:* ${date}${nl}` +
        `*Category:* ${category}${nl}` +
        `*Location:* ${location}${nl}` +
        `*Link:* ${link}${nl}` +
        `*Keypoints:*${nl}${numberedKeypoints}`;

    console.log("finalMessage (WhatsApp) =>\n" + finalMessage);

    // ✅ Hidden textarea method (preserves newlines on paste)
    let tempInput = $("<textarea>");
    $("body").append(tempInput);
    tempInput.val(finalMessage).select();
    document.execCommand("copy");
    tempInput.remove();

    // Also update your visible textarea
    $('#socialShareContent').val(finalMessage);

    showDialog(
        'Sharable Content Copied ✅ Heading, Date, Category, Location, Link & Keypoints',
        '',
        'success'
    );
    });



        $('#overlay').hide();
        $('#page_content').show();
        $('.dropdown-toggle').dropdown();

        $('a[href="#word"]').click(function() {
            $('#download_report_type').val('word');
            $("#downloadForm").submit();
        });
        $('a[href="#pdf"]').click(function() {
            $('#download_report_type').val('pdf');
            $("#downloadForm").submit();
        });
        $('a[href="#googledoc"]').click(function() {
            $('#download_report_type').val('googledoc');
            $("#downloadForm").submit();
        });
        $('a[href="#excel"]').click(function() {
            $('#download_report_type').val('excel');
            $("#downloadForm").submit();
        });
        $('a[href="#pdf_desc"]').click(function() {
                console.log('PDF DESC clicked');
                $('#download_report_type').val('pdf_desc');
                $("#downloadForm").submit();
            });
        $('a[href="#lovezihad"]').click(function() {
                console.log('Love ZIhad CLicked');
                $('#download_report_type').val('lovezihad');
                $("#downloadForm").submit();
            });

        ////////////////////////
        //new Photo layout
        ////////////////////////

        //on click on image show image in popup modal
        $('.pop').on('click', function() {
            $('#imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');
        });

        swiper = new Swiper('.swiper', {
            speed: 400,
            slidesPerView: 1,
            loop: false,
            centeredSlides: true,
            spaceBetween: 20,
            // navigation: true,
            zoom: {
                minRatio: 1,
                maxRatio: 5,
            },
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });




    });

    function openpopup(reportId) {
        console.log('open popup reportId', reportId);
        $('#imagemodal_' + reportId).modal('show');
    }
</script>
@endsection
