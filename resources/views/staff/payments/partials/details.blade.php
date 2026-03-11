<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-user-injured mr-2"></i>Patient Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $payment->patient->first_name ?? 'N/A' }}
                    {{ $payment->patient->last_name ?? '' }}</p>
                <p><strong>Email:</strong> {{ $payment->patient->email ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $payment->patient->phone ?? 'N/A' }}</p>
                <p><strong>Patient ID:</strong> PAT-{{ $payment->patient_id ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-calendar-check mr-2"></i>Appointment Details</h6>
            </div>
            <div class="card-body">
                <p><strong>Appointment ID:</strong> {{ $payment->appointment_id ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ $payment->appointment->appointment_date ?? 'N/A' }}</p>
                <p><strong>Time:</strong> {{ $payment->appointment->start_time ?? 'N/A' }}</p>
                <p><strong>Doctor:</strong> Dr. {{ $payment->appointment->doctor->first_name ?? 'N/A' }}
                    {{ $payment->appointment->doctor->last_name ?? '' }}</p>
                <p><strong>Service:</strong> {{ $payment->appointment->service ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i>Payment Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Payment ID:</strong> #{{ $payment->id }}</p>
                        <p><strong>Amount:</strong> ₹{{ number_format($payment->amount, 2) }}
                        </p>
                        <p><strong>Status:</strong> <span
                                class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($payment->status) }}</span>
                        </p>
                        <p><strong>Payment Method:</strong> {{ $payment->payment_method ?? 'N/A' }}</p>

                    </div>
                    <div class="col-md-6">
                        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id ?? 'N/A' }}</p>
                        <p><strong>Payment Date:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                        <p><strong>Processed By:</strong> System</p>
                        <p><strong>Notes:</strong> {{ data_get($payment->meta, 'notes', '—') }}</p>
                        @if ($payment->status === 'pending')
                            <div class="mt-3">
                                <button class="btn btn-sm btn-success" id="modalMarkPaidBtn"
                                    data-id="{{ $payment->id }}">Mark as Paid</button>
                                <a class="btn btn-sm btn-primary"
                                    href="{{ url('staff/payments/' . $payment->id . '/invoice') }}"
                                    target="_blank">View
                                    Invoice</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
