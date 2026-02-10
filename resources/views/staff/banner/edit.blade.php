@extends('staff.layouts.master')

@section('title', 'Edit Banner')

<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .swal2-toast {
        font-size: 12px !important;
        padding: 6px 10px !important;
        min-width: auto !important;
        width: 220px !important;
        line-height: 1.3em !important;
    }

    .swal2-toast .swal2-icon {
        width: 24px !important;
        height: 24px !important;
        margin-right: 6px !important;
    }

    .swal2-toast .swal2-title {
        font-size: 13px !important;
    }
</style>
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <h3 class="card-title">Edit Banner</h3>
                        <a href="{{ route('staff.banner.index') }}" class="btn btn-secondary btn-sm float-right">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div> --}}

                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Banner</h4>
                        <a href="{{ route('staff.banner.index') }}" class="btn btn-secondary btn-sm float-right">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('staff.banner.update', $banner) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mt-3">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" name="title" value="{{ old('title', $banner->title) }}"
                                            placeholder="Enter banner title">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3" placeholder="Enter banner description">{{ old('description', $banner->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mt-3">
                                                <label for="sort_order">Sort Order</label>
                                                <input type="number"
                                                    class="form-control @error('sort_order') is-invalid @enderror"
                                                    id="sort_order" name="sort_order"
                                                    value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                                                @error('sort_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-3">
                                                <label for="status">Status</label>
                                                <select class="form-control @error('status') is-invalid @enderror"
                                                    id="status12" name="status">
                                                    <option value="1"
                                                        {{ old('status', $banner->status) == 1 ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="0"
                                                        {{ old('status', $banner->status) == 0 ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mt-3">
                                        <label for="image">Banner Image</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="form-control custom-file-input @error('image') is-invalid @enderror"
                                                id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">
                                            Recommended size: 1920x600px, Max size: 2MB
                                        </small>
                                        <div class="mt-2">
                                            @if ($banner->image)
                                                <img src="{{ asset('storage/'.$banner->image) }}" alt="Current Image"
                                                    style="max-width: 100%; max-height: 200px; margin-top: 10px;">
                                                <p class="text-muted small mt-1">Current Image</p>
                                            @endif
                                            <img id="imagePreview" src="" alt="New Image Preview"
                                                style="display: none; max-width: 100%; max-height: 200px; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Banner
                                </button>
                                <a href="{{ route('staff.banner.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Image preview
            $('#image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endsection
