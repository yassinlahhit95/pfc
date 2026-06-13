import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";
import { avisoPush } from "./notificaciones-ui.js";

// Determine the root path relative to this script (3 levels up from public/js/firebase/firebase.js)
const scriptUrl = new URL(import.meta.url);
const appRoot = new URL('../../../', scriptUrl).pathname;

function getFirebaseConfig() {
    const el = document.getElementById('firebase-user-data');
    if (!el) return null;

    return {
        apiKey: el.dataset.apiKey,
        authDomain: el.dataset.authDomain,
        projectId: el.dataset.projectId,
        storageBucket: `${el.dataset.projectId}.firebasestorage.app`,
        messagingSenderId: el.dataset.messagingSenderId,
        appId: el.dataset.appId,
        databaseURL: el.dataset.databaseUrl
    };
}

let appFB, fcm;
const config = getFirebaseConfig();

if (config && config.apiKey && config.appId) {
    appFB = initializeApp(config);
    fcm = getMessaging(appFB);
}

export async function setupFirebase(id, rol) {
    if (!fcm) {
        console.warn("Firebase Messaging not initialized. Check your configuration.");
        return;
    }

    try {
        var p = await Notification.requestPermission();
        if (p !== 'granted') return;

        // Reuse existing SW if already registered
        var regs = await navigator.serviceWorker.getRegistrations();
        var sw = regs.find(function(r) {
            var scriptUrl = (r.active || r.installing || r.waiting || {}).scriptURL || '';
            return scriptUrl.includes('firebase-messaging-sw.js');
        });
        
        if (!sw) {
            // Use appRoot to make the path robust
            sw = await navigator.serviceWorker.register(appRoot + 'firebase-messaging-sw.js');
        }

        // Wait until the SW is active
        if (sw.installing || sw.waiting) {
            await new Promise(function(resolve) {
                var target = sw.installing || sw.waiting;
                target.addEventListener('statechange', function onStateChange() {
                    if (this.state === 'activated') {
                        target.removeEventListener('statechange', onStateChange);
                        resolve();
                    }
                });
            });
        }

        var t = await getToken(fcm, {
            vapidKey: 'BNoCI0P78ggUa8HVX8t4q3uSLeq7PoWZV3dAMuCoNCrkLKQfCKJ6PyhoLy0ZE_kaagS9S9bJzlx-gpElLlVm8y0',
            serviceWorkerRegistration: sw
        });

        if (t) {
            var fd = new FormData();
            fd.append('token', t);
            fd.append('userId', id);
            fd.append('userRole', rol);

            // Use appRoot for the fetch path
            var res = await fetch(appRoot + 'controladores/firebase/guardar_token.php', {
                method: 'POST',
                body: fd
            });
            var json = await res.json();
            if (json.success) {
                console.log("Firebase Push: Token guardado correctamente.");
            }
        }
    } catch (e) {
        console.warn("Firebase setup error:", e);
    }
}

function bumpNotifDot() {
    const dot = document.getElementById('notif-dot');
    if (!dot) return;

    let current = parseInt(dot.dataset.msgs || '0', 10);
    current++;
    
    dot.dataset.msgs = current;
    dot.textContent = current > 9 ? '9+' : current;
    dot.removeAttribute('hidden');
    
    // Animación de pulso opcional
    dot.style.animation = 'none';
    dot.offsetHeight; // trigger reflow
    dot.style.animation = 'pulse 0.5s ease-in-out';
}

if (fcm) {
    onMessage(fcm, function(p) {
        const t = (p.data && p.data.title) ? p.data.title : (p.notification ? p.notification.title : "Aviso");
        const m = (p.data && p.data.body)  ? p.data.body  : (p.notification ? p.notification.body  : "Tienes un mensaje nuevo");

        avisoPush(t, m);
        bumpNotifDot();
    });
}

export { appFB, fcm };
