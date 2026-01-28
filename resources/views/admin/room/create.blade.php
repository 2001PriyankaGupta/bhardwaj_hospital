@extends('admin.layouts.master')

@section('title', 'Add New Room')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-orange">
                    <div class="card-header bg-orange text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            {{ isset($room) ? 'Edit Room' : 'Add New Room' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($room) ? route('admin.rooms.update', $room->id) : route('admin.rooms.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($room))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_number" class="form-label text-orange">
                                            <i class="fas fa-hashtag me-1"></i>Room Number *
                                        </label>
                                        <input type="text"
                                            class="form-control @error('room_number') is-invalid @enderror" id="room_number"
                                            name="room_number" value="{{ old('room_number', $room->room_number ?? '') }}"
                                            required>
                                        @error('room_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_type_id" class="form-label text-orange">
                                            <i class="fas fa-tags me-1"></i>Room Type *
                                        </label>
                                        <select class="form-control @error('room_type_id') is-invalid @enderror"
                                            id="room_type_id" name="room_type_id" required>
                                            <option value="">Select Room Type</option>
                                            @foreach ($roomTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('room_type_id', $room->room_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }} - ₹{{ $type->base_price }}/day
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="floor_number" class="form-label text-orange">
                                            <i class="fas fa-building me-1"></i>Floor Number *
                                        </label>
                                        <input type="number"
                                            class="form-control @error('floor_number') is-invalid @enderror"
                                            id="floor_number" name="floor_number"
                                            value="{{ old('floor_number', $room->floor_number ?? '') }}" required>
                                        @error('floor_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ward_name" class="form-label text-orange">
                                            <i class="fas fa-clinic-medical me-1"></i>Ward Name
                                        </label>
                                        <input type="text" class="form-control @error('ward_name') is-invalid @enderror"
                                            id="ward_name" name="ward_name"
                                            value="{{ old('ward_name', $room->ward_name ?? '') }}">
                                        @error('ward_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bed_count" class="form-label text-orange">
                                            <i class="fas fa-bed me-1"></i>Bed Count *
                                        </label>
                                        <input type="number" class="form-control @error('bed_count') is-invalid @enderror"
                                            id="bed_count" name="bed_count"
                                            value="{{ old('bed_count', $room->bed_count ?? '') }}" required>
                                        @error('bed_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label text-orange">
                                            <i class="fas fa-info-circle me-1"></i>Status *
                                        </label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="available"
                                                {{ old('status', $room->status ?? '') == 'available' ? 'selected' : '' }}>
                                                Available
                                            </option>
                                            <option value="occupied"
                                                {{ old('status', $room->status ?? '') == 'occupied' ? 'selected' : '' }}>
                                                Occupied
                                            </option>
                                            <option value="maintenance"
                                                {{ old('status', $room->status ?? '') == 'maintenance' ? 'selected' : '' }}>
                                                Maintenance
                                            </option>
                                            <option value="cleaning"
                                                {{ old('status', $room->status ?? '') == 'cleaning' ? 'selected' : '' }}>
                                                Cleaning
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current_occupancy" class="form-label text-orange">
                                            <i class="fas fa-users me-1"></i>Current Occupancy
                                        </label>
                                        <input type="number"
                                            class="form-control @error('current_occupancy') is-invalid @enderror"
                                            id="current_occupancy" name="current_occupancy"
                                            value="{{ old('current_occupancy', $room->current_occupancy ?? 0) }}">
                                        @error('current_occupancy')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label text-orange">
                                    <i class="fas fa-sticky-note me-1"></i>Notes
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $room->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back
                                </a>
                                <button type="submit" class="btn btn-orange">
                                    <i class="fas fa-save me-1"></i>
                                    {{ isset($room) ? 'Update Room' : 'Create Room' }}
                                </button>
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

@endsection
