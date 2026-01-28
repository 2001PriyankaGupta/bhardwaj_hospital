@extends('admin.layouts.master')

@section('title', 'Department Management')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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

    /* Custom badge styles */
    .badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }

    /* If using Bootstrap 5 with dark mode */
    .badge.bg-secondary {
        color: white !important;
    }

    .badge.bg-light {
        color: #000 !important;
        background-color: #f8f9fa !important;
    }

    /* Margin utilities */
    .me-1 {
        margin-right: 0.25rem;
    }

    .mr-1 {
        margin-right: 0.25rem;
    }

    .department-tree {
        list-style: none;
        padding-left: 0;
    }

    .department-tree ul {
        list-style: none;
        padding-left: 20px;
        margin-top: 5px;
    }

    .department-tree li {
        margin: 5px 0;
        padding: 8px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .tree-node {
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .tree-handle {
        cursor: move;
        margin-right: 10px;
        color: #6c757d;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Department Management</h1>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Department
            </a>
        </div>
        <div class="row d-flex">
            <!-- Department Hierarchy -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Department Hierarchy</h6>
                    </div>
                    <div class="card-body">
                        <div id="departmentTree">
                            @include('admin.department.partials.tree', ['departments' => $tree])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <div class="text-primary font-weight-bold h5">{{ $departments->count() }}</div>
                                    <div class="text-muted small">Total Departments</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-success font-weight-bold h5">
                                    {{ $departments->where('is_active', true)->count() }}</div>
                                <div class="text-muted small">Active Departments</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Department List -->
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">All Departments</h6>
                        {{-- <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#hierarchyModal">
                                <i class="fas fa-sitemap"></i> Manage Hierarchy
                            </button>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="departmentsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Parent Department</th>
                                        <th>Head</th>
                                        <th>Services</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $department)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $department->name }}</strong>
                                                @if ($department->children->count() > 0)
                                                    <span
                                                        class="badge badge-info ml-1 text-bg-pink">{{ $department->children->count() }}
                                                        sub</span>
                                                @endif
                                            </td>
                                            <td><code>{{ $department->code }}</code></td>
                                            <td>{{ $department->parent->name ?? '-' }}</td>
                                            <td>{{ $department->head->name ?? '-' }}</td>
                                            <td>
                                                @if ($department->services && is_array($department->services))
                                                    @foreach (array_slice($department->services, 0, 2) as $service)
                                                        @if (is_string($service))
                                                            <span class="badge bg-secondary me-1">{{ $service }}</span>
                                                        @endif
                                                    @endforeach
                                                    @if (count($department->services) > 2)
                                                        <span
                                                            class="badge bg-light text-dark">+{{ count($department->services) - 2 }}
                                                            more</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No services</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">
                                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.departments.show', $department) }}"
                                                        style="height: 31px;" class="btn btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                                        style="height: 31px;" class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.departments.destroy', $department) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this department?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

    <!-- Hierarchy Modal -->
    <div class="modal fade" id="hierarchyModal" tabindex="-1" aria-labelledby="hierarchyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hierarchyModalLabel">Manage Department Hierarchy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Drag and drop departments to reorganize the hierarchy.
                        Parent-child relationships will be updated automatically.
                    </div>

                    <!-- Search and Filter Options -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchHierarchy"
                                placeholder="Search departments...">
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="filterStatus">
                                <option value="">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                            </select>
                        </div>
                    </div>

                    <!-- Interactive Tree with Drag & Drop -->
                    <div id="hierarchyTree" class="border p-3 bg-light"
                        style="min-height: 400px; max-height: 500px; overflow-y: auto;">
                        <!-- Dynamic tree will be loaded via AJAX -->
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="expandAll">
                            <i class="fas fa-expand"></i> Expand All
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="collapseAll">
                            <i class="fas fa-compress"></i> Collapse All
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" id="resetHierarchy">
                            <i class="fas fa-redo"></i> Reset to Default
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveHierarchy">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Required JavaScript Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Sortable.js for drag and drop functionality -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#departmentsTable').DataTable({
                pageLength: 25,
                order: [
                    [0, 'asc']
                ]
            });

            // Show success/error messages
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

            // Load hierarchy tree when modal is shown
            const hierarchyModal = document.getElementById('hierarchyModal');
            if (hierarchyModal) {
                hierarchyModal.addEventListener('show.bs.modal', function() {
                    loadHierarchyTree();
                });
            }

            // Save hierarchy
            $('#saveHierarchy').click(function() {
                saveHierarchy();
            });

            function loadHierarchyTree() {
                $.get('{{ route('admin.departments.hierarchy-tree') }}', function(data) {
                    $('#hierarchyTree').html(data);
                    initializeSortable();
                }).fail(function() {
                    $('#hierarchyTree').html(
                        '<p class="text-center text-danger">Failed to load hierarchy</p>');
                });
            }

            function initializeSortable() {
                const hierarchyTree = document.getElementById('hierarchyTree');
                if (hierarchyTree) {
                    new Sortable(hierarchyTree, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        handle: '.tree-handle'
                    });
                }
            }

            function saveHierarchy() {
                const hierarchy = getNestedList($('#hierarchyTree ol'));

                $.post('{{ route('admin.departments.update-hierarchy') }}', {
                    hierarchy: hierarchy,
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Hierarchy updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Close modal and reload page
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'hierarchyModal'));
                            modal.hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update hierarchy'
                        });
                    }
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update hierarchy. Please try again.'
                    });
                });
            }

            function getNestedList(element) {
                let result = [];
                element.children('li').each(function() {
                    let item = {
                        id: $(this).data('id'),
                        children: getNestedList($(this).children('ol'))
                    };
                    result.push(item);
                });
                return result;
            }
        });
    </script>
@endsection
