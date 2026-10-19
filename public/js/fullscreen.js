(function () {
    const STORAGE_KEY = 'diagnosisFullscreen';
    const APP_FULLSCREEN_CLASS = 'diagnosis-app-fullscreen';

    function isFullscreen() {
        return !!(
            document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement
        );
    }

    function requestFullscreen() {
        const el = document.documentElement;
        const request =
            el.requestFullscreen ||
            el.webkitRequestFullscreen ||
            el.mozRequestFullScreen ||
            el.msRequestFullscreen;

        if (!request) {
            return Promise.resolve();
        }

        return Promise.resolve(request.call(el)).catch(function () {});
    }

    function exitFullscreen() {
        const exit =
            document.exitFullscreen ||
            document.webkitExitFullscreen ||
            document.mozCancelFullScreen ||
            document.msExitFullscreen;

        if (!exit || !isFullscreen()) {
            return Promise.resolve();
        }

        return Promise.resolve(exit.call(document)).catch(function () {});
    }

    function isExpanded() {
        return isFullscreen() || document.documentElement.classList.contains(APP_FULLSCREEN_CLASS);
    }

    function enableAppFullscreen() {
        document.documentElement.classList.add(APP_FULLSCREEN_CLASS);
    }

    function disableAppFullscreen() {
        document.documentElement.classList.remove(APP_FULLSCREEN_CLASS);
    }

    function toggleFullscreen() {
        if (isExpanded()) {
            disableFullscreenPreference();
            disableAppFullscreen();
            updateToggleIcon();
            return exitFullscreen();
        }

        enableFullscreenPreference();
        enableAppFullscreen();
        updateToggleIcon();
        return requestFullscreen();
    }

    function enableFullscreenPreference() {
        sessionStorage.setItem(STORAGE_KEY, '1');
    }

    function disableFullscreenPreference() {
        sessionStorage.removeItem(STORAGE_KEY);
    }

    function shouldEnterFullscreen() {
        return (
            sessionStorage.getItem(STORAGE_KEY) === '1' ||
            document.body.dataset.enterFullscreen === '1'
        );
    }

    function updateToggleIcon() {
        const expanded = isExpanded();

        document.querySelectorAll('[data-fullscreen-toggle] i').forEach(function (icon) {
            icon.className = expanded ? 'fas fa-compress fa-sm' : 'fas fa-expand fa-sm';
        });

        document.querySelectorAll('[data-fullscreen-toggle]').forEach(function (button) {
            button.setAttribute('aria-label', expanded ? 'Exit full screen' : 'Enter full screen');
            button.setAttribute('title', expanded ? 'Exit full screen (F11)' : 'Full screen (F11)');
        });
    }

    function enterExpandedMode() {
        enableFullscreenPreference();
        enableAppFullscreen();
        updateToggleIcon();
        return requestFullscreen();
    }

    function exitExpandedMode() {
        disableAppFullscreen();
        updateToggleIcon();
        return exitFullscreen();
    }

    function initAuthFormFullscreen() {
        const form = document.querySelector('form[data-auth-form]');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            enableFullscreenPreference();

            requestFullscreen().finally(function () {
                form.submit();
            });
        });
    }

    function initAuthenticatedFullscreen() {
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'F11') {
                return;
            }

            event.preventDefault();
            toggleFullscreen();
        });

        document.querySelectorAll('[data-fullscreen-toggle]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                toggleFullscreen();
            });
        });

        document.querySelectorAll('form[action*="logout"]').forEach(function (form) {
            form.addEventListener('submit', function () {
                disableFullscreenPreference();
                disableAppFullscreen();
                exitFullscreen();
            });
        });

        document.addEventListener('fullscreenchange', onFullscreenChange);
        document.addEventListener('webkitfullscreenchange', onFullscreenChange);

        if (shouldEnterFullscreen()) {
            enterExpandedMode();
        }

        updateToggleIcon();
    }

    function onFullscreenChange() {
        if (isFullscreen()) {
            enableAppFullscreen();
        } else if (sessionStorage.getItem(STORAGE_KEY) !== '1') {
            disableAppFullscreen();
        }

        updateToggleIcon();
    }

    window.DiagnosisFullscreen = {
        enable: enableFullscreenPreference,
        toggle: toggleFullscreen,
        request: requestFullscreen,
        exit: exitFullscreen,
        init: initAuthenticatedFullscreen,
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (document.body.dataset.authenticated === '1') {
            initAuthenticatedFullscreen();
            return;
        }

        initAuthFormFullscreen();
    });
})();
