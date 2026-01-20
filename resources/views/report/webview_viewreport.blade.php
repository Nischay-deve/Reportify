@extends($viewLayout)
@section('title',$reportTitle)

@php
$paginationLinks = "";
if($keyNews && $mode == "view"){
$paginationLinks = $keyNews->links()->render();

}

@endphp

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

    @else .swiper {
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

<input type="text" name="socialShareContent" id="socialShareContent" value="" style="position:fixed;left:-999999px;top:-999999px;">

<div class="page-content @if($mode == " view") positionContainer @endif">

    @if($mode == "view")
    <div class="container-fluid" id="overlay">
        <div class="row">
            <div class="col-12" style="text-align: center;">
                <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                Loading...
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off" id="find_more_downloadForm"
        target="_blank">
        <input type="hidden" name="team_name" id="find_more_team_id" value="">
        <input type="hidden" name="module[]" id="find_more_module_id" value="">
        <input type="hidden" name="chapter_id[]" id="find_more_chapter_id" value="">
        <input type="hidden" name="find_more" id="find_more" value="yes">

        <input type="hidden" id="download_file_name" name="download_file_name" value="">
        <input type="hidden" id="download_file_heading" name="download_file_heading" value="">
        <input type="hidden" id="download_file_title" name="download_file_title" value="">
        <input type="hidden" id="tags" name="tags" value="">
        <input type="hidden" id="view_reports" name="view_reports" value="yes">
        <input type="hidden" id="report_type" name="report_type" value="word">
        <input type="hidden" id="news_order_by_order" name="news_order_by_order" value="desc">
        <input type="hidden" id="report_data_type" name="report_data_type" value="daily_report">
        <input type="hidden" id="table_report_state_order" name="table_report_state_order" value="state_asc">
        <input type="hidden" id="table_report_state_min_cases" name="table_report_state_min_cases" value="5">
        <input type="hidden" id="hashtags" name="hashtags" value="">
        <input type="hidden" id="document_report_tags" name="document_report_tags" value="">
    </form>

    <div class="container-fluid" id="page_content" @if($mode=="view" ) style="display: none;" @endif>

        @if($mode != 'view-all')
        <!-- Share Report -->
        <div class="row">
            <div class="col-md-4"></div>

            <div class="col-md-4  fixed-button-share">
                <button type="button" id="share_report" class="btn btn-{{ config('app.color_scheme') }}">
                    Share Report <i class="mdi mdi-share"></i>
                </button>
            </div>

            <div class="col-md-4 fixed-button">
                <form action="" method="get" autocomplete="off" id="downloadForm">
                    <input type="hidden" name="mode" value="download">
                    <input type="hidden" id="download_report_type" name="report_type" value="pdf">

                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button"
                                class="btn btn-{{ config('app.color_scheme') }} dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Download Report <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item" href="javascript:void(0);" id="word"><i class="mdi mdi-download"></i> Download Word
                                    Report</a>
                                <a class="dropdown-item" href="javascript:void(0);" id="pdf"><i class="mdi mdi-download"></i> Download Pdf
                                    Report</a>
                                <a class="dropdown-item" href="javascript:void(0);" id="googledoc"><i class="mdi mdi-download"></i> Download
                                    Google Doc Report</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        @endif

        @php
        $total_news = 0;
        @endphp
        @foreach ($dataAllTeamReport as $key => $teamData)
        @php
        $total_news = $total_news + $teamData['team_count'];
        @endphp
        @endforeach

        @if($total_news > 0)
        <div class="row" style="position: relative;top: 50px;">

            <div class="col-md-12 col-xl-12">

                <div class="card">

                    <div class="card-body">

                        <div class="table-responsive">
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer @if($page == 1) report-container @endif">
                                <div class="row">

                                    <table class="tableStyleHeading" cellspacing="0" cellpadding="2"
                                        style="text-align: center;">
                                        <tbody>
                                            @if($mode != 'view-all')
                                            <tr>
                                                <td style="text-align: center; font-size: 15px;font-weight:bold">
                                                    {{ $reportTitle }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if ($reportHeading)
                                            <tr>
                                                <td style="text-align: center; font-weight:bold">
                                                    {{ $reportHeading }}
                                                </td>
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
                                            <tr>
                                                <td colspan="2" style="background-color: #FFC000; color: #000000; text-align: center;"
                                                    width="70%">{{ $teamData['team']['name'] }}</td>
                                                <td style="background-color: #d9e2f3; color: #000000; text-align: center;"
                                                    width="20%">News Count ({{ $teamData['team_count'] }})</td>
                                            </tr>


                                            @endforeach

                                            @foreach ($data as $key => $datas)
                                            @php
                                            // dd($datas);
                                            @endphp
                                            <tr>
                                                <td style="border: 1px solid #000000; text-align: center;"
                                                    width="10%">{{ $key + 1 }}</td>
                                                <td style="border: 1px solid #000000; text-align: left;"
                                                    width="70%" align="left">
                                                    @if (isset($datas['chapter']['id']))
                                                    {{ $moduleNameArr[$datas['chapter']['id']] }} ->
                                                    {{ $datas['chapter']['name'] }}
                                                    @endif
                                                </td>
                                                <td style="border: 1px solid #000000; text-align: center;"
                                                    width="20%">
                                                    {{ $datas['chapter_count'] }}
                                                </td>
                                            </tr>
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
                                                <div class="scrolling-pagination @if($page > 1) report-container @endif">
                                                    @foreach ($keyNews as $key => $reports)
                                                    @php
                                                    //exit();
                                                    //dd($reports);

                                                    if($mode == "view" && $keyNews->hasMorePages() == ""){
                                                    $showEndReport = true;
                                                    }

                                                    if ($reports['website_id'] == '1') {
                                                    $prefix = config('app.app_domain_info').'public/';
                                                    } else {
                                                    $prefix = config('app.app_domain_issue').'public/';
                                                    }

                                                    $defaultPlaceholder = config('app.url') . 'public/'. 'placeholder.png';

                                                    $created_at = \Carbon\Carbon::createFromTimestamp(strtotime($reports['created_at']))->format('M d, Y');
                                                    $publish_at = \Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('M d, Y');
                                                    $downloadHeading = '_' . str_replace(' ', '_', strtolower($reports['heading']));
                                                    @endphp

                                                    <article class="postcard light {{ $publicReportColor }}">

                                                        <div class="row" style="overflow: scroll; width:100%;">
                                                            <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                                <!-- Slider main container -->
                                                                <div class="swiper">
                                                                    <!-- Additional required wrapper -->
                                                                    <div class="swiper-wrapper">
                                                                        @if($reports['report_type'] == 'News')

                                                                        <!-- screenshots Slides -->
                                                                        @foreach ($reports['screenshot'] as $screenshot)
                                                                        @if ($screenshot['screenshot'] != '')
                                                                        @php
                                                                        $type = $screenshot['screenshot_type'];
                                                                        $type = str_replace('_', ' ', $type);
                                                                        $type = ucwords($type) . ' Screenshot';

                                                                        $finalScreenshot = '';

                                                                        if ($screenshot['screenshot'] != '') {
                                                                        $finalScreenshot = Helper::getImageUrl($screenshot['screenshot']);
                                                                        } else {
                                                                        $finalScreenshot = $prefix . 'placeholder.png';
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
                                                                                <div
                                                                                    class="text-center">
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
                                                                        $type = ucwords('Featured Image');

                                                                        $finalsource = '';

                                                                        if ($featured_image['featured_image'] != '') {
                                                                        $finalsource = Helper::getImageUrl($featured_image['featured_image']);
                                                                        } else {
                                                                        $finalsource = $prefix.'placeholder.png';
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
                                                                                <div
                                                                                    class="text-center">
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
                                                                        $type = $document['document_type'];
                                                                        $type = str_replace('_', ' ', $type);
                                                                        $type = ucwords($type);

                                                                        if (!empty($document['document_name'])) {
                                                                        $type = $document['document_name'];
                                                                        }

                                                                        $source = $document['document'];
                                                                        $extension = "";

                                                                        $finalsource = '';

                                                                        if($document['document'] != ""){
                                                                        $finalsource = Helper::getImageUrl($document['document']);
                                                                        }else{
                                                                        $finalsource = $prefix.'placeholder.png';
                                                                        }

                                                                        $displayIcon = $prefix.'placeholder.png';
                                                                        if($extension == "pdf"){
                                                                        $displayIcon = $prefix.'placeholder_pdf.png';
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
                                                                                href="{{ $prefix.$finalsource }}"
                                                                                target="_blank"
                                                                                download="{{ $document['document_type'] }}{{ $downloadHeading }}">
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
                                                                        @endif
                                                                        @endforeach
                                                                        @else
                                                                        @php
                                                                        //reportmedias
                                                                        $type = "Social Media Image";
                                                                        $finalScreenshot = Helper::getImageUrl($reports['image']);
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
                                                                        @endif

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
                                                                        <a href="{{ $reports['link'] }}"
                                                                            target="_blank">{{ $reports['serial_number'] }}.
                                                                            {{ ucfirst($reports['heading']) }}</a>
                                                                    </h1>
                                                                    <div class="postcard__subtitle fw-bold">
                                                                        <i class="fas fa-folder mb-2 mr-2"></i>
                                                                        {{ ucfirst($reports['module']['name'] ?? '') }}
                                                                        @if(!empty($reports['module']['name']) && !empty($reports['chapter']['name']))
                                                                        ->
                                                                        @endif
                                                                        {{ ucfirst($reports['chapter']['name'] ?? '') }}
                                                                    </div>
                                                                    <div class="postcard__subtitle ">
                                                                        <time datetime="2020-05-25 12:00:00">
                                                                            <i class="fas fa-calendar-alt mr-2"></i>
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
                                                                            <i class="fas fa-link mr-2"></i>
                                                                            Related News {{ $key + 1 }}
                                                                        </a> |
                                                                        @endforeach
                                                                        @endif

                                                                        @if (!empty($reports['document']) && count($reports['document']) > 0)

                                                                        @php
                                                                        $firLink = null;
                                                                        $firImage = null;
                                                                        $docLink = null;
                                                                        $docImage = null;

                                                                        foreach ($reports['document'] as $doc) {

                                                                        // FIR
                                                                        if ($doc['document_type'] === 'fir_copy') {
                                                                        if (!empty($doc['document_link']) && !$firLink) {
                                                                        $firLink = str_starts_with($doc['document_link'], 'http')
                                                                        ? $doc['document_link']
                                                                        : 'https://' . $doc['document_link'];
                                                                        }

                                                                        if (!empty($doc['document']) && !$firImage) {
                                                                        $firImage = asset($doc['document']);
                                                                        }
                                                                        }

                                                                        // GENERAL DOCUMENT
                                                                        if ($doc['document_type'] === 'general') {
                                                                        if (!empty($doc['document_link']) && !$docLink) {
                                                                        $docLink = str_starts_with($doc['document_link'], 'http')
                                                                        ? $doc['document_link']
                                                                        : 'https://' . $doc['document_link'];
                                                                        }

                                                                        if (!empty($doc['document']) && !$docImage) {
                                                                        $docImage = asset($doc['document']);
                                                                        }
                                                                        }
                                                                        }
                                                                        @endphp

                                                                        <div class="d-inline-flex align-items-center gap-2 flex-wrap">

                                                                            {{-- FIR LINK --}}


                                                                            @if ($firLink)

                                                                            <a href="{{ $firLink }}" target="_blank">
                                                                                <i class="fas fa-link mr-1"></i> FIR Link
                                                                            </a>
                                                                            <span>|</span>
                                                                            @endif

                                                                            {{-- FIR IMAGE --}}
                                                                            @if ($firImage)
                                                                            <a href="{{ $firImage }}" target="_blank">
                                                                                <i class="fas fa-image mr-1"></i> FIR Image
                                                                            </a>
                                                                            <span>|</span>
                                                                            @endif

                                                                            {{-- DOCUMENT LINK --}}
                                                                            @if ($docLink)
                                                                            <a href="{{ $docLink }}" target="_blank">
                                                                                <i class="fas fa-link mr-1"></i> Document Link
                                                                            </a>
                                                                            <span>|</span>
                                                                            @endif

                                                                            {{-- DOCUMENT IMAGE --}}
                                                                            @if ($docImage)
                                                                            <a href="{{ $docImage }}" target="_blank">
                                                                                <i class="fas fa-image mr-1"></i> Document Image
                                                                            </a>
                                                                            @endif

                                                                        </div>

                                                                        @endif
                                                                        |

                                                                        @if (!empty($reports['videolink']) && count($reports['videolink']) > 0)

                                                                        @foreach ($reports['videolink'] as $key => $doc)
                                                                        <a href="{{ asset($doc['videolink']) }}" target="_blank">
                                                                            <i class="fas fa-file mr-2"></i>
                                                                            Video {{ $key + 1 }}
                                                                        </a> |
                                                                        @endforeach
                                                                        @endif

                                                                        {{-- IMAGE SOURCE LINKS (AFTER VIDEOS, NOT INSIDE VIDEO LOOP) --}}
                                                                        @if (!empty($reports['imagelink']) && count($reports['imagelink']) > 0)
                                                                        @foreach ($reports['imagelink'] as $key => $image)
                                                                        @if (!empty($image['imagelink']))
                                                                        @php
                                                                        $imageUrl = str_starts_with($image['imagelink'], 'http')
                                                                        ? $image['imagelink']
                                                                        : 'https://' . $image['imagelink'];
                                                                        @endphp

                                                                        <a href="{{ $imageUrl }}" target="_blank">
                                                                            <i class="fas fa-image mr-2"></i>
                                                                            Image Source {{ $key + 1 }}
                                                                        </a>
                                                                        |
                                                                        @endif
                                                                        @endforeach
                                                                        @endif





                                                                    </div>

                                                                    <div class="postcard__bar {{ $reports['report_type'] == 'News' ? 'postcard__bar_red' : 'postcard__bar_blue' }}"></div>

                                                                    <div class="postcard__preview-txt">
                                                                        <ol>
                                                                            @foreach ($reports['keypoint'] as $key => $keypoint)
                                                                            <li>{{ trim($keypoint['keypoint']) }}</li>
                                                                            @endforeach
                                                                        </ol>
                                                                    </div>

                                                                    @if (empty($find_more))
                                                                    <p style="padding-top:10px;">
                                                                        <span class="causale" style="margin:10px;color:#000; font-weight: 600; text-align:center;">
                                                                            <a href="javascript:void(0);" onclick="findSimilar({{ $reports['team_id'] }},{{ $reports['module_id'] }},{{ $reports['chapter_id'] }});" style="color:#000;"
                                                                                target="_blank">
                                                                                <i class="fas fa-file mr-2 fsicon"></i> Click here to find <span class="text-info fs-4 fw-bold">{{ $chapterReportData[$reports['chapter_id']] ?? '' }}</span> similar incidents
                                                                                of

                                                                                @if (!empty($reports['module']['name']))
                                                                                &nbsp;{{ ucfirst($reports['module']['name']) }}
                                                                                @endif

                                                                                @if (!empty($reports['chapter']['name']))
                                                                                &nbsp;->&nbsp;{{ ucfirst($reports['chapter']['name']) }}
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

                                                                        $keypointDescription = array();
                                                                        foreach ($reports['keypoint'] as $key => $keypoint){
                                                                        $keypointDescription[]=trim($keypoint['keypoint']);
                                                                        }

                                                                        $socialShareContent = ucfirst($reports['heading']);
                                                                        $socialShareContent .= $seperator.$publish_at;
                                                                        $socialShareContent .= $seperator.$reports['location_name'];
                                                                        $socialShareContent .= $seperator.strip_tags(implode("\r\n", $keypointDescription));
                                                                        /* $socialShareContent .= $seperator.$finalScreenshot; */

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

                                                    @if($showEndReport == true && $mode == "view")
                                                    <div class="row">
                                                        <div class="col-md-12 col-xl-12 text-center mt-5">
                                                            <div class="btn btn-{{ config('app.color_scheme') }}">
                                                                Report End
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($keyNews && $mode == "view")
                                                    {!!$paginationLinks!!}
                                                    @endif
                                                </div>
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>

                            @if($mode != 'view-all')
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    @if($selectedDate)
                                    <div class="row">
                                        <div class="col-md-12 col-xl-12 text-left mt-5">
                                            <h5>
                                                Other Issues
                                            </h5>
                                            <div id="sub-category-btn-group">

                                                {{-- daily Popup     --}}
                                                @php
                                                $app_domain = config('app.app_domain').$website_slug."/";

                                                $selectedDateArr = explode("-", $selectedDate);
                                                $monitoringDate = $selectedDateArr[2]."-".$selectedDateArr[1]."-".$selectedDateArr[0];
                                                $monitoringYear = $selectedDateArr[2];


                                                $dailyPopupDateArr = explode("-", $dailyPopupDate);
                                                $monitoringdailyPopupDate = $dailyPopupDateArr[2]."-".$dailyPopupDateArr[1]."-".$dailyPopupDateArr[0];
                                                $monitoringdailyPopupDateYear = $dailyPopupDateArr[2];


                                                if(empty($monitoringdailyPopupDateYear)){
                                                $monitoringdailyPopupDateYear = date("Y");
                                                }

                                                $viewDailyPopupUrl = $app_domain."?date=".$monitoringdailyPopupDate."&type=&eventYear=".$monitoringdailyPopupDateYear."&keyword=";
                                                @endphp
                                                <a href="{{$viewDailyPopupUrl}}" class="find_more">
                                                    <label
                                                        class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">Today in History {{$dailyPopupDate}} ({{$all_count_popup}} Reports)</label>
                                                </a>

                                                {{-- Social Media Monitoring --}}
                                                @php
                                                $app_domain = config('app.app_domain').$website_slug."/";

                                                $selectedDateArr = explode("-", $selectedDate);
                                                // $monitoringDate = $selectedDate;
                                                $monitoringDate = $selectedDateArr[0]."-".$selectedDateArr[1]."-".$selectedDateArr[2];
                                                $monitoringYear = $selectedDateArr[2];

                                                if(empty($monitoringYear)){
                                                $monitoringYear = date("Y");
                                                }

                                                // $viewDailyPopupUrl = $app_domain."?date=".$monitoringDate."&type=sm_cal&eventYear=".$monitoringYear."&keyword=";
                                                $viewDailyPopupUrl = $app_domain."sm-daily-monitoring-report/".$monitoringDate;
                                                @endphp
                                                <a href="{{$viewDailyPopupUrl}}" target="_blank" class="find_more">
                                                    <label
                                                        class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">Social Media ({{$all_sm_cals['0']->totalReports}} Reports)</label>
                                                </a>




                                                @foreach ($all_issues as $key => $issue)
                                                @if($issue->id != $team_name)
                                                @php
                                                $app_domain_info = config('app.app_domain_info');
                                                $app_domain_issue = config('app.app_domain_issue');
                                                $app_domain = config('app.app_domain').$website_slug."/dmr-all-issues/";
                                                $domain = $app_domain;
                                                $monitoringDate = $selectedDate;
                                                /* $viewReportUrl = $domain.config('app.view_report_url');
                                                $viewReportUrl = str_replace("##ISSUE_ID##",$issue->id,$viewReportUrl);
                                                $viewReportUrl = str_replace("##START_DATE##",$monitoringDate,$viewReportUrl);
                                                $viewReportUrl = str_replace("##END_DATE##",$monitoringDate,$viewReportUrl); */

                                                $viewReportUrl = $domain.$issue->slug."/".$monitoringDate;
                                                @endphp
                                                <a href="{{$viewReportUrl}}" class="find_more">
                                                    <label
                                                        class="border border-primary btn text-white btn-rounded me-2 mb-3 text-nowrap"
                                                        style="background: linear-gradient(40deg,#45cafc,#303f9f) !important;">
                                                        {{ ucwords(strtolower($issue->name)) }} ({{$issue->totalReports}} Reports)
                                                    </label>
                                                </a>
                                                @endif
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>



                </div>

            </div>

        </div>
        @endif

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
    @if($mode == "view")
    $('ul.pagination').hide();
    $(function() {
        $('.scrolling-pagination').jscroll({
            loadingHtml: '<center><img src="{{ asset('
            public / images / loading.svg ') }}" alt="Loading" /> <b>Loading...</b></center> ',
            autoTrigger: true,
            padding: 20,
            nextSelector: '.pagination li.active + li a',
            /* contentSelector: 'div.scrolling-pagination', */
            contentSelector: 'div.report-container',
            callback: function() {
                $('ul.pagination').remove();
            }
        });
    });
    @endif

    @if($mode == "view-all" && $currentIssue == "rising-bharat")
    document.addEventListener('DOMContentLoaded', function() {
        const teamName = "{{$issueFromUrl}}";
        console.log("teamName", teamName);
        if (teamName) {
            const element = document.getElementById(teamName);
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
    });
    @endif

    var swiper = "";

    function findSimilar(team_id, module_id, chapter_id) {
        $('#find_more_team_id').val(team_id);
        $('#find_more_module_id').val(module_id);
        $('#find_more_chapter_id').val(chapter_id);

        $("#find_more_downloadForm").submit();
    }

    $(document).ready(function() {


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

            showDialog('Sharable Content Copied : Heading, Date, Location, Description & Link.', '', 'success');
        });

        $('#overlay').hide();
        $('#page_content').show();
        $('.dropdown-toggle').dropdown();

        $('#word').click(function() {
            $('#download_report_type').val('word');
            $("#downloadForm").submit();
        });
        $('#pdf').click(function() {
            $('#download_report_type').val('pdf');
            $("#downloadForm").submit();
        });
        $('#googledoc').click(function() {
            $('#download_report_type').val('googledoc');
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