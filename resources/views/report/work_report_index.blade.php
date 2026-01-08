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
                    <h4 class="mb-0 font-size-18">Work Reports</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Research</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Work Reports</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        <table id="datatable-reports" data-display-length='-1' class="display responsive nowrap table table-striped table-bordered w-100 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info">
                            <thead>
                                <tr role="row">
                                    <th>Sno</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Yesterday Date</th>
                                    <th>Yesterday Reports</th>
                                    <th>Today Date</th>
                                    <th>Today Reports</th>                                    
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($reports as $key => $report)   
                                @php
                                    $yesterdayDate = \Carbon\Carbon::createFromTimestamp(strtotime($report->yesterdayDate))->format('M d, Y');  
                                    $todayDate = \Carbon\Carbon::createFromTimestamp(strtotime($report->todayDate))->format('M d, Y');  
                                @endphp                                                                                                       
                                <tr class="odd">
                                    <td>{{$key+1}}</td>
                                    <td>{{$report->user_name}}</td>
                                    <td>{{$report->user_email}}</td>
                                    <td>{{$yesterdayDate}}</td>
                                    <td>{{$report->yesterdayReports}}</td>
                                    <td>{{$todayDate}}</td>
                                    <td>{{$report->todayReports}}</td>
                                    <td>
                                        <a class="btn btn-xs btn-{{config('app.color_scheme')}}" target="_blank" href="{{ route('report.index', ['user_id' => $report->user_id,'for_date' => $report->yesterdayDate]) }}" title="Edit Yesterday" style="margin-inline: 4px;">
                                            Edit Yesterday
                                        </a>                                          
                                        <a class="btn btn-xs btn-{{config('app.color_scheme')}}"  target="_blank" href="{{ route('report.index', ['user_id' => $report->user_id,'for_date' => $report->todayDate]) }}" title="Edit Today" style="margin-inline: 4px;">
                                            Edit Today
                                        </a>          
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                                 
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

<script>

    $(document).ready(function () {

        $('#overlay').hide();
        $('#page_content').show();


    });



    $(document).ready(function () {
        // Setup - add a text input to each footer cell
        $('#datatable-reports thead tr')
            // .clone(true)
            .addClass('filters')
            .appendTo('#datatable-reports thead');

        var table = $('#datatable-reports').DataTable({
            searching: false,  
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'Bfrtip',
            responsive: true,
            bStateSave: true,
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                'colvis'
            ],                 
        });

        $( "#column_3" ).datepicker({ dateFormat: 'dd-mm-yy', maxDate: "+0D", changeMonth: true, changeYear: true });
        $( "#column_5" ).datepicker({ dateFormat: 'dd-mm-yy', maxDate: "+0D", changeMonth: true, changeYear: true });        

        // $('#datatable-reports_filter input').unbind();
        // $('#datatable-reports_filter input').bind('keyup', function(e) {
        //     if(e.keyCode == 13) {
        //         $('#base_search_keyword').val(this.value);

        //         $('#overlay').show();
        //         $('#page_content').hide();

        //         $('form#base_search').submit();
        //     }
        // });
        

    });    
</script>    

@endsection
@push('scripts')
        <script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <!-- Buttons examples -->
        <script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
        <script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
        <!-- Responsive examples -->
        <script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

        <!-- Datatable init js -->
        {{-- <script src="{{ asset('js/pages/datatables.init.js') }}"></script> --}}

@endpush