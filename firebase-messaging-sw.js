importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// Force the new SW to activate immediately without waiting for all tabs to close
self.addEventListener('install', function(event) {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function(event) {
    event.waitUntil(clients.claim());
});

firebase.initializeApp({
  apiKey: "AIzaSyA8qBWHhMEQ2DDbpdwRzEaH_6pi6CC7Q4s",
  authDomain: "pfc1-5c23c.firebaseapp.com",
  projectId: "pfc1-5c23c",
  storageBucket: "pfc1-5c23c.firebasestorage.app",
  messagingSenderId: "204025751806",
  appId: "1:204025751806:web:45c4d0f9a705a0083c9daf"
});

var messaging = firebase.messaging();

var ICON = self.location.origin + '/public/imagenes/aulapro.png';

// Background / closed tab: Firebase does NOT auto-show a notification when
// the page is not focused — we must call showNotification explicitly.
messaging.onBackgroundMessage(function(payload) {
  var title = (payload.data && payload.data.title)
    ? payload.data.title
    : (payload.notification ? payload.notification.title : payload.data?.centerName || 'Plataforma');
  var body  = (payload.data && payload.data.body)
    ? payload.data.body
    : (payload.notification ? payload.notification.body : '');

  return self.registration.showNotification(title, {
    body:  body,
    icon:  ICON,
    badge: ICON,
    data:  payload.data || {}
  });
});

// Clicking the notification focuses/opens the app (or a specific URL from the payload)
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  var target = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';
  var targetUrl = new URL(target, self.location.origin).href;

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
      for (var i = 0; i < list.length; i++) {
        if (list[i].url === targetUrl && 'focus' in list[i]) {
          return list[i].focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
