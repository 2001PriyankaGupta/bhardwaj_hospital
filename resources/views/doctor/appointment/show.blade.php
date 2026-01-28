@extends('doctor.layouts.master')

@section('title', 'Appointment Details')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Appointment Details</h1>
                    <p class="text-muted mb-0">Complete information about the appointment</p>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-secondary" href="{{ route('doctor.appointments.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Calendar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Appointment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Patient Details</h6>
                                <p><strong>Name:</strong> {{ $appointment->patient->first_name }}
                                    {{ $appointment->patient->last_name }}</p>
                                <p><strong>Email:</strong> {{ $appointment->patient->email }}</p>
                                <p><strong>Phone:</strong> {{ $appointment->patient->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Appointment Details</h6>
                                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}</p>
                                <p><strong>Time:</strong>
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    <span style="color: black"
                                        class="badge badge-{{ $appointment->status == 'scheduled' ? 'primary' : ($appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info')) }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Doctor Information</h6>
                                <p><strong>Name:</strong> Dr. {{ $appointment->doctor->full_name }}</p>
                                <p><strong>Specialty:</strong> {{ $appointment->doctor->specialty->name }}</p>
                                <p><strong>Fee:</strong> ₹{{ number_format($appointment->doctor->consultation_fee, 2) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Resource Information</h6>
                                @if ($appointment->resource)
                                    <p><strong>Resource:</strong> {{ $appointment->resource->name }}</p>
                                    <p><strong>Type:</strong> {{ $appointment->resource->type }}</p>
                                    <p><strong>Description:</strong> {{ $appointment->resource->description ?? 'N/A' }}</p>
                                @else
                                    <p class="text-muted">No resource allocated</p>
                                @endif
                            </div>
                        </div>

                        @if ($appointment->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Notes</h6>
                                    <div class="border p-3 rounded bg-light">
                                        {{ $appointment->notes }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($appointment->cancellation_reason)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Cancellation Reason</h6>
                                    <div class="border p-3 rounded bg-light">
                                        {{ $appointment->cancellation_reason }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">


                <!-- Appointment Timeline -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">Appointment Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item {{ $appointment->status == 'scheduled' ? 'active' : '' }}">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Scheduled</h6>
                                    <small>{{ $appointment->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>

                            @if ($appointment->status == 'confirmed' || $appointment->status == 'completed')
                                <div class="timeline-item {{ $appointment->status == 'confirmed' ? 'active' : '' }}">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Confirmed</h6>
                                        <small>Updated: {{ $appointment->updated_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($appointment->status == 'completed')
                                <div class="timeline-item active">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6>Completed</h6>
                                        <small>Completed on: {{ $appointment->updated_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($appointment->status == 'cancelled')
                                <div class="timeline-item active">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6>Cancelled</h6>
                                        <small>Cancelled on: {{ $appointment->updated_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
        }

        .timeline-item.active .timeline-marker {
            background: #007bff;
        }

        .timeline-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-content small {
            color: #6c757d;
        }
    </style>
@endsection
