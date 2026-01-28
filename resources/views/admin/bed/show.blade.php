@extends('admin.layouts.master')

@section('title', 'Bed Details')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Bed Details</h4>
                <div>
                    <a href="{{ route('admin.beds.index') }}" class="btn btn-secondary">Back</a>
                    <a href="#" class="btn btn-primary" onclick="document.getElementById('edit-link').click();">Edit</a>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Bed Code</th>
                        <td>{{ $bed->bed_number }}</td>
                    </tr>
                    <tr>
                        <th>Room</th>
                        <td>{{ $bed->room->room_number }} ({{ $bed->room->ward_name }})</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst($bed->status) }}</td>
                    </tr>
                    <tr>
                        <th>Last Occupancy</th>
                        <td>{{ $bed->last_occupancy_date ? $bed->last_occupancy_date->format('d-M-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Next Availability</th>
                        <td>{{ $bed->next_availability_date ? $bed->next_availability_date->format('d-M-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $bed->notes ?? '-' }}</td>
                    </tr>
                </table>

                <!-- Hidden link to trigger modal edit from details page if needed -->
                <a href="#bedModal" id="edit-link" class="d-none" data-bs-toggle="modal" data-bs-target="#bedModal"
                    data-id="{{ $bed->id }}" data-room="{{ $bed->room_id }}" data-bed_number="{{ $bed->bed_number }}"
                    data-status="{{ $bed->status }}"
                    data-last="{{ $bed->last_occupancy_date ? $bed->last_occupancy_date->format('Y-m-d') : '' }}"
                    data-next="{{ $bed->next_availability_date ? $bed->next_availability_date->format('Y-m-d') : '' }}"
                    data-notes="{{ $bed->notes }}"></a>
            </div>
        </div>
    </div>
@endsection
