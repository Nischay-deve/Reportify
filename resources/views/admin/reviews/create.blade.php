@extends('admin.layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Create Issue</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Issue</a></li>
                            <li class="breadcrumb-item active">Create Issue</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card ">

                    <div class="card-body">

                        <form id="add-team" action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="name" class="form-label pt-0 required">Issue Name</label>

                                        <input class="form-control" required type="text" value="" name="name" id="name" placeholder="Enter Issue Name">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label for="name" class="form-label pt-0 required">Status</label>
                                        <select name="active" class="form-select" >
                                            <option value="1" selected>ACTIVE</option>
                                            <option value="0">IN-ACTIVE</option>
                                        </select>
                                    </div>
                                </div>                                       
                            </div>
                            <input type="submit" class="btn btn-{{config('app.color_scheme')}} w-lg" name="submit" value="Save">
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>


    </div>
</div>
@endsection
