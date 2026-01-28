@extends('staff.layouts.master')

@section('title', 'Add Patient to Queue')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">

                    <div class="card-header text-white">
                        <h5 class="mb-0">Add New Patient to Queue</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('staff.queue.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient *</label>
                                        <select name="patient_id" id="patient_id" class="form-control select2" required>
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}"
                                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->first_name }} {{ $patient->last_name }}

                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_id">Doctor *</label>
                                        <select name="doctor_id" id="doctor_id" class="form-control select2" required>
                                            <option value="">Select Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}"
                                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('doctor_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="queue_type">Queue Type *</label>
                                        <select name="queue_type" id="queue_type" class="form-control" required>
                                            <option value="normal" {{ old('queue_type') == 'normal' ? 'selected' : '' }}>
                                                Normal</option>
                                            <option value="emergency"
                                                {{ old('queue_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                            <option value="follow_up"
                                                {{ old('queue_type') == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                        </select>
                                        @error('queue_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="reason_for_visit">Reason for Visit</label>
                                        <input type="text" name="reason_for_visit" id="reason_for_visit"
                                            class="form-control" placeholder="Brief reason for visit"
                                            value="{{ old('reason_for_visit') }}">
                                        @error('reason_for_visit')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-check mt-3">
                                <input type="checkbox" name="is_priority" id="is_priority" class="form-check-input"
                                    value="1" {{ old('is_priority') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_priority">
                                    Mark as Priority Patient
                                </label>
                                <small class="form-text text-muted">Check this for elderly, children, or urgent
                                    cases</small>
                                @error('is_priority')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add to Queue
                                </button>
                                <a href="{{ route('staff.queue.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@endsection
