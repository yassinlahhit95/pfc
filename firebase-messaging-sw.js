importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyA8qBWHhMEQ2DDbpdwRzEaH_6pi6CC7Q4s",
  authDomain: "pfc1-5c23c.firebaseapp.com",
  projectId: "pfc1-5c23c",
  storageBucket: "pfc1-5c23c.firebasestorage.app",
  messagingSenderId: "204025751806",
  appId: "1:204025751806:web:45c4d0f9a705a0083c9daf"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[SW] Mensaje recibido en segundo plano:', payload);
  
  const notificationTitle = payload.data.title || payload.notification.title;
  const notificationOptions = {
    body: payload.data.body || payload.notification.body,
    icon: '/pfc/public/img/logoSuperAdmin.png',
    badge: '/pfc/public/img/logoSuperAdmin.png'
  };

  return self.registration.showNotification(notificationTitle, notificationOptions);
});
