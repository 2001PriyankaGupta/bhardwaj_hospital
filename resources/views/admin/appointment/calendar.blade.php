@extends('admin.layouts.master')

@section('title', 'Appointment Calendar')

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Reset any conflicting styles */
    #appointmentCalendar {
        width: 100% !important;
        min-height: 80vh !important;
    }

    .calendar-main-card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    /* Stats Cards */
    .stats-card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    }

    .stats-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        color: white;
    }

    .stats-icon i {
        font-size: 1.25rem;
    }

    .stats-title {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stats-count {
        color: #5a5c69;
    }

    .bg-primary {
        background-color: #007bff !important;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    /* FullCalendar Custom Overrides */
    .fc {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .fc .fc-toolbar {
        padding: 1rem 1.5rem;
        margin-bottom: 0;
        background: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
    }

    .fc .fc-toolbar-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #5a5c69;
        margin: 0;
    }

    .fc .fc-button {
        background: white !important;
        border: 1px solid #d1d3e2 !important;
        color: #6e707e !important;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        transition: all 0.15s ease;
    }

    .fc .fc-button:hover {
        background: #f8f9fc !important;
        border-color: #bac8f3 !important;
        color: #007bff !important;
    }

    .fc .fc-button-active,
    .fc .fc-button:active {
        background: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .fc .fc-day-today {
        background-color: #e8f4ff !important;
    }

    .fc .fc-daygrid-day-number {
        font-weight: 600;
        color: #5a5c69;
        padding: 0.5rem;
        font-size: 0.9rem;
    }

    .fc .fc-col-header-cell {
        background: #f8f9fa;
        padding: 0.75rem 0;
    }

    .fc .fc-col-header-cell-cushion {
        color: #5a5c69;
        font-weight: 600;
        text-decoration: none !important;
        font-size: 0.9rem;
    }

    /* Event Styles */
    .fc .fc-event {
        border: none;
        border-radius: 4px;
        padding: 3px 6px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin: 1px 2px;
        color: rgb(0, 0, 0);
    }

    .fc .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .fc-event-scheduled {
        background-color: #007bff;
        border-left: 3px solid #0056b3;
    }

    .fc-event-confirmed {
        background-color: #28a745;
        border-left: 3px solid #1e7e34;
    }

    .fc-event-completed {
        background-color: #ffc107;
        border-left: 3px solid #e0a800;
        color: #000;
    }

    .fc-event-cancelled {
        background-color: #dc3545;
        border-left: 3px solid #c82333;
    }

    .fc .fc-event-time {
        font-weight: 600;
        font-size: 0.75rem;
    }

    .fc .fc-event-title {
        font-weight: 500;
        font-size: 0.75rem;
    }

    /* Calendar Grid */
    .fc .fc-daygrid-day-frame {
        min-height: 100px;
    }

    .fc .fc-timegrid-slot {
        height: 2.5em !important;
    }

    /* Ensure full width */
    .fc .fc-view-harness {
        width: 100% !important;
    }

    .fc .fc-view {
        width: 100% !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }

        .fc .fc-toolbar-chunk {
            text-align: center;
        }

        .fc .fc-toolbar-title {
            font-size: 1.2rem;
        }

        .btn-group {
            flex-wrap: wrap;
            justify-content: center;
        }

        #appointmentCalendar {
            min-height: 70vh !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid.py-3.bg-light .row.g-3 {
            margin: 0 -5px;
        }

        .container-fluid.py-3.bg-light .col-xl-3 {
            padding: 0 5px;
        }

        .stats-card .card-body {
            padding: 1rem;
        }

        .stats-icon {
            width: 2.5rem;
            height: 2.5rem;
        }

        .stats-icon i {
            font-size: 1rem;
        }
    }

    /* Remove any default margins/paddings that might cause issues */
    body {
        margin: 0;
        padding: 0;
    }

    .container-fluid.px-0 {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        max-width: 100% !important;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="container-fluid py-3 bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0 text-orange fw-bold">Appointment Calendar</h1>
                    <p class="text-muted mb-0">Manage and view all appointments in an interactive calendar</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary btn-sm" id="calendarPrevBtn" title="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="calendarNextBtn" title="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="calendarTodayBtn">
                            <i class="fas fa-calendar-day"></i> Today
                        </button>
                    </div>
                    <a class="btn btn-primary btn-sm me-2" href="{{ route('admin.appointments.create') }}">
                        <i class="fas fa-plus-circle"></i> New Appointment
                    </a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.appointments.index') }}">
                        <i class="fas fa-list"></i> List View
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="container-fluid py-3 bg-light">
            <div class="row g-3">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card h-100">
                        <div class="card-body" style="background-color: #dbd9d9;">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-primary">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-title text-uppercase text-muted mb-0">Scheduled</h6>
                                    <span class="stats-count h4 fw-bold mb-0">{{ $stats['scheduled'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card h-100">
                        <div class="card-body" style="background-color: #dbd9d9;">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-title text-uppercase text-muted mb-0">Confirmed</h6>
                                    <span class="stats-count h4 fw-bold mb-0">{{ $stats['confirmed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card h-100">
                        <div class="card-body" style="background-color: #dbd9d9;">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-title text-uppercase text-muted mb-0">Completed</h6>
                                    <span class="stats-count h4 fw-bold mb-0">{{ $stats['completed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card h-100">
                        <div class="card-body" style="background-color: #dbd9d9;">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-danger">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-title text-uppercase text-muted mb-0">Cancelled</h6>
                                    <span class="stats-count h4 fw-bold mb-0">{{ $stats['cancelled'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="container-fluid py-3">
            <div class="row">
                <div class="col-12">
                    <div class="card calendar-main-card">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Appointment Schedule</h5>

                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="appointmentCalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('appointmentCalendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'standard',
                firstDay: 1, // Monday
                weekNumbers: true,
                weekNumberFormat: {
                    week: 'numeric'
                },
                navLinks: true,
                editable: false,
                dayMaxEvents: 4,
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.5,
                views: {
                    timeGridWeek: {
                        dayMaxEvents: 6
                    },
                    timeGridDay: {
                        dayMaxEvents: 10
                    }
                },
                events: [
                    @foreach ($appointments as $appointment)
                        {
                            id: '{{ $appointment->id }}',
                            title: '{{ $appointment->patient_name ?? '' }} - Dr. {{ $appointment->doctor->first_name ?? '' }}',
                            start: '{{ $appointment->appointment_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i:s') }}',
                            end: '{{ $appointment->appointment_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i:s') }}',
                            className: 'fc-event-{{ $appointment->status }}',
                            extendedProps: {
                                patient: '{{ $appointment->patient_name }}',
                                doctor: 'Dr. {{ $appointment->doctor->first_name ?? '' }} {{ $appointment->doctor->last_name ?? '' }}',
                                status: '{{ $appointment->status }}',
                                phone: '{{ $appointment->patient_phone }}',
                                email: '{{ $appointment->patient_email }}'
                            }
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    window.location.href = '{{ url(Auth::user()->user_type . '/appointments') }}/' +
                        info.event.id;
                },
                dateClick: function(info) {
                    window.location.href = '/admin/appointments/create?date=' + info.dateStr;
                },
                eventDidMount: function(info) {
                    // Add tooltip to events
                    if (info.event.title) {
                        info.el.setAttribute('title',
                            `${info.event.extendedProps.patient} with ${info.event.extendedProps.doctor} at ${info.event.start.toLocaleTimeString()}`
                        );
                    }
                },
                loading: function(bool) {
                    if (bool) {
                        calendarEl.classList.add('calendar-loading');
                    } else {
                        calendarEl.classList.remove('calendar-loading');
                    }
                }
            });

            calendar.render();

            // Custom navigation buttons
            document.getElementById('calendarPrevBtn').addEventListener('click', function() {
                calendar.prev();
            });

            document.getElementById('calendarNextBtn').addEventListener('click', function() {
                calendar.next();
            });

            document.getElementById('calendarTodayBtn').addEventListener('click', function() {
                calendar.today();
            });

            // View switching
            document.getElementById('calendarMonthView').addEventListener('click', function() {
                calendar.changeView('dayGridMonth');
                updateActiveViewButton('month');
            });

            document.getElementById('calendarWeekView').addEventListener('click', function() {
                calendar.changeView('timeGridWeek');
                updateActiveViewButton('week');
            });

            document.getElementById('calendarDayView').addEventListener('click', function() {
                calendar.changeView('timeGridDay');
                updateActiveViewButton('day');
            });

            function updateActiveViewButton(activeView) {
                // Remove active class from all buttons
                document.querySelectorAll('.calendar-view-options .btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active class to current view button
                document.getElementById('calendar' + activeView.charAt(0).toUpperCase() + activeView.slice(1) +
                        'View')
                    .classList.add('active');
            }

            // Update active view button when view changes
            calendar.on('viewDidMount', function(view) {
                const viewType = view.type;
                if (viewType === 'dayGridMonth') {
                    updateActiveViewButton('month');
                } else if (viewType === 'timeGridWeek') {
                    updateActiveViewButton('week');
                } else if (viewType === 'timeGridDay') {
                    updateActiveViewButton('day');
                }
            });

            // Force calendar to resize on window resize
            window.addEventListener('resize', function() {
                calendar.updateSize();
            });
        });
    </script>
@endsection
