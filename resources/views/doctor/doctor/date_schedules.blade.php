@extends('doctor.layouts.master')

@section('title', 'Manage Schedule - ' . $doctor->full_name)
<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

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

    .slot-card .action-buttons {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .slot-card:hover .action-buttons {
        opacity: 1;
    }

    .btn-outline-primary.edit-slot:hover {
        background-color: #0d6efd;
        color: white;
    }

    .btn-outline-danger.delete-slot:hover {
        background-color: #dc3545;
        color: white;
    }

    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .slot-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }

    .slot-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .slot-header {
        background: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }

    .slot-available {
        border-left: 4px solid #28a745;
    }

    .slot-unavailable {
        border-left: 4px solid #dc3545;
    }

    .slot-full {
        border-left: 4px solid #ffc107;
    }

    .time-slot-badge {
        background: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.85em;
        margin: 2px;
        display: inline-block;
    }

    .time-slot-booked {
        background: #dc3545;
        color: white;
        border-color: #c82333;
    }

    .time-slot-available {
        background: #28a745;
        color: white;
        border-color: #218838;
    }

    .fc-event {
        cursor: pointer;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Schedule Management - Dr. {{ $doctor->full_name }}</h1>
                    <p class="text-muted mb-0">Manage date-wise appointment slots and availability</p>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-secondary" href="{{ route('doctor.doctors.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Add New Slot Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header  text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Date Slot</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('doctor.doctors.schedules.store', $doctor) }}" method="POST"
                            id="addSlotForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="slot_date">Date *</label>
                                        <input type="date" class="form-control" id="slot_date" name="slot_date"
                                            min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_time">Start Time *</label>
                                        <input type="time" class="form-control" id="start_time" name="start_time"
                                            value="09:00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_time">End Time *</label>
                                        <input type="time" class="form-control" id="end_time" name="end_time"
                                            value="17:00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="slot_duration">Slot Duration *</label>
                                        <select class="form-control" id="slot_duration" name="slot_duration" required>
                                            <option value="15">15 minutes</option>
                                            <option value="30" selected>30 minutes</option>
                                            <option value="45">45 minutes</option>
                                            <option value="60">60 minutes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="max_patients">Max Patients *</label>
                                        <input type="number" class="form-control" id="max_patients" name="max_patients"
                                            value="10" min="1" max="50" required>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Create Form -->
        {{-- <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Bulk Create Slots</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('doctor.doctors.schedules.bulk-create', $doctor) }}" method="POST"
                            id="bulkCreateForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date">Start Date *</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date">End Date *</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="start_time_bulk">Start Time *</label>
                                        <input type="time" class="form-control" id="start_time_bulk"
                                            name="start_time" value="09:00" required>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="end_time_bulk">End Time *</label>
                                        <input type="time" class="form-control" id="end_time_bulk" name="end_time"
                                            value="17:00" required>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="slot_duration_bulk">Duration *</label>
                                        <select class="form-control" id="slot_duration_bulk" name="slot_duration"
                                            required>
                                            <option value="15">15 min</option>
                                            <option value="30" selected>30 min</option>
                                            <option value="45">45 min</option>
                                            <option value="60">60 min</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="max_patients_bulk">Max Patients *</label>
                                        <input type="number" class="form-control" id="max_patients_bulk"
                                            name="max_patients" value="10" min="1" max="50" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Days of Week *</label>
                                        <div class="d-flex flex-wrap">
                                            @foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day)
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="days_of_week[]"
                                                        value="{{ $day }}" id="day_{{ $day }}" checked>
                                                    <label class="form-check-label" for="day_{{ $day }}">
                                                        {{ ucfirst($day) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fas fa-bolt"></i> Bulk Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Calendar View -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Calendar View</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slots List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Slots List (This Month)</h5>
                    </div>
                    <div class="card-body">
                        @if ($dateSlots->isEmpty())
                            <div class="alert alert-info">
                                No slots created for this month. Add slots using the forms above.
                            </div>
                        @else
                            @foreach ($dateSlots as $slot)
                                <div
                                    class="slot-card {{ $slot->is_available ? 'slot-available' : 'slot-unavailable' }} {{ $slot->booked_slots >= $slot->max_patients ? 'slot-full' : '' }}">
                                    <div class="slot-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ \Carbon\Carbon::parse($slot->slot_date)->format('D, M d, Y') }}</strong>
                                            <span class="badge bg-primary ms-2">{{ $slot->start_time }} -
                                                {{ $slot->end_time }}</span>
                                            <span class="badge bg-secondary ms-1">{{ $slot->slot_duration }} min
                                                slots</span>
                                            <span
                                                class="badge {{ $slot->is_available ? 'bg-success' : 'bg-danger' }} ms-1">
                                                {{ $slot->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </div>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary edit-slot"
                                                data-id="{{ $slot->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-slot"
                                                data-id="{{ $slot->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p class="mb-1">
                                                    <strong>Max Patients:</strong> {{ $slot->max_patients }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Booked:</strong>
                                                    <span
                                                        class="badge {{ $slot->booked_slots >= $slot->max_patients ? 'bg-danger' : ($slot->booked_slots > 0 ? 'bg-warning' : 'bg-success') }}">
                                                        {{ $slot->booked_slots }}/{{ $slot->max_patients }}
                                                    </span>
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Available:</strong>
                                                    <span class="badge bg-success">
                                                        {{ $slot->max_patients - $slot->booked_slots }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-8">
                                                <p class="mb-1"><strong>Time Slots:</strong></p>
                                                <div class="time-slots-container">
                                                    @if ($slot->time_slots && is_array($slot->time_slots))
                                                        @foreach ($slot->time_slots as $timeSlot)
                                                            <span
                                                                class="time-slot-badge {{ $timeSlot['booked'] >= $timeSlot['available'] ? 'time-slot-booked' : 'time-slot-available' }}">
                                                                {{ $timeSlot['start'] }}-{{ $timeSlot['end'] }}
                                                                ({{ $timeSlot['booked'] }}/{{ $timeSlot['available'] }})
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Slot Modal -->
    <div class="modal fade" id="editSlotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSlotForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Date</label>
                            <input type="date" class="form-control" id="edit_slot_date" name="slot_date" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label>Start Time *</label>
                            <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>End Time *</label>
                            <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Slot Duration *</label>
                            <select class="form-control" id="edit_slot_duration" name="slot_duration" required>
                                <option value="15">15 minutes</option>
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Max Patients *</label>
                            <input type="number" class="form-control" id="edit_max_patients" name="max_patients"
                                min="1" max="50" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_is_available" name="is_available"
                                value="1">
                            <label class="form-check-label" for="edit_is_available">
                                Available for appointments
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Slot</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

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

            // Initialize FullCalendar
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 600,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: [
                        @foreach ($dateSlots as $slot)
                            {
                                title: '{{ $slot->start_time }} - {{ $slot->end_time }} ({{ $slot->booked_slots }}/{{ $slot->max_patients }})',
                                start: '{{ $slot->slot_date }}',
                                backgroundColor: '{{ $slot->is_available ? ($slot->booked_slots >= $slot->max_patients ? '#ffc107' : '#28a745') : '#dc3545' }}',
                                borderColor: '{{ $slot->is_available ? ($slot->booked_slots >= $slot->max_patients ? '#ffc107' : '#28a745') : '#dc3545' }}',
                                textColor: 'white',
                                extendedProps: {
                                    id: {{ $slot->id }},
                                    details: 'Duration: {{ $slot->slot_duration }}min'
                                }
                            },
                        @endforeach
                    ],
                    eventClick: function(info) {
                        const slotId = info.event.extendedProps.id;
                        editSlot(slotId);
                    },
                    dateClick: function(info) {
                        $('#slot_date').val(info.dateStr);
                        $('#start_date').val(info.dateStr);
                        $('#end_date').val(info.dateStr);
                    }
                });
                calendar.render();
            }

            // CSRF Token Setup - यह ADD करें
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Edit Slot Button Click Handler
            $(document).on('click', '.edit-slot', function() {
                const slotId = $(this).data('id');
                editSlot(slotId);
            });

            function editSlot(slotId) {
                console.log('Edit slot called with ID:', slotId); // Debugging

                $.ajax({
                    url: `${baseUrl}/doctor/date-slots/${slotId}/edit`,
                    method: 'GET',
                    success: function(data) {
                        console.log('Edit data received:', data); // Debugging

                        $('#edit_slot_date').val(data.slot_date);
                        $('#edit_start_time').val(data.start_time);
                        $('#edit_end_time').val(data.end_time);
                        $('#edit_slot_duration').val(data.slot_duration);
                        $('#edit_max_patients').val(data.max_patients);
                        $('#edit_is_available').prop('checked', data.is_available == 1 || data
                            .is_available === true);

                        // Form action सही सेट करें
                        $('#editSlotForm').attr('action', `${baseUrl}/doctor/date-slots/${slotId}`);
                        $('#editSlotModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit AJAX Error:', error, xhr.responseText);

                        let errorMsg = 'Failed to load slot data';
                        if (xhr.status === 404) {
                            errorMsg = 'Slot not found';
                        } else if (xhr.status === 403) {
                            errorMsg = 'You are not authorized to edit this slot';
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }

                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }

            // Edit Form Submit Handler
            $('#editSlotForm').submit(function(e) {
                e.preventDefault();

                console.log('Edit form submitted'); // Debugging

                // Validation
                const startTime = $('#edit_start_time').val();
                const endTime = $('#edit_end_time').val();

                if (startTime >= endTime) {
                    Swal.fire('Error', 'End time must be after start time', 'error');
                    return false;
                }

                const formData = $(this).serialize();
                const actionUrl = $(this).attr('action');

                console.log('Submitting to:', actionUrl); // Debugging
                console.log('Form data:', formData); // Debugging

                $.ajax({
                    url: actionUrl,
                    method: 'POST', // PUT request के लिए POST with _method parameter
                    data: formData + '&_method=PUT',
                    success: function(response) {
                        console.log('Update success:', response); // Debugging

                        $('#editSlotModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success || 'Slot updated successfully',
                            timer: 1000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Update AJAX Error:', error, xhr.responseText);

                        let errorMsg = 'Failed to update slot';
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }

                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            });

            // Delete Slot Handler - UPDATED VERSION
            $(document).on('click', '.delete-slot', function(e) {
                e.preventDefault();

                const slotId = $(this).data('id');
                console.log('Delete slot called with ID:', slotId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the slot permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseUrl}/doctor/date-slots/${slotId}`,
                            method: 'DELETE',
                            dataType: 'json', // Expect JSON response
                            success: function(response) {
                                console.log('Delete success:', response);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || response
                                        .success || 'Slot deleted successfully',
                                    timer: 1000,
                                    showConfirmButton: false
                                });

                                // Delay and reload
                                setTimeout(() => {
                                    location.reload();
                                }, 1200);
                            },
                            error: function(xhr, status, error) {
                                console.error('Delete AJAX Error:', error, xhr
                                    .responseText);

                                let errorMsg = 'Failed to delete slot';

                                if (xhr.status === 400 || xhr.status === 422) {
                                    // Handle validation/business logic errors
                                    if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMsg = xhr.responseJSON.error;
                                    } else if (xhr.responseText && xhr.responseText
                                        .includes('Cannot delete')) {
                                        // If it's HTML response (redirect with error message)
                                        Swal.fire('Error',
                                            'Cannot delete slot with booked appointments',
                                            'error');
                                        return;
                                    }
                                }

                                Swal.fire('Error!', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // Add Slot Form Validation
            $('#addSlotForm').submit(function(e) {
                const startTime = $('#start_time').val();
                const endTime = $('#end_time').val();

                if (startTime >= endTime) {
                    e.preventDefault();
                    Swal.fire('Error', 'End time must be after start time', 'error');
                    return false;
                }

                // Additional validations
                const slotDate = $('#slot_date').val();
                const today = new Date().toISOString().split('T')[0];

                if (slotDate < today) {
                    e.preventDefault();
                    Swal.fire('Error', 'Date cannot be in the past', 'error');
                    return false;
                }

                return true;
            });

            // Bulk Create Form Validation
            $('#bulkCreateForm').submit(function(e) {
                const startTime = $('#start_time_bulk').val();
                const endTime = $('#end_time_bulk').val();

                if (startTime >= endTime) {
                    e.preventDefault();
                    Swal.fire('Error', 'End time must be after start time', 'error');
                    return false;
                }

                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const today = new Date().toISOString().split('T')[0];

                if (startDate < today) {
                    e.preventDefault();
                    Swal.fire('Error', 'Start date cannot be in the past', 'error');
                    return false;
                }

                if (startDate > endDate) {
                    e.preventDefault();
                    Swal.fire('Error', 'End date must be after start date', 'error');
                    return false;
                }

                // Check if at least one day is selected
                const daysSelected = $('input[name="days_of_week[]"]:checked').length;
                if (daysSelected === 0) {
                    e.preventDefault();
                    Swal.fire('Error', 'Please select at least one day of week', 'error');
                    return false;
                }

                return true;
            });

            // Modal close handler
            $('#editSlotModal').on('hidden.bs.modal', function() {
                // Reset form if needed
                $('#editSlotForm')[0].reset();
            });
        });
    </script>
@endsection
