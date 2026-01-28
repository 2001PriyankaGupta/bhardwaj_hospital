@extends('admin.layouts.master')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
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
                    <div class="card-header justify-content-between d-flex align-items-center">
                        <h3 class="card-title">Shifts for {{ $staff->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addShiftModal">
                                <i class="fas fa-plus"></i> Add Shift
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="shiftsTable">
                                <thead>
                                    <tr>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Shift Type</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shifts as $shift)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($shift->start_date)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->end_date)->format('M d, Y') }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'secondary') }}"
                                                    style="color: purple">
                                                    {{ ucfirst($shift->shift_type) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                            <td>{{ $shift->notes ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-shift"
                                                    data-shift="{{ json_encode($shift) }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-shift"
                                                    data-shift-id="{{ $shift->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shift Modal -->
    <div class="modal fade" id="addShiftModal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addShiftForm">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                            <div class="invalid-feedback" id="start_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>End Date *</label>
                            <input type="date" name="end_date" class="form-control" required>
                            <div class="invalid-feedback" id="end_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Shift Type *</label>
                            <select name="shift_type" class="form-control" required>
                                <option value="morning">Morning</option>
                                <option value="evening">Evening</option>
                                <option value="night">Night</option>
                            </select>
                            <div class="invalid-feedback" id="shift_type_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Start Time *</label>
                            <input type="time" name="start_time" class="form-control" required>
                            <div class="invalid-feedback" id="start_time_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>End Time *</label>
                            <input type="time" name="end_time" class="form-control" required>
                            <div class="invalid-feedback" id="end_time_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Shift Modal -->
    <div class="modal fade" id="editShiftModal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editShiftForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="shift_id" id="edit_shift_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            <div class="invalid-feedback" id="edit_start_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>End Date *</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                            <div class="invalid-feedback" id="edit_end_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Shift Type *</label>
                            <select name="shift_type" id="edit_shift_type" class="form-control" required>
                                <option value="morning">Morning</option>
                                <option value="evening">Evening</option>
                                <option value="night">Night</option>
                            </select>
                            <div class="invalid-feedback" id="edit_shift_type_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Start Time *</label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_start_time_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>End Time *</label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_end_time_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Notes</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#shiftsTable').DataTable({
                responsive: true,
                autoWidth: false,
            });

            // Show success/error messages
            @if (session('success'))
                showToast('success', "{{ session('success') }}");
            @endif

            @if (session('error'))
                showToast('error', "{{ session('error') }}");
            @endif

            // Add Shift Form Submission
            $('#addShiftForm').submit(function(e) {
                e.preventDefault();
                clearErrors();

                $.ajax({
                    url: '{{ route('admin.shifts.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addShiftModal').modal('hide');
                        $('#addShiftForm')[0].reset();
                        showToast('success', response.success);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            displayErrors(xhr.responseJSON.errors);
                        } else {
                            showToast('error', xhr.responseJSON.error || 'An error occurred');
                        }
                    }
                });
            });

            // Edit Shift - FIXED TIME DISPLAY
            $('body').on('click', '.edit-shift', function() {
                var shift = $(this).data('shift');

                console.log('Raw shift data:', shift); // Debug log
                console.log('Start time:', shift.start_time); // Debug time
                console.log('End time:', shift.end_time); // Debug time

                // Format dates for HTML date input (YYYY-MM-DD)
                var startDate = new Date(shift.start_date);
                var endDate = new Date(shift.end_date);

                var formattedStartDate = startDate.toISOString().split('T')[0];
                var formattedEndDate = endDate.toISOString().split('T')[0];

                var startTime = new Date(shift.start_time).toTimeString().slice(0, 5);
                var endTime = new Date(shift.end_time).toTimeString().slice(0, 5);


                $('#edit_shift_id').val(shift.id);
                $('#edit_start_date').val(formattedStartDate);
                $('#edit_end_date').val(formattedEndDate);
                $('#edit_shift_type').val(shift.shift_type);
                $('#edit_start_time').val(startTime);
                $('#edit_end_time').val(endTime);
                $('#edit_notes').val(shift.notes);

                $('#editShiftModal').modal('show');
            });

            // Edit Shift Form Submission
            $('#editShiftForm').submit(function(e) {
                e.preventDefault();
                clearEditErrors();

                var shiftId = $('#edit_shift_id').val();

                $.ajax({
                    url: '/admin/shifts/' + shiftId,
                    method: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function(response) {
                        $('#editShiftModal').modal('hide');
                        showToast('success', response.success);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            displayEditErrors(xhr.responseJSON.errors);
                        } else {
                            showToast('error', xhr.responseJSON.error || 'An error occurred');
                        }
                    }
                });
            });

            // Delete Shift
            $('body').on('click', '.delete-shift', function() {
                var shiftId = $(this).data('shift-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/shifts/' + shiftId,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                showToast('success', response.success);
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                showToast('error', xhr.responseJSON.error ||
                                    'An error occurred');
                            }
                        });
                    }
                });
            });

            // Clear validation errors when modal is closed
            $('.modal').on('hidden.bs.modal', function() {
                clearErrors();
                clearEditErrors();
            });

            // NEW: Helper function to format time for HTML input
            function formatTimeForInput(timeString) {
                console.log('Formatting time:', timeString); // Debug

                // If time is already in HH:MM format
                if (typeof timeString === 'string' && timeString.match(/^\d{2}:\d{2}$/)) {
                    return timeString;
                }

                // If time is in HH:MM:SS format
                if (typeof timeString === 'string' && timeString.match(/^\d{2}:\d{2}:\d{2}$/)) {
                    return timeString.substring(0, 5); // Return HH:MM
                }

                // If time is a full datetime string
                if (typeof timeString === 'string' && timeString.includes(' ')) {
                    const timePart = timeString.split(' ')[1];
                    return timePart.substring(0, 5);
                }

                // If it's a Date object
                if (timeString instanceof Date) {
                    return timeString.toTimeString().substring(0, 5);
                }

                // Fallback: try to create date and extract time
                try {
                    const date = new Date('1970-01-01T' + timeString);
                    return date.toTimeString().substring(0, 5);
                } catch (e) {
                    console.error('Error formatting time:', e);
                    return '00:00'; // Default fallback
                }
            }

            // Helper functions
            function clearErrors() {
                $('#addShiftForm .is-invalid').removeClass('is-invalid');
                $('#addShiftForm .invalid-feedback').text('');
            }

            function clearEditErrors() {
                $('#editShiftForm .is-invalid').removeClass('is-invalid');
                $('#editShiftForm .invalid-feedback').text('');
            }

            function displayErrors(errors) {
                for (const [field, messages] of Object.entries(errors)) {
                    const input = $(`[name="${field}"]`);
                    const errorDiv = $(`#${field}_error`);

                    input.addClass('is-invalid');
                    errorDiv.text(messages[0]);
                }
            }

            function displayEditErrors(errors) {
                for (const [field, messages] of Object.entries(errors)) {
                    const input = $(`#edit_${field}`);
                    const errorDiv = $(`#edit_${field}_error`);

                    input.addClass('is-invalid');
                    errorDiv.text(messages[0]);
                }
            }

            function showToast(icon, message) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                Toast.fire({
                    icon: icon,
                    title: message
                });
            }
        });
    </script>
@endsection
