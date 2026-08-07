// Widget de Planificación en los dashboards de director/secretaría — añadir y
// marcar/desmarcar tareas sin recargar el dashboard. Usa el propio <script>
// que carga este fichero para leer data-rol-base (admin|secretaria) y así
// construir las URLs de los controladores correspondientes.
(function () {
  var scriptEl = document.currentScript;
  var rolBase = scriptEl ? scriptEl.dataset.rolBase : 'admin';
  var base = '/controladores/' + rolBase + '/planificacion/';

  function tokenInput() {
    return document.querySelector('#plan-widget-add [name="csrf_token"]');
  }
  function csrfToken() {
    var el = tokenInput();
    return el ? el.value : '';
  }
  function setCsrfToken(value) {
    var el = tokenInput();
    if (el && value) el.value = value;
  }

  // Sends one request; on a CSRF failure (token went stale — expired after
  // 1h, or was rotated away by some *other* form submitted elsewhere in the
  // app while this dashboard tab sat open) the guard already hands back a
  // fresh token, so this quietly swaps it in and retries once instead of
  // dead-ending the widget until the user manually reloads the page.
  function postForm(url, params, allowRetry) {
    params.csrf_token = csrfToken();
    var body = Object.keys(params).map(function (k) {
      return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);
    }).join('&');

    return fetch(url, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.new_csrf) setCsrfToken(data.new_csrf);
        if (!data.ok && allowRetry !== false && /token de seguridad/i.test(data.msg || '') && data.new_csrf) {
          return postForm(url, params, false);
        }
        return data;
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var widget = document.getElementById('plan-widget');
    if (!widget) return;

    var lista = document.getElementById('plan-widget-lista');
    var form = document.getElementById('plan-widget-add');
    var input = document.getElementById('plan-widget-input');

    function mostrarVacioSiCorresponde() {
      if (!lista.querySelector('.plan-widget-item')) {
        lista.innerHTML = '<p class="empty-state" id="plan-widget-vacio">Nada pendiente — buen trabajo.</p>';
      }
    }

    function crearFila(id, texto) {
      var row = document.createElement('div');
      row.className = 'plan-widget-item';
      row.dataset.id = id;
      row.innerHTML = '<label class="plan-widget-check"><input type="checkbox" data-plan-toggle="' + id + '"><span class="plan-widget-checkmark"></span></label><span class="plan-widget-texto"></span>';
      row.querySelector('.plan-widget-texto').textContent = texto;
      row.style.opacity = '0';
      lista.appendChild(row);
      requestAnimationFrame(function () {
        row.style.transition = 'opacity .25s';
        row.style.opacity = '1';
      });
    }

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var texto = input.value.trim();
        if (!texto) return;
        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        input.disabled = true;

        postForm(base + 'insertar.php', { texto: texto })
          .then(function (data) {
            btn.disabled = false;
            input.disabled = false;
            if (data.ok) {
              var vacio = document.getElementById('plan-widget-vacio');
              if (vacio) vacio.remove();
              crearFila(data.idPlanTarea, data.texto);
              input.value = '';
              input.focus();
              if (window.Toast) Toast.show('Tarea añadida.', 'success');
            } else if (window.Toast) {
              Toast.show(data.msg || 'No se pudo añadir la tarea.', 'error');
            }
          })
          .catch(function () {
            btn.disabled = false;
            input.disabled = false;
            if (window.Toast) Toast.show('Error de conexión.', 'error');
          });
      });
    }

    lista.addEventListener('change', function (e) {
      var checkbox = e.target.closest('[data-plan-toggle]');
      if (!checkbox) return;
      var row = checkbox.closest('.plan-widget-item');
      var id = checkbox.dataset.planToggle;
      checkbox.disabled = true;

      postForm(base + 'toggle.php', { id: id, completada: '1' })
        .then(function (data) {
          if (data.ok) {
            // Solo el widget de "pendientes" — al completarla, desaparece de aquí
            // (la lista completa, con quién y cuándo, vive en la página Planificación).
            row.style.transition = 'opacity .25s, transform .25s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(6px)';
            setTimeout(function () {
              row.remove();
              mostrarVacioSiCorresponde();
            }, 250);
            if (window.Toast) Toast.show('Tarea completada.', 'success');
          } else {
            checkbox.checked = false;
            checkbox.disabled = false;
            if (window.Toast) Toast.show(data.msg || 'No se pudo actualizar.', 'error');
          }
        })
        .catch(function () {
          checkbox.checked = false;
          checkbox.disabled = false;
          if (window.Toast) Toast.show('Error de conexión.', 'error');
        });
    });
  });
})();
