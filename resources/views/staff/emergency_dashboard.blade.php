@extends('admin.layouts.master')
@section('title')
    Emergency Dashboard
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --accent: #ff4900;
            --accent-gradient: linear-gradient(100deg, #ff4900, #ff7433);
            --card-bg: #fff;
            --muted: #a1a1a1;
            --section-bg: #f8f9fa;
            --activity-bg: #f3f3f3;
            --critical: #e84118;
            --urgent: #ff9f1a;
            --stable: #4cd137;
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

        /* Emergency List */
        .emergency-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .emergency-item {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 12px;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--accent);
        }

        .emergency-item.critical {
            border-left-color: var(--critical);
        }

        .emergency-item.urgent {
            border-left-color: var(--urgent);
        }

        .emergency-item.stable {
            border-left-color: var(--stable);
        }

        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .priority-critical {
            background-color: rgba(232, 65, 24, 0.1);
            color: var(--critical);
        }

        .priority-urgent {
            background-color: rgba(255, 159, 26, 0.1);
            color: var(--urgent);
        }

        .priority-stable {
            background-color: rgba(76, 209, 55, 0.1);
            color: var(--stable);
        }

        /* Staff availability */
        .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
        }

        .staff-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .staff-item:last-child {
            border-bottom: none;
        }

        .staff-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .status-available {
            background-color: var(--stable);
        }

        .status-busy {
            background-color: var(--urgent);
        }

        .status-offline {
            background-color: var(--muted);
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
@endsection

@section('content')
    <div class="container-fluid mt-3">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Emergency Dashboard</h1>
                    <p>Real-time monitoring and management of emergency situations</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-2"></i>Refresh</button>
                        <button class="btn btn-primary"><i class="bi bi-bell me-2"></i>Alerts (3)</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="row">
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <div class="value">12</div>
                    <div class="label">Active Emergencies</div>
                    <div class="sub-label">+2 from yesterday</div>
                    <div class="kpi-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <div class="value">4.2<span style="font-size: 1.5rem;">min</span></div>
                    <div class="label">Avg. Response Time</div>
                    <div class="sub-label">-0.3min improvement</div>
                    <div class="kpi-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <div class="value">87<span style="font-size: 1.5rem;">%</span></div>
                    <div class="label">Staff Available</div>
                    <div class="sub-label">15/17 staff members</div>
                    <div class="kpi-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi negative">
                    <div class="value">3</div>
                    <div class="label">Critical Cases</div>
                    <div class="sub-label">Require immediate attention</div>
                    <div class="kpi-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Left Column: Active Emergencies and Triage -->
            <div class="col-lg-6">
                <!-- Active Emergencies List -->
                <div class="mini-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mini-chart-title">Active Emergencies</h5>
                        <span class="badge bg-danger">12 Active</span>
                    </div>
                    <div class="emergency-list">
                        <div class="emergency-item critical">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Cardiac Arrest - Room 204</h6>
                                    <p class="mb-1 text-muted">Patient: John Smith, 68</p>
                                    <small class="text-muted">Reported: 08:24 AM</small>
                                </div>
                                <span class="priority-badge priority-critical">Critical</span>
                            </div>
                        </div>
                        <div class="emergency-item urgent">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Severe Trauma - ER Bay 3</h6>
                                    <p class="mb-1 text-muted">Patient: Jane Doe, 34</p>
                                    <small class="text-muted">Reported: 08:42 AM</small>
                                </div>
                                <span class="priority-badge priority-urgent">Urgent</span>
                            </div>
                        </div>
                        <div class="emergency-item stable">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Respiratory Distress - Room 112</h6>
                                    <p class="mb-1 text-muted">Patient: Robert Brown, 52</p>
                                    <small class="text-muted">Reported: 09:15 AM</small>
                                </div>
                                <span class="priority-badge priority-stable">Stable</span>
                            </div>
                        </div>
                        <div class="emergency-item urgent">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Stroke Symptoms - Room 305</h6>
                                    <p class="mb-1 text-muted">Patient: Mary Johnson, 71</p>
                                    <small class="text-muted">Reported: 09:28 AM</small>
                                </div>
                                <span class="priority-badge priority-urgent">Urgent</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Triage Priority Levels -->
                <div class="mini-card">
                    <h5 class="mini-chart-title">Triage Distribution</h5>
                    <div class="row mt-3">
                        <div class="col-4 text-center">
                            <div class="mb-2">
                                <div
                                    style="width: 60px; height: 60px; border-radius: 50%; background-color: var(--critical); margin: 0 auto;">
                                </div>
                            </div>
                            <h5 class="mb-0">3</h5>
                            <small class="text-muted">Critical</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="mb-2">
                                <div
                                    style="width: 60px; height: 60px; border-radius: 50%; background-color: var(--urgent); margin: 0 auto;">
                                </div>
                            </div>
                            <h5 class="mb-0">5</h5>
                            <small class="text-muted">Urgent</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="mb-2">
                                <div
                                    style="width: 60px; height: 60px; border-radius: 50%; background-color: var(--stable); margin: 0 auto;">
                                </div>
                            </div>
                            <h5 class="mb-0">4</h5>
                            <small class="text-muted">Stable</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Staff and Response Times -->
            <div class="col-lg-6">
                <!-- Staff Availability -->
                <div class="mini-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mini-chart-title">Staff Availability</h5>
                        <div>
                            <span class="badge bg-success">15 Available</span>
                            <span class="badge bg-warning">2 Busy</span>
                        </div>
                    </div>
                    <div class="activity-log">
                        <div class="staff-item">
                            <div class="staff-status status-available"></div>
                            <div class="staff-avatar me-3">DR</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Dr. Sarah Wilson</h6>
                                <small class="text-muted">Emergency Physician</small>
                            </div>
                            <span class="badge bg-success">Available</span>
                        </div>
                        <div class="staff-item">
                            <div class="staff-status status-busy"></div>
                            <div class="staff-avatar me-3">MJ</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Dr. Michael Johnson</h6>
                                <small class="text-muted">Trauma Surgeon</small>
                            </div>
                            <span class="badge bg-warning">In Surgery</span>
                        </div>
                        <div class="staff-item">
                            <div class="staff-status status-available"></div>
                            <div class="staff-avatar me-3">ER</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Emily Roberts, RN</h6>
                                <small class="text-muted">Emergency Nurse</small>
                            </div>
                            <span class="badge bg-success">Available</span>
                        </div>
                        <div class="staff-item">
                            <div class="staff-status status-available"></div>
                            <div class="staff-avatar me-3">TP</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Thomas Parker, EMT</h6>
                                <small class="text-muted">Paramedic</small>
                            </div>
                            <span class="badge bg-success">On Call</span>
                        </div>
                    </div>
                </div>

                <!-- Response Time Tracking -->
                <div class="mini-card">
                    <h5 class="mini-chart-title">Response Time Tracking</h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="mb-3">
                                <h4 class="mb-0">4.2<span class="fs-6">min</span></h4>
                                <small class="text-muted">Current Average</small>
                            </div>
                            <div class="mb-3">
                                <h4 class="mb-0">3.8<span class="fs-6">min</span></h4>
                                <small class="text-muted">Target Average</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <canvas id="responseTimeChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Statistics and Quick Actions -->
        <div class="row mt-3">
            <!-- Emergency Statistics -->
            <div class="col-lg-8">
                <div class="mini-card">
                    <h5 class="mini-chart-title">Emergency Statistics</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="emergencyStatsChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Cardiac Cases</span>
                                    <span class="fw-bold">24%</span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 24%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Trauma Cases</span>
                                    <span class="fw-bold">19%</span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 19%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Respiratory Cases</span>
                                    <span class="fw-bold">15%</span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 15%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Neurological Cases</span>
                                    <span class="fw-bold">12%</span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 12%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="mini-card">
                    <h5 class="mini-chart-title">Quick Actions</h5>
                    <div class="quick-actions mt-3">
                        <button class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-plus-circle me-2"></i>New Emergency
                        </button>
                        <button class="btn btn-outline-success w-100 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Assign Staff
                        </button>
                        <button class="btn btn-outline-secondary w-100 mb-3">
                            <i class="bi bi-clipboard-data me-2"></i>Generate Report
                        </button>
                        <button class="btn btn-outline-secondary w-100">
                            <i class="bi bi-gear me-2"></i>System Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize charts when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Triage Distribution Chart
            const triageCtx = document.getElementById('triageChart').getContext('2d');
            const triageChart = new Chart(triageCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Critical', 'Urgent', 'Stable'],
                    datasets: [{
                        data: [3, 5, 4],
                        backgroundColor: [
                            '#e84118',
                            '#ff9f1a',
                            '#4cd137'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Response Time Chart
            const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
            const responseChart = new Chart(responseCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Response Time (min)',
                        data: [5.2, 4.8, 4.5, 4.3, 4.1, 4.6, 4.2],
                        borderColor: '#ff4900',
                        backgroundColor: 'rgba(255, 73, 0, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 3,
                            max: 6
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Emergency Statistics Chart
            const statsCtx = document.getElementById('emergencyStatsChart').getContext('2d');
            const statsChart = new Chart(statsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Emergency Cases',
                        data: [45, 52, 48, 61, 58, 67],
                        backgroundColor: '#ff4900',
                        borderRadius: 5
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
