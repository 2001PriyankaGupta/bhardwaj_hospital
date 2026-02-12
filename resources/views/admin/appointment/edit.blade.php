@extends('admin.layouts.master')

@section('title', 'Edit Appointment')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Edit Appointment</h1>
                    <p class="text-muted mb-0">Update appointment details and reschedule if needed</p>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-secondary" href="{{ route('admin.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST"
                            id="appointmentForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-label">Select Doctor *</label>
                                        <select name="doctor_id" id="doctor_id" class="form-control" required>
                                            <option value="">Choose Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}"
                                                    {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->full_name }} - {{ $doctor->specialty->name }}
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
                                        <label for="resource_id" class="form-label">Resource</label>
                                        <select name="resource_id" id="resource_id" class="form-control">
                                            <option value="">No Resource</option>
                                            @foreach ($resources as $resource)
                                                <option value="{{ $resource->id }}"
                                                    {{ old('resource_id', $appointment->resource_id) == $resource->id ? 'selected' : '' }}>
                                                    {{ $resource->name }} ({{ $resource->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status *</label>
                                        <select name="status" id="status1" class="form-control" required>
                                            <option value="scheduled"
                                                {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>
                                                Scheduled</option>
                                            <option value="confirmed"
                                                {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>
                                                Confirmed</option>
                                            <option value="cancelled"
                                                {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled</option>
                                            <option value="completed"
                                                {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>
                                                Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id" class="form-label">Patient </label>
                                        <select name="patient_id" id="patient_id" class="form-control">
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}"
                                                    {{ $patient->id == $appointment->patient->id ? 'selected' : '' }}>
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
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3" id="cancellation_reason_row"
                                style="{{ old('status', $appointment->status) == 'cancelled' ? '' : 'display: none;' }}">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="2">{{ old('cancellation_reason', $appointment->cancellation_reason) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Appointment
                                    </button>
                                    <a href="{{ route('admin.appointments.index') }}"
                                        class="btn btn-secondary">Cancel</a>
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
        let availableSlots = [];
        const appointmentId = {{ $appointment->id }};
        const currentDoctorId = {{ $appointment->doctor_id }};
        const currentDate = "{{ $appointment->appointment_date->format('Y-m-d') }}";
        const currentStartTime = "{{ substr($appointment->start_time, 0, 5) }}";
        const currentEndTime = "{{ substr($appointment->end_time, 0, 5) }}";

        $(document).ready(function() {
            // Initialize page with existing appointment data
            initializeEditForm();

            // Load dates when doctor changes
            $('#doctor_id').change(function() {
                // Reset date, time fields when doctor changes
                $('#appointment_date').html('<option value="">Select Date</option>').prop('disabled', true);
                $('#start_time').html('<option value="">Select Date First</option>').prop('disabled', true);
                $('#end_time').html('<option value="">Select Start Time First</option>').prop('disabled',
                    true);
                $('#slotDurationAlert').addClass('d-none');

                // Load new dates for selected doctor
                loadDoctorDates();
            });

            // Load slots when date changes
            $('#appointment_date').change(function() {
                // Reset time fields when date changes
                $('#start_time').html('<option value="">Select Date First</option>').prop('disabled', true);
                $('#end_time').html('<option value="">Select Start Time First</option>').prop('disabled',
                    true);
                $('#slotDurationAlert').addClass('d-none');

                loadDoctorSlots();
            });

            // Auto-fill end time when start time is selected
            $('#start_time').change(function() {
                const startTime = $(this).val();
                const selectedOption = $(this).find('option:selected');
                const endTime = selectedOption.data('end');
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
            });

            // Show/hide cancellation reason based on status
            $('#status1').change(function() {
                if ($(this).val() === 'cancelled') {
                    $('#cancellation_reason_row').show();
                } else {
                    $('#cancellation_reason_row').hide();
                }
            });
        });

        function initializeEditForm() {
            // Set the current doctor as selected
            $('#doctor_id').val(currentDoctorId);

            // Load dates for the current doctor
            loadDoctorDates();
        }

        function loadDoctorDates() {
            const doctorId = $('#doctor_id').val();
            if (!doctorId) {
                $('#appointment_date').html('<option value="">Select Doctor First</option>');
                return;
            }

            $('#appointment_date').prop('disabled', true).html('<option value="">Loading dates...</option>');

            $.ajax({
                url: '{{ route('admin.appointments.doctor-dates') }}',
                type: 'GET',
                data: {
                    doctor_id: doctorId
                },
                success: function(data) {
                    let html = '<option value="">Select Date</option>';

                    // Get today's date
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    $.each(data.dates, function(key, date) {
                        const parts = date.date.split('-');
                        const itemDate = new Date(parts[0], parts[1] - 1, parts[2]);
                        
                        // Show if future/today OR if it matches the current appointment date
                        if (itemDate >= today || date.date === currentDate) {
                            const selected = date.date === currentDate ? 'selected' : '';
                            html += `<option value="${date.date}" ${selected}>${date.formatted}</option>`;
                        }
                    });

                    $('#appointment_date').html(html).prop('disabled', false);

                    // Auto-trigger slot loading for pre-populated date
                    if (currentDate) {
                        setTimeout(function() {
                            $('#appointment_date').val(currentDate);
                            loadDoctorSlots();
                        }, 200);
                    }
                },
                error: function() {
                    alert('Error loading dates. Please try again.');
                    $('#appointment_date').prop('disabled', false);
                }
            });
        }

        function loadDoctorSlots() {
            const doctorId = $('#doctor_id').val();
            const appointmentDate = $('#appointment_date').val();

            if (!doctorId || !appointmentDate) {
                $('#start_time').html('<option value="">Select Date First</option>');
                $('#end_time').html('<option value="">Select Start Time First</option>');
                return;
            }

            $('#start_time').prop('disabled', true).html('<option value="">Loading slots...</option>');

            $.ajax({
                url: '{{ route('admin.appointments.doctor-slots') }}',
                type: 'GET',
                data: {
                    doctor_id: doctorId,
                    date: appointmentDate,
                    appointment_id: appointmentId
                },
                success: function(data) {
                    window.availableSlots = data.slots;
                    let html = '<option value="">Select Start Time</option>';

                    // Get current time
                    const now = new Date();
                    const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                    const isToday = (appointmentDate === todayStr);
                    const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();

                    $.each(data.slots, function(key, slot) {
                        // Filter past slots if today, unless it's the current appointment time
                        if (isToday) {
                             const [h, m] = slot.start.split(':').map(Number);
                             const slotMinutes = h * 60 + m;
                             
                             // Check if this is the currently selected time for this appointment
                             // currentStartTime is formatted as HH:mm
                             const isOriginalSlot = (appointmentDate === currentDate && slot.start.substring(0, 5) === currentStartTime); 
                             
                             if (slotMinutes < currentTotalMinutes && !isOriginalSlot) {
                                 return;
                             }
                        }

                        const timeDisplay = slot.display.split(' - ')[
                            0]; // Extract just start time (e.g., "09:00 AM")
                        const disabled = !slot.available ? 'disabled' : '';
                        const booked = !slot.available ? ' (Booked)' : '';
                        const selected = slot.start === currentStartTime && slot.available ?
                            'selected' : '';
                        html +=
                            `<option value="${slot.start}" data-end="${slot.end}" ${disabled} ${selected}>${timeDisplay}${booked}</option>`;
                    });

                    $('#start_time').html(html).prop('disabled', false);

                    // Explicitly set the value to current start time and trigger change
                    if (currentStartTime) {
                        $('#start_time').val(currentStartTime).trigger('change');
                    }
                },
                error: function() {
                    alert('Error loading slots. Please try again.');
                    $('#start_time').prop('disabled', false);
                }
            });
        }

        function calculateSlotDuration() {
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();

            if (!startTime || !endTime) {
                $('#slotDurationAlert').addClass('d-none');
                return;
            }

            try {
                const start = new Date('2000-01-01 ' + startTime);
                const end = new Date('2000-01-01 ' + endTime);
                const minutes = Math.round((end - start) / 60000);

                if (minutes > 0) {
                    const hours = Math.floor(minutes / 60);
                    const mins = minutes % 60;
                    let durationText = '';

                    if (hours > 0) {
                        durationText += hours + 'h ';
                    }
                    if (mins > 0 || hours === 0) {
                        durationText += mins + 'm';
                    }

                    $('#slotDurationText').text(durationText);
                    $('#slotDurationAlert').removeClass('d-none');
                }
            } catch (error) {
                console.error('Error calculating duration:', error);
            }
        }
    </script>
@endsection
