@extends('staff.layouts.master')

@section('title', 'Edit Emergency Case - ' . $emergency->case_number)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Edit Case: {{ $emergency->case_number }}
                        </h6>
                        <a href="{{ route('staff.emergency.show', $emergency) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.emergency.update', $emergency) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_name">Patient Name *</label>
                                        <input type="text"
                                            class="form-control @error('patient_name') is-invalid @enderror"
                                            id="patient_name" name="patient_name"
                                            value="{{ old('patient_name', $emergency->patient_name) }}" required>
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="age">Age *</label>
                                        <input type="number" class="form-control @error('age') is-invalid @enderror"
                                            id="age" name="age" value="{{ old('age', $emergency->age) }}"
                                            min="0" max="150" required>
                                        @error('age')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">Gender *</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                            name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male"
                                                {{ old('gender', $emergency->gender) == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('gender', $emergency->gender) == 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="Other"
                                                {{ old('gender', $emergency->gender) == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="symptoms">Symptoms & Condition *</label>
                                <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="4"
                                    required>{{ old('symptoms', $emergency->symptoms) }}</textarea>
                                @error('symptoms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="triage_level">Triage Level *</label>
                                        <select class="form-control @error('triage_level') is-invalid @enderror"
                                            id="triage_level" name="triage_level" required>
                                            <option value="">Select Triage Level</option>
                                            @foreach ($triageLevels as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('triage_level', $emergency->triage_level) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('triage_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="assigned_staff">Assigned Staff</label>
                                        <select class="form-control @error('assigned_staff') is-invalid @enderror"
                                            id="assigned_staff" name="assigned_staff">
                                            <option value="">Select Staff</option>
                                            @foreach ($staff as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ (int) old('assigned_staff', $emergency->assigned_staff) === $s->id ? 'selected' : '' }}>
                                                    {{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('assigned_staff')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status *</label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status12"
                                            name="status" required>
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('status', $emergency->status) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="notes">Medical Notes & Updates</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $emergency->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Case Information -->
                            <div class="alert alert-info mt-3">
                                <h6 class="alert-heading">Case Information:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Case Number:</strong> {{ $emergency->case_number }}<br>
                                        <strong>Created By:</strong> {{ $emergency->creator->name ?? 'System' }}<br>
                                        <strong>Created At:</strong> {{ $emergency->created_at->format('M d, Y H:i:s') }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Arrival Time:</strong>
                                        {{ $emergency->arrival_time->format('M d, Y H:i:s') }}<br>
                                        @if ($emergency->treatment_time)
                                            <strong>Treatment Time:</strong>
                                            {{ $emergency->treatment_time->format('M d, Y H:i:s') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Emergency Case
                                </button>
                                <a href="{{ route('staff.emergency.show', $emergency) }}"
                                    class="btn btn-secondary">Cancel</a>

                                <button type="button" class="btn btn-danger float-right"
                                    onclick="if(confirm('Are you sure you want to delete this case?')) { document.getElementById('delete-form').submit(); }">
                                    <i class="fas fa-trash"></i> Delete Case
                                </button>
                            </div>
                        </form>

                        <!-- Delete Form -->
                        <form id="delete-form" action="{{ route('staff.emergency.destroy', $emergency) }}"
                            method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-update treatment time when status changed to completed
            const statusSelect = document.getElementById('status');
            const triageLevelSelect = document.getElementById('triage_level');

            // Update priority score when triage level changes
            triageLevelSelect.addEventListener('change', function() {
                const priorityScores = {
                    'Red': 100,
                    'Yellow': 75,
                    'Green': 50,
                    'Blue': 25
                };

                const selectedLevel = this.value;
                if (priorityScores[selectedLevel]) {
                    console.log('Priority score would be:', priorityScores[selectedLevel]);
                    // You can display this to the user or store it in a hidden field
                }
            });
        });
    </script>
@endpush
