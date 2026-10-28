(() => {
    const scriptUrl = document.currentScript.src;
    const standalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone;
    let installPrompt = null;

    const panel = document.createElement('aside');
    panel.className = 'pwa-panel';
    panel.setAttribute('aria-label', 'App installation and connection');
    const status = document.createElement('p');
    status.className = 'pwa-status';
    status.setAttribute('role', 'status');
    const installButton = document.createElement('button');
    installButton.type = 'button';
    installButton.className = 'btn btn-outline';
    installButton.textContent = 'Install app';
    installButton.hidden = true;
    const iosHelp = document.createElement('p');
    iosHelp.className = 'pwa-help';
    iosHelp.textContent = 'Install on iPhone or iPad: open in Safari, tap Share, then Add to Home Screen.';
    iosHelp.hidden = standalone || !(/iPad|iPhone|iPod/.test(navigator.userAgent) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1));
    panel.append(status, installButton, iosHelp);
    document.querySelector('.page-content')?.prepend(panel);

    function updateConnection() {
        status.textContent = navigator.onLine ? '' : 'You’re offline. Keep this page open; reconnect before loading or saving records. Offline changes are not queued.';
    }
    updateConnection();
    window.addEventListener('online', updateConnection);
    window.addEventListener('offline', updateConnection);

    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        installPrompt = event;
        installButton.hidden = !!standalone;
    });
    window.addEventListener('appinstalled', () => {
        installPrompt = null;
        installButton.hidden = true;
        iosHelp.hidden = true;
    });
    installButton.addEventListener('click', async () => {
        if (!installPrompt) return;
        const prompt = installPrompt;
        installPrompt = null;
        installButton.disabled = true;
        try {
            await prompt.prompt();
            await prompt.userChoice;
        } catch (error) {
            console.warn('App installation prompt failed.', error);
            status.textContent = 'Could not open installation. Use your browser menu to install the app.';
        } finally {
            installButton.hidden = true;
            installButton.disabled = false;
        }
    });

    if ('serviceWorker' in navigator && window.isSecureContext) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register(new URL('../sw.js', scriptUrl), { updateViaCache: 'none' })
                .catch(error => console.warn('Offline page registration failed.', error));
        });
    }
})();
