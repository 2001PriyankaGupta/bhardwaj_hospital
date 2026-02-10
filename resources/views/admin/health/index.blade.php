@extends('admin.layouts.master')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    /* Compact Styles */
    .health-tip-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        border: 1px solid #e9ecef;
    }

    .health-tip-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .thumbnail-container {
        height: 160px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumbnail-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .default-thumbnail {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .default-thumbnail i {
        font-size: 24px;
        color: white;
    }

    .tip-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .tip-description {
        color: #6c757d;
        font-size: 13px;
        line-height: 1.4;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-body {
        padding: 15px;
    }

    .btn-action {
        padding: 5px 12px;
        font-size: 13px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: #6c5ce7;
        color: white;
        border: none;
    }

    .btn-edit:hover {
        background: #5b4bd8;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: translateY(-1px);
    }

    .link-badge {
        background: #27ae60;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }

    .stats-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .stats-1 .stats-icon {
        background: rgba(108, 92, 231, 0.1);
        color: #6c5ce7;
    }

    .stats-2 .stats-icon {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
    }

    .stats-value {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1;
    }

    .stats-label {
        color: #6c757d;
        font-size: 13px;
        margin-top: 5px;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
    }

    .empty-state-icon {
        font-size: 48px;
        color: #adb5bd;
        margin-bottom: 15px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .page-subtitle {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Compact spacing */
    .compact-spacing {
        margin-top: -5px;
        margin-bottom: -5px;
    }

    .compact-row {
        margin-left: -8px;
        margin-right: -8px;
    }

    .compact-col {
        padding-left: 8px;
        padding-right: 8px;
    }
</style>


@section('content')
    <div class="container-fluid py-3">
        <!-- Compact Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="page-title">Health Tips</h1>
                <p class="page-subtitle">Manage health tips with auto thumbnails</p>
            </div>
            <a href="{{ route('admin.healthtips.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="fas fa-plus"></i>
                Add New
            </a>
        </div>

        <!-- Compact Stats -->
        <div class="row mb-3 compact-spacing">
            <div class="col-md-6 compact-col">
                <div class="stats-card stats-1 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-value">{{ $healthTips->count() }}</div>
                            <div class="stats-label">Total Tips</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 compact-col">
                <div class="stats-card stats-2 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-value">{{ $healthTips->whereNotNull('thumbnail_image')->count() }}</div>
                            <div class="stats-label">Auto Thumbnails</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Tips Grid -->
        <div class="row compact-row">
            @if ($healthTips->count() > 0)
                @foreach ($healthTips as $tip)
                    <div class="col-lg-4 col-md-6 compact-col mb-3">
                        <div class="card health-tip-card h-100">
                            <!-- Thumbnail -->
                            <div class="thumbnail-container position-relative">
                                @if ($tip->thumbnail_image)
                                    <img src="{{ asset('storage/'.$tip->thumbnail_image) }}" alt="{{ $tip->title }}"
                                        class="thumbnail-img"
                                        onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'default-thumbnail\'><i class=\'fas fa-heartbeat\'></i></div>'">
                                @else
                                    <div class="default-thumbnail">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                @endif
                                @if ($tip->link)
                                    <a href="{{ $tip->link }}" target="_blank" class="link-badge" title="Visit Link">
                                        <i class="fas fa-link"></i>
                                    </a>
                                @endif
                            </div>

                            <!-- Card Content -->
                            <div class="card-body d-flex flex-column">
                                <h6 class="tip-title">{{ $tip->title }}</h6>
                                <p class="tip-description flex-grow-1">
                                    {{ Str::limit(strip_tags($tip->description), 80) }}
                                </p>

                                <!-- Compact Footer -->
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $tip->created_at->format('M d') }}
                                    </small>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.healthtips.edit', $tip->id) }}"
                                            class="btn btn-warning btn-sm btn-action" style="height: 25px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.healthtips.destroy', $tip->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-action"
                                                onclick="return confirm('Delete this tip?')" style="height: 25px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h5 class="mb-2">No Health Tips Yet</h5>
                        <p class="text-muted mb-3 small">Start adding health tips to help your users</p>
                        <a href="{{ route('admin.healthtips.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Create First Tip
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            @if (session('success'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    width: '300px',
                    padding: '10px'
                })

                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}',
                    background: '#f8f9fa'
                })
            @endif

            @if (session('error'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    width: '300px',
                    padding: '10px'
                })

                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}',
                    background: '#f8f9fa'
                })
            @endif
        });
    </script>
@endsection
