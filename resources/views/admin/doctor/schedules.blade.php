@extends('admin.layouts.master')

@section('title', 'Manage Schedule - ' . $doctor->full_name)
<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
                    <h1 class="h3 mb-0 text-orange fw-bold">Schedule Management - Dr. {{ $doctor->full_name }}</h1>
                    <p class="text-muted mb-0">Configure weekly timetable, appointment slots, working hours and availability
                        schedule</p>
                </div>
            </div>
            <div class="action-buttons">

                <a class="btn btn-secondary" href="{{ route('admin.doctors.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <!-- Weekly Schedule Form -->
                        <form action="{{ route('admin.doctors.schedules.store', $doctor) }}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    @php
                                        $schedule = $schedules->where('day_of_week', $day)->first();
                                    @endphp
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input day-checkbox"
                                                            id="available_{{ $day }}"
                                                            name="available[{{ $day }}]" value="1"
                                                            {{ $schedule && $schedule->is_available ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="available_{{ $day }}">
                                                            {{ ucfirst($day) }}
                                                        </label>
                                                    </div>
                                                </h5>
                                            </div>
                                            <div
                                                class="card-body day-schedule {{ $schedule && $schedule->is_available ? '' : 'bg-light' }}">
                                                <input type="hidden" name="day_of_week[{{ $day }}]"
                                                    value="{{ $day }}">

                                                <div class="form-group">
                                                    <label>Start Time</label>
                                                    <input type="time" class="form-control time-input"
                                                        name="start_time[{{ $day }}]"
                                                        value="{{ $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00' }}"
                                                        {{ $schedule && $schedule->is_available ? '' : 'disabled' }}>
                                                </div>

                                                <div class="form-group">
                                                    <label>End Time</label>
                                                    <input type="time" class="form-control time-input"
                                                        name="end_time[{{ $day }}]"
                                                        value="{{ $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}"
                                                        {{ $schedule && $schedule->is_available ? '' : 'disabled' }}>
                                                </div>

                                                <div class="form-group">
                                                    <label>Slot Duration (minutes)</label>
                                                    <select class="form-control time-input"
                                                        name="slot_duration[{{ $day }}]"
                                                        {{ $schedule && $schedule->is_available ? '' : 'disabled' }}>
                                                        <option value="15"
                                                            {{ $schedule && $schedule->slot_duration == 15 ? 'selected' : '' }}>
                                                            15 minutes</option>
                                                        <option value="30"
                                                            {{ $schedule && $schedule->slot_duration == 30 ? 'selected' : '' }}>
                                                            30 minutes</option>
                                                        <option value="45"
                                                            {{ $schedule && $schedule->slot_duration == 45 ? 'selected' : '' }}>
                                                            45 minutes</option>
                                                        <option value="60"
                                                            {{ $schedule && $schedule->slot_duration == 60 ? 'selected' : '' }}>
                                                            60 minutes</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Max Patients</label>
                                                    <input type="number" class="form-control time-input"
                                                        name="max_patients[{{ $day }}]" min="1"
                                                        max="50"
                                                        value="{{ $schedule ? $schedule->max_patients : 10 }}"
                                                        {{ $schedule && $schedule->is_available ? '' : 'disabled' }}>
                                                </div>

                                                @if ($schedule)
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            Time Slots:
                                                            @foreach ($schedule->time_slots as $slot)
                                                                <br>{{ $slot['display'] }}
                                                            @endforeach
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Save All Schedules
                                </button>
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
        });
        $(document).ready(function() {
            // Enable/disable time inputs based on availability checkbox
            $('.day-checkbox').change(function() {
                const cardBody = $(this).closest('.card').find('.day-schedule');
                const inputs = cardBody.find('.time-input');

                if ($(this).is(':checked')) {
                    cardBody.removeClass('bg-light');
                    inputs.prop('disabled', false);
                } else {
                    cardBody.addClass('bg-light');
                    inputs.prop('disabled', true);
                }
            });

            // Trigger change event on page load
            $('.day-checkbox').trigger('change');
        });
    </script>
@endsection
