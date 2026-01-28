@extends('staff.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create Notification Template</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.notifications.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Template Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Type *</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email
                                            </option>
                                            <option value="push" {{ old('type') == 'push' ? 'selected' : '' }}>Push
                                                Notification</option>
                                        </select>
                                        @error('type')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3" id="subject-field" style="display: none;">
                                <label for="subject">Email Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    value="{{ old('subject') }}">
                                @error('subject')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <label for="content">Content *</label>
                                <textarea class="form-control" id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="form-text text-muted">
                                    Use variables like {name}, {email} etc. that can be replaced during sending.
                                </small>
                            </div>

                            <div class="form-group mt-3">
                                <label for="variables">Available Variables (comma separated)</label>
                                <input type="text" class="form-control" id="variables" name="variables"
                                    value="{{ old('variables') }}" placeholder="name, email, order_id, etc.">
                                @error('variables')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group form-check mt-4 mb-4">
                                <input type="checkbox" class="form-check-input" id="status1" name="status" value="1"
                                    {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active Template</label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Template</button>
                                <a href="{{ route('staff.notifications.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            const subjectField = document.getElementById('subject-field');
            if (this.value === 'email') {
                subjectField.style.display = 'block';
                document.getElementById('subject').required = true;
            } else {
                subjectField.style.display = 'none';
                document.getElementById('subject').required = false;
            }
        });

        // Trigger change on page load
        document.getElementById('type').dispatchEvent(new Event('change'));
    </script>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
