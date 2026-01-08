@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">{{$userType}} Users</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                            {{-- <li class="breadcrumb-item active">Add User</li> --}}
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        @if($userType == App\Models\User::TYPE_INTERNAL)
        <div class="card ">
            <div class="card-body">
                <a class="btn btn-{{config('app.color_scheme')}} w-lg" href="{{ route('admin.users.create') }}">Add User</a>
            </div>
        </div>
        @endif

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
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th>Email Verified At</th>
                                                    <th>Role</th>
                                                    <th>Action</th>


                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($users as $key => $user)
                                                    <tr class="odd">
                                                        <td>{{$key+1}}</td>
                                                        <td>{{ $user->name ?? '' }}</td>
                                                        <td>{{ $user->email ?? '' }}</td>
                                                        <td>{{ $user->status ?? '' }}</td>

                                                        @php                                                        
                                                        $email_verification_display = "N/A";
                                                        if(!empty($user->email_verification)){
                                                            $email_verification_display = \Carbon\Carbon::createFromTimestamp($user->email_verification)->format('M d, Y');
                                                        }                                                        
                                                        @endphp                                                        
                                                        <td>{{ $email_verification_display ?? '' }}</td>

                                                        @foreach($user->roles as $key => $item)
                                                        <td>{{ $item->title }}</td>
                                                        @endforeach

                                                       
                                                        <td style="display: flex;">
                                                            <a class="btn btn-xs btn-{{config('app.color_scheme')}}" href="{{ route('admin.users.edit', $user->id) }}" title="Edit" style="margin-inline: 4px;">Edit</a>
                                                            @if($role_id == 1)
                                                            <br>
                                                                @if(!is_null($user->deleted_at))
                                                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display: inline-block;" id="restore_{{$user->id}}">
                                                                        <input type="hidden" name="_method" value="POST">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                        <input type="button" class="btn btn-xs btn-info" value="Show" onclick="return confirmRestore('restore_{{$user->id}}');">
                                                                    </form>                                                                 
                                                                @else
                                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;" id="del_{{$user->id}}">
                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                    {{-- <input type="submit" class="btn btn-xs btn-danger" value="Delete"> --}}
                                                                    <input type="button" class="btn btn-xs btn-danger" value="Hide" onclick="return confirmDelete('del_{{$user->id}}');">
                                                                </form>                                                                
                                                                @endif
                                                            <br>
                                                            <form action="{{ route('admin.users.loginasuser', $user->id) }}" method="POST" style="display: inline-block;margin-inline: 4px;" id="loginasuser_{{$user->id}}">
                                                                <input type="hidden" name="_method" value="POST">
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                <input type="button" class="btn btn-xs btn-info" value="Login As User" onclick="return confirmLoginAsUser('loginasuser_{{$user->id}}');">
                                                            </form>                                                                     
                                                                
                                                            @endif
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
