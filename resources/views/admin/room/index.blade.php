@extends('admin.layouts.master')

@section('title', 'Room Management')

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
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

    td {
        font-size: 12px;
    }

    :root {
        --primary-color: #FF6B35;
        --primary-light: #FF8E6B;
        --primary-dark: #E55A2B;
        --light-bg: #F8F9FA;
        --border-radius: 12px;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f5f7fb;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .text-orange {
        color: var(--primary-color) !important;
    }

    .bg-orange {
        background-color: var(--primary-color) !important;
    }

    .btn-orange {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transition: var(--transition);
    }

    .btn-orange:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .btn-outline-orange {
        color: var(--primary-color);
        border-color: var(--primary-color);
        transition: var(--transition);
    }

    .btn-outline-orange:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .border-orange {
        border-color: var(--primary-color) !important;
    }

    .card {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        font-weight: 600;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: rgba(255, 107, 53, 0.05);
    }

    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 10px;
        background-color: var(--primary-color);
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
    }

    .modal-content {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.6rem 0.75rem;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
    }

    .stats-card {
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background-color: var(--primary-color);
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .action-buttons {
        position: absolute;
        right: 20px;
        display: flex;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .action-buttons {
            position: static;
            margin-top: 1rem;
            justify-content: flex-end;
        }
    }
</style>

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div class="bg-orange rounded-circle p-3 me-3">
                    <i class="fas fa-bed fa-lg text-white"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Room Management</h1>
                    <p class="text-muted mb-0">Manage all hospital rooms and their status</p>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roomModal" id="addRoomBtn">
                    <i class="fas fa-plus me-1"></i>Add New Room
                </button>
                <a class="btn btn-primary" href="{{ route('admin.room-types.index') }}">
                    <i class="fas fa-list me-1"></i>Room Types
                </a>
            </div>
        </div>



        <!-- Rooms Table -->
        <div class="card">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="roomTable">
                        <thead class="table-light">
                            <tr>
                                <th>Room No.</th>
                                <th>Type</th>
                                <th>Floor</th>
                                <th>Ward</th>
                                <th>Beds</th>
                                <th>Occupancy</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->roomType->name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-layer-group me-1"></i>Floor {{ $room->floor_number }}
                                        </span>
                                    </td>
                                    <td>{{ $room->ward_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-bed me-1"></i>{{ $room->bed_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 me-2">
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-orange"
                                                        style="width: {{ ($room->current_occupancy / $room->bed_count) * 100 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $room->current_occupancy }}/{{ $room->bed_count }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge
                                        @if ($room->status == 'available') bg-success
                                        @elseif($room->status == 'occupied') bg-warning
                                        @elseif($room->status == 'maintenance') bg-danger
                                        @else bg-secondary @endif">
                                            <i
                                                class="fas
                                            @if ($room->status == 'available') fa-check-circle
                                            @elseif($room->status == 'occupied') fa-users
                                            @elseif($room->status == 'maintenance') fa-tools
                                            @else fa-question @endif me-1"></i>
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                    <td>

                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-sm btn-warning edit-room-btn" data-bs-toggle="tooltip"
                                                title="Edit" data-room-id="{{ $room->id }}"
                                                data-room-number="{{ $room->room_number }}"
                                                data-room-type-id="{{ $room->room_type_id }}"
                                                data-floor-number="{{ $room->floor_number }}"
                                                data-ward-name="{{ $room->ward_name }}"
                                                data-bed-count="{{ $room->bed_count }}" data-status="{{ $room->status }}"
                                                data-current-occupancy="{{ $room->current_occupancy }}"
                                                data-notes="{{ $room->notes }}" style="height: 30px; margin-right:5px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this room?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-bed text-orange"></i>
                                            <h4 class="text-muted">No rooms found</h4>
                                            <p class="mb-3">Get started by adding your first room</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ROOM ADD/EDIT MODAL -->
    <div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-orange text-white">
                    <h5 class="modal-title" style="color: white;">
                        <i class="fas fa-plus-circle me-2"></i><span id="modalTitle">Add New Room</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="roomForm" method="POST">
                        @csrf
                        <input type="hidden" id="roomId" name="id">
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-orange fw-semibold">Room Number *</label>
                                <input type="text" class="form-control" name="room_number" id="roomNumber" required>
                                <div class="invalid-feedback">Please provide a valid room number.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-orange fw-semibold">Room Type *</label>
                                <div class="input-group">
                                    <select name="room_type_id" class="form-control" id="roomTypeId" required>
                                        <option value="">Select Room Type</option>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }} -
                                                ₹{{ $type->base_price }}/day</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback">Please select a room type.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-orange fw-semibold">Floor *</label>
                                <input type="number" class="form-control" name="floor_number" id="floorNumber" required
                                    min="0">
                                <div class="invalid-feedback">Please provide a valid floor number.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-orange fw-semibold">Ward</label>
                                <input type="text" class="form-control" name="ward_name" id="wardName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-orange fw-semibold">Bed Count *</label>
                                <input type="number" class="form-control" name="bed_count" id="bedCount" required
                                    min="1">
                                <div class="invalid-feedback">Please provide a valid bed count (minimum 1).</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-orange fw-semibold">Status *</label>
                                <select name="status" class="form-control" id="status12" required>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="cleaning">Cleaning</option>
                                </select>
                                <div class="invalid-feedback">Please select a status.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-orange fw-semibold">Current Occupancy</label>
                                <input type="number" class="form-control" name="current_occupancy"
                                    id="currentOccupancy" value="0" min="0">
                                <div class="invalid-feedback">Please provide a valid occupancy count.</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-orange fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" id="notes" rows="3"
                                placeholder="Add any additional notes about this room..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><span id="submitButtonText">Save Room</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS includes -->

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
         let baseUrl = "{{ config('app.url') }}";   
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
        });
        document.addEventListener('DOMContentLoaded', function() {
            // DataTable init
            $('#roomTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [7]
                }], // Actions column
                order: [
                    [0, 'asc']
                ]
            });

            const roomModal = document.getElementById('roomModal');
            const roomForm = document.getElementById('roomForm');
            const modalTitle = document.getElementById('modalTitle');
            const submitButtonText = document.getElementById('submitButtonText');
            const formMethod = document.getElementById('formMethod');

            document.getElementById('addRoomBtn')?.addEventListener('click', function() {
                resetForm();
                modalTitle.textContent = 'Add New Room';
                submitButtonText.textContent = 'Save Room';
                formMethod.value = 'POST';
                roomForm.action = "{{ route('admin.rooms.store') }}";
            });

            // Use delegated event handling so buttons added/removed by DataTables still work
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-room-btn');
                if (!btn) return;

                const roomData = {
                    id: btn.getAttribute('data-room-id'),
                    room_number: btn.getAttribute('data-room-number'),
                    room_type_id: btn.getAttribute('data-room-type-id'),
                    floor_number: btn.getAttribute('data-floor-number'),
                    ward_name: btn.getAttribute('data-ward-name'),
                    bed_count: btn.getAttribute('data-bed-count'),
                    status: btn.getAttribute('data-status'),
                    current_occupancy: btn.getAttribute('data-current-occupancy'),
                    notes: btn.getAttribute('data-notes')
                };

                populateForm(roomData);
                modalTitle.textContent = 'Edit Room';
                submitButtonText.textContent = 'Update Room';
                formMethod.value = 'PUT';
                roomForm.action = `${baseUrl}/admin/rooms/${roomData.id}`;
                const modal = new bootstrap.Modal(document.getElementById('roomModal'));
                modal.show();
            });

            function populateForm(data) {
                document.getElementById('roomId').value = data.id;
                document.getElementById('roomNumber').value = data.room_number;
                document.getElementById('roomTypeId').value = data.room_type_id;
                document.getElementById('floorNumber').value = data.floor_number;
                document.getElementById('wardName').value = data.ward_name || '';
                document.getElementById('bedCount').value = data.bed_count;
                document.getElementById('status12').value = data.status;
                document.getElementById('currentOccupancy').value = data.current_occupancy;
                document.getElementById('notes').value = data.notes || '';
            }

            function resetForm() {
                roomForm.reset();
                document.getElementById('roomId').value = '';
                document.getElementById('currentOccupancy').value = '0';
                const formInputs = roomForm.querySelectorAll('.form-control, .form-select');
                formInputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
            }

            // Validation
            roomForm.addEventListener('submit', function(event) {
                if (!roomForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                roomForm.classList.add('was-validated');
            });



        });
    </script>

@endsection
