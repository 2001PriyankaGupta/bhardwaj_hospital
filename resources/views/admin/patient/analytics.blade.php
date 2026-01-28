@extends('admin.layouts.master')

@section('title', 'Patient Analytics - ' . $patient->first_name . ' ' . $patient->last_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="row mt-4">
        <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold"> Patient Analytics: {{ $patient->first_name }}
                        {{ $patient->last_name }}</h1>

                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary btn-sm float-right"
                    style="    margin-right: 38px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h3 class="card-title text-orange fw-bold">
                        Patient Analytics: {{ $patient->first_name }} {{ $patient->last_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Patient
                        </a>
                    </div>
                </div> --}}
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Appointments</span>
                                    <span class="info-box-number">{{ $analytics['total_appointments'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ $analytics['completed_appointments'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ $analytics['total_appointments'] > 0 ? ($analytics['completed_appointments'] / $analytics['total_appointments']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Upcoming</span>
                                    <span class="info-box-number">{{ $analytics['upcoming_appointments'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ $analytics['total_appointments'] > 0 ? ($analytics['upcoming_appointments'] / $analytics['total_appointments']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-primary">
                                <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Medical Records</span>
                                    <span class="info-box-number">{{ $analytics['medical_records_count'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Analytics -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Appointment Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="appointmentChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">Communication Types</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="communicationChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">Recent Activity Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Recent Appointments</h6>
                                            @if ($patient->appointments()->count() > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($patient->appointments()->latest()->take(5)->get() as $appointment)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $appointment->appointment_date->format('d M Y') }}</strong>
                                                                <br>
                                                                <small
                                                                    class="text-muted">{{ $appointment->doctor->first_name ?? 'N/A' }}
                                                                    {{ $appointment->doctor->last_name ?? 'N/A' }}
                                                                    - {{ ucfirst($appointment->status) }}</small>
                                                            </div>
                                                            <span
                                                                class="badge badge-{{ $appointment->status === 'completed' ? 'success' : 'primary' }} badge-pill">
                                                                {{ $appointment->status }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">No appointments found.</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Recent Medical Records</h6>
                                            @if ($patient->medicalRecords()->count() > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($patient->medicalRecords()->latest()->take(5)->get() as $record)
                                                        <li class="list-group-item">
                                                            <div class="d-flex w-100 justify-content-between">
                                                                <strong>{{ ucfirst($record->record_type) }}</strong>
                                                                <small>{{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}</small>
                                                            </div>
                                                            <p class="mb-0 text-muted small">
                                                                {{ Str::limit($record->description, 60) }}</p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">No medical records found.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
         let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            // Appointment Distribution Chart
            var appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
            var appointmentChart = new Chart(appointmentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Upcoming', 'Cancelled'],
                    datasets: [{
                        data: [
                            {{ $analytics['completed_appointments'] }},
                            {{ $analytics['upcoming_appointments'] }},
                            {{ $analytics['total_appointments'] - $analytics['completed_appointments'] - $analytics['upcoming_appointments'] }}
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Communication Types Chart (placeholder - you can enhance this with actual data)
            var communicationCtx = document.getElementById('communicationChart').getContext('2d');
            var communicationChart = new Chart(communicationCtx, {
                type: 'bar',
                data: {
                    labels: ['Email', 'SMS', 'Phone Call', 'In Person'],
                    datasets: [{
                        label: 'Communication Count',
                        data: [12, 19, 8, 5], // Replace with actual data from your system
                        backgroundColor: [
                            '#007bff',
                            '#17a2b8',
                            '#28a745',
                            '#ffc107'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
