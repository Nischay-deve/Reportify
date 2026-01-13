@php
$publish_at = \Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('d/m/Y');
@endphp

<p style="font-weight:bold; margin-top:10px;">
    {{$reports['serial_number']}}. {{$reports['heading']}}
    @if($roleId === 1)
    <span style="color:#88b165;">
        (
        @foreach($reports['tag'] as $tag) {{$tag['tag']}}, @endforeach
        @foreach($reports['team_tags'] as $team_tag)
        @if($team_tag['tag'] != 'NA') {{$team_tag['parent_tag']}} -> {{$team_tag['tag']}}, @endif
        @endforeach
        )
    </span>
    @endif
</p>

<p>
    @if($report_data_type == "custom_report")
    @if($show_source){{$reports['source']}} &nbsp;@endif
    @if($show_date){{$publish_at}} &nbsp;@endif
    @if($show_language){{ ucwords($reports['language_name']) }} &nbsp;@endif
    @if($show_link_text){{$reports['link']}}<br />@endif
    @if($show_link)<a href="{{$reports['link']}}">News Link</a> | @endif
    @else
    ({{trim($reports['source'])}}, {{trim($reports['publish_at'])}})
    <a href="{{$reports['link']}}">News Link</a> |
    @endif

    @if($show_front_page_screenshots)
    @foreach($reports['screenshot'] as $screenshot)
    @if($screenshot['screenshot']!="" && $screenshot['screenshot_type']=="front_page")
    <a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}">Front Page Screenshot Link</a> |
    @endif
    @if($screenshot['screenshot']!="" && $screenshot['screenshot_type']=="full_news")
    <a href="{!! Helper::getImageUrl($screenshot['screenshot']) !!}">Full Page Screenshot Link</a> |
    @endif
    @endforeach
    @endif

    @if($show_featuredimages)
    @foreach($reports['feateredimage'] as $k=>$featured_image)
    @if($featured_image['featured_image']!="")
    <a href="{!! Helper::getImageUrl($featured_image['featured_image']) !!}">Featured Image Link {{$k+1}}</a> |
    @endif
    @endforeach
    @endif

    @if($show_imagesources)
    @foreach($reports['imagelink'] as $k=>$image)
    <a href="{{$image['imagelink']}}">Image Link {{$k+1}}</a> |
    @endforeach
    @endif

    @if($show_videolinks)
    @foreach($reports['videolink'] as $k=>$video)
    <a href="{{$video['videolink']}}">Video Link {{$k+1}}</a> |
    @endforeach
    @endif

    @foreach ($reports['document'] as $k=>$document)
    @if ($show_documents && $document['document_type'] == 'general')
    @if ($document['document_name'] != '')
    <a href="{!! Helper::getImageUrl($document['document']) !!}">{{$document['document_name']}}</a> |
    @else
    <a href="{!! Helper::getImageUrl($document['document']) !!}">Document Link {{$k+1}}</a> |
    @endif
    @endif

    @if ($show_fir && $document['document_type'] == 'fir_copy')
    @if ($document['document_name'] != '')
    <a href="{!! Helper::getImageUrl($document['document']) !!}">{{$document['document_name']}}</a> |
    @else
    <a href="{!! Helper::getImageUrl($document['document']) !!}">FIR Link {{$k+1}}</a> |
    @endif
    @endif
    @endforeach

    @foreach($reports['followup'] as $k=>$followup)
    <a href="{{ $followup['followup_link'] }}">Related News {{$k+1}}</a> |
    @endforeach
</p>

@if($reports['location_name'])
<p>
    Location: {{$reports['location_name']}}
    @if($reports['location_state_name'] && ($reports['location_state_name'] != 'NA'))
    ( {{$reports['location_state_name']}} )
    @endif
</p>
@endif

{{-- ✅ KEY POINTS (mPDF safe spacing) --}}
@if($show_keypoints)
<p style="font-weight:bold; margin-top:6px;">Key Points:</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:4px;">
    <tbody>
        @foreach($reports['keypoint'] as $i=>$keypoint)
        <tr>
            <td style="padding:0 0 14px 14px; line-height:1.7;">
                {{ $i+1 }}. {{ trim($keypoint['keypoint']) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<br />