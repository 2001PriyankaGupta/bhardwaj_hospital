@extends('staff.layouts.master')

@section('title', 'Appointment Queue Dashboard')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    td {
        font-size: 12px;
    }
</style>
@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-10">
                <h1 class="h3 mb-0 text-gray-800"> Queue Dashboard</h1>
                <p class="text-muted">Today: {{ date('d M Y') }}</p>
            </div>
            <div class="col-md-2 text-right">
                <div class="btn-group">

                    <a href="{{ route('staff.appointments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar"></i> All Appointments
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Appointments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_appointments'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Waiting</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['waiting'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                    Completed</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Cancelled</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cancelled'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor-wise Appointments -->
        <div class="row">
            @foreach ($doctors as $doctor)
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                <small class="text-muted">({{ $doctor->specialty->name ?? 'General' }})</small>
                            </h6>
                            <span class="badge badge-info">
                                {{-- {{ $todayAppointments[$doctor->id]->count() ?? 0 }} Appointments --}}
                            </span>
                        </div>
                        <div class="card-body">
                            @php
                                $appointments = $todayAppointments[$doctor->id] ?? collect();
                            @endphp

                            @if ($appointments->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                    <p>No appointments for today</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="queueTable">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Queue #</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($appointments as $appointment)
                                                <tr>
                                                    <td>{{ date('h:i A', strtotime($appointment->start_time)) }}</td>
                                                    <td>
                                                        {{ $appointment->patient->first_name ?? 'N/A' }}
                                                        {{ $appointment->patient->last_name ?? '' }}
                                                    </td>
                                                    <td>
                                                        @if ($appointment->queue_number)
                                                            <span
                                                                class="badge bg-secondary">{{ $appointment->queue_number }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $appointment->type ?? 'Checkup' }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'scheduled' => 'warning',
                                                                'confirmed' => 'primary',
                                                                'completed' => 'success',
                                                                'cancelled' => 'danger',
                                                            ];
                                                        @endphp
                                                        <span
                                                            class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if ($appointment->status == 'scheduled' || $appointment->status == 'confirmed')
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <button class="btn btn-success btn-sm update-status"
                                                                        data-id="{{ $appointment->id }}"
                                                                        data-status="completed" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="Complete Appointment">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>

                                                                </div>
                                                            @endif
                                                            <a href="{{ route('staff.appointments.show', $appointment) }}"
                                                                class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
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
            // Initialize DataTables
            $('#queueTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ]
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-refresh every 60 seconds
            setInterval(function() {
                location.reload();
            }, 60000);

            // Update status
            $(document).on('click', '.update-status', function() {
                let appointmentId = $(this).data('id');
                let status = $(this).data('status');

                let statusText = status.charAt(0).toUpperCase() + status.slice(1);

                Swal.fire({
                    title: 'Update Status?',
                    text: `Change appointment status to "${statusText}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('staff.queue.updateAppointmentStatus', ':id') }}"
                                .replace(':id', appointmentId),
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                status: status
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.message ||
                                            'Appointment status updated successfully.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message ||
                                            'Failed to update status.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'An error occurred while updating status.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Load doctor appointments on tab click (if using tabs)
            $('.doctor-tab').click(function() {
                let doctorId = $(this).data('doctor-id');
                loadDoctorAppointments(doctorId);
            });

            function loadDoctorAppointments(doctorId) {
                $.ajax({
                    url: "{{ route('staff.queue.getDoctorAppointments', ':id') }}".replace(':id',
                        doctorId),
                    method: 'GET',
                    success: function(response) {
                        $('#doctor-' + doctorId + '-appointments').html(response);
                    }
                });
            }
        });
    </script>
@endsection
