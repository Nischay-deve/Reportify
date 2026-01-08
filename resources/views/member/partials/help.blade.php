@php
    if (!isset($helpPageContent)) {
        $helpPageContent = '';
    }
@endphp
@if ($helpPageContent)
    <div class="row" style="margin-top:40px;">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body" style="font-size:15px;">
                    <i class="fa  fa-exclamation-circle text-success" style="font-size:20px; margin:16px;"></i> {{ $helpPageContent->content }}
                </div>

            </div>
        </div>
@endif
