@extends('staff.layouts.master')

@section('title', 'My Leaves - ' . $staff->name)

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .text-orange { color: #ff4900 !important; }
        .btn-orange { background-color: #ff4900; color: white; border: none; }
        .btn-orange:hover { background-color: #e64200; color: white; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
        .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; color: #777; border-top: none; }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0 !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #ffc107 !important; }
        .bg-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745 !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545 !important; }
        .badge { font-weight: 600; padding: 8px 12px; border-radius: 8px; font-size: 12px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff4900 !important; color: white !important; border: none !important; border-radius: 5px; }
        .dataTables_wrapper .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; }
    </style>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-orange">My Leave Applications
                </h3>
                <p class="text-muted small mb-0">Manage and track your leave requests in one place.</p>
            </div>
            <div class="d-flex gap-2">
               
                <a href="{{ route('staff.leaves.create') }}" class="btn btn-primary btn-sm text-white rounded-pill px-3 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Apply for Leave
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="leavesTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Leave Type</th>
                                        <th>Period</th>
                                        <th>Duration</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Remark</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaves as $leave)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-soft-info text-info text-capitalize">
                                                    {{ $leave->leave_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-dark">{{ $leave->start_date->format('M d, Y') }}</span>
                                                    <small class="text-muted">to {{ $leave->end_date->format('M d, Y') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $leave->duration }} Day{{ $leave->duration > 1 ? 's' : '' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted small" title="{{ $leave->reason }}">
                                                    {{ Str::limit($leave->reason, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($leave->status == 'pending')
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="fas fa-hourglass-half me-1"></i>Pending
                                                    </span>
                                                @elseif($leave->status == 'approved')
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Approved
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-danger text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted fst-italic">
                                                    {{ $leave->admin_remarks ?: '---' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if ($leave->status == 'pending')
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('staff.leaves.edit', $leave) }}" class="btn btn-sm btn-outline-primary action-icon" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('staff.leaves.destroy', $leave) }}" method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn action-icon" title="Cancel">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted border py-1 px-3">
                                                        <i class="fas fa-lock me-1"></i> Finalized
                                                    </span>
                                                @endif
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
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
                },
                "drawCallback": function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });

            // Delete Confirmation
            $('.delete-btn').on('click', function() {
                const form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Cancel Leave Application?',
                    text: "You won't be able to undo this action.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Cancel it!',
                    cancelButtonText: 'Keep it',
                    borderRadius: '15px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
