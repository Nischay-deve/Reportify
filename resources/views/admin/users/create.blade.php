@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Create User</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Team</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">User</a></li>
                            <li class="breadcrumb-item active">Create User</li>
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

                        <form id="add-user" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
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
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="roles" class="form-label pt-0 required">Roles</label>
                                        <select name="roles" class="form-control" id="roles" required="" >
                                            <option value="">--Select roles--</option>
                                            @foreach($roles as $id=> $roles)
                                            <option value="{{$id}}">{{$roles}}</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                </div>


{{-- Extra --}}
                               

<div class="col-md-6">
    <div class="mb-3 ">
        <label class="form-label" for="place">Place</label>
        @php
            $placeExisting = old('place_id');
        @endphp
        <select class="form-select" name="place_id" id="place"
            autocomplete="off" style="width: 100%">
            <option value="">--Select Place--</option>
            @foreach ($locations as $key => $location)
                <option value="{{ $location->id }}"
                    @if ($placeExisting == $location->id) selected @endif>
                    {{ $location->name }}</option>
            @endforeach
        </select>

    </div>
</div>

<div class="col-md-6">
    <div class="mb-3 ">
        <label class="form-label" for="occupation">Occupation</label>
        <input type="text" id="occupation" name="occupation"
            class="form-control @error('occupation') is-invalid @enderror"
            value="{{ old('occupation') }}" placeholder="Enter occupation"
            required />
        @error('occupation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

    </div>
</div>

<div class="col-md-12">
    <div class="mb-3 ">
        <label class="form-label" for="registerMoreInfo">More info</label>
        <textarea class="form-control @error('moreinfo') is-invalid @enderror" name="moreinfo" id="moreinfo"
            placeholder="Enter moreinfo" rows="3" required>{{ old('moreinfo') }}</textarea>
        @error('moreinfo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>               

<div class="col-md-6">
    <div class="mb-3 ">
        <label class="form-label" for="reference">Name of Person who Referred you here</label>
        <input type="text" id="reference" name="reference"
        class="form-control @error('reference') is-invalid @enderror"
        value="{{ old('reference') }}" placeholder="Enter name of Person who referred you here" />          
        @error('reference')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>   

<div class="col-md-6">
    <div class="mb-3 ">
        <label class="form-label" for="reference_mobile">Mobile of Person who Referred you here</label>
        <input type="text" id="reference_mobile" name="reference_mobile"
        class="form-control @error('reference_mobile') is-invalid @enderror"
        value="{{ old('reference_mobile') }}" placeholder="Enter mobile of Person who referred you here" />          
        @error('reference_mobile')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>   

<div class="col-md-6">
    <div class="mb-3 ">
        <label class="form-label" for="place">Status</label>
        @php
            $statusExisting = old('status');
        @endphp
        <select class="form-select" name="status" autocomplete="off" style="width: 100%">
            <option value="">--Select Status--</option>
            @foreach (App\Models\User::$STATUS_LABELS as $key => $label)
                <option value="{{ $label }}"
                    @if ($statusExisting == $label) selected @endif>
                    {{ $label }}</option>
            @endforeach
        </select>

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

    $('#roles').on('change', function() {
    if ( this.value == {{config('app.center_head_role_id')}})
    //.....................^.......
    {
        $("#user_list").show();
        $('#user_id').prop('required', true);   
    }
    else
    {
        $("#user_list").hide();
        $('#user_id').prop('required', false);   
    }
    });

    // $(function () {
    //     CKEDITOR.replace('about_us', {extraPlugins: 'youtube'});
    // });

    // $.validator.addMethod("loginRegex", function(value, element) {
    //     return this.optional(element) || /^[a-z0-9\-.\s]+$/i.test(value);
    // }, "Name must contain only letters, numbers, Spaces, or Dashes.");

    // $("#add-user").submit(function (e) {
    //     e.preventDefault();
    // }).validate({
    //     rules: {
    //         email: {required: true, email: true, maxlength: 200},
    //         name: {required: true, loginRegex: true},
    //     },
    //     submitHandler: function (form) {
    //         form.submit();
    //     }
    // });
</script>
@endsection
