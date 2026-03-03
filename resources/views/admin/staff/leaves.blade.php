@extends('admin.layouts.master')

@section('title', 'Staff Leave Management')


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .text-orange { color: #ff4900 !important; }
        .bg-orange-soft { background-color: rgba(255, 73, 0, 0.1); color: #ff4900 !important; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; color: #777; border-top: none; padding: 15px; }
        .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); color: #28a745 !important; }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); color: #dc3545 !important; }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); color: #ffc107 !important; }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0 !important; }
        .badge { padding: 8px 12px; font-weight: 600; border-radius: 8px; font-size: 12px; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
        .action-btn:hover { transform: translateY(-2px); }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff4900 !important; color: white !important; border: none !important; border-radius: 5px; }
        .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; margin-left: 10px; }
    </style>


@section('content')
    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <h3 class="fw-bold mb-0 text-orange">Staff Leave Management
                    @if(isset($filtered_staff))
                        - <span class="text-dark">{{ $filtered_staff->name }}</span>
                    @endif
                </h3>
                <p class="text-muted small mb-0">
                    @if(isset($filtered_staff))
                        Displaying leave history for <strong>{{ $filtered_staff->name }}</strong>.
                    @else
                        Review and manage leave applications from all hospital staff members.
                    @endif
                </p>
            </div>
            <div class="action-buttons d-flex gap-2">
              
                <a class="btn btn-outline-secondary btn-sm rounded-pill px-3" href="{{ route('admin.staff.index') }}">
                    <i class="fas fa-users me-1"></i> Back to Staff List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="leavesTable">
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th>Department</th>
                                        <th>Leave Period</th>
                                        <th>Type</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaves as $leave)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3 bg-orange-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <span class="fw-bold">{{ substr($leave->staff->name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $leave->staff->name }}</div>
                                                        <small class="text-muted">{{ $leave->staff->position }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $leave->staff->departmentRelation->name ?? '---' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium">{{ $leave->start_date->format('M d, Y') }}</span>
                                                    <small class="text-muted">to {{ $leave->end_date->format('M d, Y') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-info text-info text-capitalize">
                                                    {{ $leave->leave_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $leave->duration }} Day{{ $leave->duration > 1 ? 's' : '' }}</span>
                                            </td>
                                            <td>
                                                @if ($leave->status == 'approved')
                                                    <span class="badge bg-success-soft text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Approved
                                                    </span>
                                                @elseif($leave->status == 'rejected')
                                                    <span class="badge bg-danger-soft text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Rejected
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-soft text-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-info action-btn view-leave" 
                                                        data-leave='@json($leave)' 
                                                        data-staff-name="{{ $leave->staff->name }}"
                                                        data-staff-dept="{{ $leave->staff->departmentRelation->name ?? 'N/A' }}"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if ($leave->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-success action-btn approve-leave" data-id="{{ $leave->id }}" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger action-btn reject-leave" data-id="{{ $leave->id }}" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewLeaveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-orange-soft border-0 p-4">
                    <h5 class="modal-title fw-bold text-orange"><i class="fas fa-info-circle me-2"></i>Leave Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="leaveDetails"></div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectLeaveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-danger-soft border-0 p-4">
                    <h5 class="modal-title fw-bold text-danger"><i class="fas fa-times-circle me-2"></i>Reject Application</h5>
                    <button type="button" class="btn-close text-danger" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectLeaveForm">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Remark/Reason for Rejection</label>
                            <textarea class="form-control border-0 bg-light p-3" name="admin_remarks" rows="4" required placeholder="Type the reason for rejection here..." style="border-radius: 12px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#leavesTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "info": true,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search applications...",
                    "paginate": {
                        "previous": "<i class='fas fa-chevron-left'></i>",
                        "next": "<i class='fas fa-chevron-right'></i>"
                    }
                }
            });

            // View Logic
            $(document).on('click', '.view-leave', function() {
                const leave = $(this).data('leave');
                const staffName = $(this).data('staff-name');
                const staffDept = $(this).data('staff-dept');
                
                let details = `
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <h6 class="text-muted text-uppercase x-small fw-bold mb-3"><i class="fas fa-user me-2"></i>Staff Information</h6>
                                <p class="mb-1"><strong>Name:</strong> ${staffName}</p>
                                <p class="mb-0"><strong>Department:</strong> ${staffDept}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <h6 class="text-muted text-uppercase x-small fw-bold mb-3"><i class="fas fa-tag me-2"></i>Leave Context</h6>
                                <p class="mb-1"><strong>Type:</strong> <span class="badge bg-soft-info text-info x-capitalize">${leave.leave_type}</span></p>
                                <p class="mb-0"><strong>Status:</strong> <span class="text-uppercase fw-bold">${leave.status}</span></p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-4 bg-orange-soft rounded-4">
                                <h6 class="text-orange text-uppercase x-small fw-bold mb-3"><i class="fas fa-comment-alt me-2"></i>Reason for Application</h6>
                                <div class="text-dark">${leave.reason}</div>
                            </div>
                        </div>
                        
                    </div>
                `;
                $('#leaveDetails').html(details);
                new bootstrap.Modal('#viewLeaveModal').show();
            });

            // Approve Logic
            $('.approve-leave').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Approve Leave Request?',
                    text: "The staff member will be notified of the approval.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve It!',
                    borderRadius: '15px'
                }).then((r) => {
                    if (r.isConfirmed) {
                        updateStatus(id, 'approved');
                    }
                });
            });

            // Reject Logic
            let curId = null;
            $('.reject-leave').click(function() {
                curId = $(this).data('id');
                new bootstrap.Modal('#rejectLeaveModal').show();
            });

            $('#rejectLeaveForm').submit(function(e) {
                e.preventDefault();
                updateStatus(curId, 'rejected', $(this).find('textarea').val());
            });

            function updateStatus(id, status, remarks = '') {
                $.ajax({
                    url: "{{ url('admin/staff-leaves') }}/" + id + "/status",
                    method: 'PUT',
                    data: { _token: '{{ csrf_token() }}', status: status, admin_remarks: remarks },
                    success: function(res) {
                        Swal.fire({
                            title: 'Status Updated!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#ff4900',
                            borderRadius: '15px'
                        }).then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong while updating status.', 'error');
                    }
                });
            }
        });
    </script>
@endsection
