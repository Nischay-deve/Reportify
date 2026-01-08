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
            <td style="text-align: center">
                    {{$reportDescription}}
            </td>
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
