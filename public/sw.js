const CACHE_NAME = 'financial-app-v1';
const urlsToCache = [
  '/',
  '/home',
  '/dashboard',
  '/transactions',
  '/accounts',
  '/categories',
  '/css/app.css',
  '/css/pwa.css',
  '/js/app.js',
  '/manifest.json',
  '/icons/android-chrome-192x192.png',
  '/icons/android-chrome-512x512.png',
  '/icons/apple-touch-icon.png',
  '/icons/favicon.ico'
];

// Install event - cache resources
self.addEventListener('install', function(event) {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Service Worker: Caching files');
        return cache.addAll(urlsToCache);
      })
      .then(function() {
        console.log('Service Worker: Installation complete');
        return self.skipWaiting();
      })
      .catch(function(error) {
        console.error('Service Worker: Installation failed:', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
  console.log('Service Worker: Activating...');
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('Service Worker: Clearing old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
    .then(function() {
      console.log('Service Worker: Activation complete');
      return self.clients.claim();
    })
  );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', function(event) {
  console.log('Service Worker: Fetching:', event.request.url);

  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // If request is in cache, return it
        if (response) {
          console.log('Service Worker: Serving from cache:', event.request.url);
          return response;
        }

        // If request is not in cache, fetch from network
        console.log('Service Worker: Fetching from network:', event.request.url);
        return fetch(event.request).then(
          function(response) {
            // Check if we received a valid response
            if(!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response since it's a stream
            var responseToCache = response.clone();

            // Add response to cache for future use
            if (event.request.method === 'GET') {
              caches.open(CACHE_NAME)
                .then(function(cache) {
                  cache.put(event.request, responseToCache);
                });
            }

            return response;
          }
        ).catch(function(error) {
          console.log('Service Worker: Network request failed, serving offline page');

          // If it's a navigation request, serve the offline page
          if (event.request.destination === 'document') {
            return caches.match('/');
          }

          // For API requests, return a custom offline response
          if (event.request.url.includes('/api/')) {
            return new Response(
              JSON.stringify({
                success: false,
                message: 'Anda sedang offline. Silakan periksa koneksi internet Anda.',
                offline: true
              }),
              {
                status: 503,
                statusText: 'Service Unavailable',
                headers: {
                  'Content-Type': 'application/json'
                }
              }
            );
          }
        });
      })
  );
});

// Push notification event
self.addEventListener('push', function(event) {
  console.log('Service Worker: Push received');

  const options = {
    body: event.data ? event.data.text() : 'Notifikasi baru dari Financial App',
    icon: '/icons/android-chrome-192x192.png',
    badge: '/icons/favicon-96x96.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Lihat Detail',
        icon: '/icons/checkmark.png'
      },
      {
        action: 'close',
        title: 'Tutup',
        icon: '/icons/xmark.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('Financial App', options)
  );
});

// Notification click event
self.addEventListener('notificationclick', function(event) {
  console.log('Service Worker: Notification click received');

  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});