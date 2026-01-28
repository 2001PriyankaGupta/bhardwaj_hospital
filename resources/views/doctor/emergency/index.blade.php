@extends('doctor.layouts.master')

@section('title', 'Emergency Cases')

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

    .progress {
        height: 20px;
    }

    .progress-bar {
        font-weight: bold;
    }

    .table-danger {
        background-color: #f8d7da !important;
    }

    .table-warning {
        background-color: #fff3cd !important;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Emergency Cases</h1>
                    <p class="text-muted mb-0">Manage all patient records and information</p>
                </div>
            </div>
            {{-- <div class="action-buttons">
                <a href="{{ route('doctor.emergency.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Case
                </a>
            </div> --}}
        </div>
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Cases</div>
                                <div class="h5 mb-0">{{ $stats['total_cases'] }}</div>
                            </div>
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Cases</div>
                                <div class="h5 mb-0">{{ $stats['pending_cases'] }}</div>
                            </div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Red Cases</div>
                                <div class="h5 mb-0">{{ $stats['red_cases'] }}</div>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Yellow Cases</div>
                                <div class="h5 mb-0">{{ $stats['yellow_cases'] }}</div>
                            </div>
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            {{-- <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Emergency Triage Cases</h6>
                <a href="{{ route('admin.emergency.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Case
                </a>
            </div> --}}
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search cases..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="triage_level" class="form-control">
                                <option value="">All Triage Levels</option>
                                @foreach ($triageLevels as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('triage_level') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('doctor.emergency.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Cases Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="emergencyTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Patient Name</th>
                                <th>Age/Gender</th>
                                <th>Triage Level</th>
                                <th>Priority</th>
                                <th>Assigned Staff</th>
                                <th>Status</th>
                                <th>Arrival Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cases as $case)
                                <tr
                                    class="@if ($case->triage_level == 'Red') table-danger @elseif($case->triage_level == 'Yellow') table-warning @endif">
                                    <td>
                                        <strong>{{ $case->case_number }}</strong>
                                    </td>
                                    <td>{{ $case->patient_name }}</td>
                                    <td>{{ $case->age }}/{{ $case->gender }}</td>

                                    <td>
                                        @if ($case->triage_level == 'Red')
                                            <span class="badge badge-danger"
                                                style="color: rgb(0, 0, 0)">{{ $case->triage_level }}</span>
                                        @elseif($case->triage_level == 'Yellow')
                                            <span class="badge badge-warning"
                                                style="color: rgb(16, 16, 15)">{{ $case->triage_level }}</span>
                                        @elseif($case->triage_level == 'Green')
                                            <span class="badge badge-success"
                                                style="color: rgb(0, 0, 0)">{{ $case->triage_level }}</span>
                                        @else
                                            <span class="badge badge-info"
                                                style="color: rgb(4, 4, 4)">{{ $case->triage_level }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar 
                                        @if ($case->triage_level == 'Red') bg-danger
                                        @elseif($case->triage_level == 'Yellow') bg-warning
                                        @elseif($case->triage_level == 'Green') bg-success
                                        @else bg-info @endif"
                                                style="width: {{ $case->priority_score }}%">
                                                {{ $case->priority_score }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($case->staff)
                                            <span class="badge badge-success"
                                                style="color: rgb(105, 9, 188)">{{ $case->staff->name }}</span>
                                        @else
                                            <span class="badge badge-secondary" style="color: black">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($case->status == 'pending')
                                            <span class="badge badge-warning" style="color: rgb(209, 209, 7)">Pending</span>
                                        @elseif($case->status == 'in_progress')
                                            <span class="badge badge-primary" style="color: rgb(4, 0, 255)">In
                                                Progress</span>
                                        @else
                                            <span class="badge badge-success" style="color: green">Completed</span>
                                        @endif
                                    </td>
                                    <td>{{ $case->arrival_time->format('H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('doctor.emergency.show', $case) }}" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('doctor.emergency.edit', $case) }}" class="btn btn-primary"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if (!$case->assigned_staff)
                                                <button type="button" class="btn btn-warning assign-staff-btn"
                                                    data-case-id="{{ $case->id }}" title="Assign Staff">
                                                    <i class="fas fa-user-md"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $cases->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Staff</h5>
                    <button type="button" class="btn-close btn-close-gray" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="assignStaffForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="assigned_staff">Staff Name</label>
                            <select class="form-control" id="assigned_staff" name="assigned_staff" required>
                                @foreach ($staff as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} -
                                        {{ $item->departmentRelation->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
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

            // DataTable init
            $('#emergencyTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [7]
                }], // Actions column
                order: [
                    [0, 'asc']
                ]
            });

            // Assign Staff Modal
            $('.assign-staff-btn').click(function() {
                const caseId = $(this).data('case-id');
                const form = $('#assignStaffForm');
                form.attr('action', `${baseUrl}/doctor/emergency/${caseId}/assign-staff`);
                $('#assignStaffModal').modal('show');
            });

            // Auto-refresh every 30 seconds for real-time updates
            setInterval(function() {
                window.location.reload();
            }, 30000);
        });
    </script>
@endsection
