@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Admin User Activity Log</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin Users Activity Log</a></li>
                            <li class="breadcrumb-item active">Activity Log</li>
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
                        <div class="table-responsive">

                            <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">

                                    <div class="col-sm-12">

                                        <table id="datatable-buttons" class="table table-striped table-bordered w-100 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info">
                                            <thead>
                                                <tr role="row">
                                                    <th>#</th>
                                                    <th>Admin User</th>
                                                    <th>Timestamp</th>
                                                    <th>Severity</th>
                                                    <th>Category</th>
                                                    {{-- <th>Target User</th> --}}
                                                    <th>Activity</th>
                                                    <th>IP&nbsp;Address</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($auditlog as $key => $log)
                                                @php
                                                    $created_at = \Carbon\Carbon::createFromTimestamp(strtotime($log->created_at))->format('d-m-Y');  
                                                    $classSeverity =  strtolower(\App\Models\Auditlog::$SEVERITY_LABELS[$log->severity]);                                              
                                                    if($classSeverity == "critical"){
                                                        $classSeverity = "danger";
                                                    }
                                                @endphp                                                                                                  
                                                    <tr class="odd">
                                                        <td>{{$key+1}}</td>
                                                        <td>{{ $log->admin_user_name ?? '' }} 
                                                            <br>{{ $log->admin_user_email ?? '' }}</td>
                                                        <td>{{ $created_at ?? '' }}</td>
                                                        <td>
                                                            <span class="btn btn-{{ $classSeverity }}">
                                                                {{ \App\Models\Auditlog::$SEVERITY_LABELS[$log->severity] ?? '' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $log->category ?? '' }}</td>
                                                        {{-- <td>{{ $log->target_user_name ?? '' }} <br>
                                                            {{ $log->target_user_email ?? '' }}</td> --}}
                                                        <td>{{ $log->activity ?? '' }}</td>
                                                        <td>{{ $log->ip_address ?? '' }}</td>
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
            </div>
        </div>



    </div>
</div>
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
        <script src="{{ asset('js/pages/datatables.init.js') }}"></script>
@endpush
