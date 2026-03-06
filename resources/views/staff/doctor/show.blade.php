@extends('staff.layouts.master')

@section('title', 'Doctor Details - ' . $doctor->full_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-4">
                <!-- Doctor Profile Card -->
                <div class="card">
                    <div class="card-body text-center">
                        @if ($doctor->profile_image)
                            <img src="{{ asset('storage/'.$doctor->profile_image) }}" alt="{{ $doctor->full_name }}"
                                class="rounded-circle" width="150" height="150">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <span
                                    class="text-white display-4">{{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h3 class="mt-3">{{ $doctor->full_name }}</h3>
                        <p class="text-muted">{{ $doctor->qualifications }}</p>
                        <span class="badge badge-info">{{ $doctor->specialty->name }}</span>

                        <div class="mt-3">
                            @if ($doctor->status == 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($doctor->status == 'inactive')
                                <span class="badge badge-secondary">Inactive</span>
                            @else
                                <span class="badge badge-warning">On Leave</span>
                            @endif

                            @if ($doctor->is_verified)
                                <span class="badge badge-primary">Verified</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong><br>{{ $doctor->email }}</p>
                        <p><strong>Phone:</strong><br>{{ $doctor->phone }}</p>
                        <p><strong>License No:</strong><br>{{ $doctor->license_number }}</p>
                        <p><strong>New Patient Fee:</strong><br>₹{{ number_format($doctor->new_patient_fee, 2) }}</p>
                        <p><strong>Old Patient Fee:</strong><br>₹{{ number_format($doctor->old_patient_fee, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Quick Stats -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon "><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Appointments</span>
                                <span class="info-box-number">{{ $performanceStats->total_appts ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon "><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completed</span>
                                <span class="info-box-number">{{ $performanceStats->completed_appts ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon "><i class="fas fa-star"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Average Rating</span>
                                <span
                                    class="info-box-number">{{ number_format($performanceStats->avg_rating ?? 0, 1) }}/5</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon "><i class="fas fa-rupee-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Revenue</span>
                                <span
                                    class="info-box-number">₹{{ number_format($performanceStats->total_revenue ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="btn-group">
                            <a href="{{ route('staff.doctors.edit', $doctor) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="{{ route('staff.doctors.schedules', $doctor) }}" class="btn btn-secondary">
                                <i class="fas fa-calendar-alt"></i> Manage Schedule
                            </a>
                            <a href="{{ route('staff.doctors.leaves', $doctor) }}" class="btn btn-warning">
                                <i class="fas fa-calendar-times"></i> Leave Applications
                            </a>
                            <a href="{{ route('staff.doctors.performance', $doctor) }}" class="btn btn-info">
                                <i class="fas fa-chart-line"></i> Performance
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bio and Experience -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">Professional Information</h5>
                    </div>
                    <div class="card-body">
                        <h6>Qualifications</h6>
                        <p>{{ $doctor->qualifications }}</p>

                        <h6>Experience</h6>
                        <p>{{ $doctor->experience ?? 'Not specified' }}</p>

                        <h6>Bio</h6>
                        <p>{{ $doctor->bio ?? 'No bio available' }}</p>
                    </div>
                </div>

                <!-- Current Week Schedule -->
                {{-- <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">Current Schedule</h5>
                    </div>
                    <div class="card-body">
                        @if ($doctor->schedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Slot Duration</th>
                                            <th>Max Patients</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($doctor->schedules as $schedule)
                                            <tr>
                                                <td>{{ ucfirst($schedule->day_of_week) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</td>
                                                <td>{{ $schedule->slot_duration }} minutes</td>
                                                <td>{{ $schedule->max_patients }}</td>
                                                <td>
                                                    @if ($schedule->is_available)
                                                        <span class="badge badge-success">Available</span>
                                                    @else
                                                        <span class="badge badge-danger">Unavailable</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No schedule configured yet.</p>
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

@endsection
