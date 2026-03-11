@extends('admin.layouts.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .small-box {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        margin-bottom: 0;
        padding: 20px;
    }

    .small-box .icon {
        font-size: 30px;
        opacity: 0.3;
    }

    .small-box .inner h3 {
        font-size: 2.2rem;
        font-weight: 700;
    }

    #paymentsTable tbody tr:hover {
        background-color: rgba(0, 0, 0, .02);
        cursor: pointer;
    }

    .badge {
        font-size: 85%;
        font-weight: 500;
        padding: 5px 10px;
    }

    .table th {
        border-top: none;
        background-color: #f8f9fa;
    }

    .appointment-info,
    .patient-info,
    .payment-info,
    .date-info {
        line-height: 1.4;
    }

    .btn-group .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, .125);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                Payment Transactions
                            </h4>

                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="statusFilter">Status</label>
                                    <select id="statusFilter" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="successful">Successful</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateFilter">Date Range</label>
                                    <input type="text" id="dateFilter" class="form-control form-control-sm"
                                        placeholder="Select date range">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="searchPatient">Search Patient</label>
                                    <input type="text" id="searchPatient" class="form-control form-control-sm"
                                        placeholder="Patient name...">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="resetFilters" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-redo mr-1"></i> Reset Filters
                                </button>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>₹{{ number_format($payments->where('status', 'successful')->sum('amount'), 2) }}
                                        </h3>
                                        <p>Total Successful Payments</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $payments->where('status', 'successful')->count() }}</h3>
                                        <p>Successful Transactions</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-check-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $payments->where('status', 'pending')->count() }}</h3>
                                        <p>Pending Payments</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $payments->whereIn('status', ['failed', 'refunded'])->count() }}</h3>
                                        <p>Failed/Refunded</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payments Table -->
                        <div class="table-responsive">
                            <table id="paymentsTable" class="table table-hover table-bordered table-striped"
                                style="width:100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Appointment Details</th>
                                        <th>Patient</th>
                                        <th>Payment Details</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#{{ $payment->id }}</span>
                                            </td>
                                            <td>
                                                <div class="appointment-info">
                                                    <strong>Appointment #{{ $payment->appointment_id }}</strong>
                                                    @if ($payment->appointment)
                                                        <div class="small text-muted">
                                                            <div><i class="fas fa-calendar-alt mr-1"></i>
                                                                {{ $payment->appointment->appointment_date ?? 'N/A' }}
                                                            </div>
                                                            <div><i class="fas fa-clock mr-1"></i>
                                                                {{ $payment->appointment->start_time ?? 'N/A' }}</div>
                                                            <div><i class="fas fa-user-md mr-1"></i> Dr.
                                                                {{ $payment->appointment->doctor->first_name ?? 'N/A' }}
                                                                {{ $payment->appointment->doctor->last_name ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-warning">Appointment Not Found</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="patient-info">
                                                    <strong>{{ $payment->patient->first_name ?? 'N/A' }}
                                                        {{ $payment->patient->last_name ?? 'N/A' }}</strong>
                                                    @if ($payment->patient)
                                                        <div class="small text-muted">
                                                            <div><i class="fas fa-phone mr-1"></i>
                                                                {{ $payment->patient->phone ?? 'N/A' }}</div>
                                                            <div><i class="fas fa-envelope mr-1"></i>
                                                                {{ $payment->patient->email ?? 'N/A' }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="payment-info">
                                                    <h5 class="mb-1">₹{{ number_format($payment->amount, 2) }}</h5>
                                                    <div class="small text-muted">
                                                        <div>Method: <span
                                                                class="badge bg-info">{{ $payment->payment_method ?? 'Credit Card' }}</span>
                                                        </div>
                                                        <div>Transaction ID:
                                                            <code>{{ $payment->transaction_id ?? 'N/A' }}</code>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'successful' => 'success',
                                                        'pending' => 'warning',
                                                        'failed' => 'danger',
                                                        'refunded' => 'info',
                                                    ];
                                                    $color = $statusColors[$payment->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <div><strong>{{ $payment->created_at->format('M d, Y') }}</strong>
                                                    </div>
                                                    <div class="small text-muted">
                                                        {{ $payment->created_at->format('h:i A') }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-info btn-sm view-payment"
                                                        data-id="{{ $payment->id }}" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if ($payment->status === 'pending')
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="markAsPaid({{ $payment->id }})"
                                                            title="Mark as Paid">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-danger btn-sm delete-payment"
                                                        data-id="{{ $payment->id }}" title="Delete Payment">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="py-5">
                                                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                                    <h5>No payment transactions found</h5>
                                                    <p class="text-muted">When payments are made, they will appear here.
                                                    </p>
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

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentDetailsContent">
                    <!-- Details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="printModalContent()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <!-- Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function() {

            // Destroy existing DataTable instance if present to avoid double initialization issues
            if ($.fn.DataTable.isDataTable('#paymentsTable')) {
                $('#paymentsTable').DataTable().destroy();
            }

            // Verify table rows match header column count and auto-fix simple mismatches
            var expectedCols = $('#paymentsTable thead th').length;
            $('#paymentsTable tbody tr').each(function() {
                var tdCount = $(this).children('td').length;
                if (tdCount !== expectedCols) {
                    console.warn('Payments table row column count mismatch', {
                        row: this,
                        expected: expectedCols,
                        found: tdCount
                    });
                    // Attempt simple fix: if only one td (usually empty state), set colspan to expected
                    if (tdCount === 1) {
                        $(this).children('td').attr('colspan', expectedCols);
                    }
                }
            });

            // Initialize DataTable (responsive + fixes)
            $('#paymentsTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ],
                responsive: true,
                autoWidth: false
            });

            // Initialize date range picker
            $('#dateFilter').daterangepicker({
                opens: 'left',
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });

            // Filter by status
            $('#statusFilter').on('change', function() {
                var status = $(this).val();
                var table = $('#paymentsTable').DataTable();
                table.column(4).search(status).draw();
            });

            // Filter by date range
            $('#dateFilter').on('apply.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');
                var table = $('#paymentsTable').DataTable();
                table.draw();
            });

            // Search patient
            $('#searchPatient').on('keyup', function() {
                var table = $('#paymentsTable').DataTable();
                table.column(2).search(this.value).draw();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#statusFilter').val('');
                $('#dateFilter').val('');
                $('#searchPatient').val('');
                var table = $('#paymentsTable').DataTable();
                table.search('').columns().search('').draw();
            });

            // View payment details
            $(document).on('click', '.view-payment', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var paymentId = $(this).data('id');
                fetchPaymentDetails(paymentId);
            });

            // Mark as paid from list
            $(document).on('click', '.btn-mark-paid', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var id = $(this).data('id');
                markAsPaid(id);
            });

            // Mark as paid from modal
            $(document).on('click', '#modalMarkPaidBtn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                markAsPaid(id, true);
            });

            // Prevent row click when action button clicked
            $('#paymentsTable tbody').on('click', '.btn-group button', function(e) {
                e.stopPropagation();
            });

            // Delete payment
            $(document).on('click', '.delete-payment', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var paymentId = $(this).data('id');
                deletePayment(paymentId);
            });

            // Make table rows clickable (single handler)
            $('#paymentsTable tbody').on('click', 'tr', function(e) {
                if (!$(e.target).closest('.btn-group').length) {
                    var paymentId = $(this).find('.view-payment').data('id');
                    if (paymentId) {
                        fetchPaymentDetails(paymentId);
                    }
                }
            });
        });

        function fetchPaymentDetails(paymentId) {
            // Ensure CSRF header is set for AJAX
            $.ajaxSetup({
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // show spinner while loading
            var $modal = $('#paymentDetailsModal');
            $('#paymentDetailsContent').html('<div class="text-center py-5">Loading...</div>');
            $modal.modal('show');

            $.ajax({
                url: '{{ url('admin/payments') }}/' + paymentId,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && res.success) {
                        $('#paymentDetailsContent').html(res.html);
                        // attach mark-as-paid handler inside modal (button has id modalMarkPaidBtn)
                    } else {
                        $modal.modal('hide');
                        Swal.fire('Error!', res.message || 'Unable to fetch payment details', 'error');
                    }
                },
                error: function(xhr) {
                    $modal.modal('hide');
                    if (xhr.status === 401 || xhr.status === 419) {
                        Swal.fire('Session Expired', 'Your session has expired. Please login again.', 'warning').then(() => {
                            window.location = '{{ url('/login') }}';
                        });
                        return;
                    }
                    var msg = 'Failed to fetch payment details';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.message) msg = json.message;
                    } catch (e) {}
                    Swal.fire('Error!', msg, 'error');
                }
            });
        }

        function printReceipt(paymentId) {
            Swal.fire('Info', 'Printing receipt for payment #' + paymentId, 'info');
            // In real implementation, this would generate/print a receipt
            // window.open('/admin/payments/' + paymentId + '/receipt', '_blank');
        }

        function markAsPaid(paymentId, fromModal = false) {
            Swal.fire({
                title: 'Mark as Paid?',
                text: 'Are you sure you want to mark payment #' + paymentId + ' as paid?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    var url = '{{ url('admin/payments') }}/' + paymentId + '/mark-paid';

                    $.post(url, {}, function(res) {
                        if (res && res.success) {
                            Swal.fire('Updated!', res.message || 'Payment updated', 'success').then(() => {
                                if (fromModal) $('#paymentDetailsModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', res.message || 'Failed to update payment', 'error');
                        }
                    }).fail(function(xhr) {
                        if (xhr.status === 401 || xhr.status === 419) {
                            Swal.fire('Session Expired', 'Your session has expired. Please login again.', 'warning').then(() => {
                                window.location = '{{ url('/login') }}';
                            });
                            return;
                        }
                        var msg = 'Failed to update payment';
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (json.message) msg = json.message;
                        } catch (e) {}
                        Swal.fire('Error!', msg, 'error');
                    });
                }
            });
        }

        function deletePayment(paymentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    var url = '{{ url('admin/payments') }}/' + paymentId;

                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(res) {
                            if (res && res.success) {
                                Swal.fire('Deleted!', res.message || 'Payment deleted successfully', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message || 'Failed to delete payment', 'error');
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Failed to delete payment';
                            try {
                                var json = JSON.parse(xhr.responseText);
                                if (json.message) msg = json.message;
                            } catch (e) {}
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                }
            });
        }

        function printModalContent() {
            var printContent = document.getElementById('paymentDetailsContent').innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = `
        <html>
            <head>
                <title>Payment Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .receipt-header { text-align: center; margin-bottom: 30px; }
                    .section { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
                    .section h4 { color: #333; margin-bottom: 10px; }
                    .text-right { text-align: right; }
                    table { width: 100%; border-collapse: collapse; }
                    table, th, td { border: 1px solid #ddd; }
                    th, td { padding: 8px; text-align: left; }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>
@endpush
