@extends('admin.layouts.master')

@section('title', 'Edit Department')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-3">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Department</h1>
            <div>

                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Departments
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Department Information</h6>
                        <span>
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.departments.update', $department) }}" method="POST"
                            id="departmentForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Department Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $department->name) }}"
                                            placeholder="Enter department name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Department Code *</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code" value="{{ old('code', $department->code) }}"
                                            placeholder="e.g., HR, IT, FIN" required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Unique code for the department</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Enter department description">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_id">Parent Department</label>
                                        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                            name="parent_id">
                                            <option value="">-- No Parent --</option>
                                            @foreach ($departments as $dept)
                                                @if ($dept->id != $department->id)
                                                    <option value="{{ $dept->id }}"
                                                        {{ old('parent_id', $department->parent_id) == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->name }} ({{ $dept->code }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="head_id">Department Head</label>
                                        <select class="form-control @error('head_id') is-invalid @enderror" id="head_id"
                                            name="head_id">
                                            <option value="">-- Select Head --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('head_id', $department->head_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} - {{ $user->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('head_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $department->email) }}"
                                            placeholder="department@company.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $department->phone) }}"
                                            placeholder="+1 234 567 8900">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" class="form-control @error('display_order') is-invalid @enderror"
                                    id="display_order" name="display_order"
                                    value="{{ old('display_order', $department->display_order) }}" min="0">
                                @error('display_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Lower numbers appear first</small>
                            </div>

                            <!-- Service Mapping Section -->
                            <div class="form-group">
                                <label>Services Mapping</label>
                                <div class="services-container border rounded p-3">
                                    <div class="form-row mb-2">
                                        <div class="col-6">
                                            <strong>Service Name</strong>
                                        </div>
                                        <div class="col-5">
                                            <strong>Service Code</strong>
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-success btn-sm" id="addService">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="servicesList">
                                        @php
                                            $services = old('services', $department->services ?? []);
                                            $serviceNames = $services['name'] ?? [];
                                            $serviceCodes = $services['code'] ?? [];
                                        @endphp

                                        @if (count($serviceNames) > 0)
                                            @foreach ($serviceNames as $index => $serviceName)
                                                <div class="form-row mb-2 service-row">
                                                    <div class="col-6">
                                                        <input type="text" class="form-control service-name"
                                                            placeholder="Service name" name="services[name][]"
                                                            value="{{ $serviceName }}">
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="text" class="form-control service-code"
                                                            placeholder="SVC_CODE" name="services[code][]"
                                                            value="{{ $serviceCodes[$index] ?? '' }}">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-service">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <template id="serviceTemplate">
                                        <div class="form-row mb-2 service-row">
                                            <div class="col-6">
                                                <input type="text" class="form-control service-name"
                                                    placeholder="Service name" name="services[name][]">
                                            </div>
                                            <div class="col-5">
                                                <input type="text" class="form-control service-code"
                                                    placeholder="SVC_CODE" name="services[code][]">
                                            </div>
                                            <div class="col-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-service">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <small class="form-text text-muted">Add services offered by this department</small>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Department</label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="button" class="btn btn-warning" onclick="resetForm()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Department
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Department Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Department Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Created:</strong><br>
                            {{ $department->created_at ? $department->created_at->format('M d, Y h:i A') : 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong><br>
                            {{ $department->updated_at ? $department->updated_at->format('M d, Y h:i A') : 'N/A' }}
                        </div>
                        @if ($department->deleted_at)
                            <div class="mb-3">
                                <strong>Deleted:</strong><br>
                                {{ $department->deleted_at ? $department->deleted_at->format('M d, Y h:i A') : 'N/A' }}
                                </span>
                            </div>
                        @endif

                        <hr>

                        <h6 class="font-weight-bold">Hierarchy Info:</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-1">
                                <strong>Parent:</strong>
                                {{ $department->parent ? $department->parent->name : 'None' }}
                            </li>
                            <li class="mb-1">
                                <strong>Sub-departments:</strong>
                                <span class="badge badge-info">{{ $department->children->count() }}</span>
                            </li>
                            <li class="mb-1">
                                <strong>Team Members:</strong>
                                <span class="badge badge-primary">{{ $department->users->count() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card shadow mb-4 border-left-danger">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Danger Zone
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($department->children->count() > 0)
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-circle"></i>
                                This department has {{ $department->children->count() }} sub-department(s).
                                You cannot delete it until all sub-departments are removed or reassigned.
                            </div>
                        @endif

                        @if ($department->users->count() > 0)
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-circle"></i>
                                This department has {{ $department->users->count() }} user(s).
                                You cannot delete it until all users are reassigned.
                            </div>
                        @endif

                        <form action="{{ route('admin.departments.destroy', $department) }}" method="POST"
                            onsubmit="return confirmDelete()" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-block"
                                {{ $department->children->count() > 0 || $department->users->count() > 0 ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i> Delete Department
                            </button>
                        </form>

                        @if ($department->children->count() > 0 || $department->users->count() > 0)
                            <small class="text-muted mt-2 d-block">
                                Remove all sub-departments and users before deletion.
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.departments.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-plus"></i> Create New Department
                            </a>
                            <a href="{{ route('admin.departments.show', $department) }}"
                                class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Service Management
            const servicesList = document.getElementById('servicesList');
            const serviceTemplate = document.getElementById('serviceTemplate');
            const addServiceBtn = document.getElementById('addService');

            // Add service row
            addServiceBtn.addEventListener('click', function() {
                const newService = serviceTemplate.content.cloneNode(true);
                servicesList.appendChild(newService);
                attachRemoveHandlers();
            });

            // Remove service row
            function attachRemoveHandlers() {
                document.querySelectorAll('.remove-service').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.service-row').remove();
                    });
                });
            }

            // Form reset function
            window.resetForm = function() {
                if (confirm('Are you sure you want to reset all changes?')) {
                    document.getElementById('departmentForm').reset();

                    // Reset services to original state
                    const originalServices = @json($department->services ?? []);
                    servicesList.innerHTML = '';

                    if (originalServices && originalServices.name) {
                        originalServices.name.forEach((name, index) => {
                            const newService = serviceTemplate.content.cloneNode(true);
                            const row = newService.querySelector('.service-row');
                            row.querySelector('.service-name').value = name;
                            row.querySelector('.service-code').value = originalServices.code[index] ||
                                '';
                            servicesList.appendChild(newService);
                        });
                    }

                    attachRemoveHandlers();
                }
            };

            // Form validation
            document.getElementById('departmentForm').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const code = document.getElementById('code').value.trim();

                if (!name || !code) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Validate service codes if any
                const serviceCodes = [];
                let hasDuplicate = false;

                document.querySelectorAll('.service-code').forEach(input => {
                    const code = input.value.trim();
                    if (code) {
                        if (serviceCodes.includes(code)) {
                            hasDuplicate = true;
                            input.classList.add('is-invalid');
                        } else {
                            serviceCodes.push(code);
                            input.classList.remove('is-invalid');
                        }
                    }
                });

                if (hasDuplicate) {
                    e.preventDefault();
                    alert('Duplicate service codes found. Please use unique codes for each service.');
                    return false;
                }
            });

            // Delete confirmation
            window.confirmDelete = function() {
                return confirm(
                    'Are you sure you want to delete this department? This action cannot be undone.');
            };

            // Initialize remove handlers
            attachRemoveHandlers();

            // Add empty service row if no services exist
            if (servicesList.children.length === 0) {
                addServiceBtn.click();
            }
        });
    </script>
@endpush
