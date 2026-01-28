@extends('admin.layouts.master')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ URL::asset('build/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

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
</style>

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Roles Management</h5>
                        <button type="button" class="btn btn-primary float-end" onclick="resetForm()"
                            data-bs-toggle="modal" data-bs-target="#roleModal">
                            Add New Role
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="roleTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->slug }}</td>
                                            <td>{{ $role->description }}</td>
                                            <td>
                                                @foreach ($role->permissions as $permission)
                                                    <span class="badge bg-info">{{ $permission->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{-- @if (!in_array($role->slug, ['admin'])) --}}
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="editRole('{{ $role->id }}', '{{ $role->name }}', '{{ $role->description }}')"
                                                    data-bs-toggle="modal" data-bs-target="#roleModal">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                                {{-- @else
                                                    <span class="badge bg-warning">System Role</span>
                                                @endif --}}
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

    <!-- Modal for Create/Edit Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Create/Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.roles.partials.role-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            $('#roleTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ]
            });
        });
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: "{{ session('success') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: "{{ session('error') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif
        });
    </script>
    <script>
        // Call initializeForm when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
        });
    </script>
    <script>
        function editRole(roleId, roleName, roleDescription) {
            // Build URL manually
            const baseUrl = window.location.origin;
            document.getElementById('roleForm').action = `${baseUrl}/admin/roles/${roleId}`;

            // Add method spoofing for PUT (use existing hidden input)
            const methodInput = document.getElementById('roleFormMethod');
            if (methodInput) {
                methodInput.value = 'PUT';
            }

            // Fill form fields
            document.getElementById('name').value = roleName;
            document.getElementById('description').value = roleDescription;

            // Fetch permissions
            fetch(`${baseUrl}/admin/roles/${roleId}/permissions`)
                .then(response => response.json())
                .then(data => {
                    // Uncheck all permissions first
                    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check the permissions for this role
                    data.permissions.forEach(permissionId => {
                        const checkbox = document.getElementById('perm_' + permissionId);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                })
                .catch(error => console.error('Error fetching permissions:', error));
        }

        function resetForm() {
            // Reset form for creating new role
            document.getElementById('roleForm').action = "{{ route('admin.roles.store') }}";

            // Reset method spoofing (reset to POST)
            const methodInput = document.getElementById('roleFormMethod');
            if (methodInput) {
                methodInput.value = '';
            }

            // Clear form fields
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';

            // Uncheck all permissions
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
@endsection
