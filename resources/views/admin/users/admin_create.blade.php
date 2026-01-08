@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Create Admin User</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin User</a></li>
                            <li class="breadcrumb-item active">Create Admin User</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif        

        
        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card ">

                    <div class="card-body">

                        <form id="add-user" action="{{ route('admin.admin_users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="title" class="form-label pt-0 required">Email</label>

                                        <input class="form-control" type="email" value="" name="email" id="email" placeholder="Enter Email" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="title" class="form-label pt-0">Password</label>

                                        <input class="form-control" type="password" name="password" id="password" placeholder="Enter Password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="title" class="form-label pt-0 required">Full Name</label>

                                        <input class="form-control" type="text" value="{{ old('name', isset($users) ? $users->name : '') }}" name="name" id="name" placeholder="Enter Name" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="title" class="form-label pt-0">Gender</label>
                                        <select name="gender" class="form-control" id="gender">
                                            <option value="">--Select gender--</option>
                                            <option value="MALE">MALE</option>
                                            <option value="FEMALE">FEMALE</option>
                                        </select>
                                       @error('gender')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="mobile" class="form-label pt-0">Mobile</label>
                                        <div class="">
                                            <input class="form-control" type="text" value="{{ old('mobile') }}" name="mobile" id="mobile" placeholder="Enter Mobile Number">
                                            @error('mobile')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="dob" class="form-label pt-0">DOB</label>
                                        <div class="">
                                            <input class="form-control" type="date" value="{{ old('dob') }}" name="dob" id="dob">
                                            @error('dob')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="slug" class="form-label pt-0 required">Slug</label>
                                        <div class="">
                                            <input class="form-control" type="text" value="{{ old('slug') }}" name="slug" id="slug" required>
                                            @error('slug')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                               

                            </div>
                            <!-- <button type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg mt-2">Add Role</button> -->
                            <input type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg" name="submit" value="Save">
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>


    </div>
</div>
<script>
    $('#name').change(function (e) {
        var slug = $(this).val().toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        $("#slug").val(slug);
    });
</script>
@endsection
