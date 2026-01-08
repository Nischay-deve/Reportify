@extends('layouts.calendar')
@section('title','Daily report - '.$selectedDate)
@section('content')
<style>
    @mobile
        .positionContainer {        
            position: absolute; 
            top:150px;
        }

    @else
        .positionContainer {
            position: absolute;
            top: 100px;
        }

                           
    @endmobile 
</style>

    <div class="cal-page-content positionContainer">

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

                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif


                @include('partials.daily_monitoring')    

            </div>

        </div>
    </div>

    <!-- Creates the bootstrap modal to show the calender -->
    <div class="modal fade" id="calendarDateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center">
                    <h5 class="modal-title">Select Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="margin-left: auto; margin-right: auto;">
                    <div id="selectedDate"></div>
                </div>
                <div class="modal-footer" id="footer">
                    <button type="button" class="btn btn-{{ config('app.color_scheme') }}" data-bs-dismiss="modal"
                        aria-label="Close">Close <i class="fa fa-remove mr-1"></i></button>            
                </div>                            
            </div>
        </div>
    </div>  
@endsection



@push('styles')
   
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#overlay').hide();
            $('#page_content').show();
        });
    </script>

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            $("#selectedDate").datepicker({
                inline: true,
                dateFormat: 'dd-mm-yy',
                maxDate: "+0D",
                changeMonth: true,
                changeYear: true,
                defaultDate: '{{$selectedDate}}',
                onSelect: function(date) {
                    var url = "{{ url($website_slug.'/daily-monitoring-report') }}/"+date;
                    window.location.href=url;
                }                
            });


            $("#dateHeading").click(function(){
                $('#calendarDateModal').modal('show');
            });

        });

    </script>
@endpush
