@extends('staff.layouts.master')

@section('title', $department->name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-3">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $department->name }}</h1>
            <div>

                <a href="{{ route('staff.departments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Departments
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Department Details -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                        <span>
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="40%">Department Code:</th>
                                        <td><code>{{ $department->code }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Parent Department:</th>
                                        <td>
                                            @if ($department->parent)
                                                <a href="{{ route('staff.departments.show', $department->parent) }}">
                                                    {{ $department->parent->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Department Head:</th>
                                        <td>
                                            @if ($department->head)
                                                {{ $department->head->name }}
                                                <br><small class="text-muted">{{ $department->head->email }}</small>
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="40%">Email:</th>
                                        <td>
                                            @if ($department->email)
                                                <a href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>
                                            @if ($department->phone)
                                                <a href="tel:{{ $department->phone }}">{{ $department->phone }}</a>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Display Order:</th>
                                        <td>{{ $department->display_order }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if ($department->description)
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p class="text-muted mt-1">{{ $department->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Services Section -->
                @if ($department->services && count($department->services) > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Mapped Services</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($department->services as $service)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-primary h-100">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">{{ $service['name'] ?? 'Unnamed Service' }}
                                                </h6>
                                                @if (isset($service['code']))
                                                    <code class="small">{{ $service['code'] }}</code>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Statistics Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="border-right">
                                        <div class="text-primary font-weight-bold h5">{{ $department->children->count() }}
                                        </div>
                                        <div class="text-muted small">Sub-departments</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-success font-weight-bold h5">{{ $department->users->count() }}</div>
                                    <div class="text-muted small">Team Members</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-departments -->
                @if ($department->children->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sub-departments</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach ($department->children as $child)
                                    <a href="{{ route('staff.departments.show', $child) }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        {{ $child->name }}
                                        <span
                                            class="badge badge-{{ $child->is_active ? 'success' : 'danger' }} badge-pill">
                                            {{ $child->is_active ? 'A' : 'I' }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="mb-2">
                                <strong>Created:</strong><br>
                                @if ($department->created_at)
                                    {{ $department->created_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>Last Updated:</strong><br>
                                @if ($department->updated_at)
                                    {{ $department->updated_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                            @if ($department->deleted_at)
                                <div class="mb-2 text-danger">
                                    <strong>Deleted:</strong><br>

                                    @if ($department->deleted_at)
                                        {{ $department->deleted_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            @endif
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
