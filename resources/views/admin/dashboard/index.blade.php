@extends('admin.layouts.admin') @section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('error'))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">

                    <div class="accordion" id="accordionExample">


                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRegistrations">
                                <button class="accordion-button fs-5" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseRegistrations" aria-expanded="false"
                                    aria-controls="collapseRegistrations">
                                    User Registrations
                                </button>
                            </h2>
                            <div id="collapseRegistrations" class="accordion-collapse"
                                aria-labelledby="headingRegistrations" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach ($users as $key => $data)
                                            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                                                <div class="card mb-3">
                                                    <div class="card-header text-white text-center bg-secondary ">
                                                        {{ $data->userType }}
                                                    </div>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">Total Users: {{ $data->totalUsers }}
                                                        </li>
                                                        <li class="list-group-item">Last User Created:
                                                            {{ date('jS F Y', strtotime($data->lastCreatedAt)) }}</li>
                                                    </ul>
                                                    <div class="card-body bg-secondary text-center">
                                                        @if ($data->userType == 'External')
                                                            <a href="{{ route('admin.users.index', ['mode' => 'external']) }}"
                                                                class="btn btn-{{ config('app.color_scheme') }}">View all
                                                                {{ $data->userType }} Users</a>
                                                        @else
                                                            <a href="{{ route('admin.users.index') }}"
                                                                class="btn btn-{{ config('app.color_scheme') }}">View all
                                                                {{ $data->userType }} Users</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingReviews">
                                <button class="accordion-button  collapsed fs-5" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseReviews" aria-expanded="false" aria-controls="collapseReviews">
                                    Reviews
                                </button>
                            </h2>
                            <div id="collapseReviews" class="accordion-collapse collapse" aria-labelledby="headingReviews"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach ($reviews as $key => $data)
                                            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                                                <div class="card mb-3">
                                                    <div class="card-header text-white text-center bg-secondary ">
                                                        Reviews
                                                    </div>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">Total Reviews:
                                                            {{ $data->totalReviews }}</li>
                                                        <li class="list-group-item">Last Query Created:
                                                            {{ date('jS F Y', strtotime($data->lastCreatedAt)) }}</li>
                                                    </ul>
                                                    <div class="card-body bg-secondary text-center">
                                                        <a href="{{ route('admin.review.index') }}"
                                                            class="btn btn-{{ config('app.color_scheme') }}">View all
                                                            reviews</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
@endpush
