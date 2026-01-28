@extends('admin.layouts.master')

@section('title', 'Communication Logs - ' . $patient->first_name . ' ' . $patient->last_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="row mt-4">
        <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold"> Communication Logs: {{ $patient->first_name }}
                        {{ $patient->last_name }}</h1>

                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary btn-sm float-right"
                    style="    margin-right: 38px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h3 class="card-title text-orange fw-bold">
                        Communication Logs: {{ $patient->first_name }} {{ $patient->last_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Patient
                        </a>
                    </div>
                </div> --}}
                <div class="card-body">
                    <!-- Add Communication Log Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Add New Communication Log</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.patients.store-communication-log', $patient) }}"
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="communication_type">Communication Type *</label>
                                                    <select class="form-control" id="communication_type"
                                                        name="communication_type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="email">Email</option>
                                                        <option value="sms">SMS</option>
                                                        <option value="call">Phone Call</option>
                                                        <option value="in_person">In Person</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label for="subject">Subject *</label>
                                                    <input type="text" class="form-control" id="subject" name="subject"
                                                        required placeholder="Enter communication subject">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="message">Message *</label>
                                                    <textarea class="form-control" id="message" name="message" rows="4" required
                                                        placeholder="Enter communication details..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Add Communication
                                                    Log</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Communication Logs Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="communicationLogsTable">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Type</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($communications as $communication)
                                            <tr>
                                                <td>{{ $communication->created_at->format('d M Y H:i') }}</td>
                                                <td>
                                                    @php
                                                        $typeColors = [
                                                            'email' => 'primary',
                                                            'sms' => 'info',
                                                            'call' => 'success',
                                                            'in_person' => 'warning',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $typeColors[$communication->communication_type] ?? 'secondary' }}">
                                                        {{ ucfirst($communication->communication_type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $communication->subject }}</td>
                                                <td>{{ Str::limit($communication->message, 80) }}</td>
                                                <td>{{ $communication->createdBy->name ?? 'System' }}</td>
                                                <td>
                                                    <button class="btn btn-info btn-sm view-communication"
                                                        data-communication="{{ $communication }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $communications->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Communication Modal -->
    <div class="modal fade" id="viewCommunicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Communication Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="communicationDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            // Initialize DataTable
            $('#communicationLogsTable').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 10
            });

            // View communication details
            $('.view-communication').click(function() {
                var communication = $(this).data('communication');
                var typeColors = {
                    'email': 'primary',
                    'sms': 'info',
                    'call': 'success',
                    'in_person': 'warning'
                };

                var html = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Communication Type:</strong>
                    <span class="badge badge-${typeColors[communication.communication_type]} ml-2">
                        ${communication.communication_type.charAt(0).toUpperCase() + communication.communication_type.slice(1)}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Date & Time:</strong> ${new Date(communication.created_at).toLocaleString('en-GB')}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <strong>Subject:</strong>
                    <p class="mt-1">${communication.subject}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <strong>Message:</strong>
                    <p class="mt-1 bg-light p-3 rounded">${communication.message}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Created By:</strong> ${communication.created_by_name || 'System'}
                </div>
                <div class="col-md-6">
                    <strong>Created At:</strong> ${new Date(communication.created_at).toLocaleString('en-GB')}
                </div>
            </div>
        `;
                $('#communicationDetails').html(html);
                $('#viewCommunicationModal').modal('show');
            });
        });
    </script>
@endsection
