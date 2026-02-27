@extends('doctor.layouts.master')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<style>
    :root {
        --accent: #ff4900;
        --accent-gradient: linear-gradient(100deg, #ff4900, #ff4900);
        --card-bg: #fff;
        --muted: #a1a1a1;
        --section-bg: #f8f9fa;
        --activity-bg: #f3f3f3;
    }

    body {
        background-color: var(--section-bg);
        font-family: 'Inter', Helvetica, Arial, sans-serif;
    }

    .dashboard-header {
        padding: 34px 24px 13px 24px;
        background: var(--card-bg);
        border-radius: 18px;
        box-shadow: 0 2px 20px rgba(52, 152, 219, 0.03);
        margin-bottom: 30px;
    }

    .dashboard-header h1 {
        font-weight: 800;
        color: #222;
        margin-bottom: 7px;
    }

    .dashboard-header p {
        color: var(--muted);
        font-size: 16px;
    }

    .dashboard-kpi {
        border-radius: 18px;
        background: var(--card-bg);
        box-shadow: 0 2px 18px rgba(49, 37, 19, .08);
        margin-bottom: 30px;
        transition: box-shadow .17s, transform .19s;
        cursor: pointer;
        min-height: 142px;
        padding: 28px 26px 20px 26px;
        text-align: left;
        position: relative;
        overflow: hidden;
        border-left: 7px solid var(--accent);
    }

    .dashboard-kpi .kpi-icon {
        font-size: 34px;
        color: var(--accent);
        position: absolute;
        right: 24px;
        top: 24px;
    }

    .dashboard-kpi .value {
        font-size: 2.45rem;
        font-weight: 800;
        line-height: 1.1;
        color: #222;
        margin-bottom: 5px;
    }

    .dashboard-kpi .label {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: #444;
    }

    .dashboard-kpi .sub-label {
        color: var(--accent);
        font-size: 1rem;
        font-weight: 500;
    }

    .dashboard-kpi.negative {
        border-left-color: #e84118;
    }

    .dashboard-kpi.warning {
        border-left-color: #f39c12;
    }

    /* Mini Cards / Charts */
    .mini-card {
        border-radius: 16px;
        background: var(--card-bg);
        box-shadow: 0 2px 14px rgba(49, 37, 19, .10);
        padding: 20px 22px 14px 22px;
        margin-bottom: 22px;
        min-height: 180px;
    }

    .mini-chart-title {
        font-weight: 700;
        color: var(--accent);
        font-size: 20px;
        margin-bottom: 7px;
    }

    .mini-chart-trend {
        font-size: 17px;
        font-weight: 600;
        color: var(--accent);
    }

    .mini-chart-trend.sub {
        color: var(--muted);
        font-size: 13px;
        font-weight: 500;
    }

    /* Activity Log */
    .activity-log {
        max-height: 350px;
        overflow-y: auto;
        margin-top: 8px;
    }

    .activity-item {
        padding: 11px 0;
        border-bottom: 1px solid #ececec;
        font-size: 1rem;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item .fw-bold {
        color: #484848;
        margin-bottom: 1px;
    }

    .activity-item .activity-time {
        color: #b3b3b3;
        font-size: 13px;
        margin-top: 1px;
    }

    /* Charts */
    canvas {
        margin-top: 6px;
    }

    /* Quick Actions */
    .quick-actions .btn {
        min-width: 215px;
        font-weight: 700;
        border-radius: 10px;
        margin-right: 20px;
        margin-bottom: 16px;
        padding: 12px 26px;
        font-size: 17px;
        transition: background .15s, color .16s;
    }

    .quick-actions .btn-primary {
        background: var(--accent-gradient);
        border-color: var(--accent);
    }

    .quick-actions .btn-outline-success,
    .quick-actions .btn-outline-secondary {
        color: var(--accent);
        border-color: #ee5227;
    }

    .quick-actions .btn-outline-success:hover,
    .quick-actions .btn-outline-secondary:hover {
        background: #ee5227;
        color: white;
    }

    .quick-actions i {
        font-size: 18px !important;
    }

    .consultation-type {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }

    .consultation-followup {
        background-color: #e3f2fd;
        color: #ee5227;
    }

    .consultation-new {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .consultation-emergency {
        background-color: #ffebee;
        color: #c62828;
    }

    @media (max-width: 1200px) {

        .mini-card,
        .dashboard-kpi {
            padding: 17px 10px 11px 16px;
        }

        .mini-chart-title {
            font-size: 17px;
        }
    }

    @media (max-width: 600px) {
        .dashboard-header {
            padding: 18px 5px 10px 5px;
        }

        .quick-actions .btn {
            width: 100%;
            min-width: unset;
            margin-right: 0;
        }

        .mini-card,
        .dashboard-kpi {
            min-height: 110px;
        }
    }
</style>


@section('content')
    <div class="container-fluid mt-3">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Doctor Dashboard</h1>
                    <p>Welcome back, <span class="fw-semibold">Dr. {{ auth()->user()->name ?? 'Doctor' }}</span>! Here's your
                        daily
                        schedule and patient summary.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('doctor.doctors.schedules', auth()->user()->doctor_id ?? auth()->user()->doctor->id) }}" class="btn btn-orange text-white">
                        <i class="fas fa-calendar-alt"></i> Manage Slots
                    </a>
                    <a href="{{ route('doctor.chat.index') }}" class="btn btn-primary">
                        <i class="fas fa-comments"></i> Chat with Patients
                    </a>
                </div>
            </div>
        </div>



        <!-- KPIs -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-calendar-check"></i></span>
                    <div class="value">{{ $totalToday }}</div>
                    <div class="label">Today's Appointments</div>
                    <div class="sub-label">{{ $pendingToday }} remaining</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-user-injured"></i></span>
                    <div class="value">{{ $activePatients }}</div>
                    <div class="label">Active Patients</div>
                    <div class="sub-label">Total patients</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi warning">
                    <span class="kpi-icon"><i class="fas fa-clock"></i></span>
                    <div class="value">{{ $pendingPrescriptions }}</div>
                    <div class="label">Active Prescriptions</div>
                    <div class="sub-label">Currently active</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi negative">
                    <span class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="value">{{ $emergencyCases }}</div>
                    <div class="label">Emergency Cases</div>
                    <div class="sub-label">Immediate attention</div>
                </div>
            </div>
        </div>

        <!-- Charts & Activities -->
        <div class="row">
            <!-- Today's Schedule -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Today's Schedule</div>
                    <div class="mini-chart-trend">{{ $totalToday }} Appointments <span
                            class="mini-chart-trend sub">({{ $pendingToday }} remaining)</span>
                    </div>
                    <div class="activity-log mt-3">
                        @forelse($todayAppointments as $appointment)
                            <div class="activity-item">
                                <div class="fw-bold mb-1">{{ $appointment->start_time }} -
                                    {{ $appointment->patient->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted">{{ $appointment->type ?? 'Consultation' }} <span
                                        class="consultation-type {{ $appointment->status == 'completed' ? 'consultation-followup' : 'consultation-new' }}">{{ ucfirst($appointment->status) }}</span>
                                </div>
                                <div class="activity-time">{{ ucfirst($appointment->status) }}</div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="text-muted">No appointments today</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Patient Statistics -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Patient Statistics</div>
                    <div class="mini-chart-trend">+12% <span class="mini-chart-trend sub">(Weekly growth)</span></div>
                    <canvas id="patientStatsChart" height="88"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-lg-4 col-md-12">
                <div class="mini-card">
                    <div class="mini-chart-title">Recent Activities</div>
                    <div class="activity-log">
                        <div class="activity-item">
                            <div class="fw-bold mb-1">Prescription Updated</div>
                            <div class="text-muted">For John Doe - Antibiotics course</div>
                            <div class="activity-time">10:15 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="fw-bold mb-1">Lab Results Reviewed</div>
                            <div class="text-muted">Blood test results for Sarah Smith</div>
                            <div class="activity-time">09:45 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="fw-bold mb-1">Emergency Case</div>
                            <div class="text-muted">Admitted chest pain patient</div>
                            <div class="activity-time">09:00 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="fw-bold mb-1">Consultation Completed</div>
                            <div class="text-muted">Follow-up with Michael Brown</div>
                            <div class="activity-time">08:30 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="fw-bold mb-1">Report Generated</div>
                            <div class="text-muted">Monthly patient summary</div>
                            <div class="activity-time">Yesterday</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="mini-card">
                    <div class="mini-chart-title">Upcoming Appointments (Next 3 Days)</div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Condition</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_date->format('M d, Y') }},
                                            {{ $appointment->start_time }}</td>
                                        <td>{{ $appointment->patient->user->name ?? 'Unknown' }}</td>
                                        <td><span
                                                class="consultation-type {{ $appointment->type == 'follow-up' ? 'consultation-followup' : 'consultation-new' }}">{{ ucfirst($appointment->type ?? 'New') }}</span>
                                        </td>
                                        <td>{{ $appointment->notes ?? 'General Checkup' }}</td>
                                        <td>
                                            <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                                class="btn btn-sm btn-outline-primary">View Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No upcoming appointments</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Patient Statistics Chart
        new Chart(document.getElementById('patientStatsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                datasets: [{
                    label: 'Consultations',
                    data: [8, 12, 6, 14, 10, 4],
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Patients'
                        }
                    }
                }
            }
        });
    </script>
@endsection
