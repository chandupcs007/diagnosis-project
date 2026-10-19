<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient ID Card — {{ $patient->display_id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sqa-theme.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #eceff1;
            font-family: 'Montserrat', 'Roboto', sans-serif;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .patient-id-print-toolbar {
                display: none !important;
            }

            .patient-id-print-sheet {
                box-shadow: none !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="patient-id-print-toolbar text-center mb-4">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" style="padding: 0.45rem 1rem; border: none; border-radius: 4px; background: #00acc1; color: #fff; cursor: pointer;">
            Print ID Card
        </button>
        <button type="button" class="btn btn-secondary btn-sm ms-2" onclick="window.close()" style="padding: 0.45rem 1rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; cursor: pointer;">
            Close
        </button>
    </div>

    <div class="patient-id-print-sheet">
        <div class="patient-id-card patient-id-card--front">
            <div class="patient-id-card-accent patient-id-card-accent--top"></div>

            <div class="patient-id-card-hospital">{{ strtoupper($hospitalSetting->hospital_name) }}</div>

            <div class="patient-id-card-photo-wrap">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $patient->full_name }}" class="patient-id-card-photo">
                @else
                    <div class="patient-id-card-photo patient-id-card-photo--empty">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="#78909c">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="patient-id-card-name">{{ $patient->full_name }}</div>
            <div class="patient-id-card-role">PATIENT</div>

            <div class="patient-id-card-front-id">
                <div class="patient-id-front-pid">
                    <span class="patient-id-front-pid-label">Patient ID</span>
                    <strong class="patient-id-front-pid-value">{{ $patient->display_id }}</strong>
                </div>
                <div class="patient-id-card-qr-wrap patient-id-card-qr-wrap--front">
                    <div id="patient-id-qr" class="patient-id-card-qr" aria-label="QR code for {{ $patient->display_id }}"></div>
                    <div class="patient-id-qr-caption">Scan Patient ID</div>
                </div>
            </div>

            <div class="patient-id-card-bars">
                <span class="patient-id-bar patient-id-bar--dark"></span>
                <span class="patient-id-bar patient-id-bar--cyan"></span>
                <span class="patient-id-bar patient-id-bar--cyan"></span>
            </div>
        </div>

        <div class="patient-id-card patient-id-card--back">
            <div class="patient-id-card-back-content">
                <div class="patient-id-back-title">Patient Details</div>

                <div class="patient-id-card-details patient-id-card-details--back">
                    <div class="patient-id-field patient-id-field--full">
                        <span class="patient-id-field-label">Full Name</span>
                        <span class="patient-id-field-value">{{ $patient->full_name }}</span>
                    </div>
                    <div class="patient-id-field">
                        <span class="patient-id-field-label">Patient ID</span>
                        <span class="patient-id-field-value">{{ $patient->display_id }}</span>
                    </div>
                    <div class="patient-id-field">
                        <span class="patient-id-field-label">Age</span>
                        <span class="patient-id-field-value">{{ $patient->age }} yrs</span>
                    </div>
                    <div class="patient-id-field">
                        <span class="patient-id-field-label">Date of Birth</span>
                        <span class="patient-id-field-value">{{ $patient->date_of_birth->format('d M Y') }}</span>
                    </div>
                    <div class="patient-id-field">
                        <span class="patient-id-field-label">Gender</span>
                        <span class="patient-id-field-value">{{ ucfirst($patient->gender) }}</span>
                    </div>
                    <div class="patient-id-field patient-id-field--full">
                        <span class="patient-id-field-label">Mobile</span>
                        <span class="patient-id-field-value">{{ $patient->phone ?: '—' }}</span>
                    </div>
                    <div class="patient-id-field patient-id-field--blood patient-id-field--full">
                        <span class="patient-id-field-label">Blood Group</span>
                        <span class="patient-id-field-value">{{ $patient->blood_group ?: '—' }}</span>
                    </div>
                </div>

                <div class="patient-id-back-footer">
                    <p class="patient-id-card-return">
                        {{ $hospitalSetting->return_message ?? config('diagnosis.id_card.return_message') }}<br>
                        or contact <strong>{{ $hospitalSetting->contact_phone }}</strong>.
                    </p>
                    <div class="patient-id-card-contact">
                        <div>{{ $hospitalSetting->address }}</div>
                        <div>{{ $hospitalSetting->website }}</div>
                    </div>
                </div>
            </div>

            <div class="patient-id-card-bars">
                <span class="patient-id-bar patient-id-bar--dark"></span>
                <span class="patient-id-bar patient-id-bar--cyan"></span>
                <span class="patient-id-bar patient-id-bar--cyan"></span>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new QRCode(document.getElementById('patient-id-qr'), {
                text: @json($patient->display_id),
                width: 56,
                height: 56,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
