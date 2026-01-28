@extends('doctor.layouts.master')

@section('title', 'Leave Management - ' . $doctor->full_name)

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    .action-btn {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .gap-1>* {
        margin: 0 2px;
    }

    .badge-pill {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    }

    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>


@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">

                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Leave Management - Dr. {{ $doctor->full_name }}</h1>

                </div>
            </div>
            <div class="action-buttons">
                <!-- Add this new button -->
                <a class="btn btn-primary" href="{{ route('doctor.leave.create', ['doctor' => $doctor->id]) }}">
                    <i class="fas fa-plus"></i> Apply for Leave
                </a>

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">


                        <!-- Leave Applications Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list mr-2"></i>Leave Applications
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover w-100" id="leavesTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Leave Period</th>
                                                <th width="10%">Type</th>
                                                <th width="10%">Duration</th>
                                                <th width="20%">Reason</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Applied On</th>
                                                <th width="15%" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leaves as $leave)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $leave->end_date->format('M d, Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $leave->start_date->diffInDays($leave->end_date) + 1 }} days
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info badge-pill text-capitalize"
                                                            style="color: rgb(233, 60, 60)">
                                                            {{ $leave->leave_type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold">
                                                            {{ $leave->duration }}
                                                            day{{ $leave->duration > 1 ? 's' : '' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="text-sm">
                                                            {{ Str::limit($leave->reason, 50) }}
                                                            @if (strlen($leave->reason) > 50)
                                                                <a href="javascript:void(0)"
                                                                    class="text-primary view-reason"
                                                                    data-reason="{{ $leave->reason }}" data-toggle="tooltip"
                                                                    title="View full reason">
                                                                    Read more
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($leave->status == 'approved')
                                                            <span class="badge badge-success badge-pill px-3"
                                                                style="color: rgb(21, 216, 64)">
                                                                <i class="fas fa-check mr-1"></i>Approved
                                                            </span>
                                                        @elseif($leave->status == 'rejected')
                                                            <span class="badge badge-danger badge-pill px-3"
                                                                style="color: rgb(233, 60, 60)">
                                                                <i class="fas fa-times mr-1"></i>Rejected
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning badge-pill px-3"
                                                                style="color: rgb(244, 234, 48)">
                                                                <i class="fas fa-clock mr-1"></i>Pending
                                                            </span>
                                                        @endif

                                                        @if ($leave->admin_remarks)
                                                            <br>
                                                            <small class="text-muted" data-toggle="tooltip"
                                                                title="{{ $leave->admin_remarks }}">
                                                                {{ Str::limit($leave->admin_remarks, 20) }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="text-sm">
                                                            <strong>{{ $leave->created_at->format('M d, Y') }}</strong>
                                                            <br>
                                                            <small
                                                                class="text-muted">{{ $leave->created_at->format('h:i A') }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <!-- View Button -->
                                                            <button type="button"
                                                                class="btn btn-info btn-sm action-btn view-leave"
                                                                data-leave='@json($leave)'
                                                                data-doctor="{{ $doctor->full_name }}"
                                                                title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>

                                                            @if ($leave->status == 'pending')
                                                                {{-- Pending: allow edit and delete (cancel) --}}
                                                                @if (Route::has('doctor.leave.edit'))
                                                                    <a href="{{ route('doctor.leave.edit', ['doctor' => $doctor->id, 'leave' => $leave->id]) }}"
                                                                        class="btn btn-warning btn-sm action-btn edit-leave"
                                                                        title="Edit Leave">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="/doctor/{{ $doctor->id }}/leaves/{{ $leave->id }}/edit"
                                                                        class="btn btn-warning btn-sm action-btn edit-leave"
                                                                        title="Edit Leave">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @endif

                                                                <form action="{{ route('doctor.leaves.destroy', $leave) }}"
                                                                    method="POST" class="d-inline delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm action-btn"
                                                                        title="Delete Leave"
                                                                        onclick="return confirm('Are you sure you want to delete this leave application?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                {{-- Approved/rejected: do not show delete --}}
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                                            <h5>No leave applications found</h5>
                                                            <p>No leave records available for this doctor</p>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Leave Modal - Bootstrap 5 Compatible -->
    <div class="modal fade" id="viewLeaveModal" tabindex="-1" aria-labelledby="viewLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewLeaveModalLabel">Leave Application Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="leaveDetails">
                    <!-- Content will be loaded dynamically -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading leave details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Leave Modal - Bootstrap 5 Compatible -->
    <div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-labelledby="rejectLeaveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectLeaveModalLabel">Reject Leave Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="rejectLeaveForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="admin_remarks" class="form-label">Rejection Reason *</label>
                            <textarea class="form-control" id="admin_remarks" name="admin_remarks" rows="4"
                                placeholder="Please provide reason for rejecting this leave application..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Leave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Bootstrap Modal JS (important for modals to work) -->

    <script>
        let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            // Initialize DataTable WITHOUT destroying original markup
            const table = $('#leavesTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [6, 'desc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search leaves...",
                },
                columnDefs: [{
                    orderable: false,
                    targets: [7] // Actions column
                }],
                // Preserve original event handlers
                drawCallback: function(settings) {
                    // Reinitialize tooltips after DataTable redraw
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            $(document).on('click', '.view-leave', function(e) {
                e.preventDefault();

                const raw = $(this).attr('data-leave') || $(this).data('leave');
                let leave;
                try {
                    leave = (typeof raw === 'string') ? JSON.parse(raw) : raw;
                } catch (err) {
                    console.error('Failed to parse leave data', err, raw);
                    return;
                }
                const doctorName = $(this).data('doctor');

                // Debug
                console.log('Leave data:', leave);
                console.log('Doctor:', doctorName);

                // Helper functions (robust)
                function parseDateLocal(dateStr) {
                    if (!dateStr) return null;
                    const ymd = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                    if (ymd) return new Date(parseInt(ymd[1]), parseInt(ymd[2]) - 1, parseInt(ymd[3]));
                    const iso = dateStr.replace(' ', 'T');
                    const d = new Date(iso);
                    return isNaN(d) ? null : d;
                }

                function formatDate(dateString) {
                    const d = parseDateLocal(dateString);
                    return d ? d.toLocaleDateString('en-GB') : 'N/A';
                }

                function formatDateTime(dateString) {
                    const d = parseDateLocal(dateString);
                    return d ? d.toLocaleString('en-GB') : 'N/A';
                }

                function getStatusBadge(status) {
                    if (status === 'approved')
                        return '<span class="badge badge-success bg-success text-capitalize">Approved</span>';
                    if (status === 'rejected')
                        return '<span class="badge badge-danger bg-danger text-capitalize">Rejected</span>';
                    return '<span class="badge badge-warning bg-warning text-capitalize">Pending</span>';
                }

                const leaveDetails = `
        <div class="row">
            <div class="col-md-6">
                <h6>Doctor Information</h6>
                <p><strong>Name:</strong> ${doctorName}</p>
            </div>
            <div class="col-md-6">
                <h6>Leave Information</h6>
                <p><strong>Type:</strong> <span class="badge bg-info text-capitalize">${leave.leave_type}</span></p>
                <p><strong>Status:</strong> ${getStatusBadge(leave.status)}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6>Leave Period</h6>
                <p><strong>From:</strong> ${formatDate(leave.start_date)}</p>
                <p><strong>To:</strong> ${formatDate(leave.end_date)}</p>
                <p><strong>Duration:</strong> ${leave.duration} day${leave.duration > 1 ? 's' : ''}</p>
            </div>
            <div class="col-md-6">
                <h6>Application Details</h6>
                <p><strong>Applied On:</strong> ${formatDateTime(leave.created_at)}</p>
                ${leave.approved_by ? `<p><strong>Approved By:</strong> Admin</p>` : ''}
                ${leave.approved_at ? `<p><strong>Approved On:</strong> ${formatDateTime(leave.approved_at)}</p>` : ''}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <h6>Reason for Leave</h6>
                <div class="border p-3 rounded bg-light">
                    ${leave.reason || 'No reason provided'}
                </div>
            </div>
        </div>

        ${leave.admin_remarks ? `
                                                                                                <div class="row mt-3">
                                                                                                    <div class="col-12">
                                                                                                        <h6>Admin Remarks</h6>
                                                                                                        <div class="border p-3 rounded bg-light">
                                                                                                            ${leave.admin_remarks}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            ` : ''}
    `;

                $('#leaveDetails').html(leaveDetails);

                // Bootstrap 5 modal show करें
                const viewModal = new bootstrap.Modal(document.getElementById('viewLeaveModal'));
                viewModal.show();
            });

            // View Full Reason (event delegation)
            $(document).on('click', '.view-reason', function(e) {
                e.preventDefault();
                const reason = $(this).data('reason');
                Swal.fire({
                    title: 'Leave Reason',
                    text: reason,
                    icon: 'info',
                    confirmButtonText: 'Close',
                    width: '600px'
                });
            });

            // Delete form confirmation (event delegation)
            $(document).on('submit', '.delete-form', function(e) {
                if (!confirm('Are you sure you want to delete this leave application?')) {
                    e.preventDefault();
                }
            });

            // Helper functions
            function getStatusBadge(status) {
                const badges = {
                    'approved': '<span class="badge badge-success">Approved</span>',
                    'rejected': '<span class="badge badge-danger">Rejected</span>',
                    'pending': '<span class="badge badge-warning">Pending</span>'
                };
                return badges[status] || status;
            }

            function formatDate(dateString) {
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                } catch (e) {
                    return dateString;
                }
            }

            function formatDateTime(dateTimeString) {
                try {
                    const date = new Date(dateTimeString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return dateTimeString;
                }
            }

            // SweetAlert notifications
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
    </script>
@endsection
