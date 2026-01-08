@extends('layouts.admin')

@section('content')

<div class="page-content">

    <div class="container-fluid" id="overlay">
        <div class="row">
            <div class="col-12" style="text-align: center;">
                <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                Loading...
            </div>
        </div>
    </div>

    <div class="container-fluid" id="page_content" style="display:none;">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Download {{ $reportTitle }}</h4>
                </div>
            </div>
        </div>

        {{-- DOWNLOAD BUTTONS --}}
        <div class="row">
            <div class="col-md-4"></div>

            <div class="col-md-4 fixed-button">
                <form action="{{ route('report.pdfview', $website_slug) }}" method="get" id="downloadForm">

                    @php
                        foreach($_GET as $name => $value){
                            if($name !== 'view_reports'){
                                if(is_array($value)){
                                    foreach($value as $v){
                                        echo '<input type="hidden" name="'.$name.'[]" value="'.$v.'">';
                                    }
                                } else {
                                    echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
                                }
                            }
                        }
                    @endphp

                    <input type="hidden" name="report_type" id="report_type" value="word">

                    <div class="btn-group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-{{config('app.color_scheme')}} dropdown-toggle" data-toggle="dropdown">
                            Download Report <i class="mdi mdi-chevron-down"></i>
                        </button>

                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#word">Word Report</a>
                            <a class="dropdown-item" href="#pdf">PDF Report</a>
                            <a class="dropdown-item" href="#googledoc">Google Doc</a>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-md-4"></div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card">
                    <div class="card-body">

                        {{-- TITLE HEADER --}}
                        <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align:center;">
                            <tbody>

                                <tr>
                                    <td style="font-size:15px; font-weight:bold;">
                                        {{ $reportTitle }}
                                    </td>
                                </tr>

                                @if($reportHeading)
                                <tr>
                                    <td style="font-weight:bold;">{{ $reportHeading }}</td>
                                </tr>
                                @endif

                                @if($reportDescription)
                                <tr>
                                    <td>{{ $reportDescription }}</td>
                                </tr>
                                @endif

                                @if(!empty($teamName))
                                <tr>
                                    <td>{{ $teamName->name }}</td>
                                </tr>
                                @endif

                                @if($from == $to)
                                    <tr><td>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</td></tr>
                                @else
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>

                        {{-- REPORT TABLE --}}
                        <table class="tableStyle" border="1" width="75%" align="center" style="border:1px solid black; text-align:center;">
                            <tbody>

                                <tr>
                                    <td colspan="6" style="text-align:center; font-weight:bold;">INDEX</td>
                                </tr>

                                <tr>
                                    <td width="10%" style="background:#d9e2f3;">#</td>
                                    <td width="20%" style="background:#FFC000;">Team Name</td>
                                    <td width="20%" style="background:#FFC000;">User Name</td>
                                    <td width="15%" style="background:#FFC000;">Daily Posts</td>
                                    <td width="15%" style="background:#FFC000;">Base Posts</td>
                                    <td width="20%" style="background:#d9e2f3;">Total Posts</td>
                                </tr>

                                @php $totalPosts = 0; @endphp

                                @foreach($allTeams as $team)
                                    @php
                                        $teamPostSum = 0;
                                        $rowNum = 1;
                                    @endphp

                                    @foreach($socialMediaReport as $row)
                                        @if($row['team_id'] == $team->id)

                                        <tr>
                                            <td>{{ $rowNum }}</td>
                                            <td style="text-align:left;">{{ ucwords(strtolower($row['team_name'])) }}</td>
                                            <td>{{ $row['user_name'] }}</td>
                                            <td>{{ $row['daily_report_count'] }}</td>
                                            <td>{{ $row['base_report_count'] }}</td>
                                            <td>{{ $row['total_reports_by_team_user'] }}</td>
                                        </tr>

                                        @php
                                            $teamPostSum += $row['total_reports_by_team_user'];
                                            $rowNum++;
                                        @endphp

                                        @endif
                                    @endforeach

                                    <tr>
                                        <td colspan="5" style="text-align:right; font-weight:bold;">
                                            Total Posts by {{ $team->name }}
                                        </td>
                                        <td style="font-weight:bold;">{{ $teamPostSum }}</td>
                                    </tr>

                                    @php $totalPosts += $teamPostSum; @endphp

                                @endforeach

                                <tr>
                                    <td colspan="5" style="text-align:right; font-weight:bold;">TOTAL POSTS</td>
                                    <td style="font-weight:bold;">{{ $totalPosts }}</td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- JS --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function(){

    $('#overlay').hide();
    $('#page_content').show();

    $('a[href="#word"]').click(function(){
        $('#report_type').val('word');
        $("#downloadForm").submit();
    });

    $('a[href="#pdf"]').click(function(){
        $('#report_type').val('pdf');
        $("#downloadForm").submit();
    });

    $('a[href="#googledoc"]').click(function(){
        $('#report_type').val('googledoc');
        $("#downloadForm").submit();
    });
});
</script>

@endsection
