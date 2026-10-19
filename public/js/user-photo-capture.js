(() => {
    const photoDataInput = document.getElementById('user_photo_data');
    const video = document.getElementById('user-photo-video');
    const preview = document.getElementById('user-photo-preview');
    const placeholder = document.getElementById('user-photo-placeholder');
    const startCameraBtn = document.getElementById('user-start-camera-btn');
    const capturePhotoBtn = document.getElementById('user-capture-photo-btn');
    const retakePhotoBtn = document.getElementById('user-retake-photo-btn');
    const photoStatus = document.getElementById('user-photo-status');

    if (!photoDataInput || !startCameraBtn) {
        return;
    }

    let mediaStream = null;

    function setPhotoStatus(message, isError = false) {
        if (!photoStatus) {
            return;
        }

        photoStatus.textContent = message;
        photoStatus.classList.toggle('text-danger', isError);
        photoStatus.classList.toggle('text-muted', !isError);
    }

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach((track) => track.stop());
            mediaStream = null;
        }

        if (video) {
            video.classList.add('d-none');
            video.srcObject = null;
        }
    }

    async function startCamera() {
        try {
            stopCamera();
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
            setPhotoStatus('Camera ready. Click Capture Photo.');
        } catch (error) {
            setPhotoStatus('Unable to access camera. Check browser permissions.', true);
        }
    }

    function capturePhoto() {
        if (!video.videoWidth) {
            setPhotoStatus('Camera is not ready yet.', true);
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        preview.src = dataUrl;
        preview.classList.remove('d-none');
        photoDataInput.value = dataUrl;

        stopCamera();
        video.classList.add('d-none');
        placeholder.classList.add('d-none');

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
    }

    startCameraBtn.addEventListener('click', startCamera);
    capturePhotoBtn.addEventListener('click', capturePhoto);
    retakePhotoBtn.addEventListener('click', retakePhoto);

    window.addEventListener('beforeunload', stopCamera);
})();
