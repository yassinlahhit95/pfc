import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";
import { avisoPush } from "./notificaciones-ui.js";

// Datos de mi proyecto en Firebase
var configFB = {
  apiKey: "AIzaSyA8qBWHhMEQ2DDbpdwRzEaH_6pi6CC7Q4s",
  authDomain: "pfc1-5c23c.firebaseapp.com",
  projectId: "pfc1-5c23c",
  storageBucket: "pfc1-5c23c.firebasestorage.app",
  messagingSenderId: "204025751806",
  appId: "1:204025751806:web:45c4d0f9a705a0083c9daf"
};

var appFB = initializeApp(configFB);
var fcm = getMessaging(appFB);

// Para pedir permiso y guardar el token
export async function setupFirebase(id, rol) {
    try {
        var p = await Notification.requestPermission();
        if (p === 'granted') {
            
            // Limpiamos registros viejos
            var regs = await navigator.serviceWorker.getRegistrations();
            for (let r of regs) {
                await r.unregister();
            }

            // Registramos el worker
            var sw = await navigator.serviceWorker.register('../../../firebase-messaging-sw.js');
            await navigator.serviceWorker.ready;

            var t = await getToken(fcm, {
                vapidKey: 'BNoCI0P78ggUa8HVX8t4q3uSLeq7PoWZV3dAMuCoNCrkLKQfCKJ6PyhoLy0ZE_kaagS9S9bJzlx-gpElLlVm8y0',
                serviceWorkerRegistration: sw
            });

            if (t) {
                var fd = new FormData();
                fd.append('token', t);
                fd.append('userId', id);
                fd.append('userRole', rol);

                var r = await fetch('../../../controladores/firebase/guardar_token.php', {
                    method: 'POST',
                    body: fd
                });
                var res = await r.json();
                if (res.success) {
                    console.log("Firebase Push: Token guardado correctamente.");
                }
            }
        }
    } catch (e) {
    }
}

// Cuando llega un mensaje
onMessage(fcm, (p) => {
    var t = (p.data && p.data.title) ? p.data.title : (p.notification ? p.notification.title : "Aviso");
    var m = (p.data && p.data.body) ? p.data.body : (p.notification ? p.notification.body : "Tienes un mensaje nuevo");

    avisoPush(t, m);
});

export { appFB, fcm };

