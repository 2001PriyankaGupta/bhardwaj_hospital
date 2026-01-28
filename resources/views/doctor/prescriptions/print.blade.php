<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $prescription->id }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .prescription-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .prescription-footer {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }

        .medicine-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .medicine-table th,
        .medicine-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .medicine-table th {
            background-color: #f2f2f2;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                font-size: 12px;
            }

            .prescription-header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Print Button -->
        <div class="no-print text-right mb-3">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>

        <!-- Header -->
        <div class="prescription-header">
            <h2>MEDICAL PRESCRIPTION</h2>
            <h4> Bhardwaj Hospital</h4>
            <p>Address Line 1, City, Country | Phone: +123456789 | Email: info@hospital.com</p>
        </div>

        <!-- Patient Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Patient Information</h5>
                <p><strong>Name:</strong> {{ $prescription->patient->first_name }}
                    {{ $prescription->patient->last_name }}</p>
                <p><strong>Age:</strong>
                    @if ($prescription->patient->date_of_birth)
                        {{ \Carbon\Carbon::parse($prescription->patient->date_of_birth)->age }} years
                    @else
                        N/A
                    @endif
                </p>
                <p><strong>Gender:</strong> {{ $prescription->patient->gender ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h5>Prescription Details</h5>
                <p><strong>Date:</strong> {{ $prescription->prescription_date->format('d-M-Y') }}</p>
                <p><strong>Valid Until:</strong>
                    @if ($prescription->valid_until)
                        {{ $prescription->valid_until->format('d-M-Y') }}
                    @else
                        Not specified
                    @endif
                </p>
                <p><strong>Doctor:</strong> Dr. {{ $prescription->doctor->first_name }}
                    {{ $prescription->doctor->last_name }}</p>
                <p><strong>Prescription ID:</strong> RX{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <!-- Medications -->
        <h5>Medications</h5>
        <table class="medicine-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->medication_details as $index => $medicine)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $medicine['medicine'] ?? 'N/A' }}</td>
                        <td>{{ $medicine['dosage'] ?? 'N/A' }}</td>
                        <td>{{ $medicine['frequency'] ?? 'N/A' }}</td>
                        <td>{{ $medicine['duration'] ?? 'N/A' }}</td>
                        <td>{{ $medicine['notes'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Instructions -->
        @if ($prescription->instructions)
            <div class="mb-4">
                <h5>Instructions</h5>
                <p>{{ $prescription->instructions }}</p>
            </div>
        @endif

        <!-- Follow Up -->
        @if ($prescription->follow_up_advice)
            <div class="mb-4">
                <h5>Follow Up Advice</h5>
                <p>{{ $prescription->follow_up_advice }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="prescription-footer">
            <div class="row">
                <div class="col-md-6 text-center">
                    <p>_________________________</p>
                    <p>Doctor's Signature</p>
                    <p>Dr. {{ $prescription->doctor->first_name }} {{ $prescription->doctor->last_name }}</p>
                    <p>License No: {{ $prescription->doctor->license_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-center">
                    <p>_________________________</p>
                    <p>Patient's Signature</p>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <small>
                        This is a computer-generated prescription. Valid only with doctor's stamp.<br>
                        Printed on: {{ date('d-M-Y h:i A') }}
                    </small>
                </p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
