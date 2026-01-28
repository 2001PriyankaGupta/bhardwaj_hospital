@extends('staff.layouts.master')

@section('title', 'Doctor Performance - ' . $doctor->full_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">

                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Performance Analytics - Dr.
                        {{ $doctor->full_name }}</h1>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-secondary" href="{{ route('staff.doctors.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <!-- Date Range Filter -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body py-3">
                                <form action="{{ route('staff.doctors.performance', $doctor) }}" method="GET"
                                    class="row align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" name="start_date" class="form-control form-control-sm"
                                                value="{{ $startDate }}" max="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label">End Date</label>
                                            <input type="date" name="end_date" class="form-control form-control-sm"
                                                value="{{ $endDate }}" max="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                                            <i class="fas fa-filter mr-1"></i> Apply
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('staff.doctors.performance', $doctor) }}"
                                            class="btn btn-secondary btn-sm btn-block">
                                            <i class="fas fa-redo mr-1"></i> Reset
                                        </a>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <span class="text-muted small">
                                            {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} -
                                            {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Performance Overview Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="text-white-50 small">Total Appointments</div>
                                                <div class="h4 font-weight-bold">{{ $performanceStats->total_appts ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <i class="fas fa-calendar-check fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="text-white-50 small">Completed</div>
                                                <div class="h4 font-weight-bold">
                                                    {{ $performanceStats->completed_appts ?? 0 }}</div>
                                            </div>
                                            <div class="mt-2">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="text-white-50 small">Avg. Rating</div>
                                                <div class="h4 font-weight-bold">
                                                    {{ number_format($performanceStats->avg_rating ?? 0, 1) }}/5</div>
                                            </div>
                                            <div class="mt-2">
                                                <i class="fas fa-star fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-info text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="text-white-50 small">Total Revenue</div>
                                                <div class="h4 font-weight-bold">
                                                    ₹{{ number_format($performanceStats->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="mt-2">
                                                <i class="fas fa-rupee-sign fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-bar mr-2"></i>Appointment Trends
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="appointmentChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-pie mr-2"></i>Appointment Distribution
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="appointmentPieChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Details Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-table mr-2"></i>Daily Performance Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover w-100"
                                        id="performanceTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Completed</th>
                                                <th>Cancelled</th>
                                                <th>No Show</th>
                                                <th>Rating</th>
                                                <th>Revenue</th>
                                                <th>Satisfaction</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($performances as $performance)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $performance->performance_date->format('M d, Y') }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $performance->performance_date->format('l') }}</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="font-weight-bold">{{ $performance->total_appointments }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-success font-weight-bold">{{ $performance->completed_appointments }}</span>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ number_format($performance->success_rate, 1) }}%</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-warning font-weight-bold">{{ $performance->cancelled_appointments }}</span>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ number_format($performance->cancellation_rate, 1) }}%</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-danger font-weight-bold">{{ $performance->no_show_appointments }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="font-weight-bold mr-2">{{ number_format($performance->average_rating, 1) }}</span>
                                                            <div class="text-warning">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= floor($performance->average_rating))
                                                                        <i class="fas fa-star"></i>
                                                                    @elseif($i - 0.5 <= $performance->average_rating)
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <br>
                                                        </div>
                                                        <small class="text-muted">({{ $performance->total_reviews }}
                                                            reviews)</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="font-weight-bold text-success">₹{{ number_format($performance->revenue_generated, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-{{ $performance->patient_satisfaction_score >= 80 ? 'success' : ($performance->patient_satisfaction_score >= 60 ? 'warning' : 'danger') }}"
                                                                role="progressbar"
                                                                style="width: {{ $performance->patient_satisfaction_score }}%"
                                                                aria-valuenow="{{ $performance->patient_satisfaction_score }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                                {{ $performance->patient_satisfaction_score }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                                                            <h5>No performance data found</h5>
                                                            <p>No performance records available for the selected date range
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        #performanceTable th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('scripts')
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
          let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            // Initialize DataTable
            $('#performanceTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search performance...",
                }
            });

            // Appointment Trends Chart
            const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
            const appointmentChart = new Chart(appointmentCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(
                        $performances->pluck('performance_date')->map(function ($date) {
                            return $date->format('M d');
                        }),
                    ) !!},
                    datasets: [{
                            label: 'Total Appointments',
                            data: {!! json_encode($performances->pluck('total_appointments')) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Completed',
                            data: {!! json_encode($performances->pluck('completed_appointments')) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Daily Appointment Trends'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Appointments'
                            }
                        }
                    }
                }
            });

            // Appointment Distribution Pie Chart
            const totalCompleted = {{ $performanceStats->completed_appts ?? 0 }};
            const totalCancelled = {{ $performanceStats->cancelled_appts ?? 0 }};
            const totalNoShow = {{ $performanceStats->no_show_appts ?? 0 }};

            const pieCtx = document.getElementById('appointmentPieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Cancelled', 'No Show'],
                    datasets: [{
                        data: [totalCompleted, totalCancelled, totalNoShow],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Appointment Status Distribution'
                        }
                    }
                }
            });
        });
    </script>

    <!-- Include DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
@endsection
