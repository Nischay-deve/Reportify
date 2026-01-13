<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            width: 100%;
            font-family: DejaVu Sans, freeserif, Arial, sans-serif;
            font-size: 11.5pt;
            line-height: 1.6;
            color: #000;
        }

        p {
            margin: 0;
        }

        a {
            color: blue;
            text-decoration: underline;
        }

        .tableStyleHeading {
            width: 100%;
        }

        .tableStyle {
            width: 100%;
            border-collapse: collapse;
        }

        .tableStyle td,
        .tableStyle th {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }

        .tableStyle th {
            text-align: center;
            font-weight: bold;
        }

        * {
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    @php $total_news = 0; @endphp
    @foreach($dataAllTeamReport as $teamData)
    @php $total_news += $teamData['team_count']; @endphp
    @endforeach

    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align:center;">
        <tbody>
            <tr>
                <td style="font-size:15px;font-weight:bold;">{{$reportTitle}}</td>
            </tr>
            @if($reportHeading)<tr>
                <td style="font-weight:bold;">{{$reportHeading}}</td>
            </tr>@endif
            @if($reportDescription)<tr>
                <td>{{$reportDescription}}</td>
            </tr>@endif

            <tr>
                <td>
                    This report consists of ({{$total_news}}) news reports from yesterday that happened in different sections of
                    @foreach($dataAllTeamReport as $key => $teamData)
                    {{ucwords(strtolower($teamData['team']['name']))}}({{$teamData['team_count']}})
                    @if($key == (count($dataAllTeamReport)-1))...@else,@endif
                    @endforeach
                </td>
            </tr>

            @if ($from == $to && !empty($to))
            <tr>
                <td>{{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
            </tr>
            @elseif(!empty($from) && !empty($to))
            <tr>
                <td>{{ \Carbon\Carbon::createFromTimestamp(strtotime($from))->format('M d, Y') }} To {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- TEAM HEADING INDEX --}}
    @if($show_index)
    <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="100%" style="border:1px solid black;">
        <tbody>
            @foreach($dataAllTeamReport as $key => $teamData)
            <tr>
                <td colspan="2" style="background-color:#FFC000; text-align:center;" width="80%" align="center">
                    {{ $teamData['team']['name'] }}
                </td>
                <td style="background-color:#d9e2f3; text-align:center;" width="20%">
                    News Count ({{ $teamData['team_count'] }})
                </td>
            </tr>

            @foreach (collect($data)->where('team_id', $teamData['team_id']) as $chapterData)
            <tr>
                <td style="text-align:center;" width="10%">{{ $loop->iteration }}</td>
                <td style="text-align:left; padding-left:3px;" width="73%">
                    @if(isset($chapterData['chapter']['id']))
                    {{ $moduleNameArr[$chapterData['chapter']['id']] }} -> {{ $chapterData['chapter']['name'] }}
                    @endif
                </td>
                <td style="text-align:center;" width="7%">{{ $chapterData['chapter_count'] }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
    @endif

    <br />