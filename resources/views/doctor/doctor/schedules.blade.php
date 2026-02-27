@extends('doctor.layouts.master')

@section('title', 'Manage Schedule - ' . $doctor->full_name)

@section('content')
<style>
    :root {
        --primary-orange: #ff4900;
        --secondary-orange: #ff8533;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --card-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .main-content {
        background: #f4f7fa;
        min-height: 100vh;
    }

    .page-title-box {
        background: white;
        padding: 25px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        border-left: 5px solid var(--primary-orange);
    }

    .bulk-card {
        background: linear-gradient(135deg, #fff 0%, #fff5f0 100%);
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .bulk-header {
        background: var(--primary-orange);
        color: white;
        padding: 15px 25px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .day-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        height: 100%;
    }

    .day-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    }

    .day-card .card-header {
        background: white;
        border-bottom: 1px solid #eee;
        padding: 15px;
        border-radius: 15px 15px 0 0 !important;
    }

    .day-card .form-check-input:checked {
        background-color: var(--primary-orange);
        border-color: var(--primary-orange);
    }

    .form-label-bold {
        font-weight: 650;
        color: #444;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .premium-input {
        border: 2px solid #edf2f7;
        border-radius: 10px;
        padding: 10px 15px;
        transition: border-color 0.2s;
    }

    .premium-input:focus {
        border-color: var(--primary-orange);
        box-shadow: none;
    }

    .btn-premium {
        background: var(--primary-orange);
        color: white;
        border: none;
        padding: 12px 35px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(255, 73, 0, 0.3);
    }

    .btn-premium:hover {
        background: #e64200;
        color: white;
        transform: scale(1.02);
    }

    .slot-preview-btn {
        background: #fff;
        border: 2px solid var(--primary-orange);
        color: var(--primary-orange);
        padding: 5px 15px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 10px;
        cursor: pointer;
    }

    .slot-preview-btn:hover {
        background: var(--primary-orange);
        color: white;
    }

    .disabled-overlay {
        background: rgba(244, 247, 250, 0.6);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 5;
        border-radius: 15px;
        display: none;
        pointer-events: none;
    }

    .day-card.disabled .disabled-overlay {
        display: block;
    }

    .day-card .card-header {
        position: relative;
        z-index: 6;
        background: white !important;
        border-radius: 15px 15px 0 0 !important;
    }

    .badge-slot {
        background: #fff5f0;
        color: var(--primary-orange);
        border: 1px solid #ffccb3;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        display: inline-block;
        margin: 2px;
    }
</style>

<div class="container-fluid mt-4">
    <div class="page-title-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1 text-orange fw-bold">Schedule Management</h1>
                <p class="text-muted mb-0">Personalize your weekly availability for <span class="fw-bold">Dr. {{ $doctor->full_name }}</span></p>
            </div>
            <div class="d-flex gap-2">
                <!-- <a class="btn btn-outline-primary" href="{{ route('doctor.doctors.date-management', $doctor) }}">
                    <i class="fas fa-calendar-alt"></i> Date-wise Calendar
                </a> -->
                <a class="btn btn-secondary" href="{{ route('doctor.dashboard') }}">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('doctor.doctors.schedules.store', $doctor) }}" method="POST">
        @csrf
        <!-- Bulk Settings Row -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card bulk-card">
                    <div class="bulk-header">
                        <i class="fas fa-bolt"></i> Bulk Generation Settings
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label-bold">Generate Slots From</label>
                                <input type="date" name="bulk_start_date" class="form-control premium-input" value="{{ date('Y-m-d') }}">
                                <small class="text-muted mb-3 d-block">Start date for calendar population</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-bold">Generate Until</label>
                                <input type="date" name="bulk_end_date" class="form-control premium-input" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                <small class="text-muted mb-3 d-block">Default: 30 days from start</small>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white p-3 border rounded-3 border-orange" style="margin-top: -15px;">
                                    <div class="d-flex align-items-center text-orange gap-2 mb-1">
                                        <i class="fas fa-circle-info"></i>
                                        <span class="fw-bold">Information</span>
                                    </div>
                                    <p class="small text-muted mb-0">System will automatically create daily time slots based on the weekly pattern below for the selected range.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Days Grid -->
        <div class="row">
            @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                @php
                    $schedule = $schedules->where('day_of_week', $day)->first();
                @endphp
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card day-card {{ $schedule && $schedule->is_available ? '' : 'disabled' }}" id="card_{{ $day }}">
                        <div class="disabled-overlay"></div>
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">{{ $day }}</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input day-checkbox"
                                    id="available_{{ $day }}"
                                    name="available[{{ $day }}]" value="1"
                                    {{ ($schedule && $schedule->is_available) || (!$schedule && $day != 'sunday') ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <input type="hidden" name="day_of_week[{{ $day }}]" value="{{ $day }}">

                            <div class="mb-3">
                                <label class="form-label-bold">Working Hours</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="time" class="form-control premium-input time-input"
                                        name="start_time[{{ $day }}]"
                                        value="{{ $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00' }}">
                                    <span class="text-muted">to</span>
                                    <input type="time" class="form-control premium-input time-input"
                                        name="end_time[{{ $day }}]"
                                        value="{{ $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-7">
                                    <label class="form-label-bold">Slot Time</label>
                                    <select class="form-control premium-input time-input" name="slot_duration[{{ $day }}]">
                                        <option value="15" {{ $schedule && $schedule->slot_duration == 15 ? 'selected' : '' }}>15 Mins</option>
                                        <option value="30" {{ ($schedule && $schedule->slot_duration == 30) || !$schedule ? 'selected' : '' }}>30 Mins</option>
                                        <option value="45" {{ $schedule && $schedule->slot_duration == 45 ? 'selected' : '' }}>45 Mins</option>
                                        <option value="60" {{ $schedule && $schedule->slot_duration == 60 ? 'selected' : '' }}>60 Mins</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <label class="form-label-bold">Max Limit</label>
                                    <input type="number" class="form-control premium-input time-input"
                                        name="max_patients[{{ $day }}]" min="1" max="50"
                                        value="{{ $schedule ? $schedule->max_patients : 10 }}">
                                </div>
                            </div>

                            @if ($schedule && $schedule->is_available && count($schedule->time_slots) > 0)
                                <button type="button" class="slot-preview-btn" onclick="showSlots('{{ $day }}', {{ json_encode($schedule->time_slots) }})">
                                    <i class="far fa-eye"></i> View Slots
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center my-5">
            <button type="submit" class="btn-premium btn-lg">
                <i class="fas fa-check-circle me-2"></i> Confirm and Update Schedule
            </button>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="slotPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-orange text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title"><i class="far fa-clock me-2"></i> <span id="previewDayName"></span> Slots</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="slotsContainer">
                <!-- Slots will be injected here -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
   
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
    let baseUrl = "{{ config('app.url') }}";
    function showSlots(day, slots) {
        $('#previewDayName').text(day.charAt(0).toUpperCase() + day.slice(1));
        let html = '<div class="d-flex flex-wrap gap-2 justify-content-center">';
        slots.forEach(slot => {
            html += `<span class="badge-slot">${slot.display}</span>`;
        });
        html += '</div>';
        $('#slotsContainer').html(html);
        $('#slotPreviewModal').modal('show');
    }

    $(document).ready(function() {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#ff4900'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ff4900'
            });
        @endif

        $('.day-checkbox').change(function() {
            const day = $(this).attr('id').replace('available_', '');
            const card = $('#card_' + day);
            if ($(this).is(':checked')) {
                card.removeClass('disabled');
                card.find('.time-input').prop('disabled', false);
            } else {
                card.addClass('disabled');
                card.find('.time-input').prop('disabled', true);
            }
        });

        // Initialize state
        $('.day-checkbox').each(function() {
            $(this).trigger('change');
        });
    });
</script>
@endsection

