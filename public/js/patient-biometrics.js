(() => {
    const config = window.SqaPatientBiometrics || {};
    const photoDataInput = document.getElementById('photo_data');
    const fingerprintInput = document.getElementById('fingerprint_template');
    const duplicateAlert = document.getElementById('duplicate-live-alert');
    const patientForm = document.querySelector('form[action*="patients"]');

    let mediaStream = null;
    let duplicateCheckTimer = null;

    const video = document.getElementById('patient-photo-video');
    const preview = document.getElementById('patient-photo-preview');
    const placeholder = document.getElementById('patient-photo-placeholder');
    const startCameraBtn = document.getElementById('start-camera-btn');
    const capturePhotoBtn = document.getElementById('capture-photo-btn');
    const retakePhotoBtn = document.getElementById('retake-photo-btn');
    const photoStatus = document.getElementById('photo-status');

    const captureFingerprintBtn = document.getElementById('capture-fingerprint-btn');
    const clearFingerprintBtn = document.getElementById('clear-fingerprint-btn');
    const fingerprintPreview = document.getElementById('fingerprint-preview');
    const fingerprintStatusText = document.getElementById('fingerprint-status-text');
    const fingerprintHelp = document.getElementById('fingerprint-help');

    function setPhotoStatus(message, isError = false) {
        if (!photoStatus) {
            return;
        }

        photoStatus.textContent = message;
        photoStatus.classList.toggle('text-danger', isError);
        photoStatus.classList.toggle('text-muted', !isError);
    }

    function setFingerprintStatus(message, state = 'idle') {
        if (fingerprintStatusText) {
            fingerprintStatusText.textContent = message;
        }

        if (fingerprintPreview) {
            fingerprintPreview.classList.toggle('is-captured', state === 'captured');
            fingerprintPreview.classList.toggle('is-scanning', state === 'scanning');
        }
    }

    function showDuplicateAlert(matches) {
        if (!duplicateAlert || !matches.length) {
            return;
        }

        const match = matches[0];
        duplicateAlert.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                <div>
                    <strong>Possible duplicate found</strong>
                    <p class="mb-1">${match.message}</p>
                    <div class="small">
                        ${match.patient.name} (${match.patient.pid})
                        ${match.patient.profile_url ? `<a href="${match.patient.profile_url}" class="ms-2">View patient</a>` : ''}
                    </div>
                </div>
            </div>
        `;
        duplicateAlert.classList.remove('d-none');
    }

    function hideDuplicateAlert() {
        if (duplicateAlert) {
            duplicateAlert.classList.add('d-none');
            duplicateAlert.innerHTML = '';
        }
    }

    async function checkDuplicates() {
        if (!config.checkDuplicateUrl || !patientForm) {
            return;
        }

        const payload = {
            first_name: patientForm.querySelector('#first_name')?.value || '',
            last_name: patientForm.querySelector('#last_name')?.value || '',
            date_of_birth: patientForm.querySelector('#date_of_birth')?.value || '',
            phone: patientForm.querySelector('#phone')?.value || '',
            fingerprint_template: fingerprintInput?.value || '',
            exclude_patient_id: config.excludePatientId || '',
        };

        if (!payload.fingerprint_template && !payload.phone && !(payload.first_name && payload.last_name && payload.date_of_birth)) {
            hideDuplicateAlert();
            return;
        }

        try {
            const response = await fetch(config.checkDuplicateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (data.duplicate) {
                showDuplicateAlert(data.matches || []);
            } else {
                hideDuplicateAlert();
            }
        } catch (error) {
            console.error('Duplicate check failed', error);
        }
    }

    function scheduleDuplicateCheck() {
        clearTimeout(duplicateCheckTimer);
        duplicateCheckTimer = setTimeout(checkDuplicates, 400);
    }

    async function startCamera() {
        if (!navigator.mediaDevices?.getUserMedia) {
            setPhotoStatus('Camera is not supported in this browser.', true);
            return;
        }

        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } },
                audio: false,
            });

            video.srcObject = mediaStream;
            video.classList.remove('d-none');
            preview.classList.add('d-none');
            placeholder.classList.add('d-none');

            startCameraBtn.classList.add('d-none');
            capturePhotoBtn.classList.remove('d-none');
            setPhotoStatus('Camera ready. Position the patient and capture.');
        } catch (error) {
            setPhotoStatus('Unable to access camera. Check browser permissions.', true);
        }
    }

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach((track) => track.stop());
            mediaStream = null;
        }

        video.classList.add('d-none');
        video.srcObject = null;
    }

    function capturePhoto() {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 640;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        preview.src = dataUrl;
        preview.classList.remove('d-none');
        photoDataInput.value = dataUrl;

        stopCamera();
        capturePhotoBtn.classList.add('d-none');
        retakePhotoBtn.classList.remove('d-none');
        setPhotoStatus('Photo captured successfully.');
    }

    function retakePhoto() {
        photoDataInput.value = '';
        preview.src = '';
        preview.classList.add('d-none');
        placeholder.classList.remove('d-none');
        retakePhotoBtn.classList.add('d-none');
        startCameraBtn.classList.remove('d-none');
        setPhotoStatus('');
        startCamera();
    }

    async function requestFingerprintFromBridge() {
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), (config.fingerprintBridgeTimeout || 15) * 1000);

        try {
            const response = await fetch(config.fingerprintBridgeUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'capture' }),
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Scanner service unavailable');
            }

            const data = await response.json();
            const template = data.template || data.fingerprint || data.FingerprintTemplate || data.ISO_Template;

            if (!template) {
                throw new Error('Scanner did not return a fingerprint template');
            }

            return template;
        } finally {
            window.clearTimeout(timeout);
        }
    }

    function simulateFingerprintTemplate() {
        const seed = [
            patientForm?.querySelector('#first_name')?.value || 'patient',
            patientForm?.querySelector('#last_name')?.value || 'demo',
            patientForm?.querySelector('#phone')?.value || '0000',
        ].join('|');

        return btoa(unescape(encodeURIComponent(`SIMULATED:${seed}`)));
    }

    async function captureFingerprint() {
        setFingerprintStatus('Waiting for fingerprint scanner...', 'scanning');

        if (captureFingerprintBtn) {
            captureFingerprintBtn.disabled = true;
        }

        try {
            let template = null;

            if (config.fingerprintSimulation) {
                template = simulateFingerprintTemplate();
            } else {
                template = await requestFingerprintFromBridge();
            }

            fingerprintInput.value = template;
            setFingerprintStatus('Fingerprint captured successfully', 'captured');
            clearFingerprintBtn?.classList.remove('d-none');

            if (config.fingerprintSimulation && fingerprintHelp) {
                fingerprintHelp.textContent = 'Simulation mode is active. Each scan generates a unique template for testing.';
            }

            scheduleDuplicateCheck();
        } catch (error) {
            setFingerprintStatus('Scanner not connected. Enable simulation or start your scanner service.', 'idle');

            if (fingerprintHelp) {
                fingerprintHelp.textContent = `Scanner bridge failed (${config.fingerprintBridgeUrl}). Set FINGERPRINT_SIMULATION=true for local testing.`;
            }
        } finally {
            if (captureFingerprintBtn) {
                captureFingerprintBtn.disabled = false;
            }
        }
    }

    function clearFingerprint() {
        fingerprintInput.value = '';
        setFingerprintStatus('Place finger on scanner', 'idle');
        clearFingerprintBtn?.classList.add('d-none');
        hideDuplicateAlert();
    }

    window.SqaFingerprint = {
        onCapture(template) {
            if (!template) {
                return;
            }

            fingerprintInput.value = template;
            setFingerprintStatus('Fingerprint captured successfully', 'captured');
            clearFingerprintBtn?.classList.remove('d-none');
            scheduleDuplicateCheck();
        },
    };

    startCameraBtn?.addEventListener('click', startCamera);
    capturePhotoBtn?.addEventListener('click', capturePhoto);
    retakePhotoBtn?.addEventListener('click', retakePhoto);
    captureFingerprintBtn?.addEventListener('click', captureFingerprint);
    clearFingerprintBtn?.addEventListener('click', clearFingerprint);

    ['#first_name', '#last_name', '#date_of_birth', '#phone'].forEach((selector) => {
        patientForm?.querySelector(selector)?.addEventListener('input', scheduleDuplicateCheck);
        patientForm?.querySelector(selector)?.addEventListener('change', scheduleDuplicateCheck);
    });

    if (fingerprintInput?.value) {
        setFingerprintStatus('Fingerprint ready', 'captured');
        clearFingerprintBtn?.classList.remove('d-none');
    }

    if (photoDataInput?.value) {
        preview.src = photoDataInput.value;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
        retakePhotoBtn?.classList.remove('d-none');
        startCameraBtn?.classList.add('d-none');
        setPhotoStatus('Photo ready from previous attempt.');
    }

    window.addEventListener('beforeunload', stopCamera);
})();
