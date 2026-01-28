@extends('admin.layouts.master')

@section('title', 'Patient Details - ' . $patient->first_name . ' ' . $patient->last_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .patient-header {
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .info-box {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .info-box-icon {
        border-radius: 8px 0 0 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
    }

    .quick-actions .btn {
        border-radius: 25px;
        padding: 8px 16px;
        margin: 5px;
        transition: all 0.3s ease;
    }

    .quick-actions .btn:hover {
        transform: scale(1.05);
    }

    .list-group-item {
        border-radius: 8px !important;
        margin-bottom: 10px;
        border: 1px solid #e3e6f0 !important;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fc;
        border-color: #b7b9cc !important;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: white;
        border-bottom: 2px solid #e3e6f0;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
        color: #ff4900;
        border-bottom: 2px solid #e3e6f0;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 20px;
    }

    .section-title {
        color: #ff4900;
        border-bottom: 2px solid #ff4900;
        padding-bottom: 8px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .back-btn {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        padding: 8px 20px;
        color: white;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        transform: translateX(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
</style>


@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- Patient Header -->
                <div class="patient-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">
                                <i class="fas fa-user-injured me-2"></i>
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </h2>
                            <p class="mb-0" style="color: black">
                                <strong>Patient ID:</strong> {{ $patient->patient_id }}
                                <span
                                    class="badge badge-status {{ $patient->is_active ? 'bg-success' : 'bg-danger' }} ms-2">
                                    {{ $patient->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.patients.index') }}" class="back-btn">
                                <i class="fas fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h4 class="section-title">
                                    <i class="fas fa-user-circle me-2"></i>Personal Information
                                </h4>
                                <table class="table table-hover">
                                    <tr>
                                        <th width="40%"><i class="fas fa-id-card me-2"></i>Patient ID</th>
                                        <td>{{ $patient->patient_id }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Full Name</th>
                                        <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-envelope me-2"></i>Email</th>
                                        <td>
                                            @if ($patient->email)
                                                <a href="mailto:{{ $patient->email }}" class="text-decoration-none">
                                                    {{ $patient->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-phone me-2"></i>Phone</th>
                                        <td>
                                            @if ($patient->phone)
                                                <a href="tel:{{ $patient->phone }}" class="text-decoration-none">
                                                    {{ $patient->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-birthday-cake me-2"></i>Date of Birth</th>
                                        <td>
                                            @if ($patient->date_of_birth)
                                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') }}
                                                <small class="text-muted">
                                                    (Age: {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years)
                                                </small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-venus-mars me-2"></i>Gender</th>
                                        <td>
                                            @if ($patient->gender)
                                                <span class="badge bg-info">{{ ucfirst($patient->gender) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-map-marker-alt me-2"></i>Address</th>
                                        <td>{{ $patient->address ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Statistics & Quick Actions -->
                            <div class="col-md-6">
                                <h4 class="section-title">
                                    <i class="fas fa-chart-bar me-2"></i>Appointment Statistics
                                </h4>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-box bg-primary text-white">
                                            <span class="info-box-icon"></span>
                                            <div class="info-box-content ps-3">
                                                <span class="info-box-text">Total Appointments</span>
                                                <span class="info-box-number display-6">
                                                    {{ $analytics['appointment_stats']['total_appointments'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-success text-white">
                                            <span class="info-box-icon"></span>
                                            <div class="info-box-content ps-3">
                                                <span class="info-box-text">Completed</span>
                                                <span class="info-box-number display-6">
                                                    {{ $analytics['appointment_stats']['completed_appointments'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-4">
                                    <h4 class="section-title">
                                        <i class="fas fa-bolt me-2"></i>Quick Actions
                                    </h4>
                                    <div class="quick-actions d-flex flex-wrap">
                                        <a href="{{ route('admin.patients.medical-records', $patient) }}"
                                            class="btn btn-outline-primary">
                                            <i class="fas fa-file-medical me-2"></i> Medical Records
                                        </a>
                                        <a href="{{ route('admin.patients.appointment-history', $patient) }}"
                                            class="btn btn-outline-info">
                                            <i class="fas fa-history me-2"></i> Appointment History
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <div class="row mt-5">
                            <!-- Recent Medical Records -->
                            {{-- <div class="col-md-6">
                                <h4 class="section-title">
                                    <i class="fas fa-file-medical me-2"></i>Recent Medical Records
                                </h4>
                                @if ($analytics['recent_medical_records']->count() > 0)
                                    <div class="list-group">
                                        @foreach ($analytics['recent_medical_records'] as $record)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 text-primary">
                                                            <i class="fas fa-stethoscope me-2"></i>
                                                            {{ ucfirst($record->record_type) }}
                                                        </h6>
                                                        <p class="mb-1">{{ Str::limit($record->description, 100) }}</p>
                                                    </div>
                                                    <small class="text-muted text-end">
                                                        {{ $record->record_date->format('d M Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No medical records found.</p>
                                    </div>
                                @endif
                            </div> --}}

                            <!-- Recent Communications -->
                            {{-- <div class="col-md-6">
                                <h4 class="section-title">
                                    <i class="fas fa-comments me-2"></i>Recent Communications
                                </h4>
                                @if ($analytics['recent_communications']->count() > 0)
                                    <div class="list-group">
                                        @foreach ($analytics['recent_communications'] as $communication)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="mb-0 text-primary">
                                                                <i class="fas fa-envelope me-2"></i>
                                                                {{ $communication->subject }}
                                                            </h6>
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($communication->communication_type) }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-1">{{ Str::limit($communication->message, 100) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $communication->created_at->format('d M Y, h:i A') }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No communication logs found.</p>
                                    </div>
                                @endif
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let baseUrl = "{{ config('app.url') }}";
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animations
            const cards = document.querySelectorAll('.info-box, .list-group-item');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
            });

            // Animate elements on load
            setTimeout(() => {
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);
        });
    </script>
@endsection
