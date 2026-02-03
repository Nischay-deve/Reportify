
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

                    {{-- Ensure report_type exists so JS can set it --}}
                    @if(empty(request('report_type')))
                    <input type="hidden" id="report_type" name="report_type" value="pdf">
                    @endif

                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-{{ config('app.color_scheme') }} dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

                                    {{-- Heading --}}
                                    <table class="tableStyleHeading" cellspacing="0" cellpadding="2" style="text-align: center;">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center;font-size: 15px;font-weight:bold">
                                                    {{ $reportTitle }}
                                                </td>
                                            </tr>

                                            @if ($reportHeading)
                                            <tr>
                                                <td style="text-align: center; font-weight:bold">
                                                    {{ $reportHeading }}
                                                </td>
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
                                                <td style="text-align: center">
                                                    {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td style="text-align: center">
                                                    {{ \Carbon\Carbon::createFromTimestamp(strtotime($from))->format('M d, Y') }}
                                                    To
                                                    {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                    {{-- Report Table --}}
                                    <table class="tableStyle" border="1" cellspacing="0" cellpadding="2" width="75%"
                                        style="border: 1px solid black;text-align:center;" align="center">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" style="border: 1px solid #000000;text-align: center">INDEX</td>
                                            </tr>

                                            <tr>
                                                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="10%">#</td>
                                                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="20%">Team name</td>
                                                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="20%">User name</td>
                                                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="15%">Daily Report Count</td>
                                                <td style="background-color: #FFC000; color: #000000; text-align: center;" width="15%">Base Report Count</td>
                                                <td style="background-color: #d9e2f3; color: #000000; text-align: center;" width="20%">No. NEWS</td>
                                            </tr>

                                            @php
                                            $grandTotalNews = 0;
                                            @endphp

                                            @foreach ($allTeams as $dataTeam)

                                            @php
                                            $totalNewsByTeam = 0;
                                            $hasRows = false;

                                            // ✅ reset numbering per team
                                            $srNo = 1;
                                            @endphp

                                            {{-- ✅ Check team has rows --}}
                                            @foreach ($workReport as $data)
                                            @if (data_get($data, 'team_id') == $dataTeam->id)
                                            @php $hasRows = true; @endphp
                                            @break
                                            @endif
                                            @endforeach

                                            @if ($hasRows)

                                            {{-- ✅ TEAM TITLE ROW --}}
                                            <tr>
                                                <td colspan="6" style="border: 1px solid #000000; font-weight: bold; text-align: left; background: #f2f2f2;">
                                                    {{ ucwords(strtolower($dataTeam->name)) }} Team
                                                </td>
                                            </tr>

                                            {{-- ✅ TEAM ROWS --}}
                                            @foreach ($workReport as $data)
                                            @if (data_get($data, 'team_id') == $dataTeam->id)

                                            @php
                                            $teamNameRow = data_get($data, 'team_name');
                                            $userNameRow = data_get($data, 'user_name');

                                            $dailyCount = (int) data_get($data, 'daily_report_count', 0);
                                            $baseCount = (int) data_get($data, 'base_report_count', 0);
                                            $totalCount = (int) data_get($data, 'total_reports_by_team_user', 0);

                                            $totalNewsByTeam += $totalCount;
                                            @endphp

                                            <tr>
                                                <td style="border: 1px solid #000000; text-align: center;" width="10%">{{ $srNo }}</td>
                                                <td style="border: 1px solid #000000; text-align: left;" width="20%">{{ ucwords(strtolower($teamNameRow)) }}</td>
                                                <td style="border: 1px solid #000000; text-align: center;" width="20%">{{ $userNameRow }}</td>
                                                <td style="border: 1px solid #000000; text-align: center;" width="15%">{{ $dailyCount }}</td>
                                                <td style="border: 1px solid #000000; text-align: center;" width="15%">{{ $baseCount }}</td>
                                                <td style="border: 1px solid #000000; text-align: center;" width="20%">{{ $totalCount }}</td>
                                            </tr>

                                            @php $srNo++; @endphp
                                            @endif
                                            @endforeach

                                            {{-- ✅ TEAM TOTAL --}}
                                            <tr>
                                                <td colspan="5" style="border: 1px solid #000000;text-align: right;font-weight:bold;">
                                                    Total News By {{ ucwords(strtolower($dataTeam->name)) }} Team
                                                </td>
                                                <td style="border: 1px solid #000000; text-align: center;font-weight:bold;">
                                                    {{ $totalNewsByTeam }}
                                                </td>
                                            </tr>

                                            {{-- ✅ Break after each team --}}
                                            <tr>
                                                <td colspan="6" style="border: 0; height: 12px;"></td>
                                            </tr>

                                            @php
                                            $grandTotalNews += $totalNewsByTeam;
                                            @endphp
                                            @endif

                                            @endforeach

                                            {{-- ✅ GRAND TOTAL --}}
                                            <tr>
                                                <td colspan="5" style="border: 1px solid #000000;text-align: right;font-weight:bold;">
                                                    Total NEWS
                                                </td>
                                                <td style="border: 1px solid #000000; text-align: center;font-weight:bold;">
                                                    {{ $grandTotalNews }}
                                                </td>
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

        $('a[href="#word"]').click(function(e) {
            e.preventDefault();
            $('#report_type').val('word');
            $("#downloadForm").submit();
        });

        $('a[href="#pdf"]').click(function(e) {
            e.preventDefault();
            $('#report_type').val('pdf');
            $("#downloadForm").submit();
        });

        $('a[href="#googledoc"]').click(function(e) {
            e.preventDefault();
            $('#report_type').val('googledoc');
            $("#downloadForm").submit();
        });
    });
</script>

@endsection
