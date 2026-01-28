@extends('staff.layouts.master')

@section('title', 'Room Types Management')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
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
    <div class="container-fluid">
        <!-- Header -->
        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="h3 text-orange mb-0">
                            <i class="fas fa-tags me-2"></i>Room Types Management
                        </h1>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('staff.rooms.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i> Back to Room
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addRoomTypeModal">
                                <i class="fas fa-plus me-1"></i> Add New Type
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Room Types Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Room Types</h6>
                <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="refreshTable">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="roomTypesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Base Price</th>
                                <th>Hourly Rate</th>
                                <th>Max Capacity</th>
                                <th>Available Rooms</th>
                                <th>Current Utilization</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; ?>
                            @forelse($roomTypes as $roomType)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $roomType->name }}</td>
                                    <td>₹{{ number_format($roomType->base_price, 2) }}</td>
                                    <td>₹{{ number_format($roomType->hourly_rate, 2) }}</td>
                                    <td>{{ $roomType->max_capacity }}</td>
                                    <td>{{ $roomType->available_rooms }}</td>
                                    <td>{{ $roomType->current_utilization }}</td>
                                    <td>
                                        @if ($roomType->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-room-type"
                                            data-id="{{ $roomType->id }}" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('staff.room-types.destroy', $roomType->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete {{ $roomType->name }}?')"
                                                data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No room types found. Add your first room type!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Type Modal -->
    <div class="modal fade" id="addRoomTypeModal" tabindex="-1" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addRoomTypeForm" action="{{ route('staff.room-types.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRoomTypeModalLabel">Add New Room Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="base_price" class="form-label">Base Price <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="base_price" name="base_price"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate</label>
                                <input type="number" step="0.01" class="form-control" id="hourly_rate"
                                    name="hourly_rate">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_capacity" class="form-label">Max Capacity <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_capacity" name="max_capacity"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="available_rooms" class="form-label">Available Rooms</label>
                                <input type="number" class="form-control" id="available_rooms" name="available_rooms">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="current_utilization" class="form-label">Current Utilization</label>
                                <input type="number" class="form-control" id="current_utilization"
                                    name="current_utilization">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amenities</label>
                            <div class="row">
                                @php
                                    $commonAmenities = [
                                        'WiFi',
                                        'AC',
                                        'TV',
                                        'Mini Bar',
                                        'Room Service',
                                        'Breakfast',
                                        'Parking',
                                        'Swimming Pool',
                                    ];
                                @endphp
                                @foreach ($commonAmenities as $amenity)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="amenities[]"
                                                value="{{ $amenity }}" id="amenity_{{ $loop->index }}">
                                            <label class="form-check-label" for="amenity_{{ $loop->index }}">
                                                {{ $amenity }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Room Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Type Modal -->
    <div class="modal fade" id="editRoomTypeModal" tabindex="-1" aria-labelledby="editRoomTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editRoomTypeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoomTypeModalLabel">Edit Room Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="editFormContent">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Room Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: "{{ session('success') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: "{{ session('error') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            $('#roomTypesTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [8] // Actions column
                }],
                order: [
                    [0, 'asc']
                ]
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Edit Room Type - Load data into modal
            $(document).on('click', '.edit-room-type', function() {
                var roomTypeId = $(this).data('id');
                var url = "{{ route('staff.room-types.edit', ':id') }}".replace(':id', roomTypeId);
                var updateUrl = "{{ route('staff.room-types.update', ':id') }}".replace(':id', roomTypeId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editFormContent').html(response);
                        $('#editRoomTypeForm').attr('action', updateUrl);
                        $('#editRoomTypeModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error loading room type data'
                        });
                    }
                });
            });

            // Handle edit form submission
            $('#editRoomTypeForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editRoomTypeModal').modal('hide');
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'Room type updated successfully!',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = 'Please fix the following errors:\n';

                        for (var field in errors) {
                            errorMessage += '- ' + errors[field][0] + '\n';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorMessage
                        });
                    }
                });
            });

            // Refresh table
            $('#refreshTable').on('click', function() {
                location.reload();
            });

            // Reset edit modal when closed
            $('#editRoomTypeModal').on('hidden.bs.modal', function() {
                $('#editRoomTypeForm').trigger('reset');
            });
        });
    </script>
@endsection
