importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyA8qBWHhMEQ2DDbpdwRzEaH_6pi6CC7Q4s",
  authDomain: "pfc1-5c23c.firebaseapp.com",
  projectId: "pfc1-5c23c",
  storageBucket: "pfc1-5c23c.firebasestorage.app",
  messagingSenderId: "204025751806",
  appId: "1:204025751806:web:45c4d0f9a705a0083c9daf"
});

const messaging = firebase.messaging();

// Manejador en segundo plano
messaging.onBackgroundMessage((p) => {
  console.log('Mensaje en fondo:', p);
  // El navegador ya enseña el aviso si viene el campo notification
});
