@extends('staff.layouts.master')
@section('title')
    Staff Dashboard
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
        --accent: #ff4900;
        --accent-gradient: linear-gradient(100deg, #3498db, #5dade2);
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

    .dashboard-kpi.success {
        border-left-color: #27ae60;
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

    .activity-item .badge {
        font-size: 11px;
        padding: 3px 8px;
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

    .quick-actions .btn-outline-primary {
        color: var(--accent);
        border-color: var(--accent);
    }

    .quick-actions .btn-outline-primary:hover {
        background: var(--accent);
        color: white;
    }

    .quick-actions i {
        font-size: 18px !important;
    }

    /* Today's Tasks */
    .task-item {
        padding: 12px 15px;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 10px;
        border-left: 4px solid var(--accent);
    }

    .task-item.completed {
        opacity: 0.7;
        border-left-color: #27ae60;
    }

    .task-item.pending {
        border-left-color: #f39c12;
    }

    .task-item.urgent {
        border-left-color: #e74c3c;
    }

    .task-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
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
            <h1>Staff Dashboard</h1>
            <p>Welcome back, <span class="fw-semibold">{{ Auth::user()->name ?? 'Staff Member' }}</span>! Here's your daily
                overview and tasks.</p>
        </div>


        <!-- KPIs -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-user-clock"></i></span>
                    <div class="value">{{ $assignedPatients ?? 15 }}</div>
                    <div class="label">My Patients</div>
                    <div class="sub-label">Assigned today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi warning">
                    <span class="kpi-icon"><i class="fas fa-tasks"></i></span>
                    <div class="value">{{ $pendingTasks ?? 8 }}</div>
                    <div class="label">Pending Tasks</div>
                    <div class="sub-label">To complete today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi success">
                    <span class="kpi-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="value">{{ $completedTasks ?? 12 }}</div>
                    <div class="label">Completed Tasks</div>
                    <div class="sub-label">Today's achievements</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-kpi negative">
                    <span class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="value">{{ $urgentCases ?? 3 }}</div>
                    <div class="label">Urgent Cases</div>
                    <div class="sub-label">Require attention</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Today's Tasks -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Today's Tasks</div>
                    <div class="mini-chart-trend">{{ $todayTaskCount ?? 5 }} tasks <span class="mini-chart-trend sub">for
                            today</span></div>
                    <div class="activity-log">
                        @if (isset($tasks) && count($tasks) > 0)
                            @foreach ($tasks as $task)
                                <div class="task-item {{ $task['status'] }}">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="task-checkbox me-3"
                                            {{ $task['status'] == 'completed' ? 'checked' : '' }}>
                                        <div>
                                            <div class="fw-bold mb-1">{{ $task['title'] }}</div>
                                            <div class="text-muted small">{{ $task['description'] }}</div>
                                            <div class="activity-time">{{ $task['time'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default tasks if no data -->
                            <div class="task-item urgent">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="task-checkbox me-3">
                                    <div>
                                        <div class="fw-bold mb-1">Administer Medication</div>
                                        <div class="text-muted small">Room 204 - Patient John Doe</div>
                                        <div class="activity-time">10:00 AM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="task-item pending">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="task-checkbox me-3">
                                    <div>
                                        <div class="fw-bold mb-1">Vital Signs Check</div>
                                        <div class="text-muted small">Ward 3 - All patients</div>
                                        <div class="activity-time">11:30 AM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="task-item completed">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="task-checkbox me-3" checked>
                                    <div>
                                        <div class="fw-bold mb-1">Morning Shift Handover</div>
                                        <div class="text-muted small">Completed with night staff</div>
                                        <div class="activity-time">08:00 AM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="task-item pending">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="task-checkbox me-3">
                                    <div>
                                        <div class="fw-bold mb-1">Documentation Update</div>
                                        <div class="text-muted small">Patient charts for Dr. Sharma</div>
                                        <div class="activity-time">02:00 PM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="task-item pending">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="task-checkbox me-3">
                                    <div>
                                        <div class="fw-bold mb-1">Equipment Check</div>
                                        <div class="text-muted small">Ventilators and monitors</div>
                                        <div class="activity-time">04:00 PM</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Patient Flow -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Medication Schedule</div>
                    <div class="mini-chart-trend">Next: 30 mins <span class="mini-chart-trend sub">(Room 204)</span></div>
                    <canvas id="medicationChart" height="120"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-lg-4 col-md-12">
                <div class="mini-card">
                    <div class="mini-chart-title">Recent Activities</div>
                    <div class="activity-log">
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold mb-1">Patient Vitals Updated</div>
                                <span class="badge bg-success">Done</span>
                            </div>
                            <div class="text-muted">Room 204 - Blood pressure recorded</div>
                            <div class="activity-time">09:45 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold mb-1">New Patient Assigned</div>
                                <span class="badge bg-info">New</span>
                            </div>
                            <div class="text-muted">Sarah Johnson - Ward 3 Bed 5</div>
                            <div class="activity-time">09:15 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold mb-1">Medication Administered</div>
                                <span class="badge bg-success">Done</span>
                            </div>
                            <div class="text-muted">Antibiotics for Patient #234</div>
                            <div class="activity-time">08:30 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold mb-1">Doctor Notification Sent</div>
                                <span class="badge bg-warning">Alert</span>
                            </div>
                            <div class="text-muted">Dr. Sharma about Patient #201</div>
                            <div class="activity-time">07:45 AM</div>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold mb-1">Shift Started</div>
                                <span class="badge bg-primary">Info</span>
                            </div>
                            <div class="text-muted">Morning shift - 7:00 AM to 3:00 PM</div>
                            <div class="activity-time">07:00 AM</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="mini-card">
                    <div class="mini-chart-title">Today's Schedule</div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Room/Ward</th>
                                    <th>Task</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($todayAppointmentsList) && $todayAppointmentsList->count() > 0)
                                    @foreach ($todayAppointmentsList as $ap)
                                        <tr>
                                            <td>
                                                @if ($ap->start_time)
                                                    {{ \Carbon\Carbon::parse($ap->start_time)->format('h:i A') }}
                                                    @if ($ap->end_time)
                                                        - {{ \Carbon\Carbon::parse($ap->end_time)->format('h:i A') }}
                                                    @endif
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td>{{ $ap->patient ? trim(($ap->patient->first_name ?? '') . ' ' . ($ap->patient->last_name ?? '')) : 'Patient' }}
                                            </td>
                                            <td>
                                                @if (isset($ap->resource) && isset($ap->resource->name))
                                                    {{ $ap->resource->name }}
                                                @elseif(isset($ap->department) && isset($ap->department->name))
                                                    {{ $ap->department->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($ap->type ?? 'Appointment') }}</td>
                                            <td>
                                                @php $status = strtolower($ap->status ?? ''); @endphp
                                                @if ($status == 'scheduled')
                                                    <span class="badge bg-info">Scheduled</span>
                                                @elseif($status == 'confirmed')
                                                    <span class="badge bg-primary">Confirmed</span>
                                                @elseif($status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($status == 'cancelled' || $status == 'canceled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary">{{ ucfirst($ap->status ?? 'Pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No appointments scheduled for
                                            today.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-lg-4">
                <div class="mini-card">
                    <div class="mini-chart-title">Quick Stats</div>
                    <div class="row mt-3">
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="value" style="font-size: 2rem; color: var(--accent);">6</div>
                                <div class="label">Medications Due</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="value" style="font-size: 2rem; color: #27ae60;">3</div>
                                <div class="label">Vitals Pending</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="value" style="font-size: 2rem; color: #f39c12;">2</div>
                                <div class="label">Doctor Alerts</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="value" style="font-size: 2rem; color: #e74c3c;">1</div>
                                <div class="label">Urgent Tasks</div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="mt-3 text-center">
                        <a href="{{ route('staff.tasks.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-list-check me-2"></i> View All Tasks
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Medication Schedule Chart
        new Chart(document.getElementById('medicationChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM'],
                datasets: [{
                    label: 'Medications Due',
                    data: [3, 5, 2, 4, 6, 1],
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
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
                            text: 'Number of Medications'
                        }
                    }
                }
            }
        });

        // Task checkbox functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.task-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const taskItem = this.closest('.task-item');
                    if (this.checked) {
                        taskItem.classList.add('completed');
                        taskItem.classList.remove('pending', 'urgent');

                        // Show notification
                        showNotification('Task marked as completed!');

                        // Update stats (in real app, this would be an AJAX call)
                        const pendingCount = document.querySelector(
                            '.dashboard-kpi.warning .value');
                        const completedCount = document.querySelector(
                            '.dashboard-kpi.success .value');

                        if (pendingCount && completedCount) {
                            let pending = parseInt(pendingCount.textContent) - 1;
                            let completed = parseInt(completedCount.textContent) + 1;

                            pendingCount.textContent = Math.max(0, pending);
                            completedCount.textContent = completed;
                        }
                    }
                });
            });

            function showNotification(message) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                notification.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
@endsection
