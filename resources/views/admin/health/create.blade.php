@extends('admin.layouts.master')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f85e1c 0%, #ffffff 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 15px 15px 0 0;
    }

    .form-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control:hover {
        border-color: #cbd5e0;
    }

    .textarea-wrapper {
        position: relative;
    }

    .char-count {
        position: absolute;
        bottom: 10px;
        right: 15px;
        font-size: 0.875rem;
        color: #a0aec0;
    }

    .btn-submit {
        background: linear-gradient(135deg, #f46c3e 0%, #ffffff 100%);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: #cbd5e0;
        transform: translateY(-2px);
    }

    .thumbnail-preview {
        border: 2px dashed #cbd5e0;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .thumbnail-preview:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .thumbnail-preview img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .thumbnail-icon {
        font-size: 3rem;
        color: #a0aec0;
        margin-bottom: 1rem;
    }

    .form-note {
        background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-bottom: 1.5rem;
    }

    .form-note i {
        color: #667eea;
        margin-right: 0.5rem;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .preview-loading {
        display: none;
        text-align: center;
        padding: 1rem;
    }

    .preview-loading i {
        font-size: 2rem;
        color: #667eea;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>


@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        {{-- <div class="mb-4">
            <a href="{{ route('admin.healthtips.index') }}" class="btn-back d-inline-flex align-items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Health Tips
            </a>
        </div> --}}

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="form-container">
                    <!-- Form Header -->
                    <div class="form-header text-white position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="h4 mb-2" style="color: white">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Create New Health Tip
                                </h2>
                                <p class="mb-0 opacity-75">Add health tips with automatic thumbnail generation</p>
                            </div>
                            <a href="{{ route('admin.healthtips.index') }}"
                                class="btn-back d-inline-flex align-items-center gap-2 bg-white text-dark px-3 py-2 rounded-3 text-decoration-none">
                                <i class="fas fa-arrow-left"></i>
                                Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <div class="form-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Informational Note -->
                        <div class="form-note mb-4">
                            <i class="fas fa-info-circle"></i>
                            <strong>Pro Tip:</strong> Leave thumbnail empty and add a link to automatically generate
                            thumbnail from the website.
                        </div>

                        <form action="{{ route('admin.healthtips.store') }}" method="POST" enctype="multipart/form-data"
                            id="healthTipForm">
                            @csrf

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-1 text-primary"></i>
                                    Title *
                                </label>
                                <input type="text" name="title" id="title" class="form-control" required
                                    value="{{ old('title') }}" placeholder="Enter health tip title">
                                @error('title')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1 text-primary"></i>
                                    Description *
                                </label>
                                <div class="textarea-wrapper">
                                    <textarea name="description" id="description" class="form-control" rows="6" required
                                        placeholder="Enter detailed health tip description">{{ old('description') }}</textarea>
                                    <div class="char-count">
                                        <span id="charCount">0</span> characters
                                    </div>
                                </div>
                                @error('description')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Link -->
                            <div class="mb-4">
                                <label for="link" class="form-label">
                                    <i class="fas fa-link me-1 text-primary"></i>
                                    Website Link
                                </label>
                                <input type="url" name="link" id="link" class="form-control"
                                    value="{{ old('link') }}" placeholder="https://example.com/article"
                                    onchange="checkLinkForThumbnail()">
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Add a link to automatically fetch thumbnail image from the website.
                                </small>
                                @error('link')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Thumbnail Preview & Upload -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image me-1 text-primary"></i>
                                    Thumbnail Image
                                </label>

                                <!-- Thumbnail Preview -->
                                <div class="thumbnail-preview mb-3" id="thumbnailPreview">
                                    <div class="thumbnail-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="text-muted mb-2">No thumbnail selected</p>
                                    <small class="text-muted">Upload an image or it will be auto-generated from link</small>
                                </div>

                                <!-- Loading Spinner -->
                                <div class="preview-loading mb-3" id="previewLoading">
                                    <i class="fas fa-spinner"></i>
                                    <p class="mt-2">Checking for auto-thumbnail...</p>
                                </div>

                                <!-- File Input -->
                                <input type="file" name="thumbnail_image" id="thumbnail_image" class="form-control"
                                    accept="image/*" onchange="previewImage(this)">
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload a custom thumbnail or leave empty for auto-generation.
                                </small>
                                @error('thumbnail_image')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center pt-3 mt-4 border-top">
                                <a href="{{ route('admin.healthtips.index') }}"
                                    class="btn-back d-inline-flex align-items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn-submit d-inline-flex align-items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    Save Health Tip
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Character counter for description
        document.getElementById('description').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Initialize character count
        document.addEventListener('DOMContentLoaded', function() {
            const description = document.getElementById('description');
            document.getElementById('charCount').textContent = description.value.length;
        });

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('thumbnailPreview');
            const previewLoading = document.getElementById('previewLoading');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.innerHTML = `
                    <img src="${e.target.result}" alt="Thumbnail Preview" class="img-fluid rounded">
                    <small class="text-muted mt-2 d-block">Preview of selected image</small>
                `;
                }

                reader.readAsDataURL(input.files[0]);
                previewLoading.style.display = 'none';
            }
        }

        // Check link for auto-thumbnail (conceptual - backend handles this)
        function checkLinkForThumbnail() {
            const linkInput = document.getElementById('link');
            const fileInput = document.getElementById('thumbnail_image');
            const previewLoading = document.getElementById('previewLoading');
            const preview = document.getElementById('thumbnailPreview');

            // Only show loading if link is valid and no file is selected
            if (linkInput.value && !fileInput.files.length) {
                previewLoading.style.display = 'block';
                preview.innerHTML = `
                <div class="thumbnail-icon">
                    <i class="fas fa-link"></i>
                </div>
                <p class="text-muted mb-2">Will auto-generate thumbnail from link</p>
                <small class="text-muted">Website: ${new URL(linkInput.value).hostname}</small>
            `;

                // Hide loading after 2 seconds
                setTimeout(() => {
                    previewLoading.style.display = 'none';
                }, 2000);
            }
        }

        // Form submission with validation
        document.getElementById('healthTipForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();

            if (!title || !description) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in all required fields (Title and Description)',
                });
            }
        });
    </script>
@endsection
