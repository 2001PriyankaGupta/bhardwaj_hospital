@extends('admin.layouts.master')

@section('title', 'Create Invoice')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Create New Invoice</h3>
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back To list
                    </a>
                </div>
                <form action="{{ route('admin.invoices.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient *</label>
                                    <select name="patient_id" id="patient_id" class="form-control" required>
                                        <option value="">Select Patient</option>
                                        @foreach ($patients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->first_name }}
                                                {{ $patient->last_name }}
                                                ({{ $patient->patient_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admission_id">Admission *</label>
                                    <select name="admission_id" id="admission_id" class="form-control" required>
                                        <option value="">Select Admission</option>
                                        @foreach ($admissions as $admission)
                                            <option value="{{ $admission->id }}"
                                                data-patient="{{ $admission->patient_id }}">
                                                {{ $admission->admission_number }} - {{ $admission->patient->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_date">Invoice Date *</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <hr>
                        <h5>Invoice Items</h5>

                        <div id="invoice-items">
                            <div class="row item-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" name="items[0][description]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount (₹)</label>
                                        <input type="number" name="items[0][amount]" class="form-control item-amount"
                                            step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select name="items[0][type]" class="form-control" required>
                                            <option value="admission">Admission</option>
                                            <option value="treatment">Treatment</option>
                                            <option value="medicine">Medicine</option>
                                            <option value="service">Service</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Service Date</label>
                                        <input type="date" name="items[0][service_date]" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-item" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
                    </div>
                </form>
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

    <script>
          let baseUrl = "{{ config('app.url') }}";
        let itemCount = 1;

        $('#add-item').click(function() {
            const newRow = `
            <div class="row item-row mt-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="items[${itemCount}][description]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="number" name="items[${itemCount}][amount]" class="form-control item-amount" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="items[${itemCount}][type]" class="form-control" required>
                            <option value="admission">Admission</option>
                            <option value="treatment">Treatment</option>
                            <option value="medicine">Medicine</option>
                            <option value="service">Service</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="date" name="items[${itemCount}][service_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <button type="button" class="btn btn-danger btn-sm mt-1 remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            $('#invoice-items').append(newRow);
            itemCount++;
        });

        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
            }
        });

        // Filter admissions based on selected patient
        $('#patient_id').change(function() {
            const patientId = $(this).val();
            $('#admission_id option').show();
            if (patientId) {
                $('#admission_id option').not('[data-patient="' + patientId + '"]').hide();
                $('#admission_id').val('');
            }
        });
    </script>
@endsection
