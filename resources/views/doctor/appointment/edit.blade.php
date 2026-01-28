@extends('doctor.layouts.master')

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
                <a class="btn btn-secondary" href="{{ route('doctor.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST"
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
                                        <input type="date" name="appointment_date" id="appointment_date"
                                            class="form-control"
                                            value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
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
                                            <option value="09:00"
                                                {{ old('start_time', $appointment->start_time) == '09:00:00' ? 'selected' : '' }}>
                                                09:00 AM</option>
                                            <option value="09:30"
                                                {{ old('start_time', $appointment->start_time) == '09:30:00' ? 'selected' : '' }}>
                                                09:30 AM</option>
                                            <option value="10:00"
                                                {{ old('start_time', $appointment->start_time) == '10:00:00' ? 'selected' : '' }}>
                                                10:00 AM</option>
                                            <option value="10:30"
                                                {{ old('start_time', $appointment->start_time) == '10:30:00' ? 'selected' : '' }}>
                                                10:30 AM</option>
                                            <option value="11:00"
                                                {{ old('start_time', $appointment->start_time) == '11:00:00' ? 'selected' : '' }}>
                                                11:00 AM</option>
                                            <option value="11:30"
                                                {{ old('start_time', $appointment->start_time) == '11:30:00' ? 'selected' : '' }}>
                                                11:30 AM</option>
                                            <option value="12:00"
                                                {{ old('start_time', $appointment->start_time) == '12:00:00' ? 'selected' : '' }}>
                                                12:00 PM</option>
                                            <option value="12:30"
                                                {{ old('start_time', $appointment->start_time) == '12:30:00' ? 'selected' : '' }}>
                                                12:30 PM</option>
                                            <option value="13:00"
                                                {{ old('start_time', $appointment->start_time) == '13:00:00' ? 'selected' : '' }}>
                                                01:00 PM</option>
                                            <option value="13:30"
                                                {{ old('start_time', $appointment->start_time) == '13:30:00' ? 'selected' : '' }}>
                                                01:30 PM</option>
                                            <option value="14:00"
                                                {{ old('start_time', $appointment->start_time) == '14:00:00' ? 'selected' : '' }}>
                                                02:00 PM</option>
                                            <option value="14:30"
                                                {{ old('start_time', $appointment->start_time) == '14:30:00' ? 'selected' : '' }}>
                                                02:30 PM</option>
                                            <option value="15:00"
                                                {{ old('start_time', $appointment->start_time) == '15:00:00' ? 'selected' : '' }}>
                                                03:00 PM</option>
                                            <option value="15:30"
                                                {{ old('start_time', $appointment->start_time) == '15:30:00' ? 'selected' : '' }}>
                                                03:30 PM</option>
                                            <option value="16:00"
                                                {{ old('start_time', $appointment->start_time) == '16:00:00' ? 'selected' : '' }}>
                                                04:00 PM</option>
                                            <option value="16:30"
                                                {{ old('start_time', $appointment->start_time) == '16:30:00' ? 'selected' : '' }}>
                                                04:30 PM</option>
                                            <option value="17:00"
                                                {{ old('start_time', $appointment->start_time) == '17:00:00' ? 'selected' : '' }}>
                                                05:00 PM</option>
                                            <option value="17:30"
                                                {{ old('start_time', $appointment->start_time) == '17:30:00' ? 'selected' : '' }}>
                                                05:30 PM</option>
                                            <option value="18:00"
                                                {{ old('start_time', $appointment->start_time) == '18:00:00' ? 'selected' : '' }}>
                                                06:00 PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time" class="form-label">End Time *</label>
                                        <select name="end_time" id="end_time" class="form-control" required>
                                            <option value="">Select End Time</option>
                                            <!-- Fixed time options -->
                                            <option value="09:30"
                                                {{ old('end_time', $appointment->end_time) == '09:30:00' ? 'selected' : '' }}>
                                                09:30 AM</option>
                                            <option value="10:00"
                                                {{ old('end_time', $appointment->end_time) == '10:00:00' ? 'selected' : '' }}>
                                                10:00 AM</option>
                                            <option value="10:30"
                                                {{ old('end_time', $appointment->end_time) == '10:30:00' ? 'selected' : '' }}>
                                                10:30 AM</option>
                                            <option value="11:00"
                                                {{ old('end_time', $appointment->end_time) == '11:00:00' ? 'selected' : '' }}>
                                                11:00 AM</option>
                                            <option value="11:30"
                                                {{ old('end_time', $appointment->end_time) == '11:30:00' ? 'selected' : '' }}>
                                                11:30 AM</option>
                                            <option value="12:00"
                                                {{ old('end_time', $appointment->end_time) == '12:00:00' ? 'selected' : '' }}>
                                                12:00 PM</option>
                                            <option value="12:30"
                                                {{ old('end_time', $appointment->end_time) == '12:30:00' ? 'selected' : '' }}>
                                                12:30 PM</option>
                                            <option value="13:00"
                                                {{ old('end_time', $appointment->end_time) == '13:00:00' ? 'selected' : '' }}>
                                                01:00 PM</option>
                                            <option value="13:30"
                                                {{ old('end_time', $appointment->end_time) == '13:30:00' ? 'selected' : '' }}>
                                                01:30 PM</option>
                                            <option value="14:00"
                                                {{ old('end_time', $appointment->end_time) == '14:00:00' ? 'selected' : '' }}>
                                                02:00 PM</option>
                                            <option value="14:30"
                                                {{ old('end_time', $appointment->end_time) == '14:30:00' ? 'selected' : '' }}>
                                                02:30 PM</option>
                                            <option value="15:00"
                                                {{ old('end_time', $appointment->end_time) == '15:00:00' ? 'selected' : '' }}>
                                                03:00 PM</option>
                                            <option value="15:30"
                                                {{ old('end_time', $appointment->end_time) == '15:30:00' ? 'selected' : '' }}>
                                                03:30 PM</option>
                                            <option value="16:00"
                                                {{ old('end_time', $appointment->end_time) == '16:00:00' ? 'selected' : '' }}>
                                                04:00 PM</option>
                                            <option value="16:30"
                                                {{ old('end_time', $appointment->end_time) == '16:30:00' ? 'selected' : '' }}>
                                                04:30 PM</option>
                                            <option value="17:00"
                                                {{ old('end_time', $appointment->end_time) == '17:00:00' ? 'selected' : '' }}>
                                                05:00 PM</option>
                                            <option value="17:30"
                                                {{ old('end_time', $appointment->end_time) == '17:30:00' ? 'selected' : '' }}>
                                                05:30 PM</option>
                                            <option value="18:00"
                                                {{ old('end_time', $appointment->end_time) == '18:00:00' ? 'selected' : '' }}>
                                                06:00 PM</option>
                                            <option value="18:30"
                                                {{ old('end_time', $appointment->end_time) == '18:30:00' ? 'selected' : '' }}>
                                                06:30 PM</option>
                                        </select>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
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
            });

            // Show/hide cancellation reason based on status
            $('#status').change(function() {
                if ($(this).val() === 'cancelled') {
                    $('#cancellation_reason_row').show();
                } else {
                    $('#cancellation_reason_row').hide();
                }
            });



            // Initialize time validation on page load
            $('#start_time').trigger('change');
        });
    </script>
@endsection
