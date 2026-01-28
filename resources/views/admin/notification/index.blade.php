@extends('admin.layouts.master')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #ff4900;
            --primary-light: #ff6a33;
            --primary-dark: #cc3b00;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --border-radius: 0.5rem;
        }

        body {
            background-color: #f5f7fb;
        }

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

        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: white;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        .status-active {
            background-color: #198754 !important;
        }

        .status-inactive {
            background-color: #dc3545 !important;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .card-header {
            color: rgb(0, 0, 0);
            border-bottom: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .card-header h5 {
            font-weight: 600;
            margin: 0;
        }

        .table th {
            background-color: var(--light-bg);
            font-weight: 600;
            color: #5a5c69;
            border-top: 1px solid #e3e6f0;
            padding: 0.85rem;
        }

        .table td {
            padding: 0.85rem;
            vertical-align: middle;
        }

        .table-responsive {
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            padding: 0;
        }

        .dataTables_length select {
            padding: 0.3rem;
            border-radius: 0.2rem;
            border: 1px solid #d1d3e2;
        }

        .dataTables_filter input {
            padding: 0.3rem;
            border-radius: 0.2rem;
            border: 1px solid #d1d3e2;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
        }

        .badge-type-sms {
            background-color: #17a2b8;
        }

        .badge-type-email {
            background-color: #28a745;
        }

        .badge-type-push {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-status-sent {
            background-color: #28a745;
        }

        .badge-status-failed {
            background-color: #dc3545;
        }

        .badge-status-cancelled {
            background-color: #6c757d;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 73, 0, 0.05);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }

        .action-buttons .btn {
            margin-right: 0.25rem;
            border-radius: 0.35rem;
        }



        .card-body {
            padding: 1.5rem;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e3e6f0, transparent);
            margin: 2rem 0;
        }
    </style>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <!-- Templates Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold"><i class="fas fa-envelope me-2"></i>Notification Templates</h5>
                        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary-custom">
                            <i class="fas fa-plus me-1"></i> Create Template
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="templatesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates as $template)
                                        <tr>
                                            <td class="fw-bold">{{ $template->name }}</td>
                                            <td>
                                                <span class="badge badge-type-{{ $template->type }}">
                                                    {{ strtoupper($template->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $template->subject ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $template->status ? 'success' : 'danger' }}">
                                                    {{ $template->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="action-buttons">
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ route('admin.notifications.edit', $template) }}"
                                                        class="btn btn-warning btn-sm me-1" title="Edit">
                                                        <i class="fas fa-edit" style="color: white;"></i>
                                                    </a>
                                                    <a href="{{ route('admin.notifications.schedule', $template) }}"
                                                        class="btn btn-info btn-sm me-1" title="Schedule">
                                                        <i class="fas fa-clock" style="color: white;"></i>
                                                    </a>
                                                    <form action="{{ route('admin.notifications.destroy', $template) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    <h5>No templates found</h5>
                                                    <p>Get started by creating your first notification template</p>
                                                    <a href="{{ route('admin.notifications.create') }}"
                                                        class="btn btn-primary-custom mt-2">
                                                        <i class="fas fa-plus me-1"></i> Create Template
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Messages Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold"><i class="fas fa-clock me-2"></i>Scheduled Messages</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="scheduledTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Template</th>
                                        <th>Recipients</th>
                                        <th>Scheduled At</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($scheduledMessages as $message)
                                        <tr>
                                            <td class="fw-bold">{{ $message->template->name }}</td>
                                            <td>
                                                <span class="fw-medium">{{ count($message->recipients) }} recipients</span>
                                                <small class="d-block text-muted">
                                                    {{ implode(', ', array_slice($message->recipients, 0, 2)) }}...
                                                </small>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-medium">{{ $message->scheduled_at->format('M d, Y') }}</span>
                                                <small class="d-block text-muted">
                                                    {{ $message->scheduled_at->format('H:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-status-{{ $message->status }}">
                                                    {{ ucfirst($message->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($message->status == 'pending')
                                                    <form action="{{ route('admin.scheduled-messages.cancel', $message) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to cancel this scheduled message?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times me-1"></i> Cancel
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">No actions</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <i class="fas fa-calendar-times"></i>
                                                    <h5>No scheduled messages</h5>
                                                    <p>Schedule a message to see it listed here</p>
                                                </div>
                                            </td>
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



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
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

            // Initialize DataTable for Templates
            $('#templatesTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [4] // Actions column
                }],
                order: [
                    [0, 'asc']
                ],
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    },
                    emptyTable: "No templates available",
                    info: "Showing _START_ to _END_ of _TOTAL_ templates",
                    infoEmpty: "Showing 0 to 0 of 0 templates",
                    infoFiltered: "(filtered from _MAX_ total templates)",
                    lengthMenu: "Show _MENU_ templates",
                    search: "Search templates:"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Initialize DataTable for Scheduled Messages
            $('#scheduledTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [4] // Actions column
                }],
                order: [
                    [2, 'asc']
                ], // Sort by Scheduled At
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    },
                    emptyTable: "No scheduled messages available",
                    info: "Showing _START_ to _END_ of _TOTAL_ scheduled messages",
                    infoEmpty: "Showing 0 to 0 of 0 scheduled messages",
                    infoFiltered: "(filtered from _MAX_ total scheduled messages)",
                    lengthMenu: "Show _MENU_ scheduled messages",
                    search: "Search scheduled messages:"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        });
    </script>
@endsection
