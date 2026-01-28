@extends('admin.layouts.master')

@section('title', 'Edit Queue Entry')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header" style="background-color: rgb(229, 229, 229)">
                        <h5 class="mb-0">Edit Queue Entry - {{ $queue->queue_number }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.queue.update', $queue) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status *</label>
                                        <select name="status" id="status12" class="form-control" required>
                                            <option value="waiting" {{ $queue->status == 'waiting' ? 'selected' : '' }}>
                                                Waiting</option>
                                            <option value="in_progress"
                                                {{ $queue->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $queue->status == 'completed' ? 'selected' : '' }}>
                                                Completed</option>
                                            <option value="cancelled" {{ $queue->status == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled</option>
                                            <option value="no_show" {{ $queue->status == 'no_show' ? 'selected' : '' }}>No
                                                Show</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="current_room">Current Room</label>
                                        <input type="text" name="current_room" id="current_room" class="form-control"
                                            value="{{ $queue->current_room }}" placeholder="Room number">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="priority_score">Priority Score</label>
                                        <input type="number" name="priority_score" id="priority_score" class="form-control"
                                            value="{{ $queue->priority_score }}" min="0" max="100">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vital_signs">Vital Signs (JSON format)</label>
                                        <textarea name="vital_signs" id="vital_signs" class="form-control" rows="3"
                                            placeholder='{"bp": "120/80", "temperature": "98.6", "pulse": "72" }'>{{ json_encode($queue->vital_signs, JSON_PRETTY_PRINT) }}</textarea>
                                        <small class="form-text text-muted">Enter vital signs in JSON format</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ $queue->notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Queue
                                </button>
                                <a href="{{ route('admin.queue.show', $queue) }}" class="btn btn-secondary">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

@endsection
