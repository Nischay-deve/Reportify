@extends('layouts.admin_full')
@section('title','Download Report')

@section('content')

<style>
    #overlay {
        position: fixed;
        /* top:100px; */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /* background: black url(spinner.gif) center center no-repeat; */
        background-color: rgba(16, 16, 16, 0.5);
        /* opacity: .9; */
        z-index: 1;
    }

    .faqHeading {
        text-align: center;
        font-weight: bold;
    }

    .accordion-button {
        font-weight: bold;
    }

    .faqSno {
        margin-right: 10px;
        color: #000000;
        font-weight: normal;
    }

    .faqAnswer {
        margin-left: 20px;
    }

    .faqReport {
        margin-top: 10px;
    }

    .cardFullHeight {
        height: 100%;
    }

    .documentSno {
        margin-right: 10px;
        color: #000000;
        font-weight: normal;
    }

    .documentAnswer {
        margin-left: 20px;
    }

    .documentReport {
        margin-top: 10px;
    }

    .documentHeading {
        text-align: center;
        font-weight: bold;
    }

    @mobile
        .positionContainer {        
            position: absolute; 
            top:140px;
            width: 98%;
        }
    @else
        .positionContainer {
            position: absolute;
            top: 20px;
            width: 98%;
        }                        
    @endmobile           
</style>

<!-- Creates the bootstrap modal to show the center options -->
<div class="modal fade" id="loadCenterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <h5 class="modal-title">Show content from:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align:left; padding:20px;">
                <form class="d-flex" action="" id="center-form" method="get"
                    autocomplete="off">
                    <table class="table table-striped table-hover">
                        <tbody>
                            @php
                                $websiteIdsFroSessionArr = explode(",", session()->get('website_id_databse_search'));
                            @endphp
                            @foreach ($websites as $website)
                                <tr>
                                    <td>
                                        <div class="form-check form-switch form-switch-md">
                                            <input class="form-check-input" name="center_website[]"
                                                id="check_{{ $website->id }}" type="checkbox"
                                                value="{{ $website->id }}"
                                                @if (in_array($website->id, $websiteIdsFroSessionArr) ) checked @endif>
                                            <label class="form-check-label switchLabel"
                                                for="check_{{ $website->id }}">{{ $website->name }}</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-{{ config('app.color_scheme') }}"
                                        id="centerSubmitButton">Submit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="page-content positionContainer">

    <div class="container-fluid" id="overlay">
        <div class="row">
            <div class="col-12" style="text-align: center;">
                <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                Loading...
            </div>
        </div>
    </div>

    <div class="container-fluid" id="page_content" style="display: none;">

        <div class="row">

            <div class="col-12">

                <div class="page-title-box d-flex align-items-center justify-content-between">

                    <h4 class="mb-0 font-size-18">Download SM Monitoring Report Here</h4>

                    <div class="page-title-right">

                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item"><a href="javascript: void(0);">Research</a></li>

                            <li class="breadcrumb-item"><a href="javascript: void(0);">Download SM Report</a></li>

                        </ol>

                    </div>

                </div>

            </div>

        </div>



        <div class="row">

            {{-- Search Reports --}}
            <div class="col-md-12">
                <div class="card cardFullHeight">
                    <div class="card-body">

                        @if (session()->has('success'))
                            <div class="alert alert-success">

                                {{ session()->get('success') }}

                            </div>
                        @endif

                        @include('partials.download-sm-report-search-form')

                    </div>
                </div>
            </div>


        </div>

    </div>

</div>

@endsection
