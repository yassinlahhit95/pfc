import { initializeApp } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js";
import { mostrarNotificacionUI } from "./notificaciones-ui.js";

const firebaseConfig = {
  apiKey: "AIzaSyA8qBWHhMEQ2DDbpdwRzEaH_6pi6CC7Q4s",
  authDomain: "pfc1-5c23c.firebaseapp.com",
  projectId: "pfc1-5c23c",
  storageBucket: "pfc1-5c23c.firebasestorage.app",
  messagingSenderId: "204025751806",
  appId: "1:204025751806:web:45c4d0f9a705a0083c9daf",
  measurementId: "G-NH8NP9TKHT"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

export async function requestPermissionAndGetToken(userId, userRole) {
    try {
        // Rutas relativas a la raíz del servidor
        const swPath = '/firebase-messaging-sw.js';
        const tokenPath = '/controladores/firebase/guardar_token.php';

        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            // Registramos el SW (si ya existe, el navegador lo gestiona)
            const registration = await navigator.serviceWorker.register(swPath);
            await navigator.serviceWorker.ready;

            const token = await getToken(messaging, { 
                vapidKey: 'BNoCI0P78ggUa8HVX8t4q3uSLeq7PoWZV3dAMuCoNCrkLKQfCKJ6PyhoLy0ZE_kaagS9S9bJzlx-gpElLlVm8y0',
                serviceWorkerRegistration: registration
            });

            if (token) {
                const response = await fetch(tokenPath, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token, userId, userRole })
                });
                const result = await response.json();
                if (result.success) console.log("Token FCM guardado con éxito.");
            }
        }
    } catch (error) {
        console.error("Error en Firebase:", error);
    }
}

onMessage(messaging, (payload) => {
    console.log("¡MENSAJE RECIBIDO DE FIREBASE!", payload);
    
    const titulo = (payload.data && payload.data.title) ? payload.data.title : (payload.notification ? payload.notification.title : "Notificación");
    const mensaje = (payload.data && payload.data.body) ? payload.data.body : (payload.notification ? payload.notification.body : "Nuevo mensaje recibido");

    if (Notification.permission === 'granted') {
        const iconPath = '/public/img/logoSuperAdmin.png';
        new Notification(titulo, {
            body: mensaje,
            icon: iconPath
        });
    }
    
    mostrarNotificacionUI(titulo, mensaje);
});

export { app, messaging };

