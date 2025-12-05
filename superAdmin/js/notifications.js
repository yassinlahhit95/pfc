// JS para manejar la UI de notificaciones (front-end sólo)
(function () {
  const profesores = [
    { id: 'p1', name: 'Ana Gómez' },
    { id: 'p2', name: 'Carlos Pérez' },
    { id: 'p3', name: 'Lucía Martínez' },
    { id: 'p4', name: 'Javier Ruiz' }
  ];

  const estudiantes = [
    { id: 'e1', name: 'María López' },
    { id: 'e2', name: 'Diego Fernández' },
    { id: 'e3', name: 'Sofía Torres' },
    { id: 'e4', name: 'Andrés Silva' },
    { id: 'e5', name: 'Valeria Ruiz' }
  ];

  const $tipo = document.getElementById('tipoDestinatario');
  const $lista = document.getElementById('listaDestinatarios');
  const $form = document.getElementById('notifForm');
  const $titulo = document.getElementById('titulo');
  const $mensaje = document.getElementById('mensaje');
  const $btnPreview = document.getElementById('btnPreview');
  const $btnSend = document.getElementById('btnSend');
  const $listaNotifs = document.getElementById('listaNotificaciones');

  function populateRecipients() {
    const tipo = $tipo.value;
    $lista.innerHTML = '';

    let items = [];
    if (tipo === 'profesores') items = profesores;
    else if (tipo === 'estudiantes') items = estudiantes;
    else items = profesores.concat(estudiantes);

    items.forEach((it) => {
      const id = document.createElement('div');
      id.style.marginBottom = '6px';
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.id = 'rec_' + it.id;
      checkbox.value = it.id;
      checkbox.dataset.name = it.name;
      const label = document.createElement('label');
      label.htmlFor = checkbox.id;
      label.style.marginLeft = '8px';
      label.textContent = it.name;
      id.appendChild(checkbox);
      id.appendChild(label);
      $lista.appendChild(id);
    });
  }

  function getSelectedRecipients() {
    const checks = Array.from($lista.querySelectorAll('input[type=checkbox]:checked'));
    return checks.map(c => ({ id: c.value, name: c.dataset.name }));
  }

  function saveNotification(obj) {
    const key = 'notificaciones';
    const arr = JSON.parse(localStorage.getItem(key) || '[]');
    arr.unshift(obj);
    localStorage.setItem(key, JSON.stringify(arr));
    renderNotifications();
  }

  function renderNotifications() {
    const arr = JSON.parse(localStorage.getItem('notificaciones') || '[]');
    if (!arr.length) {
      $listaNotifs.innerHTML = '<p style="color:#6b7280">No hay notificaciones guardadas.</p>';
      return;
    }
    $listaNotifs.innerHTML = '';
    arr.forEach((n) => {
      const card = document.createElement('div');
      card.className = 'notification-item';
      card.style.display = 'flex';
      card.style.justifyContent = 'space-between';
      card.style.alignItems = 'center';
      card.style.marginBottom = '10px';
      card.style.padding = '12px';

      const left = document.createElement('div');
      left.style.flex = '1';
      left.innerHTML = `<div style="font-weight:600">${escapeHtml(n.title)}</div>
                        <div style="color:#6b7280">${escapeHtml(n.message)}</div>
                        <div style="font-size:12px;color:#8f9bba;margin-top:6px">Tipo: ${n.tipo} · Destinatarios: ${n.recipients.length}</div>`;

      const actions = document.createElement('div');
      actions.style.display = 'flex';
      actions.style.gap = '8px';

      const sendBtn = document.createElement('button');
      sendBtn.className = 'btn-primary';
      sendBtn.textContent = n.sent ? 'Reenviar' : 'Enviar';
      sendBtn.onclick = () => {
        n.sent = true;
        n.sentAt = new Date().toISOString();
        updateNotification(n.id, n);
        alert('Notificación enviada a ' + n.recipients.length + ' destinatarios.');
      };

      const delBtn = document.createElement('button');
      delBtn.className = 'btn-cancel';
      delBtn.textContent = 'Eliminar';
      delBtn.onclick = () => {
        if (!confirm('Eliminar notificación?')) return;
        deleteNotification(n.id);
      };

      actions.appendChild(sendBtn);
      actions.appendChild(delBtn);

      card.appendChild(left);
      card.appendChild(actions);
      $listaNotifs.appendChild(card);
    });
  }

  function updateNotification(id, updated) {
    const key = 'notificaciones';
    const arr = JSON.parse(localStorage.getItem(key) || '[]');
    const idx = arr.findIndex(x => x.id === id);
    if (idx !== -1) arr[idx] = updated;
    localStorage.setItem(key, JSON.stringify(arr));
    renderNotifications();
  }

  function deleteNotification(id) {
    const key = 'notificaciones';
    const arr = JSON.parse(localStorage.getItem(key) || '[]');
    const next = arr.filter(x => x.id !== id);
    localStorage.setItem(key, JSON.stringify(next));
    renderNotifications();
  }

  function escapeHtml(str) {
    return (str || '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": "&#39;" }[c];
    });
  }

  $tipo.addEventListener('change', populateRecipients);

  $btnPreview.addEventListener('click', () => {
    const titulo = $titulo.value.trim();
    const mensaje = $mensaje.value.trim();
    const recs = getSelectedRecipients();
    if (!titulo || !mensaje) return alert('Rellena título y mensaje para previsualizar.');
    const nombreDest = recs.length ? recs.map(r => r.name).join(', ') : 'Ninguno seleccionado';
    const text = `TÍTULO: ${titulo}\n\nMENSAJE:\n${mensaje}\n\nDESTINATARIOS: ${nombreDest}`;
    alert(text);
  });

  $form.addEventListener('submit', (ev) => {
    ev.preventDefault();
    const titulo = $titulo.value.trim();
    const mensaje = $mensaje.value.trim();
    const tipo = $tipo.value;
    const recipients = getSelectedRecipients();
    if (!titulo || !mensaje) return alert('Título y mensaje son obligatorios.');
    const obj = {
      id: 'n' + Date.now(),
      title: titulo,
      message: mensaje,
      tipo,
      recipients,
      createdAt: new Date().toISOString(),
      sent: false
    };
    saveNotification(obj);
    $form.reset();
    populateRecipients();
    alert('Notificación guardada localmente.');
  });

  $btnSend.addEventListener('click', () => {
    const titulo = $titulo.value.trim();
    const mensaje = $mensaje.value.trim();
    const tipo = $tipo.value;
    const recipients = getSelectedRecipients();
    if (!titulo || !mensaje) return alert('Título y mensaje son obligatorios para enviar.');
    const obj = {
      id: 'n' + Date.now(),
      title: titulo,
      message: mensaje,
      tipo,
      recipients,
      createdAt: new Date().toISOString(),
      sent: true,
      sentAt: new Date().toISOString()
    };
    saveNotification(obj);
    $form.reset();
    populateRecipients();
    alert('Notificación enviada (simulado) y guardada.');
  });

  // Inicialización
  populateRecipients();
  renderNotifications();
})();
