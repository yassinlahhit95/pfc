// Widget de recordatorios de eventos en el dashboard (admin). Sondea
// notificaciones_recordatorios (ver modelos/notificacionesRecordatorios.php)
// cada 5 minutos — NO confundir con la campana genérica del navbar
// (core/dashboard-shell.js + modelos/notificaciones.php), es un sistema y una
// tabla totalmente distintos.
// Endpoints:
//   GET  /controladores/comunes/notificaciones/obtener.php
//   POST /controladores/comunes/notificaciones/marcar_leido.php
(function (window) {
    var POLL_MS = 5 * 60 * 1000;
    var timer = null;
    var yaMostradas = {}; // ids ya lanzados como toast, para no repetir en cada sondeo

    function resolveAppPath(relPath) {
        if (window.AulaProUtils && window.AulaProUtils.resolveAppPath) {
            return window.AulaProUtils.resolveAppPath(relPath);
        }
        return relPath;
    }

    function csrfToken() {
        if (window.AulaProUtils && window.AulaProUtils.getCSRFToken) {
            return window.AulaProUtils.getCSRFToken();
        }
        var el = document.querySelector('[name="modal_csrf"]');
        return el ? el.value : '';
    }

    function formatearNotificacion(n) {
        var fecha = (n.fechaEvento || '').slice(0, 10);
        var hora = (n.horaEvento || '').slice(0, 5);
        return n.tituloEvento + ': ' + fecha + (hora ? ' ' + hora : '');
    }

    function obtenerNotificaciones() {
        return fetch(resolveAppPath('controladores/comunes/notificaciones/obtener.php'), {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                actualizarUI((data && data.ok && data.notificaciones) || []);
                return data;
            })
            .catch(function () { /* fallo de red silencioso: se reintenta en el próximo sondeo */ });
    }

    function actualizarUI(notificaciones) {
        var badge = document.getElementById('notif-badge');
        if (badge) {
            if (notificaciones.length > 0) {
                badge.textContent = notificaciones.length > 99 ? '99+' : String(notificaciones.length);
                badge.hidden = false;
            } else {
                badge.hidden = true;
            }
        }

        var lista = document.getElementById('lista-notificaciones');
        if (lista) {
            if (!notificaciones.length) {
                lista.innerHTML = '<p class="empty-state">No hay recordatorios pendientes.</p>';
            } else {
                lista.innerHTML = notificaciones.slice(0, 5).map(function (n) {
                    return '<div class="recordatorio-item" data-id="' + n.idNotificacion + '">'
                        + '<div class="recordatorio-item-info">'
                        + '<span class="recordatorio-item-titulo">' + escapeHtml(n.tituloEvento) + '</span>'
                        + '<span class="recordatorio-item-fecha">' + escapeHtml((n.fechaEvento || '').slice(0, 10)) + ' ' + escapeHtml((n.horaEvento || '').slice(0, 5)) + '</span>'
                        + '</div>'
                        + '<button type="button" class="recordatorio-item-marcar" data-marcar-leido="' + n.idNotificacion + '">Marcar leído</button>'
                        + '</div>';
                }).join('');
            }
        }

        notificaciones.slice(0, 3).forEach(function (n) {
            if (yaMostradas[n.idNotificacion]) return;
            yaMostradas[n.idNotificacion] = true;
            if (window.Toast) window.Toast.show(formatearNotificacion(n), 'info');
        });
    }

    function escapeHtml(str) {
        return (window.AulaProUtils && window.AulaProUtils.escapeHtml)
            ? window.AulaProUtils.escapeHtml(str)
            : String(str == null ? '' : str).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
    }

    function marcarLeido(idNotificacion) {
        var body = new URLSearchParams();
        body.set('idNotificacion', idNotificacion);
        body.set('csrf_token', csrfToken());

        return fetch(resolveAppPath('controladores/comunes/notificaciones/marcar_leido.php'), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data && data.ok) {
                    if (window.Toast) window.Toast.show('Recordatorio marcado como leído', 'success');
                    obtenerNotificaciones();
                } else if (window.Toast) {
                    window.Toast.show((data && data.msg) || 'No se pudo marcar como leído', 'error');
                }
                return data;
            });
    }

    function init() {
        obtenerNotificaciones();
        if (timer) clearInterval(timer);
        timer = setInterval(obtenerNotificaciones, POLL_MS);

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-marcar-leido]');
            if (!btn) return;
            e.preventDefault();
            marcarLeido(btn.getAttribute('data-marcar-leido'));
        });
    }

    window.NotificacionesDashboard = {
        init: init,
        obtenerNotificaciones: obtenerNotificaciones,
        actualizarUI: actualizarUI,
        marcarLeido: marcarLeido
    };

    document.addEventListener('DOMContentLoaded', init);
}(window));
