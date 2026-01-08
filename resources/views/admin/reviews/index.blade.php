@extends('admin.layouts.admin')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Reviews</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Review</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @elseif(session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error') }}
                                </div>
                            @endif
                            <div class="table-responsive">

                                <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">

                                        <div class="col-sm-12">

                                            <table id="datatable-buttons"
                                                class="table table-striped table-bordered w-100 dataTable no-footer"
                                                role="grid" aria-describedby="datatable-buttons_info">
                                                <thead>
                                                    <tr role="row">

                                                        <th>#</th>
                                                        <th>Website</th>
                                                        <th>Type</th>
                                                        <th>Title</th>
                                                        <th>Posted By User</th>
                                                        <th>Review Posted</th>
                                                        <th>Posted At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($reviews as $key => $review)
                                                        @php
                                                            $created_at = \Carbon\Carbon::createFromTimestamp(strtotime($review['created_at']))->format('d-m-Y');
                                                        @endphp
                                                        <tr class="odd">
                                                            <td>{{ $review->id }}</td>
                                                            <td>{{ ucfirst($review->website) }}</td>
                                                            <td>
                                                                @if ($review->type == 'sm_cal')
                                                                    SM Calendar
                                                                @else
                                                                    Incidence Calendar
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($review->type == 'sm_cal')
                                                                    @if ($review->website == 'info')
                                                                        @foreach ($smCalsInfo as $item)
                                                                            @if ($item->id == $review->item_id)
                                                                                {{ $item->title }}
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        @foreach ($smCalsIssue as $item)
                                                                            @if ($item->id == $review->item_id)
                                                                                {{ $item->title }}
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    @if ($review->website == 'info')
                                                                        @foreach ($incidenceCalsInfo as $item)
                                                                            @if ($item->id == $review->item_id)
                                                                                {{ $item->title }}
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        @foreach ($incidenceCalsIssue as $item)
                                                                            @if ($item->id == $review->item_id)
                                                                                {{ $item->title }}
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>{{ $review->user->name }}</td>
                                                            <td>{{ $review->notes }}</td>
                                                            <td>{{ $created_at }}</td>

                                                            <td>
                                                                {{-- <a class="btn btn-xs btn-{{config('app.color_scheme')}}" href="{{ route('admin.review.edit', $review->id) }}" title="Edit" style="margin-inline: 4px;">Edit</a>                                                           --}}


                                                                @if (!is_null($review->deleted_at))
                                                                    <form
                                                                        action="{{ route('admin.review.restore', $review->id) }}"
                                                                        method="POST" style="display: inline-block;"
                                                                        id="restore_{{ $review->id }}">
                                                                        <input type="hidden" name="_token"
                                                                            value="{{ csrf_token() }}">
                                                                        <input type="button" class="btn btn-xs btn-info"
                                                                            value="Show"
                                                                            onclick="return confirmRestore('restore_{{ $review->id }}');">
                                                                    </form>
                                                                @else
                                                                    <form
                                                                        action="{{ route('admin.review.destroy', $review->id) }}"
                                                                        method="POST" style="display: inline-block;"
                                                                        id="del_{{ $review->id }}">
                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                        <input type="hidden" name="_token"
                                                                            value="{{ csrf_token() }}">
                                                                        <input type="button" class="btn btn-xs btn-danger"
                                                                            value="Hide"
                                                                            onclick="return confirmDelete('del_{{ $review->id }}');">
                                                                    </form>
                                                                @endif

                                                                @if ($review->total_reports == 0)
                                                                    <form
                                                                        action="{{ route('admin.review.delperm', $review->id) }}"
                                                                        method="POST"
                                                                        style="display: inline-block; margin-top:10px;"
                                                                        id="delperm_{{ $review->id }}">
                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                        <input type="hidden" name="_token"
                                                                            value="{{ csrf_token() }}">
                                                                        <input type="button" class="btn btn-xs btn-danger"
                                                                            value="{{ trans('Delete') }} Permanently"
                                                                            onclick="return confirmDelete('delperm_{{ $review->id }}');">
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
    <script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('js/pages/datatables.init.js') }}"></script>
@endpush
