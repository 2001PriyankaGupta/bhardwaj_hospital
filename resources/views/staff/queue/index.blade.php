@extends('staff.layouts.master')

@section('title', 'Queue Management')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ URL::asset('build/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

<style>
    /* Toast styling */
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
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Queue Management</h1>
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('staff.queue.dashboard') }}" class="btn btn-info">
                        <i class="fas fa-tv"></i> Live Dashboard
                    </a>
                    <a href="{{ route('staff.queue.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add to Queue
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="queueTable">
                        <thead>
                            <tr>
                                <th>Queue #</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Position</th>
                                <th>Check-in Time</th>
                                <th>Wait Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queues as $queue)
                                <tr>
                                    <td><span class="badge bg-dark">{{ $queue->queue_number }}</span></td>
                                    <td>{{ $queue->patient->first_name }} {{ $queue->patient->last_name }}</td>
                                    <td>Dr. {{ $queue->doctor->first_name }} {{ $queue->doctor->last_name }}</td>
                                    <td>
                                        @switch($queue->queue_type)
                                            @case('emergency')
                                                <span class="badge bg-danger">Emergency</span>
                                            @break

                                            @case('follow_up')
                                                <span class="badge bg-warning">Follow-up</span>
                                            @break

                                            @default
                                                <span class="badge bg-primary">Normal</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($queue->status)
                                            @case('waiting')
                                                <span class="badge bg-warning">Waiting</span>
                                            @break

                                            @case('in_progress')
                                                <span class="badge bg-info">In Progress</span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $queue->position }}</td>
                                    <td>{{ $queue->check_in_time->format('h:i A') }}</td>
                                    <td>{{ $queue->estimated_wait_time }} min</td>
                                    <td>
                                        <a href="{{ route('staff.queue.show', $queue) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.queue.edit', $queue) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $queues->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#queueTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ]
            });
        });
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
