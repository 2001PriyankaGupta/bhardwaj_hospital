@extends('admin.layouts.master')

@section('title', 'Add New Emergency Case')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-black">Add New Emergency Case</h5>
                        <a href="{{ route('admin.emergency.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.emergency.store') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_name">Patient Name *</label>
                                        <input type="text"
                                            class="form-control @error('patient_name') is-invalid @enderror"
                                            id="patient_name" name="patient_name" value="{{ old('patient_name') }}"
                                            required>
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="age">Age *</label>
                                        <input type="number" class="form-control @error('age') is-invalid @enderror"
                                            id="age" name="age" value="{{ old('age') }}" min="0"
                                            max="150" required>
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
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="symptoms">Symptoms & Condition *</label>
                                <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="4"
                                    required>{{ old('symptoms') }}</textarea>
                                @error('symptoms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="triage_level">Triage Level *</label>
                                        <select class="form-control @error('triage_level') is-invalid @enderror"
                                            id="triage_level" name="triage_level" required>
                                            <option value="">Select Triage Level</option>
                                            @foreach ($triageLevels as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('triage_level') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('triage_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="triage_level">Doctor</label>
                                        <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id"
                                            name="doctor_id">
                                            <option value="">Select Doctor</option>
                                            @foreach ($doctor as $doc)
                                                <option value="{{ $doc->id }}"
                                                    {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                                    {{ $doc->first_name }} {{ $doc->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="notes">Initial Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Triage Level Guidelines -->
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading">Triage Level Guidelines:</h6>
                                <ul class="mb-0 pl-3">
                                    <li><strong>Red - Immediate:</strong> Life-threatening conditions, require immediate
                                        attention</li>
                                    <li><strong>Yellow - Emergency:</strong> Serious conditions, attention within 10-15
                                        minutes</li>
                                    <li><strong>Green - Urgent:</strong> Stable conditions, can wait 30-60 minutes</li>
                                    <li><strong>Blue - Non-urgent:</strong> Minor conditions, can wait 1-2 hours</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Emergency Case
                                </button>
                                <a href="{{ route('admin.emergency.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus on patient name field
            document.getElementById('patient_name').focus();
        });
    </script>
@endsection
