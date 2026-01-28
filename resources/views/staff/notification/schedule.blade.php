@extends('staff.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Schedule Message - {{ $notification->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.notifications.schedule.store', $notification) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="recipients">Recipients *</label>
                                <textarea class="form-control" id="recipients" name="recipients" rows="3" required
                                    placeholder="Enter email addresses or phone numbers separated by commas">{{ old('recipients') }}</textarea>
                                <small class="form-text text-muted">
                                    For SMS: Enter phone numbers (e.g., +1234567890, +0987654321)<br>
                                    For Email: Enter email addresses (e.g., user1@example.com, user2@example.com)<br>
                                    For Push: Enter user IDs or device tokens
                                </small>
                            </div>

                            <div class="form-group mt-3">
                                <label for="scheduled_at">Schedule Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at"
                                    value="{{ old('scheduled_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>

                            @if (!empty($notification->variables))
                                <div class="form-group mt-3">
                                    <label>Template Variables</label>
                                    @foreach ($notification->variables as $variable)
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $variable }}</span>
                                            </div>
                                            <input type="text" class="form-control" name="variables[{{ $variable }}]"
                                                placeholder="Value for {{ $variable }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">Schedule Message</button>
                                <a href="{{ route('staff.notifications.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>

                        <!-- Template Preview -->
                        <div class="mt-4">
                            <h6>Template Preview</h6>
                            <div class="card">
                                <div class="card-body">
                                    @if ($notification->type == 'email')
                                        <strong>Subject:</strong> {{ $notification->subject }}<br><br>
                                    @endif
                                    <strong>Content:</strong><br>
                                    <div class="border p-3 bg-light">
                                        {!! nl2br(e($notification->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
