@extends('staff.layouts.master')

@section('title', 'Bed Management System')

@section('css')
    <link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        :root {
            --primary-color: #ff4900;
            --success-color: #4cc9a7;
            --danger-color: #f72585;
            --warning-color: #ff9e00;
            --info-color: #4895ef;
        }

        .dashboard-kpi {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 18px rgba(0, 0, 0, .08);
            padding: 26px;
            position: relative;
            border-left: 7px solid #e84118;
            margin-bottom: 20px;
        }

        .dashboard-kpi .value {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .status-available {
            background: rgba(76, 201, 167, 0.1);
            color: var(--success-color);
        }

        .status-occupied {
            background: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
        }

        .status-maintenance {
            background: rgba(255, 158, 0, 0.1);
            color: var(--warning-color);
        }

        .status-reserved {
            background: rgba(72, 149, 239, 0.1);
            color: var(--info-color);
        }

        .dashboard-kpi .kpi-icon {
            font-size: 18px;
            color: #e84118;
            position: absolute;
            right: 24px;
            top: 24px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">

        <!-- STAT CARDS -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"><i class="fas fa-bed fa-2x"></i></span>
                    <div class="value">{{ $totalBeds }}</div>
                    <div>Total Beds</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"> <i class="fas fa-check-circle fa-2x"></i></span>
                    <div class="value">{{ $availableBeds }}</div>
                    <div>Available</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"> <i class="fas fa-user-injured fa-2x"></i></span>
                    <div class="value">{{ $occupiedBeds }}</div>
                    <div>Occupied</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-kpi">
                    <span class="kpi-icon"> <i class="fas fa-tools fa-2x"></i></span>
                    <div class="value">{{ $maintenanceBeds }}</div>
                    <div>Maintenance</div>
                </div>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Bed Management System</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bedModal">
                    Add New Bed
                </button>
            </div>

            <div class="card-body">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bed Code</th>
                            <th>Room</th>
                            <th>Ward</th>
                            <th>Status</th>
                            <th>Last Occupancy</th>
                            <th>Next Availability</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($beds as $key => $bed)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $bed->bed_number }}</td>
                                <td>{{ $bed->room->room_number ?? 'N/A' }}</td>
                                <td>{{ $bed->room->ward_name }}</td>

                                <td>
                                    <span class="status-badge status-{{ $bed->status }}">
                                        {{ ucfirst($bed->status) }}
                                    </span>
                                </td>

                                <td>{{ $bed->last_occupancy_date ? \Carbon\Carbon::parse($bed->last_occupancy_date)->format('d-M-y') : '-' }}
                                </td>
                                <td>{{ $bed->next_availability_date ? \Carbon\Carbon::parse($bed->next_availability_date)->format('d-M-y') : '-' }}
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-warning editBed" data-id="{{ $bed->id }}"
                                        data-room="{{ $bed->room_id }}" data-bed_number="{{ $bed->bed_number }}"
                                        data-status="{{ $bed->status }}"
                                        data-last="{{ $bed->last_occupancy_date ? $bed->last_occupancy_date->format('Y-m-d') : '' }}"
                                        data-next="{{ $bed->next_availability_date ? $bed->next_availability_date->format('Y-m-d') : '' }}"
                                        data-notes="{{ $bed->notes }}" data-bs-toggle="modal" data-bs-target="#bedModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('staff.beds.destroy', $bed->id) }}" method="POST"
                                        class="d-inline deleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- ADD / EDIT MODAL -->
    <div class="modal fade" id="bedModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="modalTitle">Add New Bed</h5>
                    <button data-bs-dismiss="modal" class="btn-close"></button>
                </div>

                <form id="bedForm" method="POST" action="{{ route('staff.beds.store') }}">
                    @csrf
                    <div id="formMethod"></div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label>Room</label>
                            <select name="room_id" id="room_id" class="form-select" required>
                                <option value="">Select Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->room_number }} ({{ $room->ward_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Bed Code</label>
                            <input type="text" name="bed_number" id="bed_number" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Status</label>
                            <select name="status" id="bed_status" class="form-select" required>
                                <option value="">Select</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Last Occupancy</label>
                            <input type="date" name="last_occupancy_date" id="last_occupancy_date" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Next Availability</label>
                            <input type="date" name="next_availability_date" id="next_availability_date"
                                class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Save Bed</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
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
        $('.datatable').DataTable();

        /** Reset Modal on Add */
        $('#bedModal').on('show.bs.modal', function(e) {
            if (!$(e.relatedTarget).hasClass('editBed')) {
                $('#bedForm')[0].reset();
                $('#formMethod').html('');
                $('#bedForm').attr('action', "{{ route('staff.beds.store') }}");
                $('#modalTitle').text('Add New Bed');
            }
        });

        /** Edit Bed */
        $('.editBed').click(function() {
            let id = $(this).data('id');

            $('#modalTitle').text('Edit Bed');
            $('#formMethod').html('<input type="hidden" name="_method" value="PUT">');
            $('#bedForm').attr('action', `${baseUrl}/staff/beds/${id}`);

            $('#room_id').val($(this).data('room'));
            $('#bed_number').val($(this).data('bed_number'));
            $('#bed_status').val($(this).data('status'));
            $('#last_occupancy_date').val($(this).data('last'));
            $('#next_availability_date').val($(this).data('next'));
            $('#notes').val($(this).data('notes'));
        });

        /** Delete Confirm */
        $('.deleteForm').submit(function(e) {
            e.preventDefault();

            let form = this;

            Swal.fire({
                icon: 'warning',
                title: 'Confirm Delete?',
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
