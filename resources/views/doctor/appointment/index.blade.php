@extends('doctor.layouts.master')

@section('title', 'Appointment Calendar')
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
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Appointment Calendar</h1>
                    <p class="text-muted mb-0">Manage appointments, view schedules and allocate resources</p>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-info mr-2" href="{{ route('doctor.appointments.calendar') }}">
                    <i class="fas fa-calendar"></i> Calendar View
                </a>
                <a class="btn btn-primary" href="{{ route('doctor.appointments.create') }}">
                    <i class="fas fa-plus"></i> New Appointment
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Calendar View Controls -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="btn-group">
                                    <a href="{{ request()->fullUrlWithQuery(['view' => 'day']) }}"
                                        class="btn btn-outline-primary {{ $view == 'day' ? 'active' : '' }}">
                                        <i class="fas fa-calendar-day"></i> Day
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['view' => 'week']) }}"
                                        class="btn btn-outline-primary {{ $view == 'week' ? 'active' : '' }}">
                                        <i class="fas fa-calendar-week"></i> Week
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['view' => 'month']) }}"
                                        class="btn btn-outline-primary {{ $view == 'month' ? 'active' : '' }}">
                                        <i class="fas fa-calendar-alt"></i> Month
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group">
                                    <a href="{{ request()->fullUrlWithQuery(['date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                    <span class="btn btn-outline-secondary disabled">
                                        @if ($view == 'day')
                                            {{ date('M d, Y', strtotime($date)) }}
                                        @elseif($view == 'week')
                                            Week of {{ date('M d', strtotime('monday this week', strtotime($date))) }} -
                                            {{ date('M d', strtotime('sunday this week', strtotime($date))) }}
                                        @else
                                            {{ date('F Y', strtotime($date)) }}
                                        @endif
                                    </span>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => date('Y-m-d', strtotime($date . ' +1 day'))]) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => date('Y-m-d')]) }}"
                                        class="btn btn-outline-secondary">
                                        Today
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Appointments List -->
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-bordered table-striped table-hover w-100" id="appointmentsTable" style="min-width: 1000px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Resource</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        <tr>
                                            <td>
                                                <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if ($appointment->patient)
                                                    {{ $appointment->patient->first_name }}
                                                    {{ $appointment->patient->last_name }}
                                                    <br>
                                                    <small>{{ $appointment->patient->email }}</small>
                                                @else
                                                    NA
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info"
                                                    style="color: purple">{{ $appointment->doctor->first_name ?? 'NA' }}
                                                    {{ $appointment->doctor->last_name ?? 'NA' }}</span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $appointment->doctor->specialty->name ?? 'NA' }}</small>
                                            </td>
                                            <td>
                                                @if ($appointment->resource)
                                                    <span class="badge badge-secondary"
                                                        style="color: rgb(1, 159, 162)">{{ $appointment->resource->name ?? 'NA' }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
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
                                                <div class="d-flex justify-content-center gap-1">

                                                    <a href="{{ route('doctor.appointments.start', $appointment->id) }}"
                                                        class="btn btn-success btn-sm action-btn" title="Start Call"
                                                        style="height: 25px;">
                                                        <i class="fas fa-phone"></i>
                                                    </a>

                                                    <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                                        class="btn btn-info btn-sm action-btn" title="View"
                                                        style="height: 25px;">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>

                                                    <!-- Medical Report -->
                                                    <a href="{{ route('doctor.medical-reports.create', ['appointment_id' => $appointment->id]) }}" 
                                                       class="btn btn-sm action-btn text-white" title="Medical Report" 
                                                       style="height: 25px; background-color: #f15832;">
                                                        <i class="fas fa-file-medical"></i>
                                                    </a>

                                                    <!-- Prescription -->
                                                    <a href="{{ route('doctor.prescriptions.create', ['appointment_id' => $appointment->id]) }}" 
                                                       class="btn btn-warning btn-sm action-btn text-white" title="New Prescription" 
                                                       style="height: 25px;">
                                                        <i class="fas fa-prescription"></i>
                                                    </a>

                                                    <!-- Chat with patient -->
                                                    @if ($appointment->conversation && $appointment->conversation->status === 'closed')
                                                        <button class="btn btn-secondary btn-sm action-btn"
                                                            title="Chat Closed" disabled style="height: 25px;">
                                                            <i class="fas fa-comments"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('doctor.appointments.chat', $appointment->id) }}"
                                                            class="btn btn-secondary btn-sm action-btn" title="Chat"
                                                            style="height: 25px;">
                                                            <i class="fas fa-comments"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('doctor.appointments.edit', $appointment) }}"
                                                        class="btn btn-warning btn-sm action-btn" title="Edit"
                                                        style="height: 25px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a> 
                                                    <form action="{{ route('doctor.appointments.destroy', $appointment) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm action-btn"
                                                            title="Delete" onclick="return confirm('Are you sure?')"
                                                            style="height: 25px;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
@endsection

@section('styles')
    <style>
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

        .status-select {
            cursor: pointer;
            border-radius: 20px;
            text-align: center;
        }

        .status-select option {
            text-align: left;
        }
    </style>
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
            // Initialize DataTable
            $('#appointmentsTable').DataTable({
                responsive: false, // Set to false to allow horizontal scrolling
                scrollX: true,     // Enable horizontal scroll
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search appointments...",
                    emptyTable: "No appointments found. No appointments scheduled for the selected period"
                },
                columns: [{}, {}, {}, {}, {}, {}],
                columnDefs: [{
                    orderable: false,
                    targets: [5] // Actions column
                }]
            });

            // Status update
            $('.status-select').change(function() {
                const appointmentId = $(this).data('appointment-id');
                const status = $(this).val();

                $.ajax({
                    url: `${baseUrl}/admin/appointments/${appointmentId}/status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        toastr.success('Status updated successfully');
                    },
                    error: function(xhr) {
                        toastr.error('Error updating status');
                        location.reload();
                    }
                });
            });
        });
    </script>

    <!-- Include DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
@endsection
