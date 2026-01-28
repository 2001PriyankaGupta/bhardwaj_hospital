@extends('admin.layouts.master')

@section('title', 'Create Department')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="container-fluid mt-3">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create New Department</h1>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Departments
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.departments.store') }}" method="POST" id="departmentForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Department Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
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
                                            id="code" name="code" value="{{ old('code') }}"
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
                                    rows="3" placeholder="Enter department description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_id">Parent Department</label>
                                        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                            name="parent_id">
                                            <option value="">-- No Parent --</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}"
                                                    {{ old('parent_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }} ({{ $dept->code }})
                                                </option>
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
                                                    {{ old('head_id') == $user->id ? 'selected' : '' }}>
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

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
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
                                            id="phone" name="phone" value="{{ old('phone') }}"
                                            placeholder="+1 234 567 8900">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="display_order">Display Order</label>
                                <input type="number" class="form-control @error('display_order') is-invalid @enderror"
                                    id="display_order" name="display_order" value="{{ old('display_order', 0) }}"
                                    min="0">
                                @error('display_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Lower numbers appear first</small>
                            </div>

                            <!-- Service Mapping Section -->
                            <div class="form-group mt-3">
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
                                        <!-- Services will be added here dynamically -->
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
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Department</label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Department
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Help Section -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Creation Guide
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="font-weight-bold">Department Setup Tips:</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                <strong>Code:</strong> Use short, unique codes (2-5 characters)
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                <strong>Hierarchy:</strong> Set parent for sub-departments
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                <strong>Services:</strong> Map key services offered
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                <strong>Order:</strong> Use display order for sorting
                            </li>
                        </ul>

                        <hr>

                        <h6 class="font-weight-bold">Required Fields:</h6>
                        <ul class="list-unstyled small">
                            <li><span class="text-danger">*</span> Department Name</li>
                            <li><span class="text-danger">*</span> Department Code</li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list"></i> View All Departments
                            </a>
                            <button type="button" class="btn btn-outline-info btn-sm" id="fillSampleData">
                                <i class="fas fa-magic"></i> Fill Sample Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
         let baseUrl = "{{ config('app.url') }}";
         
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

            // Fill sample data
            document.getElementById('fillSampleData').addEventListener('click', function() {
                document.getElementById('name').value = 'Information Technology';
                document.getElementById('code').value = 'IT';
                document.getElementById('description').value =
                    'Handles all IT infrastructure, support, and development services.';
                document.getElementById('email').value = 'it@company.com';
                document.getElementById('phone').value = '+1 555 123 4567';
                document.getElementById('display_order').value = '2';

                // Add sample services
                const sampleServices = [{
                        name: 'Technical Support',
                        code: 'TS'
                    },
                    {
                        name: 'Network Administration',
                        code: 'NET'
                    },
                    {
                        name: 'Software Development',
                        code: 'DEV'
                    }
                ];

                sampleServices.forEach(service => {
                    addServiceBtn.click();
                    const rows = document.querySelectorAll('.service-row');
                    const lastRow = rows[rows.length - 1];
                    lastRow.querySelector('.service-name').value = service.name;
                    lastRow.querySelector('.service-code').value = service.code;
                });
            });

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

            // Auto-generate code from name
            document.getElementById('name').addEventListener('blur', function() {
                const nameInput = this;
                const codeInput = document.getElementById('code');

                if (!codeInput.value) {
                    const name = nameInput.value.trim();
                    if (name) {
                        // Generate code from name (first letters of words)
                        const code = name.split(' ')
                            .map(word => word.charAt(0).toUpperCase())
                            .join('')
                            .substring(0, 5);
                        codeInput.value = code;
                    }
                }
            });

            // Initialize with one empty service row
            addServiceBtn.click();
        });
    </script>
@endpush
