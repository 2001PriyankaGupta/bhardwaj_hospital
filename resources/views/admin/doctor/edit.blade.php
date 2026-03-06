@extends('admin.layouts.master')

@section('title', 'Edit Doctor')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">

                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Edit Doctor - {{ $doctor->full_name }}</h1>
                    <p class="text-muted mb-0">Update doctor profile information, modify specialties, consultation fees and
                        schedule availability</p>
                </div>
            </div>
            <div class="action-buttons">

                <a class="btn btn-secondary" href="{{ route('admin.doctors.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name *</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            id="first_name" name="first_name"
                                            value="{{ old('first_name', $doctor->first_name) }}" required>
                                        @error('first_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            id="last_name" name="last_name"
                                            value="{{ old('last_name', $doctor->last_name) }}" required>
                                        @error('last_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $doctor->email) }}"
                                            required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $doctor->phone) }}"
                                            required>
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="license_number">License Number *</label>
                                        <input type="text"
                                            class="form-control @error('license_number') is-invalid @enderror"
                                            id="license_number" name="license_number"
                                            value="{{ old('license_number', $doctor->license_number) }}" required>
                                        @error('license_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialty_id">Specialty *</label>
                                        <select class="form-control @error('specialty_id') is-invalid @enderror"
                                            id="specialty_id" name="specialty_id" required>
                                            <option value="">Select Specialty</option>
                                            @foreach ($specialties as $specialty)
                                                <option value="{{ $specialty->id }}"
                                                    {{ old('specialty_id', $doctor->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('specialty_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_patient_fee">New Patient Fee (₹) *</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('new_patient_fee') is-invalid @enderror"
                                            id="new_patient_fee" name="new_patient_fee"
                                            value="{{ old('new_patient_fee', $doctor->new_patient_fee) }}" required>
                                        @error('new_patient_fee')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="old_patient_fee">Old Patient Fee (₹) *</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('old_patient_fee') is-invalid @enderror"
                                            id="old_patient_fee" name="old_patient_fee"
                                            value="{{ old('old_patient_fee', $doctor->old_patient_fee) }}" required>
                                        @error('old_patient_fee')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="consultation_fee" value="{{ $doctor->consultation_fee }}">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status12"
                                            name="status">
                                            <option value="active"
                                                {{ old('status', $doctor->status) == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $doctor->status) == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                            <option value="on_leave"
                                                {{ old('status', $doctor->status) == 'on_leave' ? 'selected' : '' }}>On
                                                Leave</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_image">Profile Image</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('profile_image') is-invalid @enderror"
                                                id="profile_image" name="profile_image" accept="image/*">

                                            @error('profile_image')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if ($doctor->profile_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$doctor->profile_image) }}"
                                                    alt="Current Profile" class="img-thumbnail" width="100">
                                                <br>
                                                <small>Current Profile Image</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_verified"
                                            name="is_verified" value="1"
                                            {{ old('is_verified', $doctor->is_verified) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified">Verified Doctor</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="qualifications">Qualifications *</label>
                                <textarea class="form-control @error('qualifications') is-invalid @enderror" id="qualifications"
                                    name="qualifications" rows="3" required>{{ old('qualifications', $doctor->qualifications) }}</textarea>
                                @error('qualifications')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <label for="experience">Experience</label>
                                <textarea class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience"
                                    rows="2">{{ old('experience', $doctor->experience) }}</textarea>
                                @error('experience')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <label for="bio">Bio/Description</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $doctor->bio) }}</textarea>
                                @error('bio')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Doctor
                            </button>
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
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
        $(document).ready(function() {
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endsection
