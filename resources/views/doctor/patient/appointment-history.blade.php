@extends('admin.layouts.master')

@section('title', 'Appointment History - ' . $patient->first_name . ' ' . $patient->last_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-orange fw-bold mb-0">
                        Appointment History: {{ $patient->first_name }} {{ $patient->last_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Patient
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="appointmentHistoryTable">
                            <thead>
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->id }}</td>
                                        <td>
                                            {{ $appointment->appointment_date->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $appointment->start_time }} -
                                                {{ $appointment->end_time }}</small>
                                        </td>
                                        <td>{{ $appointment->doctor->first_name ?? 'N/A' }}
                                            {{ $appointment->doctor->last_name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                // Check if Bootstrap 5 (bg- classes) or Bootstrap 4 (badge- classes)
                                                $isBootstrap5 = true; // Set this based on your framework

                                                $statusColors = [
                                                    'scheduled' => $isBootstrap5 ? 'bg-primary' : 'badge-primary',
                                                    'completed' => $isBootstrap5 ? 'bg-success' : 'badge-success',
                                                    'cancelled' => $isBootstrap5 ? 'bg-danger' : 'badge-danger',
                                                    'no_show' => $isBootstrap5 ? 'bg-warning' : 'badge-warning',
                                                ];
                                            @endphp
                                            <span
                                                class="badge {{ $statusColors[$appointment->status] ?? ($isBootstrap5 ? 'bg-secondary' : 'badge-secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($appointment->notes ?? ($appointment->cancellation_reason ?? 'No reason provided'), 50) }}
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-appointment"
                                                data-appointment="{{ json_encode([
                                                    'id' => $appointment->id,
                                                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                                                    'start_time' => $appointment->start_time,
                                                    'end_time' => $appointment->end_time,
                                                    'status' => $appointment->status,
                                                    'notes' => $appointment->notes,
                                                    'cancellation_reason' => $appointment->cancellation_reason,
                                                    'doctor' => ['name' => $appointment->doctor->name ?? 'N/A'],
                                                    'department' => ['name' => $appointment->department->name ?? 'N/A'],
                                                    'created_at' => $appointment->created_at,
                                                    'updated_at' => $appointment->updated_at,
                                                ]) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">


                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="appointmentDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#appointmentHistoryTable').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // View appointment details
            $('.view-appointment').click(function() {
                var appointment = $(this).data('appointment');
                var statusColor = appointment.status === 'completed' ? 'success' :
                    appointment.status === 'cancelled' ? 'danger' :
                    appointment.status === 'no_show' ? 'warning' : 'primary';

                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Appointment ID:</strong> #${appointment.id}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span class="badge badge-${statusColor}" style="color:black;">
                                ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1).replace('_', ' ')}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Date:</strong> ${new Date(appointment.appointment_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                        </div>
                        <div class="col-md-6">
                            <strong>Time:</strong> ${appointment.start_time} - ${appointment.end_time}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Doctor:</strong> ${appointment.doctor.name}
                        </div>
                        <div class="col-md-6">
                            <strong>Department:</strong> ${appointment.department.name}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Notes:</strong>
                            <p class="mt-1">${appointment.notes || 'No notes provided'}</p>
                        </div>
                    </div>
                    ${appointment.cancellation_reason ? `
                                                                                            <div class="row mt-3">
                                                                                                <div class="col-12">
                                                                                                    <strong>Cancellation Reason:</strong>
                                                                                                    <p class="mt-1 text-danger">${appointment.cancellation_reason}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                            ` : ''}
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Created At:</strong> ${new Date(appointment.created_at).toLocaleString('en-GB')}
                        </div>
                        <div class="col-md-6">
                            <strong>Updated At:</strong> ${new Date(appointment.updated_at).toLocaleString('en-GB')}
                        </div>
                    </div>
                `;
                $('#appointmentDetails').html(html);
                $('#viewAppointmentModal').modal('show');
            });
        });
    </script>
@endsection
