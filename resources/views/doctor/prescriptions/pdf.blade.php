<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .clinic-name {
            font-size: 28px;
            font-weight: bold;
            color: #1E40AF;
        }

        .clinic-info {
            font-size: 12px;
            color: #6B7280;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .patient-info,
        .prescription-info {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-info td,
        .prescription-info td {
            padding: 8px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #4B5563;
            width: 120px;
        }

        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .medicines-table th {
            background-color: #F3F4F6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            border: 1px solid #E5E7EB;
        }

        .medicines-table td {
            padding: 10px;
            border: 1px solid #E5E7EB;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
        }

        .signature-box {
            margin-top: 40px;
            text-align: right;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
            margin-top: 30px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(59, 130, 246, 0.1);
            z-index: -1;
        }

        .prescription-id {
            background-color: #3B82F6;
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    <div class="watermark">PRESCRIPTION</div>

    <!-- Header -->
    <div class="header">
        <div style="text-align: center;">
            <div class="clinic-name">Bhardwaj Hospital</div>
            <div class="clinic-info">
                123 Medical Street, Healthcare City | Phone: (123) 456-7890 | Email: info@medcare.com
            </div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div class="prescription-id">PRESCRIPTION #{{ $prescription->id }}</div>
            <div style="font-size: 18px; font-weight: bold; color: #1F2937;">MEDICAL PRESCRIPTION</div>
            <div style="color: #6B7280; margin-top: 5px;">Issued on: {{ $prescription->created_at->format('F d, Y') }}
            </div>
        </div>
    </div>

    <!-- Patient and Doctor Information -->
    <div class="section">
        <table class="patient-info">
            <tr>
                <td style="width: 50%;">
                    <div style="font-weight: bold; color: #1F2937; margin-bottom: 10px;">PATIENT INFORMATION</div>
                    <table>
                        <tr>
                            <td class="label">Name:</td>
                            <td>{{ $prescription->patient->first_name }} {{ $prescription->patient->last_name }}</td>
                        </tr>
                        @if ($prescription->patient->age)
                            <tr>
                                <td class="label">Age:</td>
                                <td>{{ $prescription->patient->age }} years</td>
                            </tr>
                        @endif
                        @if ($prescription->patient->gender)
                            <tr>
                                <td class="label">Gender:</td>
                                <td>{{ $prescription->patient->gender }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td style="width: 50%;">
                    <div style="font-weight: bold; color: #1F2937; margin-bottom: 10px;">PRESCRIBING DOCTOR</div>
                    <table>
                        <tr>
                            <td class="label">Name:</td>
                            <td>Dr. {{ $prescription->doctor->first_name }} {{ $prescription->doctor->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="label">License No:</td>
                            <td>{{ $prescription->doctor->license_number ?? 'MD-12345' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Valid Until:</td>
                            <td>{{ $prescription->valid_until->format('F d, Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>



    <!-- Prescribed Medicines -->
    @if ($prescription->medicines && $prescription->medicines->count() > 0)
        <div class="section">
            <div class="section-title">PRESCRIBED MEDICINES</div>
            <table class="medicines-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prescription->medicines as $medicine)
                        <tr>
                            <td>
                                <strong>{{ $medicine->name }}</strong><br>
                                <small>{{ $medicine->type ?? 'Tablet' }}</small>
                            </td>
                            <td>{{ $medicine->dosage ?? 'As prescribed' }}</td>
                            <td>{{ $medicine->frequency ?? 'Daily' }}</td>
                            <td>{{ $medicine->duration ?? '7 days' }}</td>
                            <td>{{ $medicine->instructions ?? 'Take after meals' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Additional Instructions -->
    @if ($prescription->notes)
        <div class="section">
            <div class="section-title">ADDITIONAL INSTRUCTIONS</div>
            <div style="padding: 15px; border: 1px dashed #D1D5DB; border-radius: 5px;">
                {{ $prescription->notes }}
            </div>
        </div>
    @endif

    <!-- Footer and Signature -->
    <div class="footer">
        <div style="margin-bottom: 20px; font-size: 12px; color: #6B7280;">
            <strong>Important Notes:</strong>
            <ul style="margin-top: 5px; padding-left: 20px;">
                <li>This prescription is valid until {{ $prescription->valid_until->format('F d, Y') }}</li>
                <li>Do not share medications with others</li>
                <li>Complete the full course of treatment</li>
                <li>Consult your doctor if symptoms persist</li>
            </ul>
        </div>

        <div class="signature-box">
            <div style="margin-bottom: 10px;">
                <strong>Dr. {{ $prescription->doctor->first_name }}
                    {{ $prescription->doctor->last_name }}</strong><br>
                {{ $prescription->doctor->qualifications ?? 'MBBS, MD' }}<br>
                {{ $prescription->doctor->specialization ?? 'General Physician' }}
            </div>
            <div class="signature-line"></div>
            <div style="margin-top: 5px; font-size: 12px;">Signature & Stamp</div>
        </div>

        <div style="text-align: center; margin-top: 40px; font-size: 10px; color: #9CA3AF;">
            This is a computer-generated prescription. No physical signature is required.<br>
            MedCare Clinic | Phone: (123) 456-7890 | www.medcareclinic.com
        </div>
    </div>
</body>

</html>
