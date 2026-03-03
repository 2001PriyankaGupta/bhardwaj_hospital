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
        max-width: 100% !important;
        margin: 0 auto;
    }

    .flatpickr-months {
        background: #fff !important;
        margin-bottom: 10px !important;
        padding: 10px 0;
    }

    .flatpickr-current-month {
        font-size: 110% !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        padding: 0 !important;
    }

    .flatpickr-monthDropdown-months {
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    
    .numInputWrapper input {
        font-weight: 700 !important;
        color: #1e293b !important;
    }

    .flatpickr-weekdays {
        background: #fff !important;
        margin-bottom: 15px !important;
    }

    .flatpickr-weekday {
        color: #64748b !important;
        font-weight: 600 !important;
        font-size: 13px !important;
    }

    .flatpickr-days {
        width: 100% !important;
    }

    .dayContainer {
        width: 100% !important;
        max-width: 100% !important;
        justify-content: space-between !important;
    }

    .flatpickr-day {
        margin: 0 !important;
        width: 14.28% !important;
        max-width: none !important;
        height: 48px !important;
        line-height: 48px !important;
        border-radius: 8px !important;
        color: #334155 !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        border: 1px solid transparent !important;
    }

    .flatpickr-day.prevMonthDay, 
    .flatpickr-day.nextMonthDay {
        color: #cbd5e1 !important;
    }

    .flatpickr-day:hover:not(.selected):not(.disabled) {
        background: #f1f5f9 !important;
        color: #1e293b !important;
    }

    .flatpickr-day.selected {
        background: #ee3d0cff !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(238, 61, 12, 0.3);
    }

    .flatpickr-day.today {
        border: 1px solid #ee3d0cff !important;
        color: #ee3d0cff !important;
    }

    .flatpickr-day.today.selected {
        background: #ee3d0cff !important;
        color: #fff !important;
        border: none !important;
    }

    .flatpickr-day.disabled {
        color: #e2e8f0 !important;
        pointer-events: none;
    }

    /* Slot Styling */
    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        max-height: 420px;
        overflow-y: auto;
        padding: 5px;
    }

    .time-slot-btn {
        padding: 10px 5px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .time-slot-btn:hover:not(.disabled) {
        border-color: #ee3d0cff;
        color: #ee3d0cff;
        background: #fff5f2;
    }

    .time-slot-btn.selected {
        background: #ee3d0cff;
        color: #fff;
        border-color: #ee3d0cff;
        box-shadow: 0 4px 6px rgba(238, 61, 12, 0.2);
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title i {
        color: #ee3d0cff;
    }

    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 300px;
        text-align: center;
        color: #adb5bd;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #e9ecef;
    }

    .empty-state-text {
        font-size: 1rem;
        max-width: 250px;
        line-height: 1.5;
    }

    .text-orange {
        color: #ee3d0cff;
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

                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-light border-0 shadow-sm d-flex align-items-center p-3" style="background: #fff5f2; border-radius: 15px; border-left: 5px solid #ee3d0cff !important;">
                                        <div class="avatar-sm bg-orange-soft text-orange rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="background: rgba(238, 61, 12, 0.1); width: 45px; height: 45px;">
                                            <i class="fas fa-user-md fa-lg" style="color: #ee3d0cff;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">Appointment for Dr. {{ $currentDoctor->full_name ?? Auth::user()->name }}</h6>
                                            <small class="text-muted">Specialty: {{ $currentDoctor->specialty->name ?? 'Medical Specialist' }}</small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="doctor_id" id="doctor_id" value="{{ $currentDoctor->id ?? $appointment->doctor_id }}">
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
                                                    <div class="empty-state-container">
                                                        <i class="fas fa-calendar-check empty-state-icon"></i>
                                                        <p class="empty-state-text">Select a date to view available time slots for your consultation</p>
                                                    </div>
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
                                        <select name="status" id="status12" class="form-select" required>
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
                                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-light btn-sm px-5 me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-sm px-5" style="background: #ee3d0cff; border-color: #ee3d0cff;">
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
                locale: {
                    firstDayOfWeek: 1
                },
                onChange: function(selectedDates, dateStr, instance) {
                    $('#appointment_date').val(dateStr);
                    loadTimeSlots(dateStr);
                },
                onReady: function() {
                    $('.flatpickr-weekday').css('font-weight', '600');
                }
            });

            // Automatically load doctor availability on page load
            const initialDoctorId = $('#doctor_id').val();
            if(initialDoctorId) {
                setTimeout(() => {
                    $('#doctor_id').trigger('change');
                }, 100);
            }

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
                                <div style="grid-column: 1 / -1;" class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                        <span class="text-orange fw-bold"><i class="fas fa-calendar-alt me-2"></i>${dayName}</span>
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
