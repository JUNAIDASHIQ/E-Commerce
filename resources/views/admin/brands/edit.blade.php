@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Brand</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('brands.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" name="brandForm" id="brandForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $brand->name }}" class="form-control"
                                        placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $brand->slug }}" class="form-control"
                                        placeholder="Slug" readonly>
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
                                        <img width="250 px" src="{{ asset('uploads/category/' . $brand->image)  }}" alt="" >
                                    </div> --}}
                                {{-- @endif --}}
                            {{-- </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ ($brand->status == 1 ) ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ ($brand->status == 0 ) ? 'selected' : '' }} value="0">Block</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('brands.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $(document).ready(function() {
            $('#brandForm').submit(function(event) {
                // console.log('here');
                event.preventDefault();
                var form = $(this);
                $('button[type=submit]').prop('disabled', true);
                $.ajax({
                    url: '{{ route('brand.update' , $brand->id) }}',
                    type: 'put',
                    data: form.serialize(), 
                    dataType: 'json',
                    success: function(response) {
                        $('button[type=submit]').prop('disabled', false);
                        if (response['status'] == true) {
                            window.location.href = "{{ route('brands.index') }}"
                            $('#name').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                            $('#slug').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        } else {
                            if (response['notFound'] == true) {
                                window.location.href = "{{ route('brands.index') }}";
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
    </script>
@endsection
