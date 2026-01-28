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
        background: linear-gradient(135deg, #fc5631 0%, #ffffff 100%);
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
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
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

    .btn-update {
        background: linear-gradient(135deg, #ff3508 0%, #ffffff 100%);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
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

    .current-thumbnail {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        background: #f8fafc;
        margin-bottom: 1rem;
    }

    .current-thumbnail img {
        max-width: 100%;
        max-height: 150px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .form-note {
        background: linear-gradient(135deg, #4CAF5010 0%, #2E7D3210 100%);
        border-left: 4px solid #ff5731;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-bottom: 1.5rem;
    }

    .form-note i {
        color: #e53e3e;
        margin-right: 0.5rem;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .regenerate-checkbox {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 500;
        color: #2d3748;
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .preview-container {
        display: flex;
        gap: 20px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .preview-box {
        flex: 1;
        min-width: 200px;
    }

    .preview-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
</style>


@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->


        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="form-container">
                    <!-- Form Header -->
                    <div class="form-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="h4 mb-2" style="color: white;">
                                    <i class="fas fa-edit me-2"></i>
                                    Edit Health Tip
                                </h2>
                                <p class="mb-0 opacity-75">Update health tip details</p>
                            </div>
                            <div class="">
                                <a href="{{ route('admin.healthtips.index') }}"
                                    class="btn-back d-inline-flex align-items-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    Back to Health Tips
                                </a>
                            </div>
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



                        <form action="{{ route('admin.healthtips.update', $healthTip->id) }}" method="POST"
                            enctype="multipart/form-data" id="editHealthTipForm">
                            @csrf
                            @method('PUT')

                            <!-- Current Thumbnail Preview -->
                            @if ($healthTip->thumbnail_image)
                                <div class="preview-container">
                                    <div class="preview-box">
                                        <div class="preview-title">Current Thumbnail:</div>
                                        <div class="current-thumbnail">
                                            <img src="{{ Storage::url($healthTip->thumbnail_image) }}"
                                                alt="Current Thumbnail" class="img-fluid rounded"
                                                onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'text-center py-3\'><i class=\'fas fa-image fa-2x text-muted mb-2\'></i><p class=\'text-muted mb-0\'>Thumbnail not found</p></div>'">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ basename($healthTip->thumbnail_image) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-1 text-success"></i>
                                    Title *
                                </label>
                                <input type="text" name="title" id="title" class="form-control" required
                                    value="{{ old('title', $healthTip->title) }}" placeholder="Enter health tip title">
                                @error('title')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1 text-success"></i>
                                    Description *
                                </label>
                                <div class="textarea-wrapper">
                                    <textarea name="description" id="description" class="form-control" rows="6" required
                                        placeholder="Enter detailed health tip description">{{ old('description', $healthTip->description) }}</textarea>
                                    <div class="char-count">
                                        <span id="charCount">{{ strlen($healthTip->description) }}</span> characters
                                    </div>
                                </div>
                                @error('description')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Link -->
                            <div class="mb-4">
                                <label for="link" class="form-label">
                                    <i class="fas fa-link me-1 text-success"></i>
                                    Website Link
                                </label>
                                <input type="url" name="link" id="link" class="form-control"
                                    value="{{ old('link', $healthTip->link) }}" placeholder="https://example.com/article">
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Update link to fetch new thumbnail (check regenerate option below).
                                </small>
                                @error('link')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Thumbnail Options -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image me-1 text-success"></i>
                                    Thumbnail Options
                                </label>

                                <!-- Option 1: Upload New Thumbnail -->
                                <div class="mb-3">
                                    <input type="file" name="thumbnail_image" id="thumbnail_image" class="form-control"
                                        accept="image/*">
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Upload a new custom thumbnail image.
                                    </small>
                                    @error('thumbnail_image')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Option 2: Regenerate from Link -->
                                @if ($healthTip->link)
                                    <div class="regenerate-checkbox">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="regenerate_thumbnail" id="regenerate_thumbnail"
                                                value="1">
                                            <div>
                                                <strong>Regenerate thumbnail from link</strong>
                                                <p class="mb-0 text-muted small mt-1">
                                                    <i class="fas fa-sync-alt me-1"></i>
                                                    Fetch new thumbnail from current link. This will replace the current
                                                    thumbnail.
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                <!-- Option 3: Keep Current -->
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Leave both options empty to keep current thumbnail.
                                    </small>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center pt-3 mt-4 border-top">
                                <div>
                                    <a href="{{ route('admin.healthtips.index') }}"
                                        class="btn-back d-inline-flex align-items-center gap-2 me-3">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </a>

                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn-update d-inline-flex align-items-center gap-2">
                                        <i class="fas fa-save"></i>
                                        Update Health Tip
                                    </button>
                                </div>
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

        // Toggle regenerate checkbox when file is uploaded
        document.getElementById('thumbnail_image').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('regenerate_thumbnail').checked = false;
            }
        });

        // Show confirmation when regenerate is checked
        document.getElementById('regenerate_thumbnail').addEventListener('change', function() {
            if (this.checked) {
                const linkValue = document.getElementById('link').value;
                if (!linkValue) {
                    alert('Please enter a link first to regenerate thumbnail.');
                    this.checked = false;
                    return;
                }

                if (confirm(
                        'This will replace the current thumbnail with a new one fetched from the link. Continue?'
                    )) {
                    // Disable file input
                    document.getElementById('thumbnail_image').disabled = true;
                } else {
                    this.checked = false;
                    document.getElementById('thumbnail_image').disabled = false;
                }
            } else {
                document.getElementById('thumbnail_image').disabled = false;
            }
        });

        // Form validation
        document.getElementById('editHealthTipForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const regenerateCheckbox = document.getElementById('regenerate_thumbnail');

            if (!title || !description) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in all required fields (Title and Description)',
                });
                return;
            }

            // Warn if regenerating without link
            if (regenerateCheckbox && regenerateCheckbox.checked && !document.getElementById('link').value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Link Required',
                    text: 'Cannot regenerate thumbnail without a valid link.',
                });
            }
        });

        // Auto-check regenerate if link is changed
        document.getElementById('link').addEventListener('change', function() {
            const currentLink = "{{ $healthTip->link }}";
            const newLink = this.value.trim();

            if (newLink && newLink !== currentLink) {
                const regenerateCheckbox = document.getElementById('regenerate_thumbnail');
                if (regenerateCheckbox && confirm(
                        'Link changed. Do you want to regenerate thumbnail from new link?')) {
                    regenerateCheckbox.checked = true;
                    document.getElementById('thumbnail_image').disabled = true;
                }
            }
        });
    </script>
@endsection
