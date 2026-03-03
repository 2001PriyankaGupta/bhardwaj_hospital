@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="">
                    <h4 class="text-black mb-4">Create Prescription</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('doctor.prescriptions.store') }}" method="POST" id="prescriptionForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_id" class="form-label">Select Appointment *</label>
                                        <select class="form-control select2" id="appointment_id" name="appointment_id"
                                            required>
                                            <option value="">Select Appointment</option>
                                            @foreach ($appointments as $appointment)
                                                <option value="{{ $appointment->id }}"
                                                    {{ (isset($selectedAppointmentId) && $selectedAppointmentId == $appointment->id) ? 'selected' : '' }}>
                                                    {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }} - {{ $appointment->appointment_date->format('d M Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="valid_until" class="form-label">Valid Until</label>
                                        <input type="date" class="form-control" id="valid_until" name="valid_until"
                                            value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Medicines Section -->
                            <div class="mb-4">
                                <h4 class="header-title">Medication Details</h4>
                                <div id="medicines-container">
                                    <div class="medicine-row border p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Medicine Name *</label>
                                                    <input type="text" class="form-control"
                                                        name="medication_details[0][medicine]" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Dosage *</label>
                                                    <input type="text" class="form-control"
                                                        name="medication_details[0][dosage]" placeholder="e.g., 500mg"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Frequency *</label>
                                                    <select class="form-control" name="medication_details[0][frequency]"
                                                        required>
                                                        <option value="Once daily">Once daily</option>
                                                        <option value="Twice daily">Twice daily</option>
                                                        <option value="Thrice daily">Thrice daily</option>
                                                        <option value="4 times daily">4 times daily</option>
                                                        <option value="Every 6 hours">Every 6 hours</option>
                                                        <option value="Every 8 hours">Every 8 hours</option>
                                                        <option value="As needed">As needed</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Duration *</label>
                                                    <input type="text" class="form-control"
                                                        name="medication_details[0][duration]" placeholder="e.g., 7 days"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger remove-medicine w-100"
                                                        style="display: none;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-success" id="add-medicine">
                                    <i class="fas fa-plus"></i> Add Another Medicine
                                </button>
                            </div>

                            <div class="mb-3">
                                <label for="instructions" class="form-label">Instructions</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="3"
                                    placeholder="Special instructions for the patient..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="follow_up_advice" class="form-label">Follow-up Advice</label>
                                <textarea class="form-control" id="follow_up_advice" name="follow_up_advice" rows="2"
                                    placeholder="Follow-up advice..."></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_active">Active Prescription</label>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('doctor.prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Prescription</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2();

                let medicineCount = 1;

                // Add new medicine row
                $('#add-medicine').click(function() {
                    const newRow = `
                <div class="medicine-row border p-3 mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Medicine Name *</label>
                                <input type="text" class="form-control" name="medication_details[${medicineCount}][medicine]" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Dosage *</label>
                                <input type="text" class="form-control" name="medication_details[${medicineCount}][dosage]" 
                                       placeholder="e.g., 500mg" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Frequency *</label>
                                <select class="form-control" name="medication_details[${medicineCount}][frequency]" required>
                                    <option value="Once daily">Once daily</option>
                                    <option value="Twice daily">Twice daily</option>
                                    <option value="Thrice daily">Thrice daily</option>
                                    <option value="4 times daily">4 times daily</option>
                                    <option value="Every 6 hours">Every 6 hours</option>
                                    <option value="Every 8 hours">Every 8 hours</option>
                                    <option value="As needed">As needed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Duration *</label>
                                <input type="text" class="form-control" name="medication_details[${medicineCount}][duration]" 
                                       placeholder="e.g., 7 days" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger remove-medicine w-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    $('#medicines-container').append(newRow);
                    medicineCount++;

                    // Show remove button for first row if there are multiple rows
                    if (medicineCount > 1) {
                        $('.remove-medicine').show();
                    }
                });

                // Remove medicine row
                $(document).on('click', '.remove-medicine', function() {
                    $(this).closest('.medicine-row').remove();
                    medicineCount--;

                    // Hide remove button if only one row left
                    if (medicineCount <= 1) {
                        $('.remove-medicine').first().hide();
                    }
                });
            });
        </script>
    @endpush
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    @endsection     