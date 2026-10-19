<div class="sqa-biometrics-section mt-2 mb-4">
    <h3 class="sqa-subsection-header">User Photo</h3>
    <p class="text-muted mb-3">Capture a photo for this user. It will appear in the sidebar when they log in.</p>

    <div class="sqa-biometric-card">
        <div class="sqa-biometric-card-header">
            <i class="fas fa-camera me-2"></i> Profile Photo
        </div>
        <div class="sqa-biometric-card-body">
            <div class="sqa-photo-capture">
                <div class="sqa-photo-preview-wrap">
                    <video id="user-photo-video" class="sqa-photo-video d-none" autoplay playsinline muted></video>
                    <img id="user-photo-preview"
                         src="{{ old('photo_data') ? '' : '' }}"
                         alt="User photo preview"
                         class="sqa-photo-preview d-none">
                    <div id="user-photo-placeholder" class="sqa-photo-placeholder">
                        <i class="fas fa-user-circle"></i>
                        <span>No photo captured</span>
                    </div>
                </div>
                <div class="sqa-photo-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="user-start-camera-btn">
                        <i class="fas fa-video me-1"></i> Open Camera
                    </button>
                    <button type="button" class="btn btn-sm btn-primary d-none" id="user-capture-photo-btn">
                        <i class="fas fa-camera me-1"></i> Capture Photo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="user-retake-photo-btn">
                        <i class="fas fa-redo me-1"></i> Retake
                    </button>
                </div>
                <div id="user-photo-status" class="small text-muted mt-2"></div>
            </div>
            <input type="hidden" name="photo_data" id="user_photo_data" value="{{ old('photo_data') }}">
        </div>
    </div>
</div>

@push('styles')
<style>
    .sqa-subsection-header {
        font-size: 0.95rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--sqa-cyan-dark, #00acc1);
        margin-bottom: 0.5rem;
    }

    .sqa-biometric-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #fff;
        max-width: 420px;
    }

    .sqa-biometric-card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #4e73df;
    }

    .sqa-biometric-card-body {
        padding: 1rem;
    }

    .sqa-photo-preview-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .sqa-photo-video,
    .sqa-photo-preview {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #00acc1;
        background: #000;
    }

    .sqa-photo-placeholder {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 2px dashed #d1d3e2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #858796;
        background: #f8f9fc;
        text-align: center;
        padding: 1rem;
    }

    .sqa-photo-placeholder i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: #b7b9cc;
    }

    .sqa-photo-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/user-photo-capture.js') }}"></script>
@endpush
