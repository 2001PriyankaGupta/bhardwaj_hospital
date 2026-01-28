@extends('admin.layouts.master')

@section('title', 'Staff Management')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<style>
    .swal2-toast {
        font-size: 12px !important;
        padding: 6px 10px !important;
        min-width: auto !important;
        width: 220px !important;
        line-height: 1.3em !important;
    }

    .swal2-toast .swal2-icon {
        width: 24px !important;
        height: 24px !important;
        margin-right: 6px !important;
    }

    .swal2-toast .swal2-title {
        font-size: 13px !important;
    }

    .password-field {
        display: block;
    }
</style>

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Hospital Staff Management</h4>
                        <button type="button" class="btn btn-primary" id="addStaffBtn" data-bs-toggle="modal"
                            data-bs-target="#staffModal">
                            <i class="fas fa-plus"></i> Add Staff
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="staffTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count = 1; ?>
                                    @foreach ($staff as $member)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->email }}</td>
                                            <td>{{ $member->phone ?? 'N/A' }}</td>
                                            <td>{{ $member->position }}</td>
                                            <td>{{ $member->departmentRelation->name ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $member->status == 'active' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($member->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-btn"
                                                    data-id="{{ $member->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $member->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <a class="btn btn-sm btn-info shift-btn"
                                                    href="{{ route('admin.shifts.index', $member->id) }}">
                                                    <i class="fas fa-calendar-alt"></i> Shifts
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Staff Modal -->
    <div class="modal fade" id="staffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="staffForm">
                    @csrf
                    <input type="hidden" id="staff_id" name="staff_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="col-md-6 mb-3 password-field">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="form-text">Required only for new staff</div>
                            </div>
                            <div class="col-md-6 mb-3 password-field">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position *</label>
                                <select class="form-control" id="position" name="position" required>
                                    <option value="">Select Position</option>
                                    <optgroup label="Medical Staff">
                                        <option value="Chief Medical Officer">Chief Medical Officer</option>
                                        <option value="Senior Consultant">Senior Consultant</option>
                                        <option value="Consultant">Consultant</option>
                                        <option value="Resident Doctor">Resident Doctor</option>
                                        <option value="Medical Officer">Medical Officer</option>
                                        <option value="Registrar">Registrar</option>
                                        <option value="House Physician">House Physician</option>
                                        <option value="House Surgeon">House Surgeon</option>
                                    </optgroup>
                                    <optgroup label="Nursing Staff">
                                        <option value="Chief Nursing Officer">Chief Nursing Officer</option>
                                        <option value="Nursing Superintendent">Nursing Superintendent</option>
                                        <option value="Senior Nurse">Senior Nurse</option>
                                        <option value="Staff Nurse">Staff Nurse</option>
                                        <option value="Nursing Assistant">Nursing Assistant</option>
                                        <option value="Ward Nurse">Ward Nurse</option>
                                        <option value="ICU Nurse">ICU Nurse</option>
                                        <option value="OT Nurse">OT Nurse</option>
                                    </optgroup>
                                    <optgroup label="Paramedical Staff">
                                        <option value="Chief Pharmacist">Chief Pharmacist</option>
                                        <option value="Pharmacist">Pharmacist</option>
                                        <option value="Lab Technician">Lab Technician</option>
                                        <option value="Radiology Technician">Radiology Technician</option>
                                        <option value="ECG Technician">ECG Technician</option>
                                        <option value="Physiotherapist">Physiotherapist</option>
                                        <option value="Dietitian">Dietitian</option>
                                        <option value="Occupational Therapist">Occupational Therapist</option>
                                    </optgroup>
                                    <optgroup label="Administrative Staff">
                                        <option value="Hospital Administrator">Hospital Administrator</option>
                                        <option value="HR Manager">HR Manager</option>
                                        <option value="Front Desk Executive">Front Desk Executive</option>
                                        <option value="Medical Records Officer">Medical Records Officer</option>
                                        <option value="Billing Executive">Billing Executive</option>
                                        <option value="Receptionist">Receptionist</option>
                                    </optgroup>
                                    <optgroup label="Support Staff">
                                        <option value="Ward Boy">Ward Boy</option>
                                        <option value="Ayah">Ayah</option>
                                        <option value="Sanitary Worker">Sanitary Worker</option>
                                        <option value="Security Guard">Security Guard</option>
                                        <option value="Driver">Driver</option>
                                        <option value="Other">Other</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-control" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joining_date" class="form-label">Joining Date *</label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="staff_status" class="form-label">Status *</label>
                                <select class="form-control" id="staff_status" name="staff_status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" min="1"
                                    max="120">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_number" class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" id="emergency_contact_number"
                                    name="emergency_contact_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alternate_contact_number" class="form-label">Alternate Contact</label>
                                <input type="text" class="form-control" id="alternate_contact_number"
                                    name="alternate_contact_number">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#staffTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search staff...",
                },
                columnDefs: [{
                    orderable: false,
                    targets: [7]
                }]
            });

            // Handle Add button click
            $('#addStaffBtn').on('click', function() {
                $('#modalTitle').text('Add Staff');
                $('#staffForm')[0].reset();
                $('#staff_id').val('');

                // Show password fields for add
                $('.password-field').show();
                $('#password').attr('required', true);
                $('#password_confirmation').attr('required', true);

                // Clear validation
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Reset form when modal is closed
            $('#staffModal').on('hidden.bs.modal', function() {
                $('#staffForm')[0].reset();
                $('#staff_id').val('');
                $('.password-field').show();
                $('#password').attr('required', true);
                $('#password_confirmation').attr('required', true);
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Edit Staff - Fixed version
            $(document).on('click', '.edit-btn', function() {
                const staffId = $(this).attr('data-id');
                console.log('Edit button clicked, staff ID:', staffId);

                if (!staffId) {
                    alert('Staff ID not found!');
                    return;
                }

                // Show modal with loading state
                $('#staffModal').modal('show');
                $('#modalTitle').html('Loading... <i class="fas fa-spinner fa-spin"></i>');

                // Hide password fields for edit
                $('.password-field').hide();
                $('#password').removeAttr('required');
                $('#password_confirmation').removeAttr('required');

                // Fetch staff data
                $.ajax({
                    url: `${baseUrl}/admin/staff/${staffId}/edit`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Staff data received:', response);

                        if (response.error) {
                            $('#staffModal').modal('hide');
                            Swal.fire({
                                toast: true,
                                icon: 'error',
                                title: response.error,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            return;
                        }

                        // Populate form
                        $('#staff_id').val(response.id);
                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#phone').val(response.phone || '');
                        $('#position').val(response.position);
                        $('#department_id').val(response.department_id);

                        if (response.joining_date) {
                            $('#joining_date').val(response.joining_date.substring(0, 10));
                        }

                        $('#staff_status').val(response.status);
                        $('#address').val(response.address || '');
                        $('#gender').val(response.gender || '');
                        $('#age').val(response.age || '');
                        $('#emergency_contact_number').val(response.emergency_contact_number ||
                            '');
                        $('#alternate_contact_number').val(response.alternate_contact_number ||
                            '');

                        $('#modalTitle').text('Edit Staff');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        $('#staffModal').modal('hide');
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: 'Error loading staff data',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            // Form Submission
            $('#staffForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const staffId = $('#staff_id').val();

                let url = `${baseUrl}/admin/staff`;
                let method = 'POST';

                if (staffId) {
                    url = `${baseUrl}/admin/staff/${staffId}`;
                    formData.append('_method', 'PUT');
                }

                // Show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#staffModal').modal('hide');

                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: response.success || 'Operation successful!',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);

                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors)[0][0];
                        }

                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: errorMessage,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    complete: function() {
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Delete Staff
            $(document).on('click', '.delete-btn', function() {
                const staffId = $(this).attr('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseUrl}/admin/staff/${staffId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    title: response.success ||
                                        'Deleted successfully!',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'error',
                                    title: 'Error deleting staff',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
