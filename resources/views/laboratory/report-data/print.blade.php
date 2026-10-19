<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab Report — {{ $labRequest->request_number }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sqa-theme.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #fff;
            font-family: 'Roboto', sans-serif;
        }

        @media print {
            body { padding: 0; }
            .lab-report-print-actions { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="lab-report-print-actions text-end mb-3">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print Report
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.close()">Close</button>
    </div>

    @include('laboratory.partials.report-content', ['labRequest' => $labRequest])

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>
