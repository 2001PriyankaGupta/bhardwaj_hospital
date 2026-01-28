@extends('doctor.layouts.master')

@section('title', 'Doctor Management')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    .action-btn {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .gap-1>* {
        margin: 0 2px;
    }

    #doctorsTable th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #doctorsTable td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .badge-pill {
        font-size: 0.75rem;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, .02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, .1);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    .card-header {
        border-bottom: 2px solid rgba(0, 0, 0, .125);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
</style>
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
    <div class="container-fluid mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="d-flex align-items-center">

                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold">Doctor Management</h1>
                    <p class="text-muted mb-0">Manage doctor details, specialties, and availability</p>
                </div>
            </div>
            {{-- <div class="action-buttons">

                <a class="btn btn-primary" href="{{ route('doctor.doctors.create') }}">
                    <i class="fas fa-list me-1"></i>Add New Doctor
                </a>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">


                        <!-- Doctors Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="doctorsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Doctor</th>
                                        <th width="15%">Contact</th>
                                        <th width="12%">Specialty</th>
                                        <th width="10%">License</th>
                                        <th width="8%">Fee</th>
                                        <th width="10%">Status</th>
                                        <th width="20%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doctors as $doctor)
                                        <tr style="font-family: initial;">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <div class="ml-3">
                                                        <strong class="text-primary">{{ $doctor->full_name }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted text-sm">{{ Str::limit($doctor->qualifications, 30) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-sm">
                                                    <span class="text-dark">{{ $doctor->email }}</span>
                                                    <br>
                                                    <span class="text-dark">{{ $doctor->phone }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $doctor->specialty->name }}
                                            </td>
                                            <td>
                                                <code class="text-sm bg-light px-2 py-1 rounded">
                                                    {{ $doctor->license_number }}
                                                </code>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-success">
                                                    ₹{{ number_format($doctor->consultation_fee, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($doctor->status == 'active')
                                                    <span class="badge badge-success badge-pill px-3" style="color: green;">
                                                        <i class="fas fa-check-circle mr-1"></i>Active
                                                    </span>
                                                @elseif($doctor->status == 'inactive')
                                                    <span class="badge badge-secondary badge-pill px-3"
                                                        style="color: rgb(182, 52, 52);">
                                                        <i class="fas fa-pause-circle mr-1"></i>Inactive
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning badge-pill px-3"
                                                        style="color: rgb(242, 211, 8);">
                                                        <i class="fas fa-umbrella-beach mr-1"></i>On Leave
                                                    </span>
                                                @endif

                                                @if ($doctor->isOnLeave())
                                                    <br>
                                                    <small class="text-danger text-sm">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Currently on Leave
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- View Button -->
                                                    <a href="{{ route('doctor.doctors.show', $doctor) }}"
                                                        style="height: 25px;" class="btn btn-info btn-sm action-btn"
                                                        title="View Details" data-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('doctor.doctors.edit', $doctor) }}"
                                                        style="height: 25px;" class="btn btn-primary btn-sm action-btn"
                                                        title="Edit Doctor" data-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>



                                                    <!-- Performance Button -->
                                                    <a href="{{ route('doctor.doctors.performance', $doctor) }}"
                                                        style="height: 25px;" class="btn btn-warning btn-sm action-btn"
                                                        title="View Performance" data-toggle="tooltip">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>


                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-user-md fa-3x mb-3"></i>
                                                    <h5>No doctors found</h5>
                                                    <p>Get started by adding your first doctor</p>
                                                    <a href="{{ route('doctor.doctors.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus mr-1"></i> Add Doctor
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>


                    </div>
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


            $('#doctorsTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ]
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Enhanced delete confirmation
            function confirmDelete(event) {
                event.preventDefault();
                const form = event.target.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Make confirmDelete function global
            window.confirmDelete = confirmDelete;

            // Real-time filtering with DataTable
            $('#searchInput').on('keyup', function() {
                $('#doctorsTable').DataTable().search(this.value).draw();
            });

            $('#specialtyFilter, #statusFilter').on('change', function() {
                $('#filterForm').submit();
            });
        });
    </script>

    <!-- Include SweetAlert2 for beautiful alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js">
    </script>
@endsection
