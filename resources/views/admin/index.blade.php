@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
<style>
    :root {
        --accent: #ff4900;
        --accent-gradient: linear-gradient(100deg, #ff4900, #ff7433);
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
        box-shadow: 0 2px 20px rgba(255, 73, 0, 0.03);
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
        border-color: #ff4900;
    }

    .quick-actions .btn-outline-success:hover,
    .quick-actions .btn-outline-secondary:hover {
        background: #ff4900;
        color: white;
    }

    .quick-actions i {
        font-size: 18px !important;
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
            <h1>Dashboard Overview</h1>
            <p>Welcome back, <span class="fw-semibold">Administrator</span>! Here’s today’s hospital performance summary.</p>

        </div>

        <div class="quick-actions mb-4">
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i> Schedule Appointment
            </a>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-success">
                <i class="fas fa-bed me-2"></i> View Patient Records
            </a>
            <a href="{{ route('admin.emergency.index') }}" class="btn btn-secondary">
                <i class="fas fa-user-md me-2"></i> Active Emergencies
            </a>
        </div>

        <!-- KPIs -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-user-injured"></i></span>
                    <div class="value">{{ $appointmentsToday }}</div>
                    <div class="label">Today's Appointments</div>
                    <div class="sub-label">+5 from yesterday</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-procedures"></i></span>
                    <div class="value">{{ $totalPatients }}</div>
                    <div class="label">Total Patients</div>
                    <div class="sub-label">{{ $newAdmissionsToday }} new admissions</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-stethoscope"></i></span>
                    <div class="value">{{ $availableDoctors }}</div>
                    <div class="label">Available Doctors</div>
                    <div class="sub-label">{{ $doctorsOnEmergency }} on emergency</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi negative">
                    <span class="kpi-icon"><i class="fas fa-clock"></i></span>
                    <div class="value">{{ $pendingTests }}</div>
                    <div class="label">Pending Tests</div>
                    <div class="sub-label">{{ $pendingTestsDifference }} from yesterday</div>
                </div>
            </div>
        </div>

        <!-- Charts & Activities -->
        <div class="row">
            <!-- Patient Flow -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Patient Flow</div>
                    <div class="mini-chart-trend">+15% <span class="mini-chart-trend sub">(last 7 days)</span></div>
                    <canvas id="patientFlowChart" height="88"></canvas>
                </div>
            </div>
            <!-- Treatment Types -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Treatment Types</div>
                    <div class="mini-chart-trend">Emergency: 42% <span class="mini-chart-trend sub">Distribution</span>
                    </div>
                    <canvas id="treatmentChart" height="88"></canvas>
                </div>
            </div>
            <!-- Recent Activities -->
            <div class="col-lg-4 col-md-12">
                <div class="mini-card">
                    <div class="mini-chart-title">Recent Activities</div>
                    <div class="activity-log">
                        @foreach ($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="fw-bold mb-1">{{ $activity->patient->first_name }}
                                    {{ $activity->patient->last_name }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $activity->patient->email }}</div>
                                <div class="activity-time">{{ $activity->created_at->format('h:i A') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Patient Flow Chart
        new Chart(document.getElementById('patientFlowChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [120, 150, 180, 140, 200, 160, 190],
                    borderColor: '#ff4900',
                    backgroundColor: 'rgba(255, 73, 0, 0.10)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.54,
                    pointRadius: 4.5,
                    pointBackgroundColor: '#ff4900'
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Treatment Types Chart
        new Chart(document.getElementById('treatmentChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Emergency', 'OPD', 'Surgery', 'Therapy'],
                datasets: [{
                    data: [42, 25, 18, 15],
                    backgroundColor: [
                        '#ff4900',
                        '#ff6a00',
                        '#ff8c00',
                        '#ffad33'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
