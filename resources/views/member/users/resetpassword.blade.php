@extends('member.layouts.admin')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Reset Password</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Reset</a></li>
                                <li class="breadcrumb-item active">Reset Password</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="card ">

                        <div class="card-body">

                            <form id="add-user" action="{{ route('member.users.updatepassword', [$user->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row gx-3 mb-3">

                                    <div class="col-md-6">
                                            <label for="title" class="form-label pt-0">Password</label>

                                            <input class="form-control" type="password" name="password" id="password"
                                                placeholder="Enter Password">
                                            @error('password')
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

                                            <input type="submit"  style="margin-top: 7px;" class="form-control btn btn-{{ config('app.color_scheme') }} w-lg" name="submit" value="Update" >
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
    <script>
        $('#name').change(function(e) {

            var slug = $(this).val().toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $("#slug").val(slug);
        });

        $(function() {
            CKEDITOR.replace('about_us', {
                extraPlugins: 'youtube'
            });
        });

        $.validator.addMethod("loginRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9\-.\s]+$/i.test(value);
        }, "Name must contain only letters, numbers, Spaces, or Dashes.");

        $("#add-user").submit(function(e) {
            e.preventDefault();
        }).validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 200
                },
                name: {
                    required: true,
                    loginRegex: true
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    </script>
@endsection
