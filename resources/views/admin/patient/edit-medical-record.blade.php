@extends('admin.layouts.master')

@section('title', 'Edit Medical Record')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="row mt-4">
        <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Edit Medical Record: {{ $record->patient->first_name ?? '' }}
                        {{ $record->patient->last_name ?? '' }}</h1>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.patients.medical-records', $record->patient_id) }}"
                    class="btn btn-secondary btn-sm float-right" style="margin-right: 38px;">
                    <i class="fas fa-arrow-left"></i> Back to Records
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.patients.update-medical-record', $record->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="record_title">Record Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('report_title') is-invalid @enderror" id="record_title" name="report_title"
                                        value="{{ old('report_title', $record->report_title) }}" required>
                                    @error('report_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="report_type">Report Type <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('report_type') is-invalid @enderror" id="report_type" name="report_type"
                                        value="{{ old('report_type', $record->report_type) }}" required>
                                    @error('report_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="record_date">Record Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('record_date') is-invalid @enderror" id="record_date" name="record_date"
                                        value="{{ old('record_date', \Carbon\Carbon::parse($record->record_date)->format('Y-m-d')) }}"
                                        required>
                                    @error('record_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_file">Upload Report</label>
                                    <input type="file" class="form-control @error('report_file') is-invalid @enderror" id="report_file" name="report_file"
                                        accept="image/*,application/pdf" onchange="previewFile()">
                                    @error('report_file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="text-muted">Only image and PDF files are allowed. Maximum size:
                                        2MB.</small>
                                </div>

                                <!-- Current File Preview -->
                                @if ($record->report_file)
                                    <div class="mt-3">
                                        <p class="mb-1"><strong>Current File:</strong></p>
                                        <div id="currentFilePreview" class="border p-2">
                                            @php
                                                // Check file extension using multiple conditions
                                                $filePath = $record->report_file;
                                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                            @endphp

                                            @if (in_array($extension, $imageExtensions))
                                                <img src="{{ asset('storage/' . $filePath) }}" alt="Current Report"
                                                    class="img-fluid" style="max-height: 200px;">
                                                <p class="mt-2">
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                        class="btn btn-sm btn-success ms-2" download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </p>
                                            @elseif($extension === 'pdf')
                                                <div class="alert alert-info">
                                                    <i class="fas fa-file-pdf text-danger"></i> PDF File:
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                        class="ms-2">
                                                        {{ basename($filePath) }}
                                                    </a>
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View PDF
                                                        </a>
                                                        <a href="{{ asset('storage/' . $filePath) }}" download
                                                            class="btn btn-sm btn-success ms-2">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-file"></i> File:
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                        class="ms-2">
                                                        {{ basename($filePath) }}
                                                    </a>
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $filePath) }}" download
                                                            class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" id="remove_file"
                                                name="remove_file">
                                            <label class="form-check-label text-danger" for="remove_file">
                                                <i class="fas fa-trash"></i> Remove current file
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <!-- New File Preview -->
                                <div class="mt-3" id="newFilePreview" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $record->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Medical Record
                                </button>
                                <a href="{{ route('admin.patients.medical-records', $record->patient_id) }}"
                                    class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function previewFile() {
            const fileInput = document.getElementById('report_file');
            const filePreview = document.getElementById('newFilePreview');
            const file = fileInput.files[0];

            // Hide current file preview if new file is selected
            const currentPreview = document.getElementById('currentFilePreview');
            if (currentPreview && file) {
                currentPreview.style.display = 'none';
            }

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    filePreview.style.display = 'block';

                    if (file.type.startsWith('image/')) {
                        filePreview.innerHTML = `
                            <p class="mb-1"><strong>New File Preview:</strong></p>
                            <div class="border p-2">
                                <img src="${e.target.result}" alt="New Report Preview" class="img-fluid" style="max-height: 200px;">
                                <p class="mt-2 text-muted">File: ${file.name} (${(file.size/1024).toFixed(2)} KB)</p>
                            </div>
                        `;
                    } else if (file.type === 'application/pdf') {
                        filePreview.innerHTML = `
                            <p class="mb-1"><strong>New File Preview:</strong></p>
                            <div class="border p-2">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-pdf text-danger"></i> PDF File: ${file.name} (${(file.size/1024).toFixed(2)} KB)
                                </div>
                            </div>
                        `;
                    } else {
                        filePreview.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Cannot preview this file type: ${file.name}
                            </div>
                        `;
                    }
                };

                reader.readAsDataURL(file);
            } else {
                // Show current preview again if file selection is canceled
                if (currentPreview) {
                    currentPreview.style.display = 'block';
                }
                filePreview.style.display = 'none';
                filePreview.innerHTML = '';
            }
        }

        // Validate file size
        document.getElementById('report_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'File size exceeds 2MB. Please upload a smaller file.',
                    confirmButtonText: 'OK'
                });
                e.target.value = '';
                document.getElementById('newFilePreview').style.display = 'none';
                document.getElementById('newFilePreview').innerHTML = '';
            }
        });

        // Handle remove file checkbox
        document.getElementById('remove_file')?.addEventListener('change', function(e) {
            if (e.target.checked) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will remove the current file from the record.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        e.target.checked = false;
                    }
                });
            }
        });
    </script>
@endsection
