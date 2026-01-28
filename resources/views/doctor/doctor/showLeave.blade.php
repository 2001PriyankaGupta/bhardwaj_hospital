<!-- resources/views/doctor/leaves/show.blade.php -->
@extends('doctor.layouts.master')

@section('title', 'Leave Details - Dr. ' . $doctor->full_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-orange fw-bold">Leave Details</h1>
            <a href="{{ route('doctor.doctor.index', ['doctor' => $doctor->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Leave Application Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Doctor Information</h6>
                                <p><strong>Name:</strong> Dr. {{ $doctor->full_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Leave Information</h6>
                                <p><strong>Type:</strong> <span
                                        class="badge badge-info text-capitalize">{{ $leave->leave_type }}</span></p>
                                <p><strong>Status:</strong>
                                    @if ($leave->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($leave->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Leave Period</h6>
                                <p><strong>From:</strong> {{ $leave->start_date->format('M d, Y') }}</p>
                                <p><strong>To:</strong> {{ $leave->end_date->format('M d, Y') }}</p>
                                <p><strong>Duration:</strong> {{ $leave->duration }}
                                    day{{ $leave->duration > 1 ? 's' : '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Application Details</h6>
                                <p><strong>Applied On:</strong> {{ $leave->created_at->format('M d, Y h:i A') }}</p>
                                @if ($leave->approved_by)
                                    <p><strong>Approved By:</strong> Admin</p>
                                @endif
                                @if ($leave->approved_at)
                                    <p><strong>Approved On:</strong> {{ $leave->approved_at->format('M d, Y h:i A') }}</p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h6>Reason for Leave</h6>
                                <div class="border p-3 rounded bg-light">
                                    {{ $leave->reason }}
                                </div>
                            </div>
                        </div>

                        @if ($leave->admin_remarks)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6>Admin Remarks</h6>
                                    <div class="border p-3 rounded bg-light">
                                        {{ $leave->admin_remarks }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if ($leave->status == 'pending')
                                    <!-- Approve/Reject buttons for admin (if needed) -->
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('doctor.doctor.index', ['doctor' => $doctor->id]) }}"
                                    class="btn btn-secondary">Close</a>
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
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

@endsection
