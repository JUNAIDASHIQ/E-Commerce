@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('sub-categories.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" name="subCategoryForm" id="subCategoryForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $sub_category->name }}"
                                        class="form-control" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $sub_category->slug }}"
                                        class="form-control" placeholder="Slug" readonly>
                                    <p></p>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="image_id" id="image_id" value="">
                                    <label for="image">Upload Image</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Drop files here or click to upload.<br><br>
                                        </div>
                                    </div>
                                </div> --}}
                            {{-- @if (!empty($category->image)) --}}
                            {{-- <div>
                                        <img width="250 px" src="{{ asset('uploads/category/' . $sub_category->image)  }}" alt="" >
                                    </div> --}}
                            {{-- @endif --}}
                            {{-- </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $sub_category->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $sub_category->status == 0 ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="showHome">Show On Home</label>
                                    <select name="showHome" id="showHome" class="form-control">
                                        <option {{ $sub_category->showHome == 'Yes' ? 'selected' : '' }} value="Yes">Yes
                                        </option>
                                        <option {{ $sub_category->showHome == 'No' ? 'selected' : '' }} value="No">No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('sub-categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    {{-- <script>
        $('#subCategoryForm').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $.ajax({
                url: '{{ route('categories.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    var errors = response['errors'];
                    if (errors['name']) {
                        $('#name').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['name'])
                    } else {
                        $('#name').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("")
                    }
                    if (errors['slug']) {
                        $('#slug').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['slug'])
                    } 
                        else {
                            $('#slug').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("")
                        }
                },
                error: function(jqXHR, exception) {
                    console.log('Something Went Wrong');
                }
            });
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            $('#subCategoryForm').submit(function(event) {
                // console.log('here');
                event.preventDefault();
                var form = $(this); // Changed the variable name to "form"
                $('button[type=submit]').prop('disabled', true);
                $.ajax({
                    url: '{{ route('sub_categories.update', $sub_category->id) }}',
                    type: 'put',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('button[type=submit]').prop('disabled', false);
                        if (response['status'] == true) {
                            window.location.href = "{{ route('sub-categories.index') }}"
                            $('#name').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                            $('#slug').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        } else {
                            if (response['notFound'] == true) {
                                window.location.href = "{{ route('sub-categories.index') }}";
                            }
                            var errors = response['error'];
                            if (errors['name']) {
                                $('#name').addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback').html(errors['name'])
                            } else {
                                $('#name').removeClass('is-invalid')
                                    .siblings('p')
                                    .removeClass('invalid-feedback').html("")
                            }
                            if (errors['slug']) {
                                $('#slug').addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback').html(errors['slug'])
                            } else {
                                $('#slug').removeClass('is-invalid')
                                    .siblings('p')
                                    .removeClass('invalid-feedback').html("")
                            }
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log('Something Went Wrong');
                    }
                });
            });
            $('#name').change(function() {
                element = $(this);
                $('button[type=submit]').prop('disabled', true);
                $.ajax({
                    url: '{{ route('getSlug') }}',
                    type: 'get',
                    data: {
                        title: element.val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('button[type=submit]').prop('disabled', false);
                        if (response['status'] == true) {
                            $('#slug').val(response['slug'])
                        }
                    }
                });
            });
        });
        // Dropzone.autoDiscover = false;
        // const dropzone = $("#image").dropzone({
        //     init: function() {
        //         this.on('addedfile', function(file) {
        //             if (this.files.length > 1) {
        //                 this.removeFile(this.files[0]);
        //             }
        //         });
        //     },
        //     url: "{{ route('temp-images.create') }}",
        //     maxFiles: 1,
        //     paramName: 'image',
        //     addRemoveLinks: true,
        //     acceptedFiles: "image/jpeg,image/png,image/gif",
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function(file, response) {
        //         $("#image_id").val(response.image_id);
        //         //console.log(response)
        //     }
        // });
    </script>
@endsection
