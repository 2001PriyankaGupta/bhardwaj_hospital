@extends('doctor.layouts.master')

@section('title', 'Edit Appointment')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

    /* Modern Appointment UI Styles */
    .appointment-picker-card {
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #edf2f9;
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
    }

    .calendar-wrapper {
        padding: 20px !important;
        background: #fff;
    }

    .slots-wrapper {
        padding: 20px !important;
        background: #f9fbfd;
        border-left: 1px solid #edf2f9;
    }

    .flatpickr-calendar {
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        background: transparent !important;
    }

    .flatpickr-innerContainer, .flatpickr-rContainer {
        width: 100% !important;
    }

    .flatpickr-days {
        width: 100% !important;
        max-width: none !important;
    }

    .dayContainer {
        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
    }

    .flatpickr-day {
        max-width: none !important;
        height: 48px !important;
        line-height: 48px !important;
        border-radius: 10px !important;
        margin: 2px !important;
        font-weight: 500;
    }

    .flatpickr-day.selected {
        background: #f42b0dff !important;
        border-color: #f42b0dff !important;
    }

    .flatpickr-day.today {
        border-color: #f42b0dff !important;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .slot-grid::-webkit-scrollbar {
        width: 4px;
    }
    .slot-grid::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 10px;
    }

    .time-slot-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eef2f7;
        background: #fff;
        font-weight: 600;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .time-slot-btn:hover:not(.disabled) {
        border-color: #f42b0dff;
        background: #f8faff;
        color: #f42b0dff;
        transform: translateY(-2px);
    }

    .time-slot-btn.selected {
        background: #f42b0dff;
        color: #fff;
        border-color: #f42b0dff;
        box-shadow: 0 4px 6px rgba(91, 115, 232, 0.2);
    }

    .time-slot-btn.disabled {
        background: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        border-color: #f1f3f5;
        opacity: 0.6;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #343a40;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #f42b0dff;
    }

    .text-orange {
        color: #f15832;
    }
</style>
@endsection

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
                <a class="btn btn-secondary shadow-sm" href="{{ route('doctor.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST" id="appointmentForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-label fw-bold">Select Doctor <span class="text-danger">*</span></label>
                                        <select name="doctor_id" id="doctor_id" class="form-select form-select-lg" required>
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
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="appointment-picker-card">
                                        <div class="row g-0">
                                            <div class="col-md-7 calendar-wrapper">
                                                <h5 class="section-title"><i class="fas fa-calendar-day"></i> Update Date</h5>
                                                <div id="inline-calendar"></div>
                                                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-5 slots-wrapper">
                                                <h5 class="section-title"><i class="fas fa-clock"></i> Update Time Slot</h5>
                                                <div id="time-slots-grid" class="slot-grid">
                                                    <!-- Slots loaded via AJAX -->
                                                </div>
                                                <input type="hidden" name="start_time" id="start_time" value="{{ substr($appointment->start_time, 0, 5) }}" required>
                                                <input type="hidden" name="end_time" id="end_time" value="{{ substr($appointment->end_time, 0, 5) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted" id="dateLoadingMsg"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info d-none shadow-none border-info" id="slotDurationAlert" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Selected Slot Duration:</strong> <span id="slotDurationText">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="patient_id" class="form-label fw-bold">Patient <span class="text-danger">*</span></label>
                                        <select name="patient_id" id="patient_id" class="form-select" required>
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}"
                                                    {{ $patient->id == $appointment->patient_id ? 'selected' : '' }}>
                                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="resource_id" class="form-label fw-bold">Resource (Optional)</label>
                                        <select name="resource_id" id="resource_id" class="form-select">
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
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <label for="notes" class="form-label fw-bold">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3" id="cancellation_reason_row"
                                style="{{ old('status', $appointment->status) == 'cancelled' ? '' : 'display: none;' }}">
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <label for="cancellation_reason" class="form-label fw-bold">Cancellation Reason</label>
                                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="2">{{ old('cancellation_reason', $appointment->cancellation_reason) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-12 text-end">
                                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-light btn-lg px-5 me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save me-2"></i> Update Appointment
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let fp;
            let availableDates = [];
            const appointmentId = {{ $appointment->id }};
            const initialDate = "{{ $appointment->appointment_date->format('Y-m-d') }}";
            const initialStartTime = "{{ substr($appointment->start_time, 0, 5) }}";
            
            // Initialize Flatpickr in inline mode
            fp = flatpickr("#inline-calendar", {
                inline: true,
                minDate: "today",
                defaultDate: initialDate,
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        return true; // All dates disabled until doctor selected/loaded
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    $('#appointment_date').val(dateStr);
                    loadTimeSlots(dateStr);
                }
            });

            // ============ DOCTOR SELECTION HANDLER ============
            $('#doctor_id').change(function() {
                const doctorId = $(this).val();
                
                if (!doctorId) {
                    fp.set('disable', [() => true]);
                    $('#time-slots-grid').html('<div class="text-center py-5 text-muted"><p>Choose a doctor first</p></div>');
                    return;
                }

                $('#dateLoadingMsg').text('Loading available dates...');
                
                // Fetch available dates for the doctor
                $.ajax({
                    url: '{{ route('doctor.appointments.doctor-dates') }}',
                    method: 'GET',
                    data: { doctor_id: doctorId },
                    success: function(response) {
                        $('#dateLoadingMsg').text('');
                        if (response.dates && response.dates.length > 0) {
                            availableDates = response.dates.map(d => d.date);
                            
                            // Include current appointment date even if it's not in regular slots anymore
                            if (!availableDates.includes(initialDate) && $('#doctor_id').val() == {{ $appointment->doctor_id }}) {
                                availableDates.push(initialDate);
                            }

                            // Enable only the available dates in flatpickr
                            fp.set('disable', [
                                function(date) {
                                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                                    return !availableDates.includes(dStr);
                                }
                            ]);
                            
                            // If initial date is still the same, load slots for it
                            if ($('#appointment_date').val() === initialDate) {
                                loadTimeSlots(initialDate, initialStartTime);
                            }
                        } else {
                            fp.set('disable', [() => true]);
                            $('#dateLoadingMsg').text('No available dates found.').addClass('text-danger');
                        }
                    }
                });
            });

            // Initial load
            $('#doctor_id').trigger('change');

            // ============ LOAD SLOTS ============
            function loadTimeSlots(selectedDate, preSelectedTime = null) {
                const doctorId = $('#doctor_id').val();
                const grid = $('#time-slots-grid');
                
                if (!doctorId || !selectedDate) return;

                grid.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

                $.ajax({
                    url: '{{ route('doctor.appointments.doctor-slots') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: selectedDate,
                        appointment_id: appointmentId
                    },
                    success: function(response) {
                        if (response.slots && response.slots.length > 0) {
                            const dateObj = new Date(selectedDate);
                            const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                            const formattedDate = dateObj.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
                            
                            let slotHtml = `
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                        <span class="text-primary fw-bold"><i class="fas fa-calendar-alt me-2"></i>${dayName}</span>
                                        <span class="badge bg-white text-muted border">${formattedDate}</span>
                                    </div>
                                </div>
                            `;
                            
                            // Get current time
                            const now = new Date();
                            const todayStr = flatpickr.formatDate(now, "Y-m-d");
                            const isToday = (selectedDate === todayStr);
                            const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();

                            response.slots.forEach(slot => {
                                // Filter past slots if today, except if it's the already selected slot
                                if (isToday) {
                                    const [h, m] = slot.start.split(':').map(Number);
                                    if (h * 60 + m < currentTotalMinutes && slot.start !== initialStartTime) {
                                        return;
                                    }
                                }

                                const disabled = !slot.available ? 'disabled' : '';
                                const subText = !slot.available ? ' (Booked)' : '';
                                const isSelected = (preSelectedTime && slot.start === preSelectedTime) ? 'selected' : '';
                                const startTimeDisplay = slot.display.split(' - ')[0];
                                
                                slotHtml += `
                                    <div class="time-slot-btn ${disabled} ${isSelected}" 
                                         data-start="${slot.start}" 
                                         data-end="${slot.end}">
                                        ${startTimeDisplay}${subText}
                                    </div>
                                `;

                                if (isSelected) {
                                    calculateSlotDuration(slot.start, slot.end);
                                }
                            });
                            grid.html(slotHtml);
                        } else {
                            grid.html('<div class="text-center py-5 text-muted"><p>No slots available.</p></div>');
                        }
                    }
                });
            }

            // ============ SLOT CLICK HANDLER ============
            $(document).on('click', '.time-slot-btn:not(.disabled)', function() {
                $('.time-slot-btn').removeClass('selected');
                $(this).addClass('selected');
                
                const start = $(this).data('start');
                const end = $(this).data('end');
                
                $('#start_time').val(start);
                $('#end_time').val(end);
                
                calculateSlotDuration(start, end);
            });

            function calculateSlotDuration(startTime, endTime) {
                if (startTime && endTime) {
                    const [sh, sm] = startTime.split(':').map(Number);
                    const [eh, em] = endTime.split(':').map(Number);
                    const diff = (eh * 60 + em) - (sh * 60 + sm);
                    
                    const h = Math.floor(diff / 60);
                    const m = diff % 60;
                    let text = '';
                    if (h > 0) text += h + 'h ';
                    if (m > 0) text += m + 'm';
                    
                    $('#slotDurationText').text(text || '0m');
                    $('#slotDurationAlert').removeClass('d-none');
                }
            }

            // Show/hide cancellation reason
            $('#status').change(function() {
                if ($(this).val() === 'cancelled') {
                    $('#cancellation_reason_row').show();
                } else {
                    $('#cancellation_reason_row').hide();
                }
            });

            // Form validation
            $('#appointmentForm').submit(function(e) {
                if (!$('#start_time').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select a time slot.'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
