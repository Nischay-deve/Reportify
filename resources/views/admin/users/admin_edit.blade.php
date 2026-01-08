@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Edit Admin User</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin User</a></li>
                            <li class="breadcrumb-item active">Edit Admin User</li>
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

                        <form id="add-user" action="{{ route('admin.admin_users.update', [$profile->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row gx-3">
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="title" class="form-label pt-0 required">Email</label>

                                        <input class="form-control" type="text" value="{{ $profile->email }}" name="email" id="email" placeholder="Enter Email">
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

                                        <input class="form-control" type="text" name="password" id="password" placeholder="Enter Password">
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

                                        <input class="form-control" type="text" value="{{$profile->name }}" name="name" id="name" placeholder="Enter Name">
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
                                        <select name="gender" class="form-control" id="gender" required="">
                                            <option value="">--Select gender--</option>
                                            <option value="MALE" {{ ($profile->profile->gender=='MALE')? 'selected':'' }}>MALE</option>
                                            <option value="FEMALE" {{ ($profile->profile->gender=='FEMALE')? 'selected':'' }}>FEMALE</option>
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
                                            <input class="form-control" type="text" value="{{ $profile->profile->mobile }}" name="mobile" id="mobile" placeholder="Enter Mobile Number">
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
                                            <input class="form-control" type="date" value="{{ $profile->profile->dob }}" name="dob" id="dob">
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
                                            <input class="form-control" type="text" value="{{ $profile->profile->slug }}" name="slug" id="slug">
                                            @error('slug')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>                                                      

                            </div>
                            <input type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg" name="submit" value="Update">
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>


    </div>
</div>
<script>
    // $("#association").select2();
    $('#name').change(function (e) {

        var slug = $(this).val().toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        $("#slug").val(slug);
    });
</script>
@endsection
