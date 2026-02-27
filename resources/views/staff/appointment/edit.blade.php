@extends('staff.layouts.master')

@section('title', 'Edit Appointment')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Modern Appointment UI Styles */
    .appointment-picker-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e0e6ed;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        flex-wrap: wrap;
    }

    .calendar-wrapper {
        padding: 24px !important;
        background: #fff;
        border-right: 1px solid #e0e6ed;
    }

    .slots-wrapper {
        padding: 24px !important;
        background: #fcfcfc;
    }

    /* Flatpickr Customization */
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
</style>

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
                <a class="btn btn-secondary shadow-sm" href="{{ route('staff.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('staff.appointments.update', $appointment) }}" method="POST" id="appointmentForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-label fw-bold">Select Doctor <span class="text-danger">*</span></label>
                                        <select name="doctor_id" id="doctor_id" class="form-select form-select-sm" required>
                                            <option value="">Choose Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}"
                                                    {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->full_name }} - {{ $doctor->specialty->name }}
                                                    (₹{{ $doctor->consultation_fee }})
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
                                                <h5 class="section-title"><i class="fas fa-calendar-day"></i> Select Date</h5>
                                                <div id="inline-calendar"></div>
                                                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-5 slots-wrapper">
                                                <h5 class="section-title"><i class="fas fa-clock"></i> Select Available Time</h5>
                                                <div id="time-slots-grid" class="slot-grid">
                                                    <!-- Slots loaded via AJAX -->
                                                    <div class="text-center py-5 text-muted" style="grid-column: span 2;">
                                                         <div class="spinner-border text-primary" role="status"></div>
                                                         <p class="mt-2">Loading appointment details...</p>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="start_time" id="start_time" value="{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}" required>
                                                <input type="hidden" name="end_time" id="end_time" value="{{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}" required>
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
                                    <a href="{{ route('staff.appointments.index') }}" class="btn btn-light btn-sm px-5 me-2">Cancel</a>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            let fp;
            let availableDates = [];
            
            // Initial data from server
            const appointmentId = {{ $appointment->id }};
            const initialDoctorId = "{{ $appointment->doctor_id }}";
            const initialDate = "{{ $appointment->appointment_date->format('Y-m-d') }}";
            const initialStartTime = "{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}";
            const initialEndTime = "{{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}";
            
            // Handle if Flatpickr not loaded
            if (typeof flatpickr === 'undefined') {
                $('#inline-calendar').html('<div class="alert alert-danger">Calendar library failed to load.</div>');
                return;
            }

            // Initialize Flatpickr
            fp = flatpickr("#inline-calendar", {
                inline: true,
                minDate: "today",
                defaultDate: initialDate,
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                locale: { firstDayOfWeek: 1 },
                disable: [function(date) { return true; }], // Start disabled until dates fetch
                onChange: function(selectedDates, dateStr, instance) {
                    $('#appointment_date').val(dateStr);
                    if ($('#doctor_id').val()) {
                        loadTimeSlots(dateStr);
                    }
                },
                onReady: function() {
                     $('.flatpickr-weekday').each(function() { $(this).css('font-weight', '600'); });
                }
            });
            
            // Calculate initial duration
            calculateSlotDuration(initialStartTime, initialEndTime);

            // ============ DOCTOR SELECTION HANDLER ============
            // We'll define it, then trigger it immediately for the initial load
            $('#doctor_id').change(function() {
                const doctorId = $(this).val();
                
                // Show loading state
                if ($('#time-slots-grid').find('.time-slot-btn').length > 0) {
                     // Only show loading if we are changing logic, otherwise keep current view for a moment
                     $('#time-slots-grid').html('<div class="text-center py-5" style="grid-column: span 2;"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Checking availability...</p></div>');
                     $('#slotDurationAlert').addClass('d-none');
                }

                if (!doctorId) {
                    if (fp) { fp.clear(); fp.set('disable', [() => true]); }
                    $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><i class="fas fa-user-md fa-3x mb-3 opacity-25"></i><p>Select a doctor to view schedule</p></div>');
                    return;
                }

                $('#dateLoadingMsg').text('Loading available dates...');
                
                // Fetch available dates for the doctor
                $.ajax({
                    url: '{{ route("staff.appointments.doctor-dates") }}',
                    method: 'GET',
                    data: { doctor_id: doctorId },
                    success: function(response) {
                        $('#dateLoadingMsg').text('');
                        if (response.dates && response.dates.length > 0) {
                            availableDates = response.dates.map(d => d.date);
                            
                            // IMPORTANT: Ensure the *current* appointment date is enabled, even if it's not present (e.g. today/past or fully booked but occupied by this appointment)
                            // We check if we are editing the same doctor as the original appointment
                            if (doctorId == initialDoctorId) {
                                if (!availableDates.includes(initialDate)) {
                                    availableDates.push(initialDate);
                                }
                            }

                            // Enable only the available dates in flatpickr
                            fp.set('disable', [
                                function(date) {
                                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                                    return !availableDates.includes(dStr);
                                }
                            ]);
                            
                            // Logic to determine which date to select
                            let dateToLoad = $('#appointment_date').val();
                            if (!dateToLoad || !availableDates.includes(dateToLoad)) {
                                if (availableDates.includes(initialDate) && doctorId == initialDoctorId) {
                                    dateToLoad = initialDate;
                                } else {
                                    dateToLoad = availableDates[0];
                                }
                            }
                            
                            if (dateToLoad) {
                                fp.setDate(dateToLoad);
                                $('#appointment_date').val(dateToLoad);
                                loadTimeSlots(dateToLoad);
                            } else {
                                $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><div class="alert alert-warning">No available dates found for this doctor.</div></div>');
                            }
                            
                        } else {
                            // No dates at all
                             // Force enable initial date if same doctor?
                             if (doctorId == initialDoctorId) {
                                 availableDates = [initialDate];
                                 fp.set('disable', [
                                    function(date) {
                                        const dStr = flatpickr.formatDate(date, "Y-m-d");
                                        return !availableDates.includes(dStr);
                                    }
                                ]);
                                fp.setDate(initialDate);
                                loadTimeSlots(initialDate);
                             } else {
                                fp.set('disable', [() => true]);
                                $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>No available dates for this doctor</p></div>');
                             }
                        }
                    },
                    error: function() {
                        $('#dateLoadingMsg').text('Error loading dates').addClass('text-danger');
                    }
                });
            });

            // Trigger initial load
            $('#doctor_id').trigger('change');

            // ============ LOAD SLOTS ============
            function loadTimeSlots(selectedDate) {
                const doctorId = $('#doctor_id').val();
                const grid = $('#time-slots-grid');
                
                if (!doctorId || !selectedDate) return;

                grid.html('<div class="text-center py-5" style="grid-column: span 2;"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading slots...</p></div>');
                
                // We pass appointment_id to ensure the current slot is marked available
                $.ajax({
                    url: '{{ route("staff.appointments.doctor-slots") }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: selectedDate,
                        appointment_id: appointmentId 
                    },
                    success: function(response) {
                        try {
                            if (response.slots && response.slots.length > 0) {
                                const d = new Date(selectedDate);
                                const dayName = d.toLocaleDateString('en-US', { weekday: 'long', timeZone: 'UTC' }); 
                                const formattedDate = d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
                                
                                let slotHtml = `
                                    <div style="grid-column: span 2;" class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                            <span class="text-primary fw-bold"><i class="fas fa-calendar-alt me-2"></i>${dayName}</span>
                                            <span class="badge bg-white text-muted border">${formattedDate}</span>
                                        </div>
                                    </div>
                                `;
                                
                                // Check for today to filter past slots
                                const now = new Date();
                                const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                                const isToday = (selectedDate === todayStr);
                                const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();

                                let visibleSlots = 0;
                                response.slots.forEach(slot => {
                                    // Parse start time
                                    const [h, m] = slot.start.split(':').map(Number);
                                    const slotTotalMinutes = h * 60 + m;

                                    // Filter past slots logic
                                    // EXCEPTION: If this slot matches the Initial/Current Appointment Start Time, we MUST show it even if it's in the past (because we are editing it)
                                    const isCurrentAppointmentSlot = (slot.start === initialStartTime && selectedDate === initialDate && doctorId == initialDoctorId);
                                    
                                    if (!isCurrentAppointmentSlot && isToday && slotTotalMinutes < currentTotalMinutes + 15) {
                                        return;
                                    }

                                    const isBooked = !slot.available;
                                    const disabledClass = isBooked ? 'disabled' : '';
                                    const bookedText = isBooked ? ' <small class="text-danger ms-1">(Booked)</small>' : '';
                                    
                                    // Check if this slot should be 'selected'
                                    // It is selected if it matches the current input values (which default to initial values)
                                    const currentInputStart = $('#start_time').val();
                                    const isSelected = (slot.start === currentInputStart && selectedDate === $('#appointment_date').val());
                                    const selectedClass = isSelected ? 'selected' : '';

                                    const parts = slot.display.split(' - ');
                                    const startTimeDisplay = parts[0] || slot.start;

                                    slotHtml += `
                                        <div class="time-slot-btn ${disabledClass} ${selectedClass}" 
                                             data-start="${slot.start}" 
                                             data-end="${slot.end}" >
                                            ${startTimeDisplay}${bookedText}
                                        </div>
                                    `;
                                    visibleSlots++;
                                    
                                    // If we just rendered the selected slot, ensure duration is calculated/shown
                                    if (isSelected) {
                                        calculateSlotDuration(slot.start, slot.end);
                                    }
                                });

                                if (visibleSlots === 0) {
                                    grid.html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><div class="alert alert-info border-0 bg-light">No future slots available.</div></div>');
                                } else {
                                    grid.html(slotHtml);
                                }
                            } else {
                                const msg = response.message || 'No slots available for this date.';
                                grid.html(`<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>${msg}</p></div>`);
                            }
                        } catch (err) {
                            console.error("Error processing slots:", err);
                            grid.html('<div class="text-center py-5 text-danger" style="grid-column: span 2;"><p>Error processing schedule data.</p></div>');
                        }
                    },
                    error: function(xhr) {
                        grid.html('<div class="text-center py-5 text-danger" style="grid-column: span 2;"><p>Error connecting to server.</p></div>');
                    }
                });
            }

            // ============ SLOT CLICK HANDLER ============
            $(document).on('click', '.time-slot-btn', function() {
                if ($(this).hasClass('disabled')) return;

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
                    const [startHour, startMin] = startTime.split(':').map(Number);
                    const [endHour, endMin] = endTime.split(':').map(Number);

                    const startTotalMins = startHour * 60 + startMin;
                    const endTotalMins = endHour * 60 + endMin;
                    const durationMins = endTotalMins - startTotalMins;

                    const hours = Math.floor(durationMins / 60);
                    const mins = durationMins % 60;

                    let displayText = '';
                    if (hours > 0) displayText += `${hours}h `;
                    if (mins > 0) displayText += `${mins}m`;

                    $('#slotDurationText').text(displayText || '0m');
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
                const doctorId = $('#doctor_id').val();
                const date = $('#appointment_date').val();
                const startTime = $('#start_time').val();

                if (!doctorId || !date || !startTime) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please ensure Doctor, Date, and Time Slot are selected.',
                        confirmButtonColor: '#ee3d0cff'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
