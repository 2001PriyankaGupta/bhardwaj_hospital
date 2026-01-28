@extends('staff.layouts.master')

@section('title', 'Create Appointment')
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
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Schedule New Appointment</h1>
                    <p class="text-muted mb-0">Book appointment with doctor and allocate resources</p>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-secondary" href="{{ route('staff.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('staff.appointments.store') }}" method="POST" id="appointmentForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-label">Select Doctor *</label>
                                        <select name="doctor_id" id="doctor_id" class="form-control" required>
                                            <option value="">Choose Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}"
                                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->full_name }} - {{ $doctor->specialty->name }}
                                                    (₹{{ $doctor->consultation_fee }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="appointment_date" class="form-label">Appointment Date *</label>
                                        <select name="appointment_date" id="appointment_date" class="form-control" required>
                                            <option value="">Select Doctor First</option>
                                        </select>
                                        <small class="text-muted" id="dateLoadingMsg"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time" class="form-label">Start Time *</label>
                                        <select name="start_time" id="start_time" class="form-control" required>
                                            <option value="">Select Date First</option>
                                        </select>
                                        <small class="text-muted" id="startTimeLoadingMsg"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time" class="form-label">End Time *</label>
                                        <select name="end_time" id="end_time" class="form-control" required>
                                            <option value="">Select Start Time First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-info d-none" id="slotDurationAlert" role="alert">
                                        <strong>Slot Duration:</strong> <span id="slotDurationText">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="resource_id" class="form-label">Resource (Optional)</label>
                                        <select name="resource_id" id="resource_id" class="form-control">
                                            <option value="">No Resource</option>
                                            @foreach ($resources as $resource)
                                                <option value="{{ $resource->id }}"
                                                    {{ old('resource_id') == $resource->id ? 'selected' : '' }}>
                                                    {{ $resource->name }} ({{ $resource->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id" class="form-label">Patient </label>
                                        <select name="patient_id" id="patient_id" class="form-control">
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}"
                                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>


                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">Notes (Optional)</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus"></i> Schedule Appointment
                                    </button>
                                    <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary">Cancel</a>
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

            // ============ LOAD AVAILABLE DATES WHEN DOCTOR SELECTED ============
            $('#doctor_id').change(function() {
                const doctorId = $(this).val();
                const dateSelect = $('#appointment_date');
                const startTimeSelect = $('#start_time');

                // Reset selects
                dateSelect.html('<option value="">Loading dates...</option>').prop('disabled', true);
                startTimeSelect.html('<option value="">Select Date First</option>').prop('disabled', true);
                $('#end_time').html('<option value="">Select Start Time First</option>').prop('disabled',
                    true);

                if (!doctorId) {
                    dateSelect.html('<option value="">Select Doctor First</option>').prop('disabled', true);
                    return;
                }

                // Fetch available dates
                $.ajax({
                    url: '{{ route('staff.appointments.doctor-dates') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId
                    },
                    success: function(response) {
                        if (response.dates && response.dates.length > 0) {
                            let dateHtml = '<option value="">Choose Date</option>';
                            response.dates.forEach(date => {
                                dateHtml +=
                                    `<option value="${date.date}">${date.formatted}</option>`;
                            });
                            dateSelect.html(dateHtml).prop('disabled', false);
                            $('#dateLoadingMsg').text('');
                        } else {
                            dateSelect.html('<option value="">No dates available</option>')
                                .prop('disabled', true);
                            $('#dateLoadingMsg').text(response.message ||
                                'Doctor has no available dates').addClass('text-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading dates:', error);
                        dateSelect.html('<option value="">Error loading dates</option>').prop(
                            'disabled', true);
                        $('#dateLoadingMsg').text('Error loading dates').addClass(
                            'text-danger');
                    }
                });
            });

            // ============ LOAD AVAILABLE SLOTS WHEN DATE SELECTED ============
            $('#appointment_date').change(function() {
                const doctorId = $('#doctor_id').val();
                const selectedDate = $(this).val();
                const startTimeSelect = $('#start_time');

                // Reset time selects
                startTimeSelect.html('<option value="">Loading slots...</option>').prop('disabled', true);
                $('#end_time').html('<option value="">Select Start Time First</option>').prop('disabled',
                    true);

                if (!doctorId || !selectedDate) {
                    startTimeSelect.html('<option value="">Select Date First</option>').prop('disabled',
                        true);
                    return;
                }

                // Fetch available slots
                $.ajax({
                    url: '{{ route('staff.appointments.doctor-slots') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: selectedDate
                    },
                    success: function(response) {
                        if (response.slots && response.slots.length > 0) {
                            let slotHtml = '<option value="">Select Start Time</option>';
                            // Store slots for later reference
                            window.availableSlots = response.slots;

                            response.slots.forEach(slot => {
                                const disabled = !slot.available ? 'disabled' : '';
                                const booked = !slot.available ? ' (Booked)' : '';
                                // Extract start time from display (e.g., "09:00 AM" from "09:00 AM - 09:30 AM")
                                const startTimeDisplay = slot.display.split(' - ')[0];
                                slotHtml +=
                                    `<option value="${slot.start}" data-end="${slot.end}" ${disabled}>${startTimeDisplay}${booked}</option>`;
                            });
                            startTimeSelect.html(slotHtml).prop('disabled', false);
                            $('#startTimeLoadingMsg').text('');
                        } else {
                            startTimeSelect.html('<option value="">No slots available</option>')
                                .prop('disabled', true);
                            $('#startTimeLoadingMsg').text(response.message ||
                                'No available slots for this date').addClass('text-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading slots:', error);
                        startTimeSelect.html('<option value="">Error loading slots</option>')
                            .prop('disabled', true);
                        $('#startTimeLoadingMsg').text('Error loading slots').addClass(
                            'text-danger');
                    }
                });
            });

            // Auto-update end time when start time is selected
            $('#start_time').change(function() {
                const startTime = $(this).val();
                const selectedOption = $(this).find('option:selected');
                const endTime = selectedOption.data('end'); // Get end time from data attribute
                const endTimeSelect = $('#end_time');

                if (startTime && endTime) {
                    // Find the matching slot to get the end time display
                    let endTimeDisplay = endTime;

                    if (window.availableSlots) {
                        window.availableSlots.forEach(slot => {
                            if (slot.start === startTime && slot.end === endTime) {
                                // Extract end time from display (e.g., "09:30 AM" from "09:00 AM - 09:30 AM")
                                endTimeDisplay = slot.display.split(' - ')[1];
                            }
                        });
                    }

                    // Set end time select with just the end time (not the full range)
                    endTimeSelect.html(`<option value="${endTime}" selected>${endTimeDisplay}</option>`)
                        .prop('disabled', false);
                    calculateSlotDuration();
                } else {
                    endTimeSelect.html('<option value="">Select Start Time First</option>').prop('disabled',
                        true);
                }

                // Load available resources
                loadAvailableResources();
            });

            // Calculate and display slot duration
            function calculateSlotDuration() {
                const startTime = $('#start_time').val();
                const endTime = $('#end_time').val();
                const durationAlert = $('#slotDurationAlert');
                const durationText = $('#slotDurationText');

                if (startTime && endTime && endTime > startTime) {
                    // Parse times
                    const [startHour, startMin] = startTime.split(':').map(Number);
                    const [endHour, endMin] = endTime.split(':').map(Number);

                    // Calculate difference in minutes
                    const startTotalMins = startHour * 60 + startMin;
                    const endTotalMins = endHour * 60 + endMin;
                    const durationMins = endTotalMins - startTotalMins;

                    // Convert to hours and minutes
                    const hours = Math.floor(durationMins / 60);
                    const mins = durationMins % 60;

                    // Format display text
                    let displayText = '';
                    if (hours > 0) {
                        displayText += `${hours}h `;
                    }
                    if (mins > 0) {
                        displayText += `${mins}m`;
                    }

                    durationText.text(displayText || '0m');
                    durationAlert.removeClass('d-none');
                } else {
                    durationAlert.addClass('d-none');
                    durationText.text('-');
                }
            }

            // Load available resources when time is selected
            function loadAvailableResources() {
                const date = $('#appointment_date').val();
                const startTime = $('#start_time').val();
                const endTime = $('#end_time').val();

                if (!date || !startTime || !endTime) {
                    $('#availableResources').html('Select date and both times to see available resources');
                    return;
                }

                $.ajax({
                    url: '{{ route('staff.appointments.resources.available') }}',
                    method: 'GET',
                    data: {
                        date: date,
                        start_time: startTime,
                        end_time: endTime
                    },
                    success: function(response) {
                        console.log('Available resources:', response);

                        if (response.resources && response.resources.length > 0) {
                            let resourcesHtml = '';
                            response.resources.forEach(resource => {
                                resourcesHtml += `<div class="mb-1">
                                    <i class="fas fa-check text-success"></i> 
                                    ${resource.name} (${resource.type})
                                </div>`;
                            });
                            $('#availableResources').html(resourcesHtml);
                        } else {
                            $('#availableResources').html(
                                '<span class="text-danger">No resources available for selected time</span>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading resources:', error);
                        $('#availableResources').html(
                            '<span class="text-danger">Error loading resources</span>');
                    }
                });
            }

            // Event handlers
            $('#end_time, #appointment_date').change(loadAvailableResources);

            // Form validation
            $('#appointmentForm').submit(function(e) {
                const startTime = $('#start_time').val();
                const endTime = $('#end_time').val();

                if (startTime && endTime && startTime >= endTime) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time Selection',
                        text: 'End time must be after start time',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
