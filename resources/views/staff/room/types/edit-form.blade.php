<div class="row">
    <div class="col-md-6 mb-3">
        <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $roomType->name }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="edit_base_price" class="form-label">Base Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control" id="edit_base_price" name="base_price"
            value="{{ $roomType->base_price }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="edit_hourly_rate" class="form-label">Hourly Rate</label>
        <input type="number" step="0.01" class="form-control" id="edit_hourly_rate" name="hourly_rate"
            value="{{ $roomType->hourly_rate }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="edit_max_capacity" class="form-label">Max Capacity <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="edit_max_capacity" name="max_capacity"
            value="{{ $roomType->max_capacity }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="edit_available_rooms" class="form-label">Available Rooms</label>
        <input type="number" class="form-control" id="edit_available_rooms" name="available_rooms"
            value="{{ $roomType->available_rooms }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="edit_current_utilization" class="form-label">Current Utilization</label>
        <input type="number" class="form-control" id="edit_current_utilization" name="current_utilization"
            value="{{ $roomType->current_utilization }}">
    </div>
</div>

<div class="mb-3">
    <label for="edit_description" class="form-label">Description</label>
    <textarea class="form-control" id="edit_description" name="description" rows="3">{{ $roomType->description }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Amenities</label>
    <div class="row">
        @php
            $commonAmenities = [
                'WiFi',
                'AC',
                'TV',
                'Mini Bar',
                'Room Service',
                'Breakfast',
                'Parking',
                'Swimming Pool',
            ];
            $selectedAmenities = $roomType->amenities ? json_decode($roomType->amenities, true) : [];
        @endphp
        @foreach ($commonAmenities as $amenity)
            <div class="col-md-3 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}"
                        id="edit_amenity_{{ $loop->index }}"
                        {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}>
                    <label class="form-check-label" for="edit_amenity_{{ $loop->index }}">
                        {{ $amenity }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1"
        {{ $roomType->is_active ? 'checked' : '' }}>
    <label class="form-check-label" for="edit_is_active">Active</label>
</div>
