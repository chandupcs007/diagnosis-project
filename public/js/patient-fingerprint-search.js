(() => {
    const config = window.SqaFingerprintSearch || {};
    const scanBtn = document.getElementById('fingerprint-search-btn');
    const statusEl = document.getElementById('fingerprint-search-status');
    const alertEl = document.getElementById('fingerprint-search-alert');

    if (!scanBtn || !config.searchUrl) {
        return;
    }

    function setStatus(message, type = 'muted') {
        if (!statusEl) {
            return;
        }

        statusEl.textContent = message;
        statusEl.className = 'sqa-fingerprint-search-status text-' + (type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'muted'));
    }

    function hideAlert() {
        if (alertEl) {
            alertEl.classList.add('d-none');
            alertEl.innerHTML = '';
        }
    }

    function showAlert(message, type = 'warning') {
        if (!alertEl) {
            return;
        }

        alertEl.className = 'alert alert-' + type + ' py-2 px-3 mb-0 sqa-fingerprint-search-alert';
        alertEl.innerHTML = message;
        alertEl.classList.remove('d-none');
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

    async function captureTemplate() {
        if (config.fingerprintSimulation && config.simulationPatients?.length) {
            const names = config.simulationPatients.map((p, i) => `${i + 1}. ${p.name} (${p.pid})`).join('\n');
            const choice = window.prompt(
                'Simulation mode: enter the number of the patient to identify:\n\n' + names
            );

            if (!choice) {
                return null;
            }

            const index = parseInt(choice, 10) - 1;
            const patient = config.simulationPatients[index];

            if (!patient?.simulation_template) {
                throw new Error('Invalid simulation selection.');
            }

            return patient.simulation_template;
        }

        if (config.fingerprintSimulation) {
            throw new Error('No registered fingerprints available for simulation.');
        }

        return requestFingerprintFromBridge();
    }

    async function searchPatient(template) {
        const response = await fetch(config.searchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ fingerprint_template: template }),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'No matching patient found.');
        }

        return data;
    }

    scanBtn.addEventListener('click', async () => {
        hideAlert();
        scanBtn.disabled = true;
        setStatus('Place finger on scanner...', 'muted');

        try {
            const template = await captureTemplate();

            if (!template) {
                setStatus('Scan cancelled.', 'muted');
                return;
            }

            setStatus('Searching for patient...', 'muted');
            const result = await searchPatient(template);

            setStatus('Patient found. Opening profile...', 'success');
            showAlert(
                `<i class="fas fa-check-circle me-2"></i>Matched <strong>${result.patient.name}</strong> (${result.patient.pid}). Redirecting...`,
                'success'
            );

            window.setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 600);
        } catch (error) {
            setStatus('Scan failed.', 'error');
            showAlert(
                `<i class="fas fa-exclamation-circle me-2"></i>${error.message || 'Unable to identify patient from fingerprint.'}`,
                'warning'
            );
        } finally {
            scanBtn.disabled = false;
        }
    });
})();
