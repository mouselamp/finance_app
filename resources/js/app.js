require('./bootstrap');
require('./api');

// Import Alpine.js v2
import 'alpinejs';

// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('Service Worker registered successfully with scope:', registration.scope);

                // Check for updates
                registration.addEventListener('updatefound', function() {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', function() {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New content is available, show notification
                            if (confirm('Aplikasi telah diperbarui. Muat ulang halaman untuk mendapatkan versi terbaru?')) {
                                window.location.reload();
                            }
                        }
                    });
                });
            })
            .catch(function(error) {
                console.error('Service Worker registration failed:', error);
            });
    });
}

// Handle online/offline status
window.addEventListener('online', function() {
    console.log('App is online');
    document.body.classList.remove('offline');
});

window.addEventListener('offline', function() {
    console.log('App is offline');
    document.body.classList.add('offline');
});

// Check initial online status
if (!navigator.onLine) {
    document.body.classList.add('offline');
}

// PWA Install Prompt
let deferredPrompt;
const installPrompt = document.getElementById('pwaInstallPrompt');
const installBtn = document.getElementById('installBtn');
const dismissBtn = document.getElementById('dismissBtn');

window.addEventListener('beforeinstallprompt', function(e) {
    console.log('beforeinstallprompt fired');
    e.preventDefault();
    deferredPrompt = e;

    // Show install prompt after a delay
    setTimeout(() => {
        if (installPrompt) {
            installPrompt.classList.add('show');
        }
    }, 3000);
});

if (installBtn) {
    installBtn.addEventListener('click', function() {
        if (deferredPrompt) {
            console.log('Installing PWA...');
            deferredPrompt.prompt();

            deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                } else {
                    console.log('User dismissed the install prompt');
                }
                deferredPrompt = null;
                installPrompt.classList.remove('show');
            });
        }
    });
}

if (dismissBtn) {
    dismissBtn.addEventListener('click', function() {
        installPrompt.classList.remove('show');
    });
}

// Hide install prompt if already installed
window.addEventListener('appinstalled', function() {
    console.log('PWA was installed');
    installPrompt.classList.remove('show');
});
