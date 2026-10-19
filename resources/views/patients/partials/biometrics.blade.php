@php
    $biometricsLayout = $layout ?? 'default';
@endphp

<div class="sqa-biometrics-section mt-2 mb-3 {{ $biometricsLayout === 'sidebar' ? 'sqa-biometrics-section--sidebar' : '' }}">
    <h3 class="sqa-subsection-header">Patient Identification</h3>
    <p class="sqa-biometrics-help text-muted mb-2 small">
        Photo and fingerprint help prevent duplicate registrations.
    </p>

    @if($errors->has('duplicate') || session('duplicate_patient'))
        @php $duplicate = session('duplicate_patient'); @endphp
        <div class="alert alert-warning sqa-duplicate-alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Possible duplicate patient detected</strong>
                    <p class="mb-2">{{ $errors->first('duplicate') ?? 'This patient may already be registered.' }}</p>
                    @if($duplicate)
                        <div class="sqa-duplicate-match d-flex align-items-center gap-3">
                            @if(!empty($duplicate['patient']['photo_url']))
                                <img src="{{ $duplicate['patient']['photo_url'] }}"
                                     alt="Existing patient photo"
                                     class="sqa-biometric-preview-photo">
                            @else
                                <div class="sqa-biometric-preview-photo sqa-biometric-preview-photo--empty">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <div><strong>{{ $duplicate['patient']['name'] ?? 'Unknown' }}</strong></div>
                                <div class="small text-muted">ID: {{ $duplicate['patient']['pid'] ?? 'N/A' }}</div>
                                @if(!empty($duplicate['patient']['phone']))
                                    <div class="small text-muted">Phone: {{ $duplicate['patient']['phone'] }}</div>
                                @endif
                                @if(!empty($duplicate['patient']['profile_url']))
                                    <a href="{{ $duplicate['patient']['profile_url'] }}" class="btn btn-sm btn-outline-primary mt-2">
                                        View Existing Patient
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div id="duplicate-live-alert" class="alert alert-warning d-none sqa-duplicate-alert"></div>

    <div class="row g-2 sqa-biometrics-row {{ $biometricsLayout === 'sidebar' ? 'flex-column' : '' }}">
        <div class="{{ $biometricsLayout === 'sidebar' ? 'col-12' : 'col-lg-6' }}">
            <div class="sqa-biometric-card">
                <div class="sqa-biometric-card-header">
                    <i class="fas fa-camera me-2"></i> Patient Photo
                </div>
                <div class="sqa-biometric-card-body">
                    <div class="sqa-photo-capture">
                        <div class="sqa-photo-preview-wrap">
                            <video id="patient-photo-video" class="sqa-photo-video d-none" autoplay playsinline muted></video>
                            <img id="patient-photo-preview"
                                 src="{{ $photoUrl ?? '' }}"
                                 alt="Patient photo preview"
                                 class="sqa-photo-preview {{ empty($photoUrl) ? 'd-none' : '' }}">
                            <div id="patient-photo-placeholder"
                                 class="sqa-photo-placeholder {{ !empty($photoUrl) ? 'd-none' : '' }}">
                                <i class="fas fa-user-circle"></i>
                                <span>No photo captured</span>
                            </div>
                        </div>
                        <div class="sqa-photo-actions">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="start-camera-btn">
                                <i class="fas fa-video me-1"></i> Open Camera
                            </button>
                            <button type="button" class="btn btn-sm btn-primary d-none" id="capture-photo-btn">
                                <i class="fas fa-camera me-1"></i> Capture Photo
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="retake-photo-btn">
                                <i class="fas fa-redo me-1"></i> Retake
                            </button>
                        </div>
                        <div id="photo-status" class="small text-muted mt-2"></div>
                    </div>
                    <input type="hidden" name="photo_data" id="photo_data" value="{{ old('photo_data') }}">
                </div>
            </div>
        </div>

        <div class="{{ $biometricsLayout === 'sidebar' ? 'col-12' : 'col-lg-6' }}">
            <div class="sqa-biometric-card">
                <div class="sqa-biometric-card-header">
                    <i class="fas fa-fingerprint me-2"></i> Fingerprint
                </div>
                <div class="sqa-biometric-card-body">
                    <div class="sqa-fingerprint-capture">
                        <div class="sqa-fingerprint-preview-wrap">
                            <div id="fingerprint-preview" class="sqa-fingerprint-preview {{ !empty($patient) && $patient->hasFingerprint() ? 'is-captured' : '' }}">
                                <i class="fas fa-fingerprint sqa-fingerprint-icon"></i>
                                <span id="fingerprint-status-text">
                                    {{ !empty($patient) && $patient->hasFingerprint() ? 'Fingerprint on file' : 'Place finger on scanner' }}
                                </span>
                            </div>
                        </div>
                        <div class="sqa-fingerprint-actions">
                            <button type="button" class="btn btn-sm btn-primary" id="capture-fingerprint-btn">
                                <i class="fas fa-fingerprint me-1"></i> Scan Fingerprint
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="clear-fingerprint-btn">
                                <i class="fas fa-times me-1"></i> Clear
                            </button>
                            <div id="fingerprint-help" class="sqa-fingerprint-help small text-muted">
                                Scanner or dev simulation.
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="fingerprint_template" id="fingerprint_template" value="{{ old('fingerprint_template') }}">
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .sqa-subsection-header {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--sqa-primary, #1cc88a);
        margin-bottom: 0.25rem;
    }

    .sqa-biometric-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        overflow: hidden;
        height: 100%;
        background: #fff;
    }

    .sqa-biometric-card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.4rem 0.65rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #4e73df;
    }

    .sqa-biometric-card-body {
        padding: 0.65rem;
    }

    .sqa-photo-capture,
    .sqa-fingerprint-capture {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 0.65rem;
    }

    .sqa-photo-preview-wrap,
    .sqa-fingerprint-preview-wrap {
        flex-shrink: 0;
        margin-bottom: 0;
    }

    .sqa-photo-capture .sqa-photo-actions,
    .sqa-fingerprint-capture .sqa-fingerprint-actions {
        flex: 1;
        min-width: 7rem;
        align-self: center;
    }

    .sqa-fingerprint-help {
        margin: 0.15rem 0 0;
        line-height: 1.25;
        font-size: 0.65rem;
    }

    .sqa-photo-video,
    .sqa-photo-preview {
        width: 112px;
        height: 112px;
        object-fit: cover;
        border-radius: 0.35rem;
        border: 2px solid #e3e6f0;
        background: #000;
    }

    .sqa-photo-placeholder,
    .sqa-fingerprint-preview {
        width: 112px;
        height: 112px;
        border-radius: 0.35rem;
        border: 2px dashed #d1d3e2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #858796;
        background: #f8f9fc;
        text-align: center;
        padding: 0.35rem;
        font-size: 0.7rem;
        line-height: 1.2;
    }

    .sqa-photo-placeholder i,
    .sqa-fingerprint-icon {
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
        color: #b7b9cc;
    }

    .sqa-fingerprint-preview.is-captured {
        border-color: #1cc88a;
        border-style: solid;
        background: #eefaf4;
        color: #1cc88a;
    }

    .sqa-fingerprint-preview.is-captured .sqa-fingerprint-icon {
        color: #1cc88a;
    }

    .sqa-fingerprint-preview.is-scanning {
        border-color: #4e73df;
        animation: sqa-pulse 1.2s ease-in-out infinite;
    }

    @keyframes sqa-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.25); }
        50% { box-shadow: 0 0 0 8px rgba(78, 115, 223, 0); }
    }

    .sqa-photo-actions,
    .sqa-fingerprint-actions {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: center;
    }

    .sqa-photo-actions .btn,
    .sqa-fingerprint-actions .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    #photo-status {
        flex: 1 1 100%;
        margin-top: 0 !important;
        font-size: 0.7rem;
    }

    .sqa-biometric-preview-photo {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 0.35rem;
        border: 1px solid #e3e6f0;
    }

    .sqa-biometric-preview-photo--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fc;
        color: #b7b9cc;
    }

    .sqa-biometrics-section--sidebar {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .sqa-biometrics-section--sidebar .sqa-photo-capture,
    .sqa-biometrics-section--sidebar .sqa-fingerprint-capture {
        flex-direction: column;
        align-items: center;
    }

    .sqa-biometrics-section--sidebar .sqa-photo-capture .sqa-photo-actions,
    .sqa-biometrics-section--sidebar .sqa-fingerprint-capture .sqa-fingerprint-actions {
        width: 100%;
        align-items: stretch;
    }

    .sqa-biometrics-section--sidebar .sqa-photo-actions .btn,
    .sqa-biometrics-section--sidebar .sqa-fingerprint-actions .btn {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    window.SqaPatientBiometrics = {
        checkDuplicateUrl: @json(route('patients.check-duplicate')),
        fingerprintBridgeUrl: @json(config('diagnosis.fingerprint_bridge_url')),
        fingerprintBridgeTimeout: @json(config('diagnosis.fingerprint_bridge_timeout')),
        fingerprintSimulation: @json((bool) config('diagnosis.fingerprint_simulation')),
        excludePatientId: @json(isset($patient) ? $patient->id : null),
    };
</script>
<script src="{{ asset('js/patient-biometrics.js') }}"></script>
@endpush
