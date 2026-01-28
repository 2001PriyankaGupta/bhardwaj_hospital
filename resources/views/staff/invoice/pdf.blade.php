<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background-color: #fff;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
        }

        .hospital-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .hospital-address {
            font-size: 13px;
            margin-bottom: 5px;
        }

        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }

        .invoice-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-info-table td {
            padding: 8px;
            vertical-align: top;
            border: none;
        }

        .info-label {
            font-weight: bold;
            white-space: nowrap;
        }

        .divider {
            border-top: 2px dashed #333;
            margin: 15px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .items-table td {
            border: 1px solid #333;
            padding: 8px;
        }

        .section-header {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 6px;
            border: 1px solid #333;
        }

        .text-right {
            text-align: right;
        }

        .amount-section {
            margin-top: 30px;
            width: 100%;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .amount-label {
            font-weight: bold;
        }

        .amount-value {
            font-weight: bold;
        }

        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 12px 0;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .date-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .date-item {
            width: 48%;
        }

        .bold-text {
            font-weight: bold;
        }

        .payment-history {
            margin-top: 30px;
        }

        .notes-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="hospital-header">
            <div class="hospital-name">Bhardwaj Hospital</div>
            <div class="hospital-address">D 65/336, <br>LAHARTARA, BAULIYA LAHARTARA VARANASI <br> UTTAR PRADESH 221002
            </div>
        </div>

        <div class="invoice-title">Inpatient Invoice</div>
        @php
            $patient_id = $invoice->patient->user_id;
            $userdata = \App\Models\User::where('id', $patient_id)->first();
        @endphp

        <table class="invoice-info-table">
            <tr>
                <td width="33%">
                    <span class="info-label">Invoice ID:</span> {{ $invoice->invoice_number }}<br>
                    <span class="info-label">Patient Name:</span> {{ $invoice->patient->first_name }}
                    {{ $invoice->patient->last_name }}<br>
                    <span class="info-label">Patient Id:</span> {{ $invoice->patient->patient_id }}
                </td>
                <td width="33%">
                    <span class="info-label">Age:</span> {{ $userdata->age ?? 'N/A' }}<br>
                    <span class="info-label">Gender:</span> {{ ucfirst($invoice->patient->gender) }}<br>
                    <span class="info-label">Phone:</span> {{ $invoice->patient->phone }}
                </td>
                <td width="33%">
                    <span class="info-label">Bill Date:</span> {{ $invoice->invoice_date->format('d-m-Y') }}<br>
                    <span class="info-label">Account:</span> {{ ucfirst($invoice->payment_method ?? 'Cash') }}<br>
                </td>
            </tr>
        </table>


        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Rate</th>
                    <th width="20%">Total</th>
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
                    foreach ($groupedItems as $category => $items) {
                        $categoryTotals[$category] = collect($items)->sum(function ($item) {
                            return ($item->quantity ?? 1) * ($item->rate ?? $item->amount);
                        });
                    }

                    $grandTotal = array_sum($categoryTotals);
                @endphp

                @foreach ($groupedItems as $category => $items)
                    <tr>
                        <td colspan="4" class="section-header">{{ strtoupper($category) }}</td>
                    </tr>

                    @foreach ($items as $item)
                        <tr>
                            <td>
                                {{ $item->service_date ? $item->service_date->format('d-m-Y') . ' - ' : '' }}
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
                        <td colspan="3" class="text-right bold-text">{{ strtoupper($category) }}</td>
                        <td class="text-right bold-text">{{ number_format($categoryTotals[$category], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row amount-section" style="display: flex;">
            <div class="col-4 amount-row total-row">
                <div class="amount-label">Total Amount:</div>
                <div class="amount-value">{{ number_format($invoice->total_amount ?? $grandTotal, 2) }}</div>
            </div>

            <div class="col-4 amount-row">
                <div class="amount-label">Paid Amount:</div>
                <div class="amount-value">{{ number_format($invoice->paid_amount, 2) }}</div>
            </div>

            <div class="col-4 amount-row">
                <div class="amount-label">Due Amount:</div>
                <div class="amount-value">{{ number_format($invoice->due_amount, 2) }}</div>
            </div>
        </div>
        {{-- 
        @if ($invoice->notes)
            <div class="notes-section">
                <strong>Notes:</strong>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif --}}

        @if ($invoice->payments && !$invoice->payments->isEmpty())
            <div class="payment-history">
                <h4>Payment History</h4>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Payment Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer">
            <p>Thank you for choosing Bhardwaj Hospital</p>
            <p>For any queries, please contact: +91-4785693256 | Email: infobhardwaj@hospital.com</p>
            <p class="no-print">Generated on: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
