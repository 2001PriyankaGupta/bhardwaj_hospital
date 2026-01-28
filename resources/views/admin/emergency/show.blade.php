@extends('admin.layouts.master')

@section('title', 'Emergency Case Details - ' . $emergency->case_number)
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
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .timeline-content {
        padding-bottom: 10px;
    }

    .timeline-item.active .timeline-marker {
        background-color: #007bff !important;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #28a745 !important;
    }

    .badge-lg {
        font-size: 14px;
        padding: 8px 12px;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-black">
                            Case Details: {{ $emergency->case_number }}
                        </h5>
                        <div>
                            <a href="{{ route('admin.emergency.edit', $emergency) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.emergency.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Case Number:</th>
                                        <td>
                                            <strong class="text-primary">{{ $emergency->case_number }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Patient Name:</th>
                                        <td>{{ $emergency->patient_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Age / Gender:</th>
                                        <td>{{ $emergency->age }} years / {{ $emergency->gender }}</td>
                                    </tr>
                                    <tr>
                                        <th>Arrival Time:</th>
                                        <td>
                                            {{ $emergency->arrival_time->format('M d, Y H:i:s') }}
                                            <small
                                                class="text-muted">({{ $emergency->arrival_time->diffForHumans() }})</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Triage Level:</th>
                                        <td>
                                            @if ($emergency->triage_level == 'Red')
                                                <span class="badge badge-danger badge-lg" style="color: red">RED -
                                                    Immediate</span>
                                            @elseif($emergency->triage_level == 'Yellow')
                                                <span class="badge badge-warning badge-lg" style="color: yellow">YELLOW -
                                                    Emergency</span>
                                            @elseif($emergency->triage_level == 'Green')
                                                <span class="badge badge-success badge-lg" style="color: #28a745">GREEN -
                                                    Urgent</span>
                                            @else
                                                <span class="badge badge-info badge-lg" style="color: #007bff">BLUE -
                                                    Non-urgent</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Priority Score:</th>
                                        <td>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar 
                                                @if ($emergency->triage_level == 'Red') bg-danger
                                                @elseif($emergency->triage_level == 'Yellow') bg-warning
                                                @elseif($emergency->triage_level == 'Green') bg-success
                                                @else bg-info @endif"
                                                    style="width: {{ $emergency->priority_score }}%">
                                                    <strong>{{ $emergency->priority_score }}/100</strong>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Assigned Staff:</th>
                                        <td>
                                            @if ($emergency->assigned_staff && $emergency->staff)
                                                <span class="badge badge-success p-2"
                                                    style="color: #28a745">{{ $emergency->staff->name }}</span>
                                            @elseif ($emergency->assigned_staff)
                                                <span class="badge badge-success p-2" style="color: #28a745">ID:
                                                    {{ $emergency->assigned_staff }}</span>
                                            @else
                                                <span class="badge badge-secondary p-2" style="color: gray">Not
                                                    Assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if ($emergency->status == 'pending')
                                                <span class="badge badge-warning p-2"
                                                    style="color: rgb(194, 194, 3)">PENDING</span>
                                            @elseif($emergency->status == 'in_progress')
                                                <span class="badge badge-primary p-2" style="color: #007bff">IN
                                                    PROGRESS</span>
                                            @else
                                                <span class="badge badge-success p-2"
                                                    style="color: #28a745">COMPLETED</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><strong>Symptoms & Condition:</strong></label>
                                    <div class="border p-3 bg-light rounded">
                                        {{ $emergency->symptoms }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><strong>Notes:</strong></label>
                                    <div class="border p-3 bg-light rounded">
                                        {{ $emergency->notes ?: 'No notes available' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($emergency->treatment_time)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-success">
                                        <strong>Treatment Completed:</strong>
                                        {{ $emergency->treatment_time->format('M d, Y H:i:s') }}
                                        <small
                                            class="text-muted">({{ $emergency->treatment_time->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        {{-- @if (!$emergency->assigned_staff)
                            <form action="{{ route('admin.emergency.assign-staff', $emergency) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <div class="form-group">
                                    <label for="quick_assigned_staff">Assign Staff</label>
                                    <input type="text" class="form-control" id="quick_assigned_staff"
                                        name="assigned_staff" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-user-md"></i> Assign Staff
                                </button>
                            </form>
                        @endif --}}

                        <form action="{{ route('admin.emergency.update-status', $emergency) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="quick_status">Update Status</label>
                                <select class="form-control" id="quick_status" name="status" required>
                                    <option value="pending" {{ $emergency->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="in_progress"
                                        {{ $emergency->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $emergency->status == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sync-alt"></i> Update Status
                            </button>
                        </form>

                        @if ($emergency->status != 'completed')
                            <hr>
                            <form action="{{ route('admin.emergency.update', $emergency) }}" method="POST" class="mb-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Mark this case as completed?')">
                                    <i class="fas fa-check-circle"></i> Mark as Completed
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Case Timeline Card -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Case Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item {{ $emergency->status == 'completed' ? 'completed' : 'active' }}">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Case Created</h6>
                                    <small class="text-muted">{{ $emergency->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>

                            @if ($emergency->assigned_staff)
                                <div
                                    class="timeline-item {{ $emergency->status == 'completed' ? 'completed' : 'active' }}">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6>Staff Assigned</h6>
                                        <small
                                            class="text-muted">{{ $emergency->staff->name ?? 'ID: ' . $emergency->assigned_staff }}</small>
                                        <br>
                                        <small
                                            class="text-muted">{{ $emergency->updated_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($emergency->status == 'completed')
                                <div class="timeline-item completed">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Treatment Completed</h6>
                                        <small
                                            class="text-muted">{{ $emergency->treatment_time->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
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
    </script>
@endsection
