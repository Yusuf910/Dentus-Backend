<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            margin-top: 20px;
        }
        .content p {
            margin: 10px 0;
        }
        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .prescription-table th, .prescription-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .prescription-table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Prescription</h1>
        <p><strong>Dr {{ $dr_name }}</strong></p>
        <p><strong>{{ $dr_spec }}</strong></p>
        <p> {{ $clinic_address }}</p>
        <p> {{ $clinic_register }}</p>
        <p><strong>Booking ID:</strong> {{ $booking_id }}</p>
    </div>

    <div class="content">
        <p><strong>Notes:</strong> {{ $notes ?? 'N/A' }}</p>
        <p><strong>Diagnosis:</strong> {{ $diagnosis ?? 'N/A' }}</p>
        <p><strong>Advice:</strong> {{ $advice ?? 'N/A' }}</p>

        <h3>Prescription:</h3>
        <table class="prescription-table">
            <thead>
                <tr>
                    <th>Drug</th>
                    <th>Dosage</th>
                    <th>Duration</th>
                    <th>Repeat</th>
                    <th>Time</th>
                    <th>Taken</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription as $medication)
                <tr>
                    <td>{{ $medication['drug'] }}</td>
                    <td>{{ $medication['dosage'] }}</td>
                    <td>{{ $medication['duration'] }}</td>
                    <td>{{ $medication['repeat'] }}</td>
                    <td>{{ implode(', ', $medication['time']) }}</td>
                    <td>{{ $medication['taken'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
