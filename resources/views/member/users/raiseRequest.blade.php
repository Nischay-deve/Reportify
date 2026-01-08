@extends('member.layouts.admin')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Raise Request</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Reset</a></li>
                                <li class="breadcrumb-item active">Raise Request</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="card ">

                        <div class="card-body">

                            <form id="sendrequest" action="{{ route('member.users.sendrequest') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row gx-3 mb-3">

                                    <div class="col-md-6">
                                            <label for="title" class="form-label pt-0">Request</label>

                                            <textarea class="form-control" name="comments" id="comments" placeholder="Enter your request!" required></textarea>
                                            @error('comments')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                    </div>

                                    <div class="col-md-3">
                                            <label for="title" class="form-label pt-0"></label>
                                          
                                            <a href="{{ route('calendar.index', $website_slug) }}" title="Cancel" >
                                                <button type="button" class="form-control btn btn-secondary w-lg mr-5"  style="margin-top: 7px;">Cancel</button>
                                            </a>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="title" class="form-label pt-0"></label>
                                        <input type="submit"  style="margin-top: 7px;" class="form-control btn btn-{{ config('app.color_scheme') }} w-lg" name="submit" value="Submit" >
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
            </div>


        </div>
    </div>
    
@endsection
