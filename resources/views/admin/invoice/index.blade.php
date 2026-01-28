@extends('admin.layouts.master')

@section('title', 'Billing & Invoices')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
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
</style>

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Billing & Invoices</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Fixed ID here: invoiceTabless -> invoiceTable -->
                        <table class="table table-bordered table-hover" id="invoiceTable">
                            <thead>
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Patient</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                                            <br>
                                            <small style="color: purple"> {{ $invoice->patient->patient_id }} </small>
                                        </td>

                                        <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>₹{{ number_format($invoice->paid_amount, 2) }}</td>
                                        <td>₹{{ number_format($invoice->due_amount, 2) }}</td>
                                        <td>
                                            <span style="color: black"
                                                class="badge badge-{{ $invoice->status == 'paid'
                                                    ? 'success'
                                                    : ($invoice->status == 'partial'
                                                        ? 'warning'
                                                        : ($invoice->status == 'overdue'
                                                            ? 'danger'
                                                            : 'secondary')) }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $invoice) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="#" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Remove pagination if using DataTables -->
                    <!-- <div class="mt-3">
                                                    {{ $invoices->links() }}
                                                </div> -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            // DataTable init - Fixed ID here to match HTML
            $('#invoiceTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [6] // Actions column (index 6, not 7)
                }],
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
@endsection
