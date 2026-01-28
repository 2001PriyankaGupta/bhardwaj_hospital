<!DOCTYPE html>
<html>

<head>
    <title>Bhardwaj Hospital – Medical Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            margin: 40px;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header-left {
            flex: 1;
        }

        .hospital-name {
            font-size: 26px;
            font-weight: bold;
            color: #0d6efd;
        }

        .hospital-info {
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }

        .report-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            background: #e9f2ff;
            padding: 8px 10px;
            font-weight: bold;
            border-left: 4px solid #0d6efd;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        td,
        th {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
            background: #fafafa;
        }

        .vital-table td {
            width: 25%;
        }

        p {
            margin: 0;
            line-height: 1.6;
            font-size: 14px;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
            font-size: 14px;
        }

        .signature strong {
            display: block;
            margin-top: 5px;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">
            <div class="hospital-name">Bhardwaj Hospital</div>
            <div class="hospital-info">
                Address, City, State – PIN<br>
                Phone: +91 XXXXX XXXXX | Email: bhardwajhospital@example.com
            </div>
        </div>
    </div>

    <div class="report-title">Medical Report</div>

    <div class="section">
        <div class="section-title">Patient Information</div>
        <table>
            <tr>
                <td class="label">Patient Name</td>
                <td>{{ $record->patient->first_name }} {{ $record->patient->last_name }}</td>
            </tr>
            <tr>
                <td class="label">Record Date</td>
                <td>{{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Doctor</td>
                <td>Dr. {{ $record->doctor->first_name }} {{ $record->doctor->last_name }}</td>
            </tr>
            <tr>
                <td class="label">Report Title</td>
                <td>{{ $record->report_title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Report Type</td>
                <td>{{ $record->report_type ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Vital Signs</div>
        <table class="vital-table">
            <tr>
                <td class="label">Height</td>
                <td>{{ $record->height ?? 'N/A' }}</td>
                <td class="label">Weight</td>
                <td>{{ $record->weight ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Blood Pressure</td>
                <td>{{ $record->blood_pressure ?? 'N/A' }}</td>
                <td class="label">Temperature</td>
                <td>{{ $record->temperature ?? 'N/A' }} °C</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Symptoms</div>
        <p>{{ $record->symptoms }}</p>
    </div>

    <div class="section">
        <div class="section-title">Diagnosis</div>
        <p>{{ $record->diagnosis }}</p>
    </div>

    @if ($record->treatment_plan)
        <div class="section">
            <div class="section-title">Treatment Plan</div>
            <p>{{ $record->treatment_plan }}</p>
        </div>
    @endif

    @if ($record->prescription)
        <div class="section">
            <div class="section-title">Prescription</div>
            <table>
                <thead>
                    <tr style="background:#f5f7fa;">
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->prescription->medication_details as $medicine)
                        <tr>
                            <td>{{ $medicine['medicine'] }}</td>
                            <td>{{ $medicine['dosage'] }}</td>
                            <td>{{ $medicine['frequency'] }}</td>
                            <td>{{ $medicine['duration'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="signature">
        <p>______________________________</p>
        <strong>Dr. {{ $record->doctor->first_name }} {{ $record->doctor->last_name }}</strong>
        <span>Medical Registration No: {{ $record->doctor->registration_no ?? 'N/A' }}</span><br>
        <span>Date: {{ now()->format('d M Y') }}</span>
    </div>

    <div class="footer">
        This is a system generated medical report from Bhardwaj Hospital.
    </div>

</body>

</html>
