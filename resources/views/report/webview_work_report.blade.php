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

        <div class="container-fluid" id="page_content" style="display: none;">

            <div class="row">

                <div class="col-12">

                    <div class="page-title-box d-flex align-items-center justify-content-between">

                        <h4 class="mb-0 font-size-18">Download {{ $reportTitle }}</h4>

                    </div>

                </div>

            </div>

            <div class="row">
                <div class="col-md-4"></div>

                <div class="col-md-4 fixed-button">
                    <form action="{{ route('report.pdfview', $website_slug) }}" method="get" autocomplete="off" id="downloadForm">
                        @php
                        foreach ($_GET as $name => $value) {                                         
                            
                            if ($name !== 'view_reports') {
                                if(is_array($value)){
                                    foreach($value as $key => $keyValue){
                                        $name = htmlspecialchars($name);
                                        $keyValue = htmlspecialchars($keyValue);                                            
                                        echo '<input type="hidden" name="' . $name . '[]" value="' . $keyValue . '">';
                                    }
                                }else{
                                    $name = htmlspecialchars($name);
                                    $value = htmlspecialchars($value);                                        
                                    echo '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '">';
                                }
                            }
                        }
                        @endphp
                        {{-- <button type="submit" name="filter" class="btn btn-{{config('app.color_scheme')}} form-control"><i
                        class="mdi mdi-download"></i> Download Report</button> --}}

                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                              <button id="btnGroupDrop1" type="button" class="btn btn-{{config('app.color_scheme')}} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Download Report <i class="mdi mdi-chevron-down"></i> 
                              </button>
                              <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item" href="#word"><i class="mdi mdi-download"></i> Download Word Report</a>
                                <a class="dropdown-item" href="#pdf"><i class="mdi mdi-download"></i> Download Pdf Report</a>
                                <a class="dropdown-item" href="#googledoc"><i class="mdi mdi-download"></i> Download Google Doc Report</a>
                              </div>
                            </div>
                        </div>                        
                    </form>
                </div>
                
                <div class="col-md-4"></div>
            </div>

            <div class="row">

                <div class="col-md-12 col-xl-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive">
                                <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">



                                        <table class="tableStyleHeading" cellspacing="0" cellpadding="2"
                                            style="text-align: center;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center;font-size: 15px;font-weight:bold">
                                                        {{ $reportTitle }}</td>
                                                </tr>
                                                @if ($reportHeading)
                                                    <tr>
                                                        <td style="text-align: center; font-weight:bold">
                                                            {{ $reportHeading }}</td>
                                                    </tr>
                                                @endif
                                                @if ($reportDescription)
                                                    <tr>
                                                        <td style="text-align: center">{{ $reportDescription }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($teamName))
                                                    <tr>
                                                        <td style="text-align: center">{{ $teamName->name }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($moduleName))
                                                    <tr>
                                                        <td style="text-align: center">{{ $moduleName->name }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($chapterName))
                                                    <tr>
                                                        <td style="text-align: center">{{ $chapterName->name }}</td>
                                                    </tr>
                                                @endif

                                                @if ($from == $to)
                                                    <tr>
                                                        <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
                                                    </tr>
                                                @else<tr>
                                                        <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($from))->format('M d, Y') }} To
                                                            {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="75%"
                                            style="border: 1px solid black;text-align:center;" align="center">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="border: 1px solid #000000;text-align: center">
                                                        INDEX</td>
                                                </tr>
                                                <tr>
                                                    <td style="background-color: #d9e2f3; color: #000000; text-align: center;"
                                                        width="10%">#</td>
                                                    <td style="background-color: #FFC000; color: #000000; text-align: center;"
                                                        width="20%">Team name</td>
                                                    <td style="background-color: #FFC000; color: #000000; text-align: center;"
                                                        width="20%">User name</td>
                                                    <td style="background-color: #FFC000; color: #000000; text-align: center;"
                                                        width="15%">Daily Report Count</td>
                                                    <td style="background-color: #FFC000; color: #000000; text-align: center;"
                                                        width="15%">Base Report Count</td>                                                                                                                
                                                    <td style="background-color: #d9e2f3; color: #000000; text-align: center;"
                                                        width="20%">No. NEWS</td>
                                                </tr>
                                                @php
                                                    $totalNews = 0;
                                                @endphp
                                                @foreach ($allTeams as $keyTeam => $dataTeam)
                                                    @php
                                                        $totalNewsByTeam = 0;
                                                    @endphp

                                                    @foreach ($workReport as $key => $data)
                                                        @if ($data['team_id'] == $dataTeam->id)
                                                            <tr>
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="10%">{{ $key + 1 }}
                                                                </td>
                                                                <td style="border: 1px solid #000000; text-align: left;"
                                                                    width="20%">
                                                                    {{ ucwords(strtolower($data['team_name'])) }}</td>
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="20%">
                                                                    {{ $data['user_name'] }}</td>
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="15%">
                                                                    {{ $data['daily_report_count'] }}</td>
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="15%">
                                                                    {{ $data['base_report_count'] }}</td>                                                                                                                                        
                                                                <td style="border: 1px solid #000000; text-align: center;"
                                                                    width="20%">
                                                                    {{ $data['total_reports_by_team_user'] }}</td>
                                                            </tr>
                                                            @php
                                                                $totalNewsByTeam = $totalNewsByTeam + $data['total_reports_by_team_user'];
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    <tr>
                                                        <td colspan="5"
                                                            style="border: 1px solid #000000;text-align: right;font-weight:bold;">
                                                            Total News By
                                                            {{ ucwords(strtolower($dataTeam->name)) }} Team</td>
                                                        <td
                                                            style="border: 1px solid #000000; text-align: center;font-weight:bold;">
                                                            {{ $totalNewsByTeam }}
                                                        </td>
                                                    </tr>

                                                    @php
                                                        $totalNews = $totalNews + $totalNewsByTeam;
                                                    @endphp
                                                @endforeach
                                                <tr>
                                                    <td colspan="5"
                                                        style="border: 1px solid #000000;text-align: right;font-weight:bold;">
                                                        Total NEWS</td>
                                                    <td
                                                        style="border: 1px solid #000000; text-align: center;font-weight:bold;">
                                                        {{ $totalNews }}</td>
                                                </tr>
                                            </tbody>
                                        </table>



                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>

                </div>

            </div>

        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#overlay').hide();
            $('#page_content').show();
            $('.dropdown-toggle').dropdown();

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
