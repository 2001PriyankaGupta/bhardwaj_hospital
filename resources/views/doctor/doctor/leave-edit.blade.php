@extends('doctor.layouts.master')

@section('title', 'Edit Leave - Dr. ' . $doctor->full_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-black">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-plus mr-2 text-black"></i> Edit Leave - Dr.
                            {{ $doctor->full_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <form
                                    action="{{ route('doctor.leave.update', ['doctor' => $doctor->id, 'leave' => $leave->id]) }}"
                                    method="POST" id="leaveForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-3">
                                        <label for="leave_type" class="font-weight-bold">Leave Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('leave_type') is-invalid @enderror"
                                            id="leave_type" name="leave_type" required>
                                            <option value="">Select Leave Type</option>
                                            <option value="sick"
                                                {{ old('leave_type', $leave->leave_type) == 'sick' ? 'selected' : '' }}>Sick
                                                Leave</option>
                                            <option value="casual"
                                                {{ old('leave_type', $leave->leave_type) == 'casual' ? 'selected' : '' }}>
                                                Casual Leave</option>
                                            <option value="emergency"
                                                {{ old('leave_type', $leave->leave_type) == 'emergency' ? 'selected' : '' }}>
                                                Emergency Leave
                                            </option>
                                            <option value="vacation"
                                                {{ old('leave_type', $leave->leave_type) == 'vacation' ? 'selected' : '' }}>
                                                Vacation</option>
                                        </select>
                                        @error('leave_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="start_date" class="font-weight-bold">Start Date <span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    id="start_date" name="start_date"
                                                    value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}"
                                                    min="{{ date('Y-m-d') }}" required>
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="end_date" class="font-weight-bold">End Date <span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    id="end_date" name="end_date"
                                                    value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}"
                                                    min="{{ date('Y-m-d') }}" required>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3 ">
                                        <div class="col-12">
                                            <div class="alert alert-info" id="durationInfo">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <span id="durationText">Select dates to see duration</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="reason" class="font-weight-bold">Reason for Leave <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="5"
                                            placeholder="Please provide detailed reason for your leave application..." required>{{ old('reason', $leave->reason) }}</textarea>
                                        <small class="form-text text-muted">Please provide sufficient details for
                                            approval</small>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <strong>Important:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Leave applications are subject to approval</li>
                                                <li>Apply at least 3 days in advance for planned leaves</li>
                                                <li>Emergency leaves require prior notification</li>
                                                <li>You'll be notified via email about the status</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('doctor.doctors.leaves', $doctor) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane mr-2"></i>Update Leave Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
            // Calculate duration when dates change
            $('#start_date, #end_date').on('change', function() {
                calculateDuration();
            });

            function parseDateLocal(dateStr) {
                if (!dateStr) return null;
                const m = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (m) return new Date(parseInt(m[1]), parseInt(m[2]) - 1, parseInt(m[3]));
                const iso = dateStr.replace(' ', 'T');
                const d = new Date(iso);
                return isNaN(d) ? null : d;
            }

            function calculateDuration() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                const start = parseDateLocal(startDate);
                const end = parseDateLocal(endDate);

                if (!start || !end) {
                    $('#durationText').text('Select dates to see duration');
                    return;
                }

                // Validate dates
                if (end < start) {
                    $('#durationText').html(
                        '<span class="text-danger">End date cannot be before start date!</span>');
                    $('#end_date').addClass('is-invalid');
                    return;
                }

                $('#end_date').removeClass('is-invalid');

                const msPerDay = 1000 * 3600 * 24;
                const daysDiff = Math.floor((end.getTime() - start.getTime()) / msPerDay) + 1;

                if (daysDiff < 1) {
                    $('#durationText').html('<span class="text-danger">Invalid date range!</span>');
                    return;
                }

                const startFormatted = start.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                const endFormatted = end.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                $('#durationText').html(`
                    <strong>Leave Period:</strong> ${startFormatted} to ${endFormatted}<br>
                    <strong>Total Days:</strong> ${daysDiff} day${daysDiff > 1 ? 's' : ''}
                `);
            }

            // Initialize with existing values
            calculateDuration();
        });
    </script>
@endsection
