<!DOCTYPE html>
<html>
<head>


    <style>
        body {
            width: 100%;
            font-family: 'Hind', serif;
        }

        /*Common CSS Code for Table*/
        .tableStyleHeading {
            width: 100%;

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
            color:blue;
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
            <tr>
                <td style="text-align: center">{{$reportDescription}}</td>
            </tr>            
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

    <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="75%" style="border: 1px solid black;text-align:center;" align="center">
        <tbody>
            <tr>
                <td colspan="4" style="border: 1px solid #000000;text-align: center">INDEX</td>
            </tr>
            <tr>
                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="10%">#</td>
                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="35%">Team name</td>
                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="35%">User name</td>                
                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="20%">No. NEWS</td>
            </tr>
            @php
            $totalNews=0;
            @endphp
            @foreach($allTeams as $keyTeam => $dataTeam)
                @php
                    $totalNewsByTeam=0;
                @endphp            

                @foreach($workReport as $key=> $data)
                    @if($data['team_id'] == $dataTeam->id)
                        <tr>
                            <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $key+1 }}</td>
                            <td style="border: 1px solid #000000; text-align: left;" width="35%">{{ucwords(strtolower($data['team_name']))}}</td>
                            <td style="border: 1px solid #000000; text-align: center;" width="35%">{{$data['user_name']}}</td>
                            <td style="border: 1px solid #000000; text-align: center;" width="20%">{{$data['total_reports_by_team_user']}}</td>
                        </tr>
                        @php
                            $totalNewsByTeam= $totalNewsByTeam + $data['total_reports_by_team_user'];
                        @endphp                          
                    @endif  
                @endforeach

                <tr>
                    <td colspan="3" style="border: 1px solid #000000;text-align: right;font-weight:bold;">Total News By {{ucwords(strtolower($dataTeam->name))}} Team</td>
                    <td style="border: 1px solid #000000; text-align: center;font-weight:bold;">{{$totalNewsByTeam}}</td>
                </tr>       
                
                @php               
                    $totalNews = $totalNews + $totalNewsByTeam;
                @endphp                

            @endforeach    
            <tr>
                <td colspan="3" style="border: 1px solid #000000;text-align: right;font-weight:bold;">Total NEWS</td>
                <td style="border: 1px solid #000000; text-align: center;font-weight:bold;">{{$totalNews}}</td>
            </tr>                
        </tbody>
    </table>      

</body>
</html>
