@extends($viewLayout)
@section('title',$reportTitle)

@section('content')

    @include('report_public_css')
    <style>
        @mobile
            .swiper {
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

        <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off" id="find_more_downloadForm"
              target="_blank">
            <input type="hidden" name="team_name[]" id="find_more_team_id" value="">
            <input type="hidden" name="module[]" id="find_more_module_id" value="">
            <input type="hidden" name="chapter_id[]" id="find_more_chapter_id" value="">
            <input type="hidden" name="find_more" id="find_more" value="yes">
            @php
                foreach ($_GET as $name => $value) {
                    if ($name !== 'team_name[]' && $name !== 'module[]' && $name !== 'chapter[]' && $name != 'from_date' && $name != 'to_date' && $name != 'from_user' && $name != 'report_from') {
                        if (is_array($value)) {
                            // ignore arrays here intentionally
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

            <div class="row">
                <div class="col-md-4"></div>

                <div class="col-md-4 fixed-button-share">
                    <button type="button" id="share_report" class="btn btn-{{ config('app.color_scheme') }}">
                        Share Report <i class="mdi mdi-share"></i>
                    </button>
                </div>

                <div class="col-md-4 fixed-button">
                    <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off" id="downloadForm">
                        @php
                            foreach ($_GET as $name => $value) {
                                if ($name !== 'view_reports') {
                                    if (is_array($value)) {
                                        foreach ($value as $key => $keyValue) {
                                            $name = htmlspecialchars($name);
                                            $keyValue = htmlspecialchars($keyValue);
                                            echo '<input type="hidden" name="' . $name . '[]" value="' . $keyValue . '">';
                                        }
                                    } else {
                                        $name = htmlspecialchars($name);
                                        $value = htmlspecialchars($value);
                                        echo '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '">';
                                    }
                                }
                            }
                        @endphp

                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button"
                                        class="btn btn-{{ config('app.color_scheme') }} dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    Download Report <i class="mdi mdi-chevron-down"></i>
                                </button>

                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" href="#word"><i class="mdi mdi-download"></i> Download Word Report</a>
                                    <a class="dropdown-item" href="#pdf"><i class="mdi mdi-download"></i> Download Pdf Report</a>
                                    <a class="dropdown-item" href="#googledoc"><i class="mdi mdi-download"></i> Download Google Doc Report</a>
                                    <a class="dropdown-item" href="#excel"><i class="mdi mdi-download"></i> Download Excel Report</a>
                                    @if(isset($tagArr))
                                        <a class="dropdown-item" href="#pdf_desc"><i class="mdi mdi-download"></i> Download PDF Description.</a>
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
                                <div class="row">

                                    {{-- reportTitle --}}
                                    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align: center;">
                                        <tbody>
                                        <tr>
                                            <td style="text-align: center;font-size: 15px;font-weight:bold">
                                                {{ $reportTitle }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    {{-- INDEX --}}
                                    <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="100%" style="border: 1px solid black;">
                                        <tbody>
                                        <tr>
                                            <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="10%">#</td>
                                            <td style="background-color: #FFC000; color: #000000; text-align: center;" width="70%">Topic</td>
                                            <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="20%">News Count ({{ $repcount }})</td>
                                        </tr>

                                        @if ($report_data_type == 'base_report')
                                            @foreach ($dataModule as $key => $datas)
                                                <tr>
                                                    <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $key + 1 }}</td>
                                                    <td style="border: 1px solid #000000; text-align: left;" width="70%" align="left">
                                                        {{ $datas['module']['name'] }}
                                                    </td>
                                                    <td style="border: 1px solid #000000; text-align: center;" width="20%">{{ $datas['module_count'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($data as $key => $datas)
                                                <tr>
                                                    <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $key + 1 }}</td>
                                                    <td style="border: 1px solid #000000; text-align: left;" width="70%" align="left">
                                                        @if (isset($datas['chapter']['id']))
                                                            {{ $moduleNameArr[$datas['chapter']['id']] }} -> {{ $datas['chapter']['name'] }}
                                                        @endif
                                                    </td>
                                                    <td style="border: 1px solid #000000; text-align: center;" width="20%">{{ $datas['chapter_count'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                    @php
                                        $showEndReport = false;
                                        $serial = (method_exists($keyNews, 'currentPage') && method_exists($keyNews, 'perPage'))
                                            ? (($keyNews->currentPage() - 1) * $keyNews->perPage()) + 1
                                            : 1;
                                    @endphp

                                    <div class="col-md-12 col-xl-12">
                                        <section class="light">
                                            <div class="container-fluid py-2 mt-3">
                                                <div class="scrolling-pagination">

                                                    @foreach ($keyNews as $key => $reports)
                                                        @php
                                                            // $reports is App\Models\Report (Eloquent model) as per your dd()

                                                            if($keyNews->hasMorePages() == ""){
                                                                $showEndReport = true;
                                                            }

                                                            $websiteId = data_get($reports, 'website_id');
                                                            if ($websiteId == '1') {
                                                                $prefix = config('app.app_domain_info').'public/';
                                                            } else {
                                                                $prefix = config('app.app_domain_issue').'public/';
                                                            }

                                                            $defaultPlaceholder = config('app.url') . 'public/'. 'placeholder.png';
                                                            $publish_at = \Carbon\Carbon::createFromTimestamp(strtotime(data_get($reports, 'publish_at')))->format('M d, Y');
                                                            $downloadHeading = '_' . str_replace(' ', '_', strtolower((string) data_get($reports,'heading','')));

                                                            // Relations (always safe)
                                                            $videoLinks = collect(data_get($reports, 'videolink', []));
                                                            $imageLinks = collect(data_get($reports, 'imagelink', []));
                                                            $followups  = collect(data_get($reports, 'followup', []));
                                                            $documents  = collect(data_get($reports, 'document', []));

                                                            // FIR vs GENERAL docs (based on document_type)
                                                            $firDocs = $documents->filter(function($d){
                                                                $t = strtolower((string) data_get($d,'document_type',''));
                                                                return str_contains($t, 'fir');
                                                            })->values();

                                                            $genDocs = $documents->reject(function($d){
                                                                $t = strtolower((string) data_get($d,'document_type',''));
                                                                return str_contains($t, 'fir');
                                                            })->values();

                                                            // URL normalizer
                                                            $withScheme = function ($url) {
                                                                $url = trim((string)$url);
                                                                if ($url === '') return '';
                                                                if (!\Illuminate\Support\Str::startsWith($url, ['http://','https://'])) return 'https://'.$url;
                                                                return $url;
                                                            };

                                                            $reportType = data_get($reports,'report_type');
                                                        @endphp

                                                        <article class="postcard light {{ $publicReportColor }}">
                                                            <div class="row" style="overflow: scroll; width:100%;">
                                                                <div @mobile class="col-12" @else class="col-6" @endmobile>

                                                                    <div class="swiper">
                                                                        <div class="swiper-wrapper">

                                                                            @if($reportType == 'News')

                                                                                {{-- screenshots --}}
                                                                                @foreach (collect(data_get($reports,'screenshot',[])) as $screenshot)
                                                                                    @if (!empty(data_get($screenshot,'screenshot')))
                                                                                        @php
                                                                                            $type = ucwords(str_replace('_', ' ', (string) data_get($screenshot,'screenshot_type'))) . ' Screenshot';
                                                                                            $finalScreenshot = Helper::getImageUrl(data_get($screenshot,'screenshot'));
                                                                                        @endphp

                                                                                        <div class="swiper-slide">
                                                                                            <a href="javascript:void(0);" class="pop">
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
                                                                                               download="{{ data_get($screenshot,'screenshot_type') }}{{ $downloadHeading }}">
                                                                                                <i class="fas fa-download"></i>
                                                                                            </a>

                                                                                            <div class="container py-5" style="bottom: -55px; position: absolute;">
                                                                                                <div class="text-center">
                                                                                                    <p class="btn btn-{{ config('app.color_scheme') }}">{{ $type }}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach

                                                                                {{-- featured image --}}
                                                                                @foreach (collect(data_get($reports,'feateredimage',[])) as $featured_image)
                                                                                    @if (!empty(data_get($featured_image,'featured_image')))
                                                                                        @php
                                                                                            $type = 'Featured Image';
                                                                                            $finalsource = Helper::getImageUrl(data_get($featured_image,'featured_image'));
                                                                                        @endphp

                                                                                        <div class="swiper-slide">
                                                                                            <a href="javascript:void(0);" class="pop">
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
                                                                                                <i class="fas fa-download"></i>
                                                                                            </a>

                                                                                            <div class="container py-5" style="bottom: -55px; position: absolute;">
                                                                                                <div class="text-center">
                                                                                                    <p class="btn btn-{{ config('app.color_scheme') }}">{{ $type }}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach

                                                                                {{-- documents (slides icons) --}}
                                                                                @foreach ($documents as $document)
                                                                                    @if (!empty(data_get($document,'document')))
                                                                                        @php
                                                                                            $type = ucwords(str_replace('_', ' ', (string) data_get($document,'document_type')));
                                                                                            if (!empty(data_get($document,'document_name'))) $type = data_get($document,'document_name');

                                                                                            $finalDocUrl = Helper::getImageUrl(data_get($document,'document'));
                                                                                            $displayIcon = $prefix.'placeholder.png';
                                                                                        @endphp

                                                                                        <div class="swiper-slide">
                                                                                            <a href="javascript:void(0);" class="pop">
                                                                                                <img class="postcard__img lazyload blur-up"
                                                                                                     src="{{ $defaultPlaceholder }}"
                                                                                                     data-sizes="auto"
                                                                                                     data-src="{{ $displayIcon }}"
                                                                                                     data-srcset="{{ $displayIcon }}"
                                                                                                     alt="{{ $type }}" />
                                                                                            </a>

                                                                                            <a class="downloadImage"
                                                                                               href="{{ $finalDocUrl }}"
                                                                                               target="_blank"
                                                                                               download="{{ data_get($document,'document_type') }}{{ $downloadHeading }}">
                                                                                                <i class="fas fa-download"></i>
                                                                                            </a>

                                                                                            <div class="container py-5" style="bottom: -55px; position: absolute;">
                                                                                                <div class="text-center">
                                                                                                    <p class="btn btn-{{ config('app.color_scheme') }}">{{ $type }}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach

                                                                            @else
                                                                                @php
                                                                                    $type = "Social Media Image";
                                                                                    $finalScreenshot = Helper::getImageUrl(data_get($reports,'image'));
                                                                                @endphp

                                                                                <div class="swiper-slide">
                                                                                    <a href="javascript:void(0);" class="pop">
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
                                                                                        <i class="fas fa-download"></i>
                                                                                    </a>

                                                                                    <div class="container py-5" style="bottom: -55px; position: absolute;">
                                                                                        <div class="text-center">
                                                                                            <p class="btn btn-{{ config('app.color_scheme') }}">{{ $type }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <div class="swiper-button-prev"></div>
                                                                        <div class="swiper-button-next"></div>
                                                                    </div>
                                                                </div>

                                                                <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                                    <div class="postcard__text t-dark">
                                                                        <h1 class="postcard__title {{ $publicReportColor }}">
                                                                            <a href="{{ data_get($reports,'link') }}" target="_blank">
                                                                                {{ $serial }}. {{ ucfirst((string) data_get($reports,'heading')) }}
                                                                            </a>
                                                                        </h1>
                                                                        @php $serial++; @endphp

                                                                        <div class="postcard__subtitle fw-bold">
                                                                            <i class="fas fa-folder mb-2 mr-2"></i>
                                                                            {{ ucfirst((string) data_get($reports,'module.name','')) }} -> {{ ucfirst((string) data_get($reports,'chapter.name','')) }}
                                                                        </div>

                                                                        {{-- ✅ ONE LINE: Date | Language | Location | Source | Video | Image | FIR | Docs | Followups --}}
                                                                        <div class="postcard__subtitle">
                                                                            <time datetime="2020-05-25 12:00:00">
                                                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                                                {{ $publish_at }}
                                                                            </time>

                                                                            &nbsp;
                                                                            <i class="fas fa-language mr-2 fsicon"></i>
                                                                            {{ ucwords((string) data_get($reports,'language_name','')) }}

                                                                            @if (data_get($reports,'location_name'))
                                                                                &nbsp; <i class="fas fa-location-arrow mr-2"></i>
                                                                                {{ data_get($reports,'location_name') }}
                                                                                @if (data_get($reports,'location_state_name'))
                                                                                    ({{ data_get($reports,'location_state_name') }})
                                                                                @endif
                                                                            @endif

                                                                            @if (data_get($reports,'link'))
                                                                                &nbsp; <span class="tag__item play {{ $publicReportColor }}">
                                                                                    <a href="{{ data_get($reports,'link') }}" target="_blank" rel="noopener">
                                                                                        <i class="fas fa-link mr-2"></i> {{ ucwords((string) data_get($reports,'source','')) }}
                                                                                    </a>
                                                                                </span>
                                                                            @endif

                                                                            {{-- ===================== INLINE EXTRA LINKS (SAME LINE) ===================== --}}
                                                                            @php
                                                                                $hasAnyExtra = $videoLinks->count() || $imageLinks->count() || $firDocs->count() || $genDocs->count() || $followups->count();
                                                                            @endphp

                                                                            @if($reportType == 'News' && $hasAnyExtra)
                                                                                &nbsp; | &nbsp;

                                                                                {{-- Video links --}}
                                                                                @if($videoLinks->count())
                                                                                    @foreach($videoLinks as $k => $v)
                                                                                        @php
                                                                                            $url = $withScheme(data_get($v,'videolink',''));
                                                                                        @endphp
                                                                                        @if($url)
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-video mr-2"></i> Video {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                            @if($k < $videoLinks->count() - 1) &nbsp; @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                    &nbsp; | &nbsp;
                                                                                @endif

                                                                                {{-- Image source links --}}
                                                                                @if($imageLinks->count())
                                                                                    @foreach($imageLinks as $k => $img)
                                                                                        @php
                                                                                            $url = $withScheme(data_get($img,'imagelink', data_get($img,'image_link','')));
                                                                                        @endphp
                                                                                        @if($url)
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-image mr-2"></i> Image Source {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                            @if($k < $imageLinks->count() - 1) &nbsp; @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                    &nbsp; | &nbsp;
                                                                                @endif

                                                                                {{-- FIR: Link + File (from report_documents) --}}
                                                                                @if($firDocs->count())
                                                                                    @foreach($firDocs as $k => $doc)
                                                                                        @php
                                                                                            $firLink = $withScheme(data_get($doc,'document_link',''));
                                                                                            $firFileRaw = data_get($doc,'document','');
                                                                                            $firFile = $firFileRaw
                                                                                                ? (\Illuminate\Support\Str::startsWith($firFileRaw, ['http://','https://']) ? $firFileRaw : Helper::getImageUrl($firFileRaw))
                                                                                                : '';
                                                                                        @endphp

                                                                                        @if($firLink)
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $firLink }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-link mr-2"></i> FIR Link {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                        @endif

                                                                                        @if($firFile)
                                                                                            &nbsp;
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $firFile }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-file mr-2"></i> FIR File {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                        @endif

                                                                                        @if($k < $firDocs->count() - 1) &nbsp; @endif
                                                                                    @endforeach
                                                                                    &nbsp; | &nbsp;
                                                                                @endif

                                                                                {{-- General Documents: Link + File --}}
                                                                                @if($genDocs->count())
                                                                                    @foreach($genDocs as $k => $doc)
                                                                                        @php
                                                                                            $docLink = $withScheme(data_get($doc,'document_link',''));
                                                                                            $docFileRaw = data_get($doc,'document','');
                                                                                            $docFile = $docFileRaw
                                                                                                ? (\Illuminate\Support\Str::startsWith($docFileRaw, ['http://','https://']) ? $docFileRaw : Helper::getImageUrl($docFileRaw))
                                                                                                : '';

                                                                                            $label ='Documents';
                                                                                            if(empty($label)) $label = ucwords(str_replace('_',' ', (string) data_get($doc,'document_type','Document')));
                                                                                        @endphp

                                                                                        @if($docLink)
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $docLink }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-link mr-2"></i> {{ $label }} Link {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                        @endif

                                                                                        @if($docFile)
                                                                                            &nbsp;
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $docFile }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-file mr-2"></i> {{ $label }} File {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                        @endif

                                                                                        @if($k < $genDocs->count() - 1) &nbsp; @endif
                                                                                    @endforeach
                                                                                    &nbsp; | &nbsp;
                                                                                @endif

                                                                                {{-- Followups --}}
                                                                                @if($followups->count())
                                                                                    @foreach($followups as $k => $fu)
                                                                                        @php
                                                                                            $fuUrl = $withScheme(data_get($fu,'followup_link', data_get($fu,'link', data_get($fu,'url',''))));
                                                                                        @endphp
                                                                                        @if($fuUrl)
                                                                                            <span class="tag__item play {{ $publicReportColor }}">
                                                                                                <a href="{{ $fuUrl }}" target="_blank" rel="noopener">
                                                                                                    <i class="fas fa-link mr-2"></i> Related News {{ $k + 1 }}
                                                                                                </a>
                                                                                            </span>
                                                                                            @if($k < $followups->count() - 1) &nbsp; @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                            @endif
                                                                        </div>

                                                                        <div class="postcard__bar {{ $reportType == 'News' ? 'postcard__bar_red' : 'postcard__bar_blue' }}"></div>

                                                                        <div class="postcard__preview-txt">
                                                                            @if($reportType == 'News')
                                                                                <ol>
                                                                                    @foreach (collect(data_get($reports,'keypoint',[])) as $keypoint)
                                                                                        <li>{{ trim((string) data_get($keypoint,'keypoint','')) }}</li>
                                                                                    @endforeach
                                                                                </ol>
                                                                            @else
                                                                                {!! trim((string) data_get($reports,'description','')) !!}
                                                                            @endif
                                                                        </div>

                                                                        @php
                                                                            $seperator = "\r\n \r\n";
                                                                            $keypointDescription = [];
                                                                            foreach (collect(data_get($reports,'keypoint',[])) as $kp){
                                                                                $keypointDescription[] = trim((string) data_get($kp,'keypoint',''));
                                                                            }

                                                                            $socialShareContent = ucfirst((string) data_get($reports,'heading',''));
                                                                            $socialShareContent .= $seperator.$publish_at;
                                                                            $socialShareContent .= $seperator.(string) data_get($reports,'location_name','');
                                                                            $socialShareContent .= $seperator.(string) data_get($reports,'link','');
                                                                            $socialShareContent .= $seperator.strip_tags(implode("\r\n", $keypointDescription));
                                                                        @endphp

                                                                        <div style="text-align: right;">
                                                                            <div class="copyBtn" data-content="{{ $socialShareContent }}">
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

                                                    @if($showEndReport == true)
                                                        <div class="row">
                                                            <div class="col-md-12 col-xl-12 text-center mt-5">
                                                                <div class="btn btn-{{ config('app.color_scheme') }}">
                                                                    Report End
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($keyNews)
                                                        {{ $keyNews->links() }}
                                                    @endif
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
    </div>

    {{-- Image preview modal --}}
    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Preview</h4>
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

    <script type="text/javascript">
        // ✅ Init all swipers (works for multiple cards + jscroll)
        function initSwipers() {
            document.querySelectorAll('.swiper').forEach(function (el) {
                if (el.classList.contains('swiper-initialized')) return;

                new Swiper(el, {
                    speed: 400,
                    slidesPerView: 1,
                    loop: false,
                    centeredSlides: true,
                    spaceBetween: 20,
                    zoom: { minRatio: 1, maxRatio: 5 },
                    navigation: {
                        nextEl: el.querySelector('.swiper-button-next'),
                        prevEl: el.querySelector('.swiper-button-prev'),
                    },
                });
            });
        }

        // ✅ jscroll setup
        $('ul.pagination').hide();
        $(function() {
            $('.scrolling-pagination').jscroll({
                loadingHtml: '<center><img src="{{ asset('public/images/loading.svg') }}" alt="Loading" /> <b>Loading...</b></center>',
                autoTrigger: true,
                padding: 20,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scrolling-pagination',
                callback: function() {
                    $('ul.pagination').remove();
                    initSwipers(); // ✅ re-init after new items loaded
                }
            });
        });

        function loadOtherSubCategory(chapter_id) {
            $('#find_more_team_id').val('{{ implode(",",$team_name) }}');
            $('#find_more_module_id').val('{{ implode(",",$module_ids_arr) }}');
            $('#find_more_chapter_id').val(chapter_id);
            $("#find_more_downloadForm").submit();
        }

        function findSimilar(team_id, module_id, chapter_id) {
            $('#find_more_team_id').val(team_id);
            $('#find_more_module_id').val(module_id);
            $('#find_more_chapter_id').val(chapter_id);
            $("#find_more_downloadForm").submit();
        }

        $(document).ready(function() {

            $('#overlay').hide();
            $('#page_content').show();
            $('.dropdown-toggle').dropdown();

            // ✅ Delegated click: works for dynamically loaded content (jscroll)
            $(document).on('click', '.copyBtn', function() {
                var textToCopy = $(this).data("content");
                $('#socialShareContent').val(textToCopy);

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy);
                } else {
                    $('#socialShareContent').focus();
                    $('#socialShareContent').select();
                    document.execCommand('copy');
                }

                if (typeof showDialog === "function") {
                    showDialog('Sharable Content Copied : Heading, Date, Location, Link & Keypoints.', '', 'success');
                } else {
                    alert('Sharable Content Copied.');
                }
            });

            // ✅ Delegated pop image preview
            $(document).on('click', '.pop', function() {
                var src = $(this).find('img').attr('data-src') || $(this).find('img').attr('src');
                $('#imagepreview').attr('src', src);
                $('#imagemodal').modal('show');
            });

            // Download type handlers
            $(document).on('click','a[href="#word"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('word'); $("#downloadForm").submit(); });
            $(document).on('click','a[href="#pdf"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('pdf'); $("#downloadForm").submit(); });
            $(document).on('click','a[href="#googledoc"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('googledoc'); $("#downloadForm").submit(); });
            $(document).on('click','a[href="#excel"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('excel'); $("#downloadForm").submit(); });
            $(document).on('click','a[href="#pdf_desc"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('pdf_desc'); $("#downloadForm").submit(); });
            $(document).on('click','a[href="#lovezihad"]', function(e){ e.preventDefault(); $('#downloadForm #report_type').val('lovezihad'); $("#downloadForm").submit(); });

            // ✅ Init sliders on first load
            initSwipers();
        });
    </script>
@endsection
