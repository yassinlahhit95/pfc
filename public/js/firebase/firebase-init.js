import { requestPermissionAndGetToken } from './firebase.js';

window.addEventListener('load', () => {
    const userData = document.getElementById('firebase-user-data');
    if (userData) {
        const userId = userData.dataset.userId;
        const userRole = userData.dataset.userRole;
        if (userId && userRole) {
            requestPermissionAndGetToken(userId, userRole);
        }
    }
});
