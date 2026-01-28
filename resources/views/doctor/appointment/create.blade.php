@extends('doctor.layouts.master')

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
                <a class="btn btn-secondary" href="{{ route('doctor.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('doctor.appointments.store') }}" method="POST" id="appointmentForm">
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
                                        <input type="date" name="appointment_date" id="appointment_date"
                                            class="form-control" value="{{ old('appointment_date', date('Y-m-d')) }}"
                                            min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time" class="form-label">Start Time *</label>
                                        <select name="start_time" id="start_time" class="form-control" required>
                                            <option value="">Select Start Time</option>
                                            <!-- Fixed time options -->
                                            <option value="09:00">09:00 AM</option>
                                            <option value="09:30">09:30 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="10:30">10:30 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="11:30">11:30 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="12:30">12:30 PM</option>
                                            <option value="13:00">01:00 PM</option>
                                            <option value="13:30">01:30 PM</option>
                                            <option value="14:00">02:00 PM</option>
                                            <option value="14:30">02:30 PM</option>
                                            <option value="15:00">03:00 PM</option>
                                            <option value="15:30">03:30 PM</option>
                                            <option value="16:00">04:00 PM</option>
                                            <option value="16:30">04:30 PM</option>
                                            <option value="17:00">05:00 PM</option>
                                            <option value="17:30">05:30 PM</option>
                                            <option value="18:00">06:00 PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time" class="form-label">End Time *</label>
                                        <select name="end_time" id="end_time" class="form-control" required>
                                            <option value="">Select End Time</option>
                                            <!-- Fixed time options -->
                                            <option value="09:30">09:30 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="10:30">10:30 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="11:30">11:30 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="12:30">12:30 PM</option>
                                            <option value="13:00">01:00 PM</option>
                                            <option value="13:30">01:30 PM</option>
                                            <option value="14:00">02:00 PM</option>
                                            <option value="14:30">02:30 PM</option>
                                            <option value="15:00">03:00 PM</option>
                                            <option value="15:30">03:30 PM</option>
                                            <option value="16:00">04:00 PM</option>
                                            <option value="16:30">04:30 PM</option>
                                            <option value="17:00">05:00 PM</option>
                                            <option value="17:30">05:30 PM</option>
                                            <option value="18:00">06:00 PM</option>
                                            <option value="18:30">06:30 PM</option>
                                        </select>
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
                                    <a href="{{ route('doctor.appointments.index') }}"
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

            // Auto-update end time when start time is selected
            $('#start_time').change(function() {
                const startTime = $(this).val();
                const endTimeSelect = $('#end_time');

                if (startTime) {
                    // Enable all end time options
                    endTimeSelect.find('option').prop('disabled', false);

                    // Disable end times that are before or equal to start time
                    endTimeSelect.find('option').each(function() {
                        const endTime = $(this).val();
                        if (endTime && endTime <= startTime) {
                            $(this).prop('disabled', true);
                        }
                    });

                    // Reset end time selection if it's now invalid
                    const currentEndTime = endTimeSelect.val();
                    if (currentEndTime && currentEndTime <= startTime) {
                        endTimeSelect.val('');
                    }
                }

                // Load available resources
                loadAvailableResources();
            });

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
                    url: '{{ route('admin.appointments.resources.available') }}',
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
