@extends('staff.layouts.master')

@section('title', 'Live Queue Dashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">Live OPD Queue Dashboard</h1>
                <p class="text-muted">Last updated: <span id="lastUpdate">{{ now()->format('h:i:s A') }}</span></p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('staff.queue.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Queue Management
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Waiting Patients</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_waiting'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    In Consultation</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_in_progress'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-md fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed Today</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_completed'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Avg. Wait Time</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ round($stats['avg_wait_time']) }} min
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor-wise Queues -->
        <div class="row">
            @foreach ($doctors as $doctor)
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                <small class="text-muted">({{ $doctor->specialty->name ?? 'General' }})</small>
                            </h6>
                            <div>
                                <button class="btn btn-sm btn-success call-next-btn" data-doctor-id="{{ $doctor->id }}">
                                    <i class="fas fa-bell"></i> Call Next
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $doctorQueues = $todayQueues[$doctor->id] ?? collect();
                            @endphp

                            @if ($doctorQueues->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-3"></i>
                                    <p>No patients in queue</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Pos</th>
                                                <th>Patient</th>
                                                <th>Queue #</th>
                                                <th>Type</th>
                                                <th>Wait Time</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($doctorQueues as $queue)
                                                <tr class="@if ($queue->is_priority) table-warning @endif">
                                                    <td>{{ $queue->position }}</td>
                                                    <td>
                                                        {{ $queue->patient->first_name }} {{ $queue->patient->last_name }}
                                                        @if ($queue->is_priority)
                                                            <span class="badge bg-danger">Priority</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $queue->queue_number }}</td>
                                                    <td>
                                                        @if ($queue->queue_type == 'emergency')
                                                            <span class="badge bg-danger">E</span>
                                                        @elseif($queue->queue_type == 'follow_up')
                                                            <span class="badge bg-warning">F</span>
                                                        @else
                                                            <span class="badge bg-primary">N</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $queue->estimated_wait_time }} min</td>
                                                    <td>
                                                        @if ($queue->status == 'waiting')
                                                            <span class="badge bg-warning">Waiting</span>
                                                        @else
                                                            <span class="badge bg-info">In Progress</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($queue->status == 'in_progress')
                                                            <form action="{{ route('staff.queue.complete', $queue) }}"
                                                                method="POST" style="display:inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-check"></i> Complete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer text-muted text-center">
                            Currently with:
                            {{ $doctorQueues->where('status', 'in_progress')->first() ? 'Patient ' . $doctorQueues->where('status', 'in_progress')->first()->queue_number : 'No patient' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);

        // Call next patient
        $('.call-next-btn').click(function() {
            let doctorId = $(this).data('doctor-id');
            let button = $(this);

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Calling...');

            $.ajax({
                url: "{{ route('staff.queue.callNext', ':doctorId') }}".replace(':doctorId', doctorId),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Patient Called!',
                            text: 'Queue #' + response.queue.queue_number + ' - ' +
                                response.queue.patient.first_name + ' ' +
                                response.queue.patient.last_name,
                            timer: 3000
                        });
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Patients',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to call next patient'
                    });
                },
                complete: function() {
                    button.prop('disabled', false).html('<i class="fas fa-bell"></i> Call Next');
                }
            });
        });

        // Update last updated time
        setInterval(function() {
            let now = new Date();
            let timeString = now.toLocaleTimeString('en-US', {
                hour12: true
            });
            $('#lastUpdate').text(timeString);
        }, 1000);
    </script>
@endsection
