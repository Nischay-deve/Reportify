<!DOCTYPE html>
<html>
<head>

    <style>
        body {
            font-family: 'Hind', serif;
        }

        /*Common CSS Code for Table*/
        .tableStyleHeading {
            width: 100%;
            line-height: 1.50;
            padding-bottom: 10px;
        }

        .tableStyle {
            width: 100%;
            /* line-height: 1.50; */
        }

        /*TD and TH Style*/
        .tableStyle td,
        .tableStyle th {
            border: 1px solid #000000;
            padding: 10px;
        }

        /*Style for Table Head - TH*/
        .tableStyle th {
            border: solid #000;
            border-width: 0 1px;
            text-align: center;
        }

        .tableStyle caption {
            text-align: center;
        }

        ul {
            list-style-type: none;
            /* Remove bullets */
            padding: 0;
            /* Remove padding */
            margin: 0;
            /* Remove margins */
        }
        a{
            color:#0645AD;
        }
    </style>
</head>

<body>

    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align: center;">
        <tbody>
            <tr>
                <td style="text-align: center;font-size: 15px;font-weight:bold">{{$reportTitle}}</td>
            </tr>
            @if($reportHeading)
            <tr><td style="text-align: center; font-weight:bold">{{$reportHeading}}</td></tr>            
            @endif             
            @if($reportDescription)
            <tr><td style="text-align: center">{{$reportDescription}}</td></tr>            
            @endif            
            @if(!empty($teamName))<tr>
                <td style="text-align: center">{{$teamName->name}}</td>
            </tr>@endif
            @if(!empty($moduleName))<tr>
                <td style="text-align: center">{{$moduleName->name}}</td>
            </tr>@endif
            @if(!empty($chapterName))<tr>
                <td style="text-align: center">{{$chapterName->name}}</td>
            </tr>@endif

            @if ($from == $to && !empty($to))<tr>
                <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
            </tr>
            @elseif(!empty($from) && !empty($to))<tr>
                <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($from))->format('M d, Y') }} To {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
            </tr>@endif
        </tbody>
    </table>

    <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="100%" style="border: 1px solid black;">
        <tbody>
            <tr>
                <td colspan="3" style="border: 1px solid #000000;text-align: center">INDEX</td>
            </tr>
            <tr>
                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="10%">#</td>
                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="70%">Topic</td>
                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="20%">News Count ({{$repcount}})</td>
            </tr>
            @if($report_data_type == "base_report")
            @foreach($dataModule as $key=> $datas)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $key+1 }}</td>
                <td style="border: 1px solid #000000; text-align: left;" width="70%" align="left">{{$datas['module']['name']}}</td>
                <td style="border: 1px solid #000000; text-align: center;" width="20%">{{$datas['module_count']}}</td>
            </tr>
            @endforeach
            @else
            @foreach($data as $key=> $datas)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $key+1 }}</td>
                <td style="border: 1px solid #000000; text-align: left;" width="70%" align="left">{{$moduleNameArr[$datas['chapter']['id']]}} -> {{$datas['chapter']['name']}}</td>
                <td style="border: 1px solid #000000; text-align: center;" width="20%">{{$datas['chapter_count']}}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>   
    

@if ($report_data_type == 'document_report')          
<table align="left" cellspacing="3" cellpadding="3" style="margin:10pt;width:100%;text-align:left;">
<tbody>

@foreach($keyNews[$dataRep['chapter']['id']] as $key=>$reports)
<tr>
<td><p style="font-weight:bold;margin-top: 10px;">{{$reports['serial_number']}}. {{$reports['heading']}} @if($roleId === 1) <span style="color:#88b165;">(@foreach($reports['tag'] as $key=>$tag) {{$tag['tag']}}, @endforeach @foreach($reports['team_tags'] as $key=>$team_tag) @if($team_tag['tag'] != 'NA') {{$team_tag['parent_tag']}} -> {{$team_tag['tag']}}, @endif @endforeach)</span>@endif</p>
@foreach($reports['keypoint'] as $key=>$keypoint)
{{ trim($keypoint['keypoint']) }} &nbsp;
@endforeach
@if($reports['location'])
<p>
Location: {{$reports['location']->name}} @if($reports['locationState'] && ($reports['locationState']->name != 'NA')) ( {{$reports['locationState']->name}} ) @endif
</p>
@endif
</td>
</tr>

@endforeach
</tbody>
</table>

<table align="left" cellspacing="3" cellpadding="3" style="margin:10pt;width:100%;text-align:left;">
<tbody>
<tr><td  style="font-weight:bold;">References:</td></tr>
 
@foreach ($keyNews as $key => $reports)
<tr>
<td>{{$sno}}. {{ ucfirst($reports['source']) }}, {{ \Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('M d, Y') }}
<br/><a href="{{ $reports['link'] }}" style="color:blue">{{ $reports['link'] }}</a><br/>
@foreach ($reports['screenshots'] as $screenshot)
@if ($screenshot['screenshot'] != '' && $screenshot['screenshot_type'] == 'front_page')
<a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}" style="color:blue">Front Page Screenshot</a> | 
@endif
@if ($screenshot['screenshot'] != '' && $screenshot['screenshot_type'] == 'full_news')
<a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}" style="color:blue">Full Page Screenshot</a> | 
@endif
@endforeach
@foreach ($reports['feateredimage'] as $key => $featured_image)
@if ($featured_image['featured_image'] != '')
<a href="{!! Helper::getImageUrl($featured_image['featured_image']) !!}" style="color:blue">Featured Image</a> | 
@endif
@endforeach
@foreach ($reports['imagelink'] as $key => $image)
<a href="{{ $image['imagelink'] }}" style="color:blue">Image</a> | 
@endforeach
@foreach ($reports['videolink'] as $key => $video)
<a href="{{ $video['videolink'] }}" style="color:blue">Video</a> | 
@endforeach
@foreach($reports['followup'] as $key=>$followup)
<a href="{{ $followup['followup_link'] }}" style="color:blue">Related News {{$key+1}}</a> | 
@endforeach 
</td>
</tr>

@endforeach
</tbody>
</table>  
@else
<table align="left" cellspacing="3" cellpadding="3" style="margin:10pt;width:100%;text-align:left;">
<tbody>

@foreach($keyNews as $key=>$reports)
<tr>
<td><p style="font-weight:bold;margin-top: 10px;">{{$reports['serial_number']}}. {{$reports['heading']}} @if($roleId === 1) <span style="color:#88b165;">(@foreach($reports['tag'] as $key=>$tag) {{$tag['tag']}}, @endforeach @foreach($reports['team_tags'] as $key=>$team_tag) @if($team_tag['tag'] != 'NA') {{$team_tag['parent_tag']}} -> {{$team_tag['tag']}}, @endif @endforeach)</span>@endif</p>
<p>
@php
$publish_at = \Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('d/m/Y');                                                                                                                      
@endphp    
({{$reports['source']}}, {{$publish_at}}) 
<a href="{{$reports['link']}}" style="color:blue">News Link</a> | 
@foreach($reports['screenshot'] as $screenshot)
@if($screenshot['screenshot']!="" && $screenshot['screenshot_type']=="front_page")<a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}" style="color:blue">Front Page Screenshot Link</a> | @endif
@if($screenshot['screenshot']!="" && $screenshot['screenshot_type']=="full_news")<a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}" style="color:blue">Full Page Screenshot Link</a> |  @endif 
@endforeach
@foreach($reports['feateredimage'] as $key=>$featured_image)
@if($featured_image['featured_image']!="") &nbsp; <a href="{!! Helper::getImageUrl($featured_image['featured_image']) !!}" style="color:blue">Featured Image Link {{$key+1}}</a> | @endif
@endforeach
@foreach($reports['imagelink'] as $key=>$image)
<a href="{{$image['imagelink']}}" style="color:blue">Image Link {{$key+1}}</a> | 
@endforeach
@foreach($reports['videolink'] as $key=>$video)
<a href="{{$video['videolink']}}" style="color:blue">Video Link {{$key+1}}</a> | 
@endforeach
@foreach ($reports['document'] as $key=>$document)
@if ($document['document_type'] != '' && $document['document_type'] == 'general')@if ($document['document_name'] != '')<a href="{!! Helper::getImageUrl($document['document']) !!}" style="color:blue">{{$document['document_name']}}</a> | @else<a href="{!! Helper::getImageUrl($document['document']) !!}" style="color:blue">Document Link {{$key+1}}</a> | @endif @endif
@if ($document['document_type'] != '' && $document['document_type'] == 'fir_copy')@if ($document['document_name'] != '')<a href="{!! Helper::getImageUrl($document['document']) !!}" style="color:blue">{{$document['document_name']}}</a> | @else<a href="{!! Helper::getImageUrl($document['document']) !!}" style="color:blue">FIR Link {{$key+1}}</a> | @endif @endif
@endforeach  
@foreach($reports['followup'] as $key=>$followup)
<a href="{{ $followup['followup_link'] }}" style="color:blue">Related News {{$key+1}}</a> | 
@endforeach 
</p>
@if($reports['location_name'])
<p>
Location: {{$reports['location_name']}} @if($reports['location_state_name'] && ($reports['location_state_name'] != 'NA')) ( {{$reports['location_state_name']}} ) @endif
</p>
@endif
Key Points:
@foreach($reports['keypoint'] as $key=>$keypoint)
<p>{{$key+1}}. {{ trim($keypoint['keypoint']) }}</p>
@endforeach
</td>
</tr>

@endforeach

</tbody>
</table>
@endif

</body>

</html>
