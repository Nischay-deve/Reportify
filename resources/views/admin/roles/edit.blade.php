@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Edit Role</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Role</a></li>
                            <li class="breadcrumb-item active">Edit Role</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
       
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    
                    <div class="card-body">
                        
                        <form action="{{ route('admin.roles.update', [$role->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3 ">
                                <label for="title" class="form-label pt-0">Title</label>
                                <div class="">
                                    <input class="form-control" type="text" value="{{ old('title', isset($role) ? $role->title : '') }}" name="title" id="title" placeholder="Enter Role">
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- <button type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg mt-2">Add Role</button> -->
                            <input type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg" name="submit" value="Update">
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
        

    </div> 
</div>
@endsection