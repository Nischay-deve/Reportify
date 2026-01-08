<link href="{{ asset('css/timeline.css') }}" rel="stylesheet" />
@include('report_public_css')

<style>
    @mobile
        .swiper {        
            height: 100%;
            max-height: 550px;
        }
    @else
        .swiper {
            width: 500px;
            height: 100%;
            max-height: 550px;
        }
    @endmobile    

    .swiper-button-next, .swiper-button-prev {
        top: 50% !important;
    }

    .modal-footer{
        position: relative;
        bottom: 50px;
        right: 15px;
    }    

    
    #loadMoreButton{
        z-index: 100;
        position: relative;
    }
</style>  


@php
$placeholderPrefix = config("app.app_domain")."public/";
@endphp


<div class="section" id="section{{$sectionCnt}}">
    <h2 class="sticky-header">History of - {{$selectedDate}}   </h2>

    <a id="history-report"></a>
    <div class="container-fluid">

    <input type="text" name="socialShareContent" id="socialShareContent" value="" style="position:fixed;left:-999999px;top:-999999px;">                                        
    {{--
    <div class="text-center">
        <h4>
            History of - {{$selectedDate}}   
        </h4>
    </div>
    --}}

    {{-- all_items --}}
        <div class="row">
            @if (count($all_items) > 0)
                <div id="timeline">

                    @php
                        $loadingForYearInLoop = '';
                    @endphp


                    @foreach ($all_items as $key => $item)

                        @php
                            $dateTime = DateTime::createFromFormat('Y-m-d', $item->date);
                            $loadingForYear = $dateTime->format('Y');
                            $displayItemDate = $dateTime->format('M d, Y');
                        @endphp

                        @if ($loadingForYearInLoop != $loadingForYear)
                            @php
                                $loadingForYearInLoop = $loadingForYear;
                            @endphp
                            <div class="row timeline-movement timeline-movement-top" id="{{ $loadingForYear }}">
                                <div class="timeline-badge timeline-future-movement">
                                    <p>{{ $loadingForYear }}</p>
                                </div>
                            </div>
                        @endif

                        @if($item->type == "sm_cal")
                            {{-- SM CAL AREA --}}
                            <div class="row timeline-movement">                       
                                <div
                                    class="timeline-item">
                                    <div class="row">
                                        <div
                                            class="timeline-panel anim animate animated">
                                            <ul class="timeline-panel-ul">
                                                <div class="row" style="height:100%; width: 100%;">
                                                    <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                        <div class="lefting-wrap mb-3 text-center">
                                                            <li class="img-wraping mb-3 text-center">

                                                                @if ($item->sm_calendars_image)
                                                                    @php
                                                                        $hashtag = $item->sm_calendars_hashtag;
                                                                        $finalScreenshot = Helper::getImageUrl($item->sm_calendars_image);
                                                                    @endphp

                                                                    @if(!empty($item->sm_calendars_link))
                                                                        <a href="{{ $item->sm_calendars_link }}" target="_blank">
                                                                            <img class="img-thumbnail @mobile max-image-height-mobile @else max-image-height-desktop @endif text-center" src="{{ $finalScreenshot }}"
                                                                                alt="{{ $item->sm_calendars_hashtag }}" />
                                                                        </a>         
                                                                    @else
                                                                        <img class="img-thumbnail @mobile max-image-height-mobile @else max-image-height-desktop @endif text-center" src="{{ $finalScreenshot }}" alt="{{ $item->sm_calendars_hashtag }}" />
                                                                    @endif                                                   
                                                                @else
                                                                    @php
                                                                        //$finalScreenshot = URL::to('public/placeholder.png');
                                                                    @endphp

                                                                    @if($item->sm_calendars_team_id)    
                                                                        @if(file_exists(URL::to('public/images/teams/' . $item->sm_calendars_team_id.'.jpg')))
                                                                            <img class="img-thumbnail @mobile max-image-height-mobile @else max-image-height-desktop @endif" src="{{ URL::to('public/images/teams/' . $item->sm_calendars_team_id.'.jpg') }}" />
                                                                        @else
                                                                            <img class="img-thumbnail @mobile max-image-height-mobile @else max-image-height-desktop @endif" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                                                                        @endif
                                                                    @else
                                                                        <img class="img-thumbnail @mobile max-image-height-mobile @else max-image-height-desktop @endif" src="{{ URL::to('public/images/teams/0.jpg') }}" />
                                                                    @endif
                                                                @endif
                                                                
                                                            </li>
                                                        </div>
                                                    </div>    
                                                    <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                        <div class="righting-wrap">
                                                            <li class="content-text-left  mb-3">
                                                                <h3>
                                                                @if(!empty($item->sm_calendars_link))
                                                                    <a href="{{ $item->sm_calendars_link }}" target="_blank" class="importo">{{ ucfirst($item->sm_calendars_hashtag) }}</a>
                                                                @else
                                                                    {{ ucfirst($item->sm_calendars_hashtag) }}
                                                                @endif
                                                                </h3>
                                                            </li>
                                                            <li class="content-text-justify mb-3">

                                                                @if (!empty($item->team_name))
                                                                    <i class="fas fa-flag mr-2 fsicon"></i>&nbsp;{{ $item->team_name }}
                                                                @endif
                        
                                                                @if (!empty($item->module_name))
                                                                    &nbsp;->&nbsp;{{ $item->module_name }}
                                                                @endif
                        
                                                                @if (!empty($item->chapter_name))
                                                                    &nbsp;->&nbsp;{{ $item->chapter_name }} &nbsp;
                                                                @endif                                                            

                                                                <a href="{{ $item->sm_calendars_link }}" target="_blank" style="color: #666;">
                                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                                    <span class="d-none d-md-inline-block">
                                                                        {{ $displayItemDate }}
                                                                    </span>
                                                                </a>  
                                                            </li>
                                                            <li class="content-text-justify mb-3">
                                                                <span class="causale storyDetailText">{!! $item->sm_calendars_description !!}</span> 
                                                            </li>
                                                            <li class="content-text-left">
                                                                <div class="row">                                                                   
                                                                    <div class="col-12 text-end">                                                                                                                                        
                                                                        @php
                                                                            $seperator = "\r\n";
                                                                            /* $seperator = "<br><br>"; */
                                                                            $socialShareContent = ucfirst($item->sm_calendars_hashtag);
                                                                            $socialShareContent .= $seperator.$displayItemDate;

                                                                            // First, decode all HTML entities to their applicable characters
                                                                            $decodedContent = html_entity_decode($item->sm_calendars_description, ENT_QUOTES | ENT_HTML5);

                                                                            $socialShareContent .= $seperator.strip_tags($decodedContent);
                                                                            $socialShareContent .= $seperator.strip_tags($item->sm_calendars_link);

                                                                            /* echo $socialShareContent; */
                                                                        @endphp
                                                                    
                                                                        <div class="copyBtn" data-content="{{$socialShareContent}}">
                                                                            <button type="button" class="btn btn-{{ config('app.color_scheme') }} copyBtnText">
                                                                                <i class="fas fa-share" style="font-size: 18px; margin-right:5px;"></i> Share
                                                                            </button>
                                                                        </div>                                                                    

                                                                    </div>
                                                                </div>  
                                                            </li>
                                                            
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="clear"></div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($item->type == "incidence_cal")
                            {{-- INCIDENCE AREA --}}
                            @php
                                /* dump($item); */
                                $dateTime = DateTime::createFromFormat('Y-m-d', $item->report_publish_at);                       
                                $loadingForYear = $dateTime->format('Y');
                                $displayItemDate = $dateTime->format('jS \o\f F Y');
                                $publish_at = \Carbon\Carbon::createFromTimestamp(strtotime($item->report_publish_at))->format('M d, Y');
                                $publicReportColor="blue";                            

                                $prefix = config('app.app_domain_issue').'public/';
                                $keypoints = DB::connection('setfacts')->select("select report_keypoints.* from report_keypoints where report_keypoints.report_id = ".$item->incidence_calendars_report_id." order by report_keypoints.id ASC");
                                $tags = DB::connection('setfacts')->select("select report_tags.* from report_tags where report_tags.report_id = ".$item->incidence_calendars_report_id." order by report_tags.id ASC");
                                $videos = DB::connection('setfacts')->select("select report_videolinks.* from report_videolinks where report_videolinks.report_id = ".$item->incidence_calendars_report_id." order by report_videolinks.id ASC");
                                $images = DB::connection('setfacts')->select("select report_imagelinks.* from report_imagelinks where report_imagelinks.report_id = ".$item->incidence_calendars_report_id." order by report_imagelinks.id ASC");
                                $screenshots = DB::connection('setfacts')->select("select report_screenshots.* from report_screenshots where report_screenshots.report_id = ".$item->incidence_calendars_report_id." order by report_screenshots.id ASC");
                                $featuredimages = DB::connection('setfacts')->select("select report_featuredimages.* from report_featuredimages where report_featuredimages.report_id = ".$item->incidence_calendars_report_id." order by report_featuredimages.id ASC");
                                $documents = DB::connection('setfacts')->select("select report_documents.* from report_documents where report_documents.report_id = ".$item->incidence_calendars_report_id." order by report_documents.id ASC");
                                $followups = DB::connection('setfacts')->select("select report_followups.* from report_followups where report_followups.report_id = ".$item->incidence_calendars_report_id." order by report_followups.id ASC");

                                $app_domain_issue = config('app.app_domain');
                                $viewSimilarReportUrl = $app_domain_issue.$website_slug."/".config('app.view_similar_report_url');
                                $viewSimilarReportUrl = str_replace("##ISSUE_ID##",$item->report_team_id,$viewSimilarReportUrl);
                                $viewSimilarReportUrl = str_replace("##MODULE_ID##",$item->report_module_id,$viewSimilarReportUrl);
                                $viewSimilarReportUrl = str_replace("##CHAPTER_ID##",$item->report_chapter_id,$viewSimilarReportUrl);                                                

                            @endphp   

                            <div class="row timeline-movement">                       
                                <div
                                    class="timeline-item">
                                    <div class="row">
                                        <div
                                            class="timeline-panel anim animate animated">
                                            <ul class="timeline-panel-ul">
                                                <div class="row" style="height:100%; width: 100%;">
                                                    <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                        <div class="lefting-wrap mb-3 text-center">

                                                            <li class="img-wraping mb-3 text-center" >

                                                                <!-- Slider main container -->
                                                                <div class="swiper">
                                                                    <!-- Additional required wrapper -->
                                                                    <div class="swiper-wrapper">
                                                                            <!-- screenshots Slides -->
                                                                            @foreach ($screenshots as $screenshot)
                                                                                @if ($screenshot->screenshot != '')
                                                                                    @php
                                                                                        $type = $screenshot->screenshot_type;
                                                                                        $type = str_replace('_', ' ', $type);
                                                                                        $type = ucwords($type)." Screenshot";
                                                                                        
                                                                                        // $sourceScreenshot = Helper::getImageUrl($screenshot->screenshot);
                                                                                        $finalScreenshot = '';
                                                                                        // if (file_exists($sourceScreenshot)) {
                                                                                        //     $finalScreenshot = $screenshot->screenshot;
                                                                                        // } else {
                                                                                        //     $finalScreenshot = 'placeholder.png';
                                                                                        // }

                                                                                        if($screenshot->screenshot != ""){
                                                                                            $finalScreenshot =  Helper::getImageUrl($screenshot->screenshot);
                                                                                            $encodedFileUrl = base64_encode($finalScreenshot);

                                                                                        }else{
                                                                                            $finalScreenshot = $placeholderPrefix.'placeholder.png';
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="swiper-slide">
                                                                                        <div style="width:100%; height:100%;">
                                                                                            <a href="javascript:void(openpopup({{$item->incidence_calendars_report_id}}));" class="popup">
                                                                                                <img class="postcard__img @mobile max-image-height-mobile @else max-image-height-desktop @endif"
                                                                                                src="{{ $finalScreenshot }}"
                                                                                                alt="{{$type}}" />
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
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach

                                                                            <!-- featuredimage Slides -->
                                                                            @foreach ($featuredimages as $key => $featured_image)
                                                                                @if ($featured_image->featured_image != '')
                                                                                    @php
                                                                                        $type = ucwords("Featured Image");
                                                                                        
                                                                                        // $source = Helper::getImageUrl($featured_image->featured_image);
                                                                                        $finalsource = '';
                                                                                        // if (file_exists($source)) {
                                                                                        //     $finalsource = $featured_image->featured_image;
                                                                                        // } else {
                                                                                        //     $finalsource = 'placeholder.png';
                                                                                        // }

                                                                                        if($featured_image->featured_image != ""){
                                                                                            $finalsource = Helper::getImageUrl($featured_image->featured_image);
                                                                                            $encodedFileUrl = base64_encode($finalsource);
                                                                                            
                                                                                        }else{
                                                                                            $finalsource = $placeholderPrefix.'placeholder.png';
                                                                                        }                                                        
                                                                                    @endphp
                                                                                    <div class="swiper-slide">

                                                                                        <a href="javascript:void(openpopup({{$item->incidence_calendars_report_id}}));" class="popup">
                                                                                        <img class="postcard__img @mobile max-image-height-mobile @else max-image-height-desktop @endif"
                                                                                            src="{{ $finalsource }}"
                                                                                            alt="{{$type}}" />
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
                                                                            @foreach ($documents as $key=>$document)
                                                                                @if ($document->document != '' && $document->document_type == "general")
                                                                                    @php
                                                                                        $type = $document->document_type;
                                                                                        $type = str_replace('_', ' ', $type);
                                                                                        $type = ucwords($type);

                                                                                        if(!empty($document->document_name)){
                                                                                            $type = $document->document_name;
                                                                                        }
                                                                                        
                                                                                        // $source = Helper::getImageUrl($document->document);
                                                                                        $source = $document->document;
                                                                                        $extension = "";
                                                                                        // $info = pathinfo($source);                                                        
                                                                                        // if($info){
                                                                                        //     $extension = $info->extension;
                                                                                        // }

                                                                                        $finalsource = '';
                                                                                        // if (file_exists($source)) {
                                                                                        //     $finalsource = $document->document;
                                                                                        // } else {
                                                                                        //     $finalsource = 'placeholder.png';
                                                                                        // }

                                                                                        if($document->document != ""){
                                                                                            $finalsource = Helper::getImageUrl($document->document);
                                                                                            $encodedFileUrl = base64_encode($finalsource);
                                                                                            
                                                                                        }else{
                                                                                            $finalsource = $placeholderPrefix.'placeholder.png';
                                                                                        }                                                         

                                                                                        $displayIcon = $placeholderPrefix.'placeholder.png';
                                                                                        if($extension == "pdf"){
                                                                                            $displayIcon = $placeholderPrefix.'placeholder_pdf.png';
                                                                                        }                                                                                            
                                                                                    @endphp
                                                                                    <div class="swiper-slide">
                                                                                        <a href="javascript:void(openpopup({{$item->incidence_calendars_report_id}}));" class="popup">
                                                                                        <img class="postcard__img @mobile max-image-height-mobile @else max-image-height-desktop @endif"
                                                                                            src="{{ $displayIcon }}"
                                                                                            alt="{{$type}}" />
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

                                                                            <!-- FIR Slides -->
                                                                            @foreach ($documents as $key=>$document)
                                                                                @if ($document->document != '' && $document->document_type == "fir_copy")
                                                                                    @php
                                                                                        $type = $document->document_type;
                                                                                        $type = str_replace('_', ' ', $type);
                                                                                        $type = ucwords($type);

                                                                                        if(!empty($document->document_name)){
                                                                                            $type = $document->document_name;
                                                                                        }
                                                                                        
                                                                                        // $source = Helper::getImageUrl($document->document);
                                                                                        $source = $document->document;
                                                                                        $extension = "";
                                                                                        // $info = pathinfo($source);
                                                                                        // if($info){
                                                                                        //     $extension = $info->extension;
                                                                                        // }

                                                                                        $finalsource = '';
                                                                                        // if (file_exists($source)) {
                                                                                        //     $finalsource = $document->document;
                                                                                        // } else {
                                                                                        //     $finalsource = 'placeholder.png';
                                                                                        // }

                                                                                        if($document->document != ""){
                                                                                            $finalsource = Helper::getImageUrl($document->document);
                                                                                            $encodedFileUrl = base64_encode($finalsource);
                                                                                            
                                                                                        }else{
                                                                                            $finalsource = $placeholderPrefix.'placeholder.png';
                                                                                        }                                                         

                                                                                        $displayIcon = $placeholderPrefix.'placeholder.png';
                                                                                        if($extension == "pdf"){
                                                                                            $displayIcon = $placeholderPrefix.'placeholder_pdf.png';
                                                                                        }                                                                                            
                                                                                    @endphp
                                                                                    <div class="swiper-slide">
                                                                                        <a href="javascript:void(openpopup({{$item->incidence_calendars_report_id}}));" class="popup">
                                                                                        <img class="postcard__img @mobile max-image-height-mobile @else max-image-height-desktop @endif"
                                                                                            src="{{ $displayIcon }}"
                                                                                            alt="{{$type}}" />
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
                                                                
                                                            </li>
                                                        </div>
                                                    </div>    
                                            
                                                    <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                        <div class="righting-wrap">
                                                            <li class="content-text-left mb-3">
                                                                <h3>
                                                                @if(!empty($item->sm_calendars_link))
                                                                    <a href="{{ $item->sm_calendars_link }}" target="_blank" class="importo">{{ ucfirst($item->incidence_calendars_heading) }}</a>
                                                                @else
                                                                {{ ucfirst($item->incidence_calendars_heading) }}
                                                                @endif
                                                                </h3>
                                                            </li>

                                                            <li class="text-start mb-3">

                                                                <!--{{$item->tdty_checked}} - {{$item->incidence_calendars_report_id}} => -->

                                                                @if (!empty($item->team_name))
                                                                    <i class="fas fa-flag mr-2 fsicon"></i>&nbsp;{{ $item->team_name }}
                                                                @endif
                        
                                                                @if (!empty($item->module_name))
                                                                    &nbsp;->&nbsp;{{ $item->module_name }}
                                                                @endif
                        
                                                                @if (!empty($item->chapter_name))
                                                                    &nbsp;->&nbsp;{{ $item->chapter_name }} &nbsp;
                                                                @endif                                                            

                                                                <i class="fas fa-calendar-alt mr-2"></i> {{ $publish_at }}
                                                            
                                                                &nbsp; <i class="fas fa-language mr-2 fsicon"></i>
                                                                {{ ucwords($item->language_name) }}

                                                                &nbsp;
                                                                                                    
                                                                <i class="fas fa-location-arrow mr-2"></i>
                                                                {{ $item->location_name }}
                                                                @if ($item->location_state_name)
                                                                ({{ $item->location_state_name }})
                                                                @endif
                                                                                                                                                                                        
                                                                &nbsp; <span class="tag__item play {{ $publicReportColor }}">
                                                                    <a href="{{ $item->report_link }}" target="_blank"><i class="fas fa-link mr-2"></i>
                                                                        {{ ucwords($item->report_source) }}</a>
                                                                </span>                                                            
                                                    
                                                                &nbsp;
                                                                <span style="color:#000000;">
                                                                    @if(count($tags)>0)
                                                                    @foreach($tags as $key=>$tag)
                                                                    <i class="fas fa-tag mr-2"></i> {{$tag->tag}},
                                                                    @endforeach
                                                                    @endif
                                                                    {{-- @if(count($item->team_tags)>0)
                                                                    @foreach($item->team_tags as $key=>$team_tag)
                                                                    @if($team_tag->tag != 'NA')<i class="fas fa-tag mr-2"></i> {{trim($team_tag->parent_tag)}} -> {{trim($team_tag->tag)}},@endif
                                                                    @endforeach
                                                                    @endif --}}
                                                                </span>
                        
                                                                @if(count($followups)>0)
                                                                <br>
                                                                    @foreach($followups as $key=>$followup)
                                                                    <a href="{{ $followup->followup_link }}" target="_blank">
                                                                        <i class="fas fa-link mr-2"></i> Related News {{$key+1}}
                                                                    </a> | 
                                                                    @endforeach 
                                                                @endif   
                                                                
                                                                
                                                            
                                                            </li>
                                                        

                                                            <li class="content-text-justify mb-3 storyDetailText">
                                                                @if($item->tdty_checked == 1 && !empty($item->tdty_checked_description) && ($item->tdty_checked_description != $item->incidence_calendars_heading))
                                                                    {!!ucfirst($item->tdty_checked_description)!!}
                                                                @else
                                                                    <ol>
                                                                        @foreach($keypoints as $key=>$keypoint)
                                                                            <li class="storyDetailText">{{ trim($keypoint->keypoint) }}</li>
                                                                        @endforeach                                         
                                                                    </ol>
                                                                @endif
                                                            </li>                                            


                                                            <li class="content-text-left">
                                                                @php
                                                                    $seperator = "\r\n";
                                                                    /* $seperator = "<br><br>"; */
                                                                    $socialShareContent = ucfirst($item->incidence_calendars_heading);
                                                                    $socialShareContent .= $seperator.$publish_at;

                                                                    $socialShareContent .= " ".$item->location_name;
                                                                    if ($item->location_state_name){
                                                                        $socialShareContent .= " ".$item->location_state_name;
                                                                    }
                                                                    
                                                                    if($item->tdty_checked == 1 && !empty($item->tdty_checked_description) && ($item->incidence_calendars_heading != $item->tdty_checked_description)){
                                                                        
                                                                        // First, decode all HTML entities to their applicable characters
                                                                        $decodedContent = html_entity_decode($item->tdty_checked_description, ENT_QUOTES | ENT_HTML5);

                                                                        $socialShareContent .= $seperator.ucfirst(strip_tags($decodedContent));
                                                                    } else{
                                                                        foreach($keypoints as $key=>$keypoint){
                                                                            $socialShareContent .= $seperator.trim(strip_tags(html_entity_decode($keypoint->keypoint, ENT_QUOTES | ENT_HTML5)));
                                                                        }
                                                                    }

                                                                    $socialShareContent .= $seperator.strip_tags($item->report_link);

                                                                    /* echo $socialShareContent; */
                                                                @endphp

                                                                <div class="container" style="max-width: 100%;">                                                               
                                                                    <div class="row justify-content-md-center">
                                                                        <div class="col-12 text-center">
                                                                            <span class="causale" style="margin:10px;color:#000; font-weight: 600; text-align:center;">
                                                                                <a href="{{ $viewSimilarReportUrl }}" style="color:#000;"
                                                                                    target="_blank">
                                                                                    <i class="fas fa-file mr-2 fsicon"></i> Click here to find <span class="text-info fs-4 fw-bold">{{ isset($chapterReportData) && isset($chapterReportData[$item->report_chapter_id]) ? $chapterReportData[$item->report_chapter_id] : '' }}</span> similar incidents
                                                                                    @if (!empty($item->team_name))
                                                                                    of {{ $item->team_name }}
                                                                                    @endif
                            
                                                                                    @if (!empty($item->module_name))
                                                                                        &nbsp;->&nbsp;{{ $item->module_name }}
                                                                                    @endif

                                                                                    @if (!empty($item->chapter_name))
                                                                                        &nbsp;->&nbsp;{{ $item->chapter_name }}
                                                                                    @endif                                                                                                                                
                                                                                    <i class="fa fa-external-link-square"></i>
                                                                                </a>                                                                
                                                                            </span>                                                                
                                                                        </div>                                                                  
                                                                    </div>    
                                                                    
                                                                    <div class="row justify-content-md-center" style="margin-top:10px;">
                                                                        <div class="col-8 text-center">                                                                                                                    
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <div class="copyBtn" data-content="{{$socialShareContent}}" style="margin:10px;color:#000; font-weight: 600; text-align:right; float: right;">
                                                                                <button type="button" class="btn btn-{{ config('app.color_scheme') }} copyBtnText">
                                                                                    <i class="fas fa-share" style="font-size: 18px; margin-right:5px;"></i> Share
                                                                                </button>
                                                                            </div>                                                                  

                                                                        </div>
                                                                    </div>                                                                
                                                                </div>
                                                                
                                                            </li>


                                                        </div>
                                                    </div>
                                                </div>        
                                                <div class="clear"></div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                
                        

                            {{-- Model popup Start --}}
                            <!-- Creates the bootstrap modal where the image will appear -->
                            <div class="modal fade" id="imagemodal_{{$item->incidence_calendars_report_id}}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-fullscreenCustom modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h4 class="modal-title" style="color: black;">Preview</h4>
                                        
                                        <button type="button" class="btn btn-{{ config('app.color_scheme') }}"  onclick="javascript:void(closepopup({{$item->incidence_calendars_report_id}}));">Close <i class="fa fa-remove mr-1"></i></button>
                                    </div>
                                    <div class="modal-body" style="text-align: center">
                                        <!-- Slider main container -->
                                        <div class="swiper" style="max-height:100%">
                                            <!-- Additional required wrapper -->
                                            <div class="swiper-wrapper">

                                                <!-- screenshots Slides -->
                                                @foreach ($screenshots as $screenshot)
                                                    @if ($screenshot->screenshot != '')
                                                        @php
                                                            $type = $screenshot->screenshot_type;
                                                            $type = str_replace('_', ' ', $type);
                                                            $type = ucwords($type)." Screenshot";
                                                            
                                                            // $sourceScreenshot = Helper::getImageUrl($screenshot->screenshot);
                                                            $finalScreenshot = '';
                                                            // if (file_exists($sourceScreenshot)) {
                                                            //     $finalScreenshot = $screenshot->screenshot;
                                                            // } else {
                                                            //     $finalScreenshot = 'placeholder.png';
                                                            // }

                                                            if($screenshot->screenshot != ""){
                                                                $finalScreenshot = Helper::getImageUrl($screenshot->screenshot);
                                                                $encodedFileUrl = base64_encode($finalScreenshot);
                                                                
                                                            }else{
                                                                $finalScreenshot = $placeholderPrefix.'placeholder.png';
                                                            }                                                            
                                                        @endphp
                                                        <div class="swiper-slide">
                                                            <div class="swiper-zoom-container">
                                                                <img 
                                                                src="{{ $finalScreenshot }}" style="width:100%; height:100%;" 
                                                                alt="{{$type}}" />
                                                            </div>


                                                            {{-- <div class="container" style="bottom: -55px; position: absolute;">
                                                                <div class="text-center">
                                                                    <p
                                                                        class="btn btn-{{ config('app.color_scheme') }}">
                                                                        {{ $type }}
                                                                    </p>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    @endif
                                                @endforeach

                                                <!-- featuredimage Slides -->
                                                @foreach ($featuredimages as $key => $featured_image)
                                                    @if ($featured_image->featured_image != '')
                                                        @php
                                                            $type = ucwords("Featured Image");
                                                            
                                                            // $source = Helper::getImageUrl($featured_image->featured_image);
                                                            $finalsource = '';
                                                            // if (file_exists($source)) {
                                                            //     $finalsource = $featured_image->featured_image;
                                                            // } else {
                                                            //     $finalsource = 'placeholder.png';
                                                            // }

                                                            if($featured_image->featured_image != ""){
                                                                $finalsource = Helper::getImageUrl($featured_image->featured_image);
                                                                $encodedFileUrl = base64_encode($finalsource);
                                                                
                                                            }else{
                                                                $finalsource = $placeholderPrefix.'placeholder.png';
                                                            }                                                             
                                                        @endphp
                                                        <div class="swiper-slide">
                                                            <div class="swiper-zoom-container">
                                                            <img 
                                                                src="{{ $finalsource }}"  style="width:100%; height:100%;" 
                                                                alt="{{$type}}" />
                                                            </div>

                                                            
                                                        {{-- <div class="container py-5"
                                                                style="bottom: -55px; position: absolute;">
                                                                <div class="text-center">
                                                                    <p
                                                                        class="btn btn-{{ config('app.color_scheme') }}">
                                                                        {{ $type }}
                                                                    </p>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    @endif
                                                @endforeach                    
                                                                                                                                    
                                                <!-- Document Slides -->
                                                @foreach ($documents as $key=>$document)
                                                    @if ($document->document != '')
                                                        @php
                                                            $type = $document->document_type;
                                                            $type = str_replace('_', ' ', $type);
                                                            $type = ucwords($type);

                                                            if(!empty($document->document_name)){
                                                                $type = $document->document_name;
                                                            }
                                                            
                                                            // $source = Helper::getImageUrl($document->document);
                                                            $source = $document->document;
                                                            $extension ="";
                                                            // $info = pathinfo($source);
                                                            // if($info){
                                                            //     $extension = $info->extension;
                                                            // }

                                                            $finalsource = '';
                                                            // if (file_exists($source)) {
                                                            //     $finalsource = $document->document;
                                                            // } else {
                                                            //     $finalsource = 'placeholder.png';
                                                            // }

                                                            if($document->document != ""){
                                                                $finalsource = Helper::getImageUrl($document->document);
                                                                $encodedFileUrl = base64_encode($finalsource);
                                                                
                                                            }else{
                                                                $finalsource = $placeholderPrefix.'placeholder.png';
                                                            }  

                                                            $displayIcon = $placeholderPrefix.'placeholder.png';
                                                            if($extension == "pdf"){
                                                                $displayIcon = $placeholderPrefix.'placeholder_pdf.png';
                                                            }                                                                                            
                                                        @endphp
                                                        <div class="swiper-slide">
                                                            <img class="postcard__img @mobile max-image-height-mobile @else max-image-height-desktop @endif"
                                                                src="{{ $displayIcon }}"
                                                                alt="{{$type}}" style="width:100%; height:100%;" />


                                                                {{-- <div class="container py-5"
                                                                style="bottom: -55px; position: absolute;">
                                                                <div class="text-center">
                                                                    <p
                                                                        class="btn btn-{{ config('app.color_scheme') }}">
                                                                        {{ $type }}
                                                                    </p>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    @endif
                                                @endforeach                                                                                                            

                                            </div>
                                                                    
                                            <!-- If we need navigation buttons -->
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
                                                                                                                                
                                        </div>
                                                    
                                    </div>
                                    {{-- <div class="modal-footer">
                                        <button type="button" class="btn btn-{{ config('app.color_scheme') }}"  onclick="javascript:void(closepopup({{$item->incidence_calendars_report_id}}));">Close <i class="fa fa-remove mr-1"></i></button>                                    
                                    </div>  --}}
                                </div>
                                </div>
                            </div>  
                            {{-- Model popup End --}}                    

                        @elseif($item->type == "document")
                            
                                {{-- Document cal --}}
                                @php
                                    $dateTime = DateTime::createFromFormat('Y-m-d', $item->date);                       
                                    $loadingForYear = $dateTime->format('Y');
                                    $displayItemDate = $dateTime->format('jS \o\f F Y');
                                    $event_date = \Carbon\Carbon::createFromTimestamp(strtotime($item->date))->format('M d, Y');
                                    $publicReportColor="blue";                            

                                
                                    $prefix = config('app.app_domain_issue').'public/';
                                    $app_domain_issue = config('app.app_domain');
                                    $viewSimilarReportUrl = $app_domain_issue.$website_slug."/".config('app.view_similar_report_url');
                                    $viewSimilarReportUrl = str_replace("##ISSUE_ID##",$item->team_id,$viewSimilarReportUrl);
                                    $viewSimilarReportUrl = str_replace("##MODULE_ID##",$item->module_id,$viewSimilarReportUrl);
                                    $viewSimilarReportUrl = str_replace("##CHAPTER_ID##",$item->chapter_id,$viewSimilarReportUrl);           

                                    $screenshots = DB::connection('setfacts')->select("select document_screenshots.* from document_screenshots where document_screenshots.document_id = ".$item->id." order by document_screenshots.id ASC");
                                    $files = DB::connection('setfacts')->select("select document_files.* from document_files where document_files.document_id = ".$item->id." order by document_files.id ASC");
                    
                                
                                                        

                                @endphp


                                <div class="row">                                                        
                                    <div class="col-12">
                                        <article class="postcard light {{ $publicReportColor }}">    
                                            <div class="row" style="overflow: scroll; width: 100%;">
                                                <div @mobile class="col-12" @else class="col-6" @endmobile>
                                                    <!-- Slider main container -->
                                                    <div class="swiper">
                                                        <!-- Additional required wrapper -->
                                                        <div class="swiper-wrapper">

                                                        @foreach ($screenshots as $screenshot)
                                                                @if ($screenshot->screenshot != '')
                                                                    @php
                                                                        $finalsource = Helper::getImageUrl($item->document);
                                                                        $encodedFileUrlDocument = base64_encode($finalsource);
                                                                        $doctype = "Report";

                                                                        $type = "Report";
                                                                        
                                                                        $finalScreenshot = '';
                                                                        if($screenshot->screenshot != ""){
                                                                            $finalScreenshot =  Helper::getImageUrl($screenshot->screenshot);
                                                                            $encodedFileUrl = base64_encode($finalScreenshot);

                                                                        }else{
                                                                            $finalScreenshot = $placeholderPrefix.'placeholder.png';
                                                                        }
                                                                    @endphp
                                                                    <div class="swiper-slide">

                                                                        
                                                                        <img class="postcard__img"
                                                                            src="{{ $finalScreenshot }}"
                                                                            alt="{{$type}}" />
                                                                        

                                                                        <a class="downloadImage" href="{{ $finalsource }}" target="_blank">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>

                                                                        <div class="container py-5"
                                                                            style="bottom: -55px; position: absolute;">
                                                                            <a href="{{ $finalsource }}" target="_blank">
                                                                                <div class="text-center">
                                                                                    <p
                                                                                        class="btn btn-{{ config('app.color_scheme') }}">
                                                                                        {{ $type }}                                                                        
                                                                                    </p>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach                                            
                                                        
                                                            @foreach ($files as $file)
                                                                @if ($file->file != '')
                                                                <!-- file Slides -->                                            
                                                                @php
                                                                    $source = $file->file;
                                                                    $type = "Report";
                                                                    $extension = '';
                                                                    $info = @pathinfo($source);
                                                                    // dump($source);
                                                                    // dd($info);
                                                                    if(isset($info['extension'])){
                                                                        $extension = $info['extension'];
                                                                    }

                                                                    $finalsource = '';
                                                                
                                                                    if($file->file != ""){
                                                                        $finalsource = Helper::getImageUrl($file->file);
                                                                        $encodedFileUrl = base64_encode($finalsource);
                                                                    }else{
                                                                        $finalsource = $placeholderPrefix.'placeholder.png';
                                                                    }                                                         

                                                                    $displayIcon = $placeholderPrefix.'placeholder.png';
                                                                    if($extension == "pdf"){
                                                                        $displayIcon = $placeholderPrefix.'placeholder_pdf.png';
                                                                    }                                                                                            
                                                                @endphp
                                                                <div class="swiper-slide">
                                                                    <img class="postcard__img"
                                                                        src="{{ $displayIcon }}"
                                                                        alt="{{$type}}" />

                                                                    <a class="downloadImage" href="{{ $finalsource }}" target="_blank">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>

                                                                    <div class="container py-5"
                                                                        style="bottom: -55px; position: absolute;">
                                                                        <a href="{{ $finalsource }}" target="_blank">
                                                                        <div class="text-center">
                                                                            <p
                                                                                class="btn btn-{{ config('app.color_scheme') }}">
                                                                                {{ $type }}
                                                                            </p>
                                                                        </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            @endforeach 

                                                            @if ($item->document != '')
                                                            
                                                                <!-- Document Slides -->                                            
                                                                @php
                                                                    $source = $item->document;
                                                                    $type = "Report";
                                                                    $extension = '';
                                                                    $info = @pathinfo($source);
                                                                    // dump($source);
                                                                    // dd($info);
                                                                    if(isset($info['extension'])){
                                                                        $extension = $info['extension'];
                                                                    }

                                                                    $finalsource = '';
                                                                
                                                                    if($item->document != ""){
                                                                        $finalsource = Helper::getImageUrl($item->document);
                                                                        $encodedFileUrl = base64_encode($finalsource);
                                                                    }else{
                                                                        $finalsource = $placeholderPrefix.'placeholder.png';
                                                                    }                                                         

                                                                    $displayIcon = $placeholderPrefix.'placeholder.png';
                                                                    if($extension == "pdf"){
                                                                        $displayIcon = $placeholderPrefix.'placeholder_pdf.png';
                                                                    }                                                                                            
                                                                @endphp
                                                                <div class="swiper-slide">
                                                                    <img class="postcard__img"
                                                                        src="{{ $displayIcon }}"
                                                                        alt="{{$type}}" />

                                                                    <a class="downloadImage" href="{{ $finalsource }}" target="_blank">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>

                                                                    <div class="container py-5"
                                                                        style="bottom: -55px; position: absolute;">
                                                                        <a href="{{ $finalsource }}" target="_blank">
                                                                        <div class="text-center">
                                                                            <p
                                                                                class="btn btn-{{ config('app.color_scheme') }}">
                                                                                {{ $type }}
                                                                            </p>
                                                                        </div>
                                                                        </a>
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
                                                        <h1 class="postcard__title {{ $publicReportColor }}"><a href="{!! Helper::getImageUrl($item->document) !!}" target="_blank">{{ ucfirst($item->title) }}</a></h1>
                                                        <div class="postcard__subtitle ">
                                                            <p class="text-info fs-4 fw-bold">
                                                                <i class="fas fa-calendar-alt mr-2"></i> {{ $event_date }}
                                                            </p>
                                                            
                                                            <p>
                                                                <i
                                                                class="fas fa-flag mr-2 fsicon"></i>&nbsp;{{ $item->team_name }}
                        
                                                                @if (!empty($item->module_name))
                                                                    &nbsp;->&nbsp;{{ $item->module_name }}
                                                                @endif
                        
                                                                @if (!empty($item->chapter_name))
                                                                    &nbsp;->&nbsp;{{ $item->chapter_name }}
                                                                @endif
                                                            
                                                                                                            
                                                                @if (!empty($item->tags))
                                                                &nbsp;
                                                                <span style="color:#000000;">
                                                                    <i class="fas fa-tag mr-2"></i> {{ ucwords($item->tags) }}
                                                                </span> 
                                                                @endif    
                                                            </p>      
                                                                                    
                                                
                                                        </div>
                                                
                                                        <div class="postcard__bar postcard__bar_blue"></div>

                                                        <div class="postcard__preview-txt">                                  
                                                            {!!$item->description!!}
                                                        </div>     

                                                        <div class="container" style="max-width: 100%;">
                                                            <div class="row">                                               
                                                                <div class="col-12 text-left">
                                                                    @if(isset($chapterReportData[$item->chapter_id]))
                                                                        <span class="causale" style="margin:10px;color:#000; font-weight: 600; text-align:center;">
                                                                            <a href="{{ $viewSimilarReportUrl }}" style="color:#000;"
                                                                                target="_blank">
                                                                                <i class="fas fa-file mr-2 fsicon"></i> 
                                                                                Click here to find <span class="text-info fs-4 fw-bold">{{ isset($chapterReportData[$item->chapter_id]) ? $chapterReportData[$item->chapter_id] : '' }}</span> similar incidents
                                                                                @if (!empty($item->team_name))
                                                                                of {{ $item->team_name }}
                                                                                @endif
                        
                                                                                @if (!empty($item->module_name))
                                                                                    &nbsp;->&nbsp;{{ $item->module_name }}
                                                                                @endif

                                                                                @if (!empty($item->chapter_name))
                                                                                    &nbsp;->&nbsp;{{ $item->chapter_name }}
                                                                                @endif                                                                                                                                
                                                                                <i class="fa fa-external-link-square"></i>
                                                                            </a>                                                             
                                                                        </span>                                        
                                                                    @endif                        
                                                                </div>
                                                            </div>   
                                                            
                                                            <div class="row justify-content-md-center" style="margin-top:10px;">                                               
                                                                <div class="col-8 text-center">
                                                                                
                                                                </div>
                                                                <div class="col text-center">
                                                                    @php
                                                                        /* $seperator = "\r\n \r\n"; */
                                                                        /* $seperator = "<br><br>"; */
                                                                        $seperator = "\r\n";
                                                                        $socialShareContent = ucfirst($item->title);
                                                                        $socialShareContent .= $seperator.$event_date;
                                                                        
                                                                        // First, decode all HTML entities to their applicable characters
                                                                        $decodedContent = html_entity_decode($item->description, ENT_QUOTES | ENT_HTML5);
                                                                        
                                                                        $socialShareContent .= $seperator.strip_tags($decodedContent);

                                                                        $socialShareContent .= $seperator.Helper::getImageUrl($item->document);

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
                                            </div>
                                        </article>
                                    </div>
                                </div>

                        

                        @endif
                    @endforeach

                </div>
            @endif
        </div>    
    </div>

</div>

