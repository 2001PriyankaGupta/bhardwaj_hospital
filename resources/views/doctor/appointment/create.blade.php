@extends('doctor.layouts.master')

@section('title', 'Create Appointment')

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
        padding: 20px;
        background: #fff;
    }

    .slots-wrapper {
        padding: 20px;
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
        background: #5b73e8 !important;
        border-color: #5b73e8 !important;
    }

    .flatpickr-day.today {
        border-color: #5b73e8 !important;
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
        border-color: #5b73e8;
        background: #f8faff;
        color: #5b73e8;
        transform: translateY(-2px);
    }

    .time-slot-btn.selected {
        background: #5b73e8;
        color: #fff;
        border-color: #5b73e8;
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
        color: #5b73e8;
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

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-label fw-bold">Select Doctor <span class="text-danger">*</span></label>
                                        <select name="doctor_id" id="doctor_id" class="form-select form-select-lg" required>
                                            <option value="">Choose Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}"
                                                    {{ old('doctor_id', auth()->id()) == $doctor->id ? 'selected' : '' }}>
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
                                                <input type="hidden" name="appointment_date" id="appointment_date" required>
                                            </div>
                                            <div class="col-md-5 slots-wrapper">
                                                <h5 class="section-title"><i class="fas fa-clock"></i> Select Available Time</h5>
                                                <div id="time-slots-grid" class="slot-grid">
                                                    <div class="text-center py-5 text-muted invisible-on-load">
                                                        <i class="fas fa-user-md fa-3x mb-3 opacity-25"></i>
                                                        <p>Choose a doctor first</p>
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
                                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-light btn-lg px-5 me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
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

    <script>
        $(document).ready(function() {
            let fp;
            let availableDates = [];
            const urlParams = new URLSearchParams(window.location.search);
            const urlDate = urlParams.get('date');
            
            if (urlDate) {
                $('#appointment_date').val(urlDate);
            }
            
            // Initialize Flatpickr in inline mode
            fp = flatpickr("#inline-calendar", {
                inline: true,
                minDate: "today",
                defaultDate: urlDate || null,
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        // All dates disabled until doctor selected
                        return true; 
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    $('#appointment_date').val(dateStr);
                    loadTimeSlots(dateStr);
                }
            });

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

            // ============ DOCTOR SELECTION HANDLER ============
            $('#doctor_id').change(function() {
                const doctorId = $(this).val();
                
                // Reset everything
                $('#appointment_date').val('');
                $('#start_time').val('');
                $('#end_time').val('');
                $('#time-slots-grid').html('<div class="text-center py-5 text-muted"><p>Please select a date</p></div>');
                $('#slotDurationAlert').addClass('d-none');
                
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
                            
                            // Enable only the available dates in flatpickr
                            fp.set('disable', [
                                function(date) {
                                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                                    return !availableDates.includes(dStr);
                                }
                            ]);
                            
                        } else {
                            fp.set('disable', [() => true]);
                            $('#dateLoadingMsg').text('No available dates found for this doctor.').addClass('text-danger');
                        }
                    },
                    error: function() {
                        $('#dateLoadingMsg').text('Error loading dates').addClass('text-danger');
                    }
                });
            });

            // Trigger change if doctor is already selected (for pre-filled doctors)
            if($('#doctor_id').val()) {
                $('#doctor_id').trigger('change');
            }

            // ============ LOAD SLOTS ============
            function loadTimeSlots(selectedDate) {
                const doctorId = $('#doctor_id').val();
                const grid = $('#time-slots-grid');
                
                if (!doctorId || !selectedDate) return;

                grid.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading slots...</p></div>');
                $('#start_time').val('');
                $('#end_time').val('');

                $.ajax({
                    url: '{{ route('doctor.appointments.doctor-slots') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: selectedDate
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
                            
                            // Get current time for comparison if today
                            const now = new Date();
                            const todayStr = flatpickr.formatDate(now, "Y-m-d");
                            const isToday = (selectedDate === todayStr);
                            const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();

                            let visibleSlots = 0;
                            response.slots.forEach(slot => {
                                // Skip past slots if date is today
                                if (isToday) {
                                    const [h, m] = slot.start.split(':').map(Number);
                                    const slotTotalMinutes = h * 60 + m;
                                    if (slotTotalMinutes < currentTotalMinutes + 5) { // 5-minute buffer
                                        return;
                                    }
                                }

                                const disabled = !slot.available ? 'disabled' : '';
                                const subText = !slot.available ? ' (Booked)' : '';
                                const startTimeDisplay = slot.display.split(' - ')[0];
                                
                                slotHtml += `
                                    <div class="time-slot-btn ${disabled}" 
                                         data-start="${slot.start}" 
                                         data-end="${slot.end}" 
                                         data-display="${startTimeDisplay}">
                                        ${startTimeDisplay}${subText}
                                    </div>
                                `;
                                visibleSlots++;
                            });

                            if (visibleSlots === 0) {
                                grid.html('<div class="text-center py-5 text-muted"><p>No future slots available for today.</p></div>');
                            } else {
                                grid.html(slotHtml);
                            }
                        } else {
                            grid.html('<div class="text-center py-5 text-muted"><p>No slots available for this date.</p></div>');
                        }
                    },
                    error: function() {
                        grid.html('<div class="text-center py-5 text-danger"><p>Error loading slots.</p></div>');
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
                const date = $('#appointment_date').val();
                const startTime = $('#start_time').val();

                if (!date || !startTime) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select both a date and a time slot.',
                        confirmButtonColor: '#5b73e8'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
