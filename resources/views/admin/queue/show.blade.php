@extends('admin.layouts.master')

@section('title', 'Queue Details')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        margin-bottom: 15px;
        position: relative;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -20px;
        top: 8px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #007bff;
    }

    .timeline-time {
        margin-left: 10px;
        color: #6c757d;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Queue Details - {{ $queue->queue_number }}</h5>
                        <div>
                            <span class="badge bg-light text-dark">{{ $queue->status }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Patient Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Name:</th>
                                        <td>{{ $queue->patient->first_name }} {{ $queue->patient->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Patient ID:</th>
                                        <td>{{ $queue->patient->patient_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $queue->patient->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender/Age:</th>
                                        <td>{{ $queue->patient->gender }} /
                                            @if ($queue->patient->date_of_birth)
                                                {{ now()->diffInYears($queue->patient->date_of_birth) }} years
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6>Queue Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Doctor:</th>
                                        <td>Dr. {{ $queue->doctor->first_name }} {{ $queue->doctor->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Queue Type:</th>
                                        <td>
                                            <span
                                                class="badge 
                                            @if ($queue->queue_type == 'emergency') bg-danger
                                            @elseif($queue->queue_type == 'follow_up') bg-warning
                                            @else bg-primary @endif">
                                                {{ ucfirst($queue->queue_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Position:</th>
                                        <td>{{ $queue->position }}</td>
                                    </tr>
                                    <tr>
                                        <th>Check-in Time:</th>
                                        <td>{{ $queue->check_in_time->format('h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Wait Time:</th>
                                        <td>{{ $queue->estimated_wait_time }} minutes</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Visit Details</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Reason for Visit:</strong><br>
                                            {{ $queue->reason_for_visit ?? 'Not specified' }}</p>

                                        @if ($queue->vital_signs)
                                            <p><strong>Vital Signs:</strong></p>
                                            <ul>
                                                @foreach ($queue->vital_signs as $key => $value)
                                                    <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($queue->notes)
                                            <p><strong>Notes:</strong><br>
                                                {{ $queue->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Timeline</h6>
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <span class="badge bg-primary">Check-in</span>
                                        <span class="timeline-time">{{ $queue->check_in_time->format('h:i A') }}</span>
                                    </div>
                                    @if ($queue->called_at)
                                        <div class="timeline-item">
                                            <span class="badge bg-info">Called</span>
                                            <span class="timeline-time">{{ $queue->called_at->format('h:i A') }}</span>
                                        </div>
                                    @endif
                                    @if ($queue->consultation_start_time)
                                        <div class="timeline-item">
                                            <span class="badge bg-success">Consultation Started</span>
                                            <span
                                                class="timeline-time">{{ $queue->consultation_start_time->format('h:i A') }}</span>
                                        </div>
                                    @endif
                                    @if ($queue->consultation_end_time)
                                        <div class="timeline-item">
                                            <span class="badge bg-secondary">Consultation Ended</span>
                                            <span
                                                class="timeline-time">{{ $queue->consultation_end_time->format('h:i A') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.queue.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('admin.queue.edit', $queue) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

@endsection
