@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Role</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Role</a></li>
                            <li class="breadcrumb-item active">User Role</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="card ">
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif
            <div class="card-body">
                <a class="btn btn-{{config('app.color_scheme')}} w-lg" href="{{ route('admin.roles.create') }}">Add Role</a>
            </div>
        </div>
            
        

        <div class="col-md-12 col-xl-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Roles</h5>
                    
                    <div class="table-responsive-sm">
                        <table class="table mb-0 color-table">
                            <thead>
                                <tr class="">
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $key=> $role)
                                    <tr class="table-{{config('app.color_scheme')}}">
                                        <th scope="row">{{ $key+1 }}</th>
                                        <td>{{$role->title}}</td>
                                        <td>
                                            <a class="btn btn-xs btn-{{config('app.color_scheme')}}" href="{{ route('admin.roles.edit', $role->id) }}" title="Edit" style="margin-inline: 4px;">Edit
                                            </a>
                                            <!--<form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('{{ trans('Are You Sure?') }}');"  style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="submit" class="btn btn-xs btn-danger" value="DELETE">
                                            </form>-->
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- end table -->
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
       
    </div>
</div>
@endsection