@extends('staff.layouts.master')

@section('title', 'Edit Leave Application - ' . $staff->name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-black">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit mr-2 text-black"></i> Edit Leave Application -
                            {{ $staff->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <form action="{{ route('staff.leaves.update', $leave) }}" method="POST" id="leaveForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-3">
                                        <label for="leave_type" class="font-weight-bold">Leave Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('leave_type') is-invalid @enderror"
                                            id="leave_type" name="leave_type" required>
                                            <option value="">Select Leave Type</option>
                                            <option value="sick" {{ (old('leave_type') ?? $leave->leave_type) == 'sick' ? 'selected' : '' }}>Sick
                                                Leave</option>
                                            <option value="casual" {{ (old('leave_type') ?? $leave->leave_type) == 'casual' ? 'selected' : '' }}>
                                                Casual Leave</option>
                                            <option value="emergency"
                                                {{ (old('leave_type') ?? $leave->leave_type) == 'emergency' ? 'selected' : '' }}>Emergency Leave
                                            </option>
                                            <option value="vacation"
                                                {{ (old('leave_type') ?? $leave->leave_type) == 'vacation' ? 'selected' : '' }}>Vacation</option>
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
                                                    id="start_date" name="start_date" value="{{ old('start_date') ?? $leave->start_date->format('Y-m-d') }}"
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
                                                    id="end_date" name="end_date" value="{{ old('end_date') ?? $leave->end_date->format('Y-m-d') }}"
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
                                                <span id="durationText">Calculating duration...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="reason" class="font-weight-bold">Reason for Leave <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="5"
                                            placeholder="Please provide detailed reason for your leave application..." required>{{ old('reason') ?? $leave->reason }}</textarea>
                                        <small class="form-text text-muted">Please provide sufficient details for
                                            approval</small>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('staff.leaves.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Update Leave Application
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


    <style>
        .card { border: none; box-shadow: 0 0 20px rgba(0, 0, 0, 0.08); border-radius: 10px; }
        .card-header { border-radius: 10px 10px 0 0 !important; }
        .form-control { border-radius: 8px; border: 1px solid #ddd; padding: 10px 15px; transition: all 0.3s; }
        .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
        .alert { border-radius: 8px; border: none; }
        #durationInfo { background-color: #f8f9fa; border-left: 4px solid #17a2b8; }
    </style>

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            calculateDuration();

            $('#start_date, #end_date').on('change', function() {
                calculateDuration();
            });

            function calculateDuration() {
                const startDateStr = $('#start_date').val();
                const endDateStr = $('#end_date').val();

                if (!startDateStr || !endDateStr) {
                    $('#durationText').text('Select dates to see duration');
                    return;
                }

                const start = new Date(startDateStr);
                const end = new Date(endDateStr);

                if (end < start) {
                    $('#durationText').html('<span class="text-danger">End date cannot be before start date!</span>');
                    $('#end_date').addClass('is-invalid');
                    return;
                }

                $('#end_date').removeClass('is-invalid');

                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                $('#durationText').html(`<strong>Total Days:</strong> ${diffDays} day${diffDays > 1 ? 's' : ''}`);
            }

            $('#leaveForm').submit(function(e) {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                if (new Date(endDate) < new Date(startDate)) {
                    e.preventDefault();
                    Swal.fire('Error!', 'End date cannot be before start date.', 'error');
                    return false;
                }

                e.preventDefault();
                Swal.fire({
                    title: 'Update Leave Application?',
                    text: "Review changes before saving.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
