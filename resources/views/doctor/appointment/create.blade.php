@extends('doctor.layouts.master')

@section('title', 'Create Appointment')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Modern Appointment UI Styles */
    .appointment-picker-card {
       
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
                    <h1 class="h3 mb-0 text-orange fw-bold">Schedule New Appointment</h1>
                    <p class="text-muted mb-0">Book appointment with doctor and allocate resources</p>
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
                        <form action="{{ route('doctor.appointments.store') }}" method="POST" id="appointmentForm">
                            @csrf

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
                                    <input type="hidden" name="doctor_id" id="doctor_id" value="{{ $currentDoctor->id ?? '' }}">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="appointment-picker-card">
                                        <div class="row g-0">
                                            <div class="col-md-7 calendar-wrapper">
                                                <h5 class="section-title"><i class="fas fa-calendar-day"></i> Select Date</h5>
                                                <div id="inline-calendar"></div>
                                                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-5 slots-wrapper">
                                                <h5 class="section-title"><i class="fas fa-clock"></i> Select Available Time</h5>
                                                <div id="time-slots-grid" class="slot-grid">
                                                    <div class="text-center py-5 text-muted" style="grid-column: span 2;">
                                                        <i class="fas fa-calendar-day fa-3x mb-3 opacity-25"></i>
                                                        <p>Select a doctor and date to view available slots</p>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="start_time" id="start_time" required>
                                                <input type="hidden" name="end_time" id="end_time" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted" id="dateLoadingMsg"></small>
                                        <small class="text-muted" id="startTimeLoadingMsg"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                        <label for="patient_id" class="form-label fw-bold">Patient <span class="text-danger">*</span></label>
                                        <select name="patient_id" id="patient_id" class="form-select" required>
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
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="resource_id" class="form-label fw-bold">Resource (Optional)</label>
                                        <select name="resource_id" id="resource_id" class="form-select">
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
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label fw-bold">Notes (Optional)</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any special instructions here...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-12 text-end">
                                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-light btn-sm px-5 me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-sm px-5" style="background: #ee3d0cff; border-color: #ee3d0cff;">
                                        <i class="fas fa-calendar-check me-2"></i> Confirm Schedule
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
            
            // Set default date to today
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            $('#appointment_date').val(todayStr);
            
            // Check if Flatpickr is loaded
            if (typeof flatpickr === 'undefined') {
                console.error('Flatpickr library not loaded!');
                $('#inline-calendar').html('<div class="alert alert-danger">Calendar library failed to load. Please refresh the page.</div>');
                return;
            }

            try {
                // Initialize Flatpickr with proper weekday display
                fp = flatpickr("#inline-calendar", {
                    inline: true,
                    minDate: "today",
                    defaultDate: today,
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                    locale: {
                        firstDayOfWeek: 1 // Monday as first day of week
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        $('#appointment_date').val(dateStr);
                        if ($('#doctor_id').val()) {
                            loadTimeSlots(dateStr);
                        }
                    },
                    // Ensure proper weekdays
                    onReady: function(selectedDates, dateStr, instance) {
                        // Fix any potential display issues
                        $('.flatpickr-weekday').each(function() {
                            $(this).css('font-weight', '600');
                        });
                    }
                });
            } catch (e) {
                console.error('Flatpickr init error:', e);
                $('#inline-calendar').html('<div class="alert alert-danger">Error initializing calendar.</div>');
            }

            // Automatically load doctor availability on page load
            const initialDoctorId = $('#doctor_id').val();
            if(initialDoctorId) {
                // We need to wait a tiny bit for Flatpickr to be fully ready
                setTimeout(() => {
                    $('#doctor_id').trigger('change');
                }, 100);
            }

            // Success message handling
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

            // Error message handling
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

            // ============ DOCTOR SELECTION HANDLER ============
            $('#doctor_id').change(function() {
                const doctorId = $(this).val();
                
                // Show loading state immediately to indicate activity
                $('#time-slots-grid').html('<div class="text-center py-5" style="grid-column: span 2;"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Checking availability...</p></div>');
                $('#slotDurationAlert').addClass('d-none');
                
                if (!doctorId) {
                    if (fp) {
                        fp.clear();
                        fp.set('disable', [() => true]);
                    }
                    $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><i class="fas fa-user-md fa-3x mb-3 opacity-25"></i><p>Select a doctor to view schedule</p></div>');
                    return;
                }

                $('#dateLoadingMsg').text('Loading available dates...');
                
                // Fetch available dates for the doctor
                $.ajax({
                    url: '{{ route("doctor.appointments.doctor-dates") }}',
                    method: 'GET',
                    data: { doctor_id: doctorId },
                    success: function(response) {
                        $('#dateLoadingMsg').text('');
                        if (response.dates && response.dates.length > 0) {
                            availableDates = response.dates.map(d => d.date);
                            
                            // Enable only the available dates in flatpickr
                            fp.set('disable', [
                                function(date) {
                                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                                    return !availableDates.includes(dStr);
                                }
                            ]);
                            
                            // Determine which date to load:
                            // 1. Currently selected date (if valid)
                            // 2. Today (if valid)
                            // 3. First available date
                            
                            const currentSelected = $('#appointment_date').val();
                            const todayYMD = new Date().toISOString().split('T')[0];
                            
                            let dateToLoad = null;
                            
                            if (currentSelected && availableDates.includes(currentSelected)) {
                                dateToLoad = currentSelected;
                            } else if (availableDates.includes(todayYMD)) {
                                dateToLoad = todayYMD;
                            } else {
                                // Default to first available date
                                dateToLoad = availableDates[0];
                            }
                            
                            if (dateToLoad) {
                                fp.setDate(dateToLoad);
                                $('#appointment_date').val(dateToLoad);
                                loadTimeSlots(dateToLoad);
                            } else {
                                $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>No available dates for this doctor</p></div>');
                            }
                            
                        } else {
                            fp.set('disable', [() => true]);
                            $('#dateLoadingMsg').text('No available dates found for this doctor.').addClass('text-danger');
                            $('#time-slots-grid').html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>No available dates for this doctor</p></div>');
                        }
                    },
                    error: function() {
                        $('#dateLoadingMsg').text('Error loading dates').addClass('text-danger');
                        $('#time-slots-grid').html('<div class="text-center py-5 text-danger" style="grid-column: span 2;"><p>Error connecting to server.</p></div>');
                    }
                });
            });

            // ============ LOAD SLOTS ============
            function loadTimeSlots(selectedDate) {
                const doctorId = $('#doctor_id').val();
                const grid = $('#time-slots-grid');
                
                if (!doctorId) {
                    grid.html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>Please select a doctor first.</p></div>');
                    return;
                }
                
                if (!selectedDate) return;

                // Show loading state
                grid.html('<div class="text-center py-5" style="grid-column: span 2;"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading available slots...</p></div>');
                
                // Reset inputs
                $('#start_time').val('');
                $('#end_time').val('');
                $('#slotDurationAlert').addClass('d-none');
                $('.time-slot-btn').removeClass('selected');

                $.ajax({
                    url: '{{ route("doctor.appointments.doctor-slots") }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: selectedDate
                    },
                    success: function(response) {
                        try {
                            if (response.slots && response.slots.length > 0) {
                                // Formatting header
                                const d = new Date(selectedDate);
                                const dayName = d.toLocaleDateString('en-US', { weekday: 'long', timeZone: 'UTC' }); 
                                const formattedDate = d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
                                
                                let slotHtml = `
                                    <div style="grid-column: 1 / -1;" class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                            <span class="text-orange fw-bold"><i class="fas fa-calendar-alt me-2"></i>${dayName}</span>
                                            <span class="badge bg-white text-muted border">${formattedDate}</span>
                                        </div>
                                    </div>
                                `;
                                
                                let visibleSlots = 0;
                                response.slots.forEach(slot => {
                                    const isBooked = !slot.available;
                                    const disabledClass = isBooked ? 'disabled' : '';
                                    const bookedText = isBooked ? ' <small class="text-danger ms-1">(Booked)</small>' : '';
                                    
                                    // Extract start - end for display
                                    const parts = slot.display.split(' - ');
                                    const startTimeDisplay = parts[0] || slot.start;

                                    slotHtml += `
                                        <div class="time-slot-btn ${disabledClass}" 
                                             data-start="${slot.start}" 
                                             data-end="${slot.end}" >
                                             ${startTimeDisplay}${bookedText}
                                        </div>
                                    `;
                                    visibleSlots++;
                                });

                                if (visibleSlots === 0) {
                                    grid.html('<div class="text-center py-5 text-muted" style="grid-column: span 2;"><div class="alert alert-info border-0 bg-light"><i class="fas fa-info-circle me-2"></i>No future slots available for this date.</div></div>');
                                } else {
                                    grid.html(slotHtml);
                                }
                            } else {
                                // Explicit message from backend or empty slots
                                const msg = response.message || 'No slots available for this date.';
                                grid.html(`<div class="text-center py-5 text-muted" style="grid-column: span 2;"><p>${msg}</p></div>`);
                            }
                        } catch (err) {
                            console.error("Error processing slots:", err);
                            grid.html('<div class="text-center py-5 text-danger" style="grid-column: span 2;"><p>Error processing schedule data.</p></div>');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        grid.html('<div class="text-center py-5 text-danger" style="grid-column: span 2;"><p>Error loading slots from server.</p></div>');
                    }
                });
            }

            // ============ SLOT CLICK HANDLER ============
            $(document).on('click', '.time-slot-btn', function() {
                // Ignore if disabled
                if ($(this).hasClass('disabled')) return;

                // Toggle selection visual
                $('.time-slot-btn').removeClass('selected');
                $(this).addClass('selected');
                
                const start = $(this).data('start');
                const end = $(this).data('end');
                
                // Set hidden inputs
                $('#start_time').val(start);
                $('#end_time').val(end);
                
                // Trigger duration calc
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

            // Form validation
            $('#appointmentForm').submit(function(e) {
                const doctorId = $('#doctor_id').val();
                const date = $('#appointment_date').val();
                const startTime = $('#start_time').val();

                if (!doctorId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select a doctor.',
                        confirmButtonColor: '#ee3d0cff'
                    });
                    return false;
                }

                if (!date || !startTime) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select both a date and a time slot.',
                        confirmButtonColor: '#ee3d0cff'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection