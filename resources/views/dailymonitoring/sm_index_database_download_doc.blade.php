<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            width: 100%;
            font-family: 'freeserif', sans-serif;
        }

        /*Common CSS Code for Table*/
        .tableStyleHeading {
            width: 100%;

        }

        .tableStyle {
            width: 100%;
            line-height: 1.50;
        }

        /*TD and TH Style*/
        .tableStyle td,
        .tableStyle th {
            /* border: 1px solid #000000; */
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

        a {
            color: blue;
        }
    </style>
</head>

<body>

    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align: center;">
        <tbody>
            <tr>
                <td style="text-align: center;font-size: 15px;font-weight:bold">SM Monitoring Report</td>
            </tr>
        </tbody>
    </table>

    <table class="tableStyle" border="0" cellspacing="0" cellpadding="2" width="100%">
        <tbody>
@foreach ($all_items_sm_cal as $key => $item)
@php
$dateTime = DateTime::createFromFormat('Y-m-d', $item->date);
$loadingForYear = $dateTime->format('Y');
// $displayItemDate = $dateTime->format('jS \o\f F Y');
$displayItemDate = $dateTime->format('M d, Y');
@endphp
<tr>
    <td align="center">
        @if ($item->image)
            @php
                $hashtag = $item->heading;
                $finalScreenshot = Helper::getImageUrl($item->image);
            @endphp
            @if (!empty($item->link))
                <img src="{{ $finalScreenshot }}" alt="{{ $item->hashtag }}"  width="400" />
            @else
                <img src="{{ $finalScreenshot }}" alt="{{ $item->hashtag }}" width="400" />
            @endif
        @else
            @if ($item->team_id)
                @if (file_exists(URL::to('public/images/teams/' . $item->team_id . '.jpg')))
                    <img src="{{ URL::to('public/images/teams/' . $item->team_id . '.jpg') }}" />
                @else
                    <img src="{{ URL::to('public/images/teams/0.jpg') }}" />
                @endif
            @else
                <img src="{{ URL::to('public/images/teams/0.jpg') }}" />
            @endif
        @endif
    </td>
</tr>
@if(!empty($item->link))
<tr><td><b><a href="{{ $item->link }}" style="color:blue">{{$key+1}}. {{ ucfirst($item->hashtag) }}</a></b></td></tr>
<tr><td>{{ strip_tags($item->description) }}</td></tr>
<tr><td><a href="{{ $item->link }}" style="color:blue">{{ $displayItemDate }}</a></td></tr>
@else
<tr><td><b>{{$key+1}}. {{ ucfirst($item->hashtag) }}</b></td></tr>
<tr><td>{{ strip_tags($item->description) }}</td></tr>
<tr><td>{{ $displayItemDate }}</td></tr>
@endif
<tr><td><hr/></td></tr>
@endforeach
</tbody>
</table>
</body>
</html>
