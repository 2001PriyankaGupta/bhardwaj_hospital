@extends('admin.layouts.master')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ URL::asset('build/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
    /* Toast styling */
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

    /* Custom CSS Variables */
    :root {
        --primary-color: #fd653f;
        --primary-light: #ffeae5;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-color: #dee2e6;
        --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Dashboard Styles */
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .header-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, #fc7453 100%);
        padding: 19px;
        margin-bottom: 25px;
        color: white;
        box-shadow: var(--shadow);
        border-radius: 10px;
    }

    .header-section h1 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .header-section p {
        opacity: 0.9;
        max-width: 700px;
    }

    .nav-tabs-container {
        background-color: white;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
    }

    .nav-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        border: none;
    }

    .nav-tab {
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--secondary-color);
        font-weight: 500;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .nav-tab:hover {
        background-color: #f3f4f6;
        color: var(--dark-color);
    }

    .nav-tab.active {
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .nav-tab i {
        margin-right: 8px;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid var(--border-color);
        padding: 20px 25px;
        border-radius: 10px 10px 0 0 !important;
    }

    .card-header h2 {
        margin: 0;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }

    .card-header h2 i {
        margin-right: 10px;
        color: var(--primary-color);
    }

    .card-body {
        padding: 25px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #e55a37;
        border-color: #e55a37;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 80px;
    }

    .status-active {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .status-inactive {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .action-btn {
        transition: all 0.2s ease-in-out;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-right: 8px;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-edit {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    .btn-delete {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid var(--border-color);
        padding: 12px 15px;
        font-weight: 600;
        color: var(--dark-color);
    }

    .table tbody td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Feature item styling */
    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .feature-item input {
        flex: 1;
        margin-right: 8px;
    }

    .feature-item button {
        flex-shrink: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-tabs {
            flex-direction: column;
        }

        .nav-tab {
            width: 100%;
            justify-content: center;
        }

        .card-header {
            padding: 15px;
        }

        .card-body {
            padding: 15px;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>


@section('content')
    <div class="">
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h1 class="text-white">Pricing Management</h1>
                    <p>Efficiently manage your service catalog, configure rate cards, create bundled packages, and set
                        up discount strategies in one centralized platform.</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs-container">
            <div class="nav-tabs">
                <a href="#" class="nav-tab active" data-tab="services">
                    <i class="fas fa-cube"></i>
                    Service Catalog
                </a>
                <a href="#" class="nav-tab" data-tab="rate-cards">
                    <i class="fas fa-tags"></i>
                    Rate Cards
                </a>
                <a href="#" class="nav-tab" data-tab="packages">
                    <i class="fas fa-box"></i>
                    Packages
                </a>
                <a href="#" class="nav-tab" data-tab="discounts">
                    <i class="fas fa-percentage"></i>
                    Discounts
                </a>
            </div>
        </div>

        <!-- Services Tab Content -->
        <div id="services" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h2><i class="fas fa-list-alt"></i> Service Catalog</h2>
                            <p class="mb-0 mt-1 text-muted">Manage all your cloud services and their configurations</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal"
                                onclick="openServiceModal()">
                                <i class="fas fa-plus me-1"></i>Add New Service
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="servicesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                @foreach ($services as $service)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->category }}</td>
                                        <td>{{ $service->description }}</td>
                                        <td>
                                            <span
                                                class="status-badge {{ $service->status == 'active' ? 'status-active' : 'status-inactive' }}">

                                                {{ ucfirst($service->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="action-btn btn-edit"
                                                onclick="editService({{ $service->id }})">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <form action="{{ route('admin.services.destroy', $service->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete"
                                                    onclick="return confirm('Are you sure you want to delete this service?')">
                                                    <i class="fas fa-trash me-1"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rate Cards Tab Content -->
        <div id="rate-cards" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h2><i class="fas fa-tags"></i> Rate Cards</h2>
                            <p class="mb-0 mt-1 text-muted">Manage pricing for your services</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rateCardModal"
                                onclick="openRateCardModal()">
                                <i class="fas fa-plus me-1"></i>Add New Rate Card
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="rateCardsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Service</th>
                                    <th>Price</th>
                                    <th>Billing Cycle</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                @foreach ($rateCards as $rateCard)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $rateCard->name }}</td>
                                        <td>{{ $rateCard->service->name ?? '' }}</td>
                                        <td>{{ $rateCard->currency }} {{ number_format($rateCard->price, 2) }}</td>
                                        <td>{{ ucfirst($rateCard->billing_cycle) }}</td>
                                        <td>
                                            <span
                                                class="status-badge {{ $rateCard->is_active ? 'status-active' : 'status-inactive' }}">

                                                {{ $rateCard->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="action-btn btn-edit"
                                                onclick="editRateCard({{ $rateCard->id }})">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <button type="button" class="action-btn btn-delete"
                                                onclick="deleteRateCard({{ $rateCard->id }})">
                                                <i class="fas fa-trash me-1"></i>
                                            </button>


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Tab Content -->
        <div id="packages" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h2><i class="fas fa-box"></i> Packages</h2>
                            <p class="mb-0 mt-1 text-muted">Create and manage service bundles</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal"
                                onclick="openPackageModal()">
                                <i class="fas fa-plus me-1"></i>Create New Package
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="packagesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Included Services</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->currency }} {{ number_format($package->price, 2) }}</td>
                                        <td>
                                            @php
                                                $serviceIds = json_decode($package->included_services, true);
                                                $serviceNames = [];
                                                foreach ($services as $service) {
                                                    if (in_array($service->id, $serviceIds)) {
                                                        $serviceNames[] = $service->name;
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $serviceNames) }}
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge {{ $package->is_active ? 'status-active' : 'status-inactive' }}">

                                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="action-btn btn-edit"
                                                onclick="editPackage({{ $package->id }})">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <form action="{{ route('admin.packages.destroy', $package->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete"
                                                    onclick="return confirm('Are you sure you want to delete this package?')">
                                                    <i class="fas fa-trash me-1"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discounts Tab Content -->
        <div id="discounts" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h2><i class="fas fa-percentage"></i> Discounts</h2>
                            <p class="mb-0 mt-1 text-muted">Configure promotional offers and discounts</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#discountModal"
                                onclick="openDiscountModal()">
                                <i class="fas fa-plus me-1"></i>Add New Discount
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="discountsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Applicable To</th>
                                    <th>Valid From</th>
                                    <th>Valid Until</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($discounts as $discount)
                                    <tr data-discount-id="{{ $discount->id }}"
                                        data-description="{{ $discount->description }}"
                                        data-usage-limit="{{ $discount->usage_limit }}"
                                        data-status="{{ $discount->is_active }}"
                                        data-valid-from="{{ $discount->valid_from }}"
                                        data-valid-until="{{ $discount->valid_until }}">
                                        <td>{{ $discount->id }}</td>
                                        <td>{{ $discount->name }}</td>
                                        <td>{{ ucfirst($discount->type) }}</td>
                                        <td>
                                            @if ($discount->type == 'percentage')
                                                {{ $discount->value }}%
                                            @else
                                                {{ $discount->currency ?? 'USD' }}
                                                {{ number_format($discount->value, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($discount->applicable_to) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($discount->valid_from)->format('M d, Y') }}</td>
                                        <td>{{ $discount->valid_until ? \Carbon\Carbon::parse($discount->valid_until)->format('M d, Y') : 'No expiry' }}
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge {{ $discount->is_active ? 'status-active' : 'status-inactive' }}">

                                                {{ $discount->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="action-btn btn-edit"
                                                onclick="editDiscount({{ $discount->id }})">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <form action="{{ route('admin.discounts.destroy', $discount->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete"
                                                    onclick="return confirm('Are you sure you want to delete this discount?')">
                                                    <i class="fas fa-trash me-1"></i>
                                                </button>
                                            </form>
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

    <!-- Service Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.services.store') }}" id="serviceForm">
                    @csrf
                    <input type="hidden" id="serviceId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" name="name" required>
                            <div class="invalid-feedback">Please provide a service name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="serviceCategory" class="form-label">Category</label>
                            <select class="form-select" id="serviceCategory" name="category" required>
                                <option value="">Select Category</option>

                                <!-- Hospital Management Categories -->
                                <option value="Administration">Administration</option>
                                <option value="Health Records">Health Records</option>
                                <option value="OPD Management">OPD Management</option>
                                <option value="Pharmacy">Pharmacy</option>
                                <option value="Finance">Finance</option>
                                <option value="Diagnostics">Diagnostics</option>
                                <option value="Inpatient Care">Inpatient Care</option>
                                <option value="Telehealth">Telehealth</option>
                                <option value="Emergency Services">Emergency Services</option>
                                <option value="HR">HR</option>
                                <option value="Blood Bank">Blood Bank</option>
                                <option value="OT Management">OT Management</option>
                                <option value="Equipment">Equipment</option>
                            </select>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="serviceDescription" name="description" rows="3" required></textarea>
                            <div class="invalid-feedback">Please provide a description.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusActive"
                                        value="active" checked>
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusInactive"
                                        value="inactive">
                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveServiceBtn">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rate Card Modal -->
    <div class="modal fade" id="rateCardModal" tabindex="-1" aria-labelledby="rateCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rateCardModalLabel">Add New Rate Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.rate-cards.store') }}" id="rateCardForm">
                    @csrf
                    <input type="hidden" id="rateCardId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rateCardName" class="form-label">Rate Card Name</label>
                                <input type="text" class="form-control" id="rateCardName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rateCardService" class="form-label">Service</label>
                                <select class="form-select" id="rateCardService" name="service_id" required>
                                    <option value="">Select Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rateCardDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="rateCardDescription" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rateCardPrice" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="rateCardPrice"
                                        name="price" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rateCardCurrency" class="form-label">Currency</label>
                                <select class="form-select" id="rateCardCurrency" name="currency">
                                    <option value="USD" selected>USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rateCardBillingCycle" class="form-label">Billing Cycle</label>
                                <select class="form-select" id="rateCardBillingCycle" name="billing_cycle" required>
                                    <option value="">Select Billing Cycle</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            id="rateCardActive" value="1" checked>
                                        <label class="form-check-label" for="rateCardActive">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            id="rateCardInactive" value="0">
                                        <label class="form-check-label" for="rateCardInactive">Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features</label>
                            <div id="featuresContainer">
                                <div class="feature-item">
                                    <input type="text" class="form-control" name="features[]"
                                        placeholder="Enter feature">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="removeFeature(this)">Remove</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFeature()">Add
                                Feature</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveRateCardBtn">Save Rate Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Package Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalLabel">Create New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.packages.store') }}" id="packageForm">
                    @csrf
                    <input type="hidden" id="packageId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="packageName" class="form-label">Package Name</label>
                                <input type="text" class="form-control" id="packageName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="packagePrice" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="packagePrice"
                                        name="price" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="packageDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="packageDescription" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="packageCurrency" class="form-label">Currency</label>
                            <select class="form-select" id="packageCurrency" name="currency">
                                <option value="USD" selected>USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Included Services</label>
                            <select class="form-select select2" id="packageServices" name="included_services[]" multiple>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_active" id="packageActive"
                                        value="1" checked>
                                    <label class="form-check-label" for="packageActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_active" id="packageInactive"
                                        value="0">
                                    <label class="form-check-label" for="packageInactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="savePackageBtn">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Discount Modal -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalLabel">Add New Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.discounts.store') }}" id="discountForm">
                    @csrf
                    <input type="hidden" id="discountId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discountName" class="form-label">Discount Name</label>
                                <input type="text" class="form-control" id="discountName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discountType" class="form-label">Type</label>
                                <select class="form-select" id="discountType" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="discountDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="discountDescription" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discountValue" class="form-label">Value</label>
                                <input type="number" step="0.01" class="form-control" id="discountValue"
                                    name="value" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discountApplicableTo" class="form-label">Applicable To</label>
                                <select class="form-select" id="discountApplicableTo" name="applicable_to" required>
                                    <option value="">Select Applicable To</option>
                                    <option value="services">Services</option>
                                    <option value="packages">Packages</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discountValidFrom" class="form-label">Valid From</label>
                                <input type="date" class="form-control" id="discountValidFrom" name="valid_from"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discountValidUntil" class="form-label">Valid Until</label>
                                <input type="date" class="form-control" id="discountValidUntil" name="valid_until">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discountUsageLimit" class="form-label">Usage Limit</label>
                                <input type="number" class="form-control" id="discountUsageLimit" name="usage_limit"
                                    placeholder="Leave empty for unlimited">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            id="discountActive" value="1" checked>
                                        <label class="form-check-label" for="discountActive">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            id="discountInactive" value="0">
                                        <label class="form-check-label" for="discountInactive">Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="applicableIdsContainer" style="display: none;">
                            <label class="form-label">Select Specific Items</label>
                            <div id="servicesSelect" class="applicable-select" style="display: none;">
                                <select class="form-select" name="applicable_ids[]" multiple>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="packagesSelect" class="applicable-select" style="display: none;">
                                <select class="form-select" name="applicable_ids[]" multiple>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveDiscountBtn">Save Discount</button>
                    </div>
                </form>
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
        $(document).ready(function() {
            // Initialize DataTables
            $('#servicesTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [5] // Actions column
                }],
                order: [
                    [0, 'asc']
                ]
            });

            $('#rateCardsTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [6] // Actions column
                }],
                order: [
                    [0, 'asc']
                ]
            });

            $('#packagesTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [5] // Actions column
                }],
                order: [
                    [0, 'asc']
                ]
            });

            $('#discountsTable').DataTable({
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [8] // Actions column
                }],
                order: [
                    [0, 'asc']
                ]
            });





            // Tab switching functionality
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                $('.nav-tab').removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Hide all tab content
                $('.tab-content').removeClass('active');

                // Show the selected tab content
                const tabId = $(this).data('tab');
                $('#' + tabId).addClass('active');
            });

            // Handle applicable_to change for discounts
            $('#discountApplicableTo').on('change', function() {
                const applicableTo = $(this).val();
                const container = $('#applicableIdsContainer');
                const servicesSelect = $('#servicesSelect');
                const packagesSelect = $('#packagesSelect');

                // Hide all selects first
                $('.applicable-select').hide();
                container.hide();

                if (applicableTo === 'services') {
                    servicesSelect.show();
                    container.show();
                } else if (applicableTo === 'packages') {
                    packagesSelect.show();
                    container.show();
                }
            });
        });

        // Service Functions
        function openServiceModal() {
            $('#serviceModalLabel').text('Add New Service');
            $('#serviceForm').attr('action', '{{ route('admin.services.store') }}');
            $('#serviceId').val('');
            $('#serviceName').val('');
            $('#serviceCategory').val('');
            $('#serviceDescription').val('');
            $('#statusActive').prop('checked', true);

            // Remove any existing _method field
            $('input[name="_method"]').remove();
        }

        function editService(id) {
            $('#serviceModalLabel').text('Edit Service');
            $('#serviceForm').attr('action', '{{ route('admin.services.update', ':id') }}'.replace(':id', id));

            // Remove any existing _method field and add PUT method
            $('input[name="_method"]').remove();
            $('#serviceForm').append('<input type="hidden" name="_method" value="PUT">');

            // In a real application, you would fetch the service data via AJAX
            // For demo purposes, we'll use the data from the table
            const row = $(`#servicesTable tr:has(td:first-child:contains('${id}'))`);
            $('#serviceId').val(id);
            $('#serviceName').val(row.find('td:nth-child(2)').text());
            $('#serviceCategory').val(row.find('td:nth-child(3)').text());
            $('#serviceDescription').val(row.find('td:nth-child(4)').text());

            const status = row.find('.status-badge').text().trim().toLowerCase();
            if (status === 'active') {
                $('#statusActive').prop('checked', true);
            } else {
                $('#statusInactive').prop('checked', true);
            }

            $('#serviceModal').modal('show');
        }

        // Rate Card Functions
        function openRateCardModal() {
            $('#rateCardModalLabel').text('Add New Rate Card');
            $('#rateCardForm').attr('action', '{{ route('admin.rate-cards.store') }}');
            $('#rateCardId').val('');
            $('#rateCardName').val('');
            $('#rateCardService').val('');
            $('#rateCardDescription').val('');
            $('#rateCardPrice').val('');
            $('#rateCardCurrency').val('USD');
            $('#rateCardBillingCycle').val('');
            $('#rateCardActive').prop('checked', true);
            $('#featuresContainer').html(
                '<div class="feature-item"><input type="text" class="form-control" name="features[]" placeholder="Enter feature"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFeature(this)">Remove</button></div>'
            );

            // Remove any existing _method field
            $('input[name="_method"]').remove();
        }

        function editRateCard(id) {
            $('#rateCardModalLabel').text('Edit Rate Card');
            $('#rateCardForm').attr('action', `${baseUrl}/admin/rate-cards/${id}`);

            // Remove any existing _method field and add PUT method
            $('input[name="_method"]').remove();
            $('#rateCardForm').append('<input type="hidden" name="_method" value="PUT">');

            // In a real application, you would fetch the rate card data via AJAX
            const row = $(`#rateCardsTable tr:has(td:first-child:contains('${id}'))`);
            $('#rateCardId').val(id);
            $('#rateCardName').val(row.find('td:nth-child(2)').text());
            // Service selection would need to be handled based on the service name
            // For demo, we'll just clear it
            $('#rateCardService').val('');

            // Price extraction (remove currency symbol)
            const priceText = row.find('td:nth-child(4)').text();
            const price = priceText.replace(/[^\d.]/g, '');
            $('#rateCardPrice').val(price);

            $('#rateCardBillingCycle').val(row.find('td:nth-child(5)').text().toLowerCase());

            const status = row.find('.status-badge').text().trim().toLowerCase();
            if (status === 'active') {
                $('#rateCardActive').prop('checked', true);
            } else {
                $('#rateCardInactive').prop('checked', true);
            }

            $('#rateCardModal').modal('show');
        }

        function addFeature() {
            $('#featuresContainer').append(
                '<div class="feature-item"><input type="text" class="form-control" name="features[]" placeholder="Enter feature"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFeature(this)">Remove</button></div>'
            );
        }

        function removeFeature(button) {
            $(button).closest('.feature-item').remove();
        }

        // Package Functions
        function openPackageModal() {
            $('#packageModalLabel').text('Create New Package');
            $('#packageForm').attr('action', '{{ route('admin.packages.store') }}');
            $('#packageId').val('');
            $('#packageName').val('');
            $('#packagePrice').val('');
            $('#packageDescription').val('');
            $('#packageCurrency').val('USD');
            $('#packageServices').val([]).trigger('change');
            $('#packageActive').prop('checked', true);

            // Remove any existing _method field
            $('input[name="_method"]').remove();
        }

        function editPackage(id) {
            $('#packageModalLabel').text('Edit Package');
            $('#packageForm').attr('action', `${baseUrl}/admin/packages/${id}`);

            // Remove any existing _method field and add PUT method
            $('input[name="_method"]').remove();
            $('#packageForm').append('<input type="hidden" name="_method" value="PUT">');

            // In a real application, you would fetch the package data via AJAX
            const row = $(`#packagesTable tr:has(td:first-child:contains('${id}'))`);
            $('#packageId').val(id);
            $('#packageName').val(row.find('td:nth-child(2)').text());

            // Price extraction (remove currency symbol)
            const priceText = row.find('td:nth-child(3)').text();
            const price = priceText.replace(/[^\d.]/g, '');
            $('#packagePrice').val(price);

            const status = row.find('.status-badge').text().trim().toLowerCase();
            if (status === 'active') {
                $('#packageActive').prop('checked', true);
            } else {
                $('#packageInactive').prop('checked', true);
            }

            $('#packageModal').modal('show');
        }

        // Discount Functions
        function openDiscountModal() {
            $('#discountModalLabel').text('Add New Discount');
            $('#discountForm').attr('action', '{{ route('admin.discounts.store') }}');
            $('#discountId').val('');
            $('#discountName').val('');
            $('#discountType').val('');
            $('#discountDescription').val('');
            $('#discountValue').val('');
            $('#discountApplicableTo').val('');
            $('#discountValidFrom').val('');
            $('#discountValidUntil').val('');
            $('#discountUsageLimit').val('');
            $('#discountActive').prop('checked', true);
            $('#applicableIdsContainer').hide();
            $('.applicable-select').hide();

            // Remove any existing _method field
            $('input[name="_method"]').remove();
        }

        function editDiscount(id) {
            $('#discountModalLabel').text('Edit Discount');
            $('#discountForm').attr('action', `${baseUrl}/admin/discounts/${id}`);

            // Remove any existing _method field and add PUT method
            $('input[name="_method"]').remove();
            $('#discountForm').append('<input type="hidden" name="_method" value="PUT">');

            // Use data attributes selector
            const row = $(`#discountsTable tr[data-discount-id="${id}"]`);

            console.log('Row found:', row.length);

            if (row.length === 0) {
                console.error('Discount row not found for ID:', id);
                return;
            }

            // Debug: Check all data attributes
            console.log('All data attributes:', row.data());
            console.log('valid-from:', row.data('valid-from'));
            console.log('valid-until:', row.data('valid-until'));

            // Basic fields from table
            $('#discountId').val(id);
            $('#discountName').val(row.find('td:nth-child(2)').text().trim());
            $('#discountType').val(row.find('td:nth-child(3)').text().trim().toLowerCase());

            // Value extraction
            const valueText = row.find('td:nth-child(4)').text().trim();
            let value = valueText;
            if (valueText.includes('%')) {
                value = valueText.replace('%', '').trim();
            } else {
                value = valueText.replace(/[^\d.]/g, '');
            }
            $('#discountValue').val(value);

            $('#discountApplicableTo').val(row.find('td:nth-child(5)').text().trim().toLowerCase());

            // FIXED: Get data from data attributes
            $('#discountDescription').val(row.data('description') || '');
            $('#discountUsageLimit').val(row.data('usage-limit') || '');

            // FIXED: Get dates with better handling
            const validFrom = row.data('valid-from');
            const validUntil = row.data('valid-until');

            console.log('Raw validFrom:', validFrom);
            console.log('Raw validUntil:', validUntil);

            // Format dates for input fields (YYYY-MM-DD)
            if (validFrom) {
                const formattedFrom = formatDateForInput(validFrom);
                console.log('Formatted validFrom:', formattedFrom);
                $('#discountValidFrom').val(formattedFrom);
            } else {
                $('#discountValidFrom').val('');
            }

            if (validUntil && validUntil !== 'No expiry') {
                const formattedUntil = formatDateForInput(validUntil);
                console.log('Formatted validUntil:', formattedUntil);
                $('#discountValidUntil').val(formattedUntil);
            } else {
                $('#discountValidUntil').val('');
            }

            // FIXED: Get status from data attribute instead of badge text
            const status = row.data('status');
            console.log('Status from data:', status);

            // Convert to boolean if needed
            const isActive = (status === true || status === '1' || status === 1 || status === 'true');

            if (isActive) {
                $('#discountActive').prop('checked', true);
                $('#discountInactive').prop('checked', false);
            } else {
                $('#discountActive').prop('checked', false);
                $('#discountInactive').prop('checked', true);
            }

            // Trigger change to show applicable IDs if needed
            $('#discountApplicableTo').trigger('change');

            $('#discountModal').modal('show');
        }

        // Helper function to format date for input field
        function formatDateForInput(dateString) {
            if (!dateString) return '';

            // If it's already in YYYY-MM-DD format, return as is
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                return dateString;
            }

            // If it's a different format, convert it
            try {
                const date = new Date(dateString);
                if (!isNaN(date.getTime())) {
                    return date.toISOString().split('T')[0];
                }
            } catch (e) {
                console.error('Date formatting error:', e);
            }

            return '';
        }

        // Helper function to convert "M d, Y" to "YYYY-MM-DD"
        function convertToDateInputFormat(dateString) {
            if (!dateString || dateString === 'No expiry') return '';

            const months = {
                'Jan': '01',
                'Feb': '02',
                'Mar': '03',
                'Apr': '04',
                'May': '05',
                'Jun': '06',
                'Jul': '07',
                'Aug': '08',
                'Sep': '09',
                'Oct': '10',
                'Nov': '11',
                'Dec': '12'
            };

            const parts = dateString.replace(',', '').split(' ');
            if (parts.length === 3) {
                const month = months[parts[0]];
                const day = parts[1].padStart(2, '0');
                const year = parts[2];
                return `${year}-${month}-${day}`;
            }

            return '';
        }

        function deleteRateCard(id) {
            if (confirm('Are you sure you want to delete this rate card?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${baseUrl}/admin/rate-cards/${id}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
