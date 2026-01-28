@extends('admin.layouts.master')

@section('title', 'Invoice Details')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<style>
    @media print {

        .btn-group,
        .card-header .btn,
        .add-payment-section,
        .no-print {
            display: none !important;
        }

        .card-header {
            border-bottom: 1px solid #dee2e6;
        }

        body {
            background: white !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .invoice-print-container {
            border: 1px solid #000 !important;
            padding: 20px !important;
        }
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

    /* Main Invoice Container */
    .invoice-print-container {
        border: 2px solid #333;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        background: #fff;
        font-family: 'Arial', sans-serif;
    }

    /* Hospital Header Styling */
    .hospital-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #333;
    }

    .hospital-name {
        font-size: 28px;
        font-weight: 900;
        text-transform: uppercase;
        color: #000;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .hospital-address {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        line-height: 1.4;
    }

    .invoice-title {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        text-decoration: underline;
        margin: 25px 0;
        color: #000;
    }

    /* Invoice Info Box */
    .invoice-info-box {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 25px;
    }

    .patient-info-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .invoice-details-box {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 15px;
        text-align: right;
        float: right;
        width: 48%;
    }

    .section-title {
        color: #000;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 18px;
    }

    /* Items Table Styling */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .items-table th {
        background-color: #333;
        color: white;
        border: 1px solid #000;
        padding: 10px;
        text-align: left;
        font-weight: 700;
    }

    .items-table td {
        border: 1px solid #000;
        padding: 8px 10px;
    }

    .items-table tfoot {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .items-table tfoot td {
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
    }

    /* Amount Summary Styling */
    .amount-summary {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px dashed #333;
    }

    .amount-row-three {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        border: 2px solid #333;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .amount-column {
        flex: 1;
        text-align: center;
        padding: 0 15px;
    }

    .amount-column:not(:last-child) {
        border-right: 1px solid #ccc;
    }

    .amount-label {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }

    .amount-value {
        font-size: 20px;
        font-weight: 800;
        color: #000;
    }

    .total-amount {
        color: #000;
    }

    .paid-amount {
        color: #28a745;
    }

    .due-amount {
        color: #dc3545;
    }

    /* Payment History Styling */
    .payment-history-section {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        background-color: #fff;
    }

    .add-payment-section {
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 20px;
        background-color: #f8fff9;
    }

    .separator-line {
        border-top: 2px dashed #333;
        margin: 30px 0;
    }

    .invoice-details-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 25px;
    }

    .bill-to-section {
        width: 48%;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
    }

    .invoice-no-section {
        width: 48%;
        text-align: right;
    }

    .amount-highlight {
        font-size: 1.2rem;
        font-weight: 700;
    }

    .table-section-header {
        background-color: #e9ecef;
        font-weight: bold;
        padding: 10px;
        border: 1px solid #000;
        text-transform: uppercase;
    }

    .text-bold {
        font-weight: 700 !important;
    }
</style>

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        Invoice Details - {{ $invoice->invoice_number }}
                    </h3>
                    <div class="btn-group no-print">
                        <button onclick="window.print()" class="btn btn-info mr-2">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-danger mr-2">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back To List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Invoice Print Container -->
                    <div class="invoice-print-container">
                        <!-- Hospital Header -->
                        <div class="hospital-header">
                            <div class="hospital-name">BHARDWAJ HOSPITAL</div>
                            <div class="hospital-address">
                                D 65/336, LAHARTARA, BAULIYA LAHARTARA<br>
                                VARANASI, UTTAR PRADESH - 221002
                            </div>
                        </div>

                        <!-- Invoice Title -->
                        <div class="invoice-title">
                            INPATIENT INVOICE
                        </div>
                        @php
                            $patient_id = $invoice->patient->user_id;
                            $userdata = \App\Models\User::where('id', $patient_id)->first();
                        @endphp

                        <!-- Invoice Information -->
                        <div class="invoice-info-box">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Invoice ID:</strong> {{ $invoice->invoice_number }}</p>
                                    <p class="mb-1"><strong>Patient Name:</strong> {{ $invoice->patient->first_name }}
                                        {{ $invoice->patient->last_name }}</p>
                                    <p class="mb-0"><strong>Patient ID:</strong> {{ $invoice->patient->patient_id }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Age:</strong> {{ $userdata->age ?? 'N/A' }} Years</p>
                                    <p class="mb-1"><strong>Gender:</strong> {{ ucfirst($invoice->patient->gender) }}</p>
                                    <p class="mb-0"><strong>Phone:</strong> {{ $invoice->patient->phone }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Bill Date:</strong>
                                        {{ $invoice->invoice_date->format('d-m-Y') }}</p>
                                    <p class="mb-0"><strong>Account:</strong>
                                        {{ ucfirst($invoice->payment_method ?? 'Cash') }}</p>
                                </div>
                            </div>


                        </div>

                        <div class="separator-line"></div>

                        <!-- Invoice Items Table -->
                        <h5 class="section-title">Invoice Items</h5>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th width="10%">Qty</th>
                                    <th width="15%">Rate (₹)</th>
                                    <th width="20%">Total (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Group items by category
                                    $groupedItems = [];
                                    foreach ($invoice->items as $item) {
                                        $category = $item->category ?? 'HOSPITAL CHARGE';
                                        if (!isset($groupedItems[$category])) {
                                            $groupedItems[$category] = [];
                                        }
                                        $groupedItems[$category][] = $item;
                                    }

                                    // Calculate totals per category
                                    $categoryTotals = [];
                                    $grandTotal = 0;
                                    foreach ($groupedItems as $category => $items) {
                                        $categoryTotal = collect($items)->sum(function ($item) {
                                            return ($item->quantity ?? 1) * ($item->rate ?? $item->amount);
                                        });
                                        $categoryTotals[$category] = $categoryTotal;
                                        $grandTotal += $categoryTotal;
                                    }
                                @endphp

                                @foreach ($groupedItems as $category => $items)
                                    <tr>
                                        <td colspan="4" class="table-section-header">{{ strtoupper($category) }}</td>
                                    </tr>

                                    @foreach ($items as $item)
                                        <tr>
                                            <td>
                                                @if ($item->service_date)
                                                    {{ $item->service_date->format('d-m-Y') }} -
                                                @endif
                                                {{ $item->description }}
                                            </td>
                                            <td>{{ $item->quantity ?? 1 }}</td>
                                            <td>{{ number_format($item->rate ?? $item->amount, 2) }}</td>
                                            <td class="text-right">
                                                {{ number_format(($item->quantity ?? 1) * ($item->rate ?? $item->amount), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" class="text-right text-bold">{{ strtoupper($category) }}</td>
                                        <td class="text-right text-bold">{{ number_format($categoryTotals[$category], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Amount Summary -->
                        <div class="amount-summary">
                            <div class="amount-row-three">
                                <div class="amount-column">
                                    <div class="amount-label">Total Amount</div>
                                    <div class="amount-value total-amount">
                                        ₹{{ number_format($invoice->total_amount ?? $grandTotal, 2) }}</div>
                                </div>

                                <div class="amount-column">
                                    <div class="amount-label">Paid Amount</div>
                                    <div class="amount-value paid-amount">₹{{ number_format($invoice->paid_amount, 2) }}
                                    </div>
                                </div>

                                <div class="amount-column">
                                    <div class="amount-label">Due Amount</div>
                                    <div class="amount-value due-amount">₹{{ number_format($invoice->due_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        @if ($invoice->notes)
                            <div class="notes-section mt-4 p-3 border rounded">
                                <h6 class="text-bold mb-2">Notes:</h6>
                                <p class="mb-0">{{ $invoice->notes }}</p>
                            </div>
                        @endif

                        {{-- <div class="footer mt-4 pt-3 border-top text-center">
                            <p class="mb-1"><strong>Thank you for choosing Bhardwaj Hospital</strong></p>
                            <p class="mb-0 text-muted">For any queries, contact: +91-XXXXXXXXXX | Email: info@hospital.com
                            </p>
                            <p class="text-muted no-print">Generated on: {{ now()->format('d M Y H:i') }}</p>
                        </div> --}}
                    </div>

                    <!-- Payment History (Admin View Only) -->
                    <div class="payment-history-section no-print">
                        <h5 class="section-title">
                            <i class="fas fa-history mr-2"></i>Payment History
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Payment Date</th>
                                        <th>Amount (₹)</th>
                                        <th>Method</th>
                                        <th>Transaction ID</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->payments as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td class="text-success font-weight-bold">
                                                ₹{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span style="color: black"
                                                    class="badge badge-info px-2 py-1">{{ ucfirst($payment->payment_method) }}</span>
                                            </td>
                                            <td>
                                                <code>{{ $payment->transaction_id ?? 'N/A' }}</code>
                                            </td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($invoice->payments->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle mr-2"></i>No payments recorded yet
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add Payment Form (Admin View Only) -->
                    @if ($invoice->due_amount > 0)
                        <div class="add-payment-section no-print">
                            <h5 class="section-title text-success">
                                <i class="fas fa-plus-circle mr-2"></i>Add New Payment
                            </h5>
                            <div class="alert alert-warning mb-3">
                                <strong><i class="fas fa-exclamation-triangle mr-2"></i>Outstanding Amount:</strong>
                                <span class="amount-highlight ml-2">₹{{ number_format($invoice->due_amount, 2) }}</span>
                            </div>

                            <form action="{{ route('admin.invoices.payments.store', $invoice) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="amount" class="font-weight-bold">Amount (₹) *</label>
                                            <input type="number" name="amount" id="amount"
                                                class="form-control border-success" max="{{ $invoice->due_amount }}"
                                                step="0.01" required placeholder="Enter amount">
                                            <small class="form-text text-muted">
                                                Maximum: ₹{{ number_format($invoice->due_amount, 2) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="payment_method" class="font-weight-bold">Payment Method *</label>
                                            <select name="payment_method" id="payment_method"
                                                class="form-control border-success" required>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="upi">UPI</option>
                                                <option value="netbanking">Net Banking</option>
                                                <option value="cheque">Cheque</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="payment_date" class="font-weight-bold">Payment Date *</label>
                                            <input type="date" name="payment_date" id="payment_date"
                                                class="form-control border-success" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="transaction_id" class="font-weight-bold">Transaction ID</label>
                                            <input type="text" name="transaction_id" id="transaction_id"
                                                class="form-control border-success" placeholder="Optional">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="payment_notes" class="font-weight-bold">Notes</label>
                                    <textarea name="notes" id="payment_notes" class="form-control border-success" rows="2"
                                        placeholder="Add payment notes (optional)"></textarea>
                                </div>
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-4">
                                        <i class="fas fa-check-circle mr-2"></i>Record Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
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

            // Set max amount validation
            $('#amount').on('change', function() {
                let dueAmount = {{ $invoice->due_amount }};
                let enteredAmount = parseFloat($(this).val());

                if (enteredAmount > dueAmount) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Amount Exceeded',
                        text: 'Entered amount cannot be more than due amount (₹' + dueAmount
                            .toFixed(2) + ')',
                    });
                    $(this).val(dueAmount);
                }
            });
        });
    </script>
@endsection
