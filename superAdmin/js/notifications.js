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

  // Elementos del DOM con IDs en español
  const $tipo = document.getElementById('tipoDestinatario');
  const $lista = document.getElementById('listaDestinatarios');
  const $form = document.getElementById('formularioNotificaciones');
  const $titulo = document.getElementById('titulo');
  const $mensaje = document.getElementById('mensaje');
  const $btnPreview = document.getElementById('botonPrevisualizar');
  // Eliminamos $btnSend porque usaremos el evento submit del formulario
  const $listaNotifs = document.getElementById('listaNotificaciones');

  function populateRecipients() {
    const tipo = $tipo.value;
    $lista.innerHTML = '';

    let items = [];
    if (tipo === 'profesores') items = profesores;
    else if (tipo === 'estudiantes') items = estudiantes;
    else items = profesores.concat(estudiantes);

    items.forEach((it) => {
      const div = document.createElement('div');
      div.style.marginBottom = '6px';
      
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.id = 'rec_' + it.id;
      checkbox.value = it.id;
      checkbox.dataset.name = it.name;
      
      const label = document.createElement('label');
      label.htmlFor = checkbox.id;
      label.style.marginLeft = '8px';
      label.textContent = it.name;
      label.style.cursor = 'pointer';
      label.style.color = '#4b5563';

      div.appendChild(checkbox);
      div.appendChild(label);
      $lista.appendChild(div);
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
      $listaNotifs.innerHTML = '<p style="color:#6b7280; text-align:center; padding:20px;">No hay notificaciones guardadas.</p>';
      return;
    }
    $listaNotifs.innerHTML = '';
    arr.forEach((n) => {
      const card = document.createElement('div');
      card.className = 'item-notificacion';
      card.style.display = 'flex';
      card.style.justifyContent = 'space-between';
      card.style.alignItems = 'center';
      card.style.marginBottom = '10px';
      card.style.padding = '15px';
      card.style.borderBottom = '1px solid #e5e7eb';
      card.style.backgroundColor = '#f9fafb';
      card.style.borderRadius = '8px';

      const left = document.createElement('div');
      left.style.flex = '1';
      left.innerHTML = `<div style="font-weight:700; color:#1f2937; margin-bottom:5px;">${escapeHtml(n.title)}</div>
                        <div style="color:#4b5563; margin-bottom:5px;">${escapeHtml(n.message)}</div>
                        <div style="font-size:12px; color:#6b7280;">
                            <span style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:12px; margin-right:5px;">${n.tipo}</span> 
                            Destinatarios: ${n.recipients.length} · Enviado: ${n.sent ? 'Sí' : 'No'}
                        </div>`;

      const actions = document.createElement('div');
      actions.style.display = 'flex';
      actions.style.gap = '8px';
      actions.style.marginLeft = '15px';

      const sendBtn = document.createElement('button');
      sendBtn.className = 'boton-primario';
      sendBtn.textContent = 'Reenviar';
      sendBtn.style.padding = '6px 12px';
      sendBtn.style.fontSize = '12px';
      sendBtn.onclick = () => {
        n.sent = true;
        n.sentAt = new Date().toISOString();
        updateNotification(n.id, n);
        alert('Notificación reenviada a ' + n.recipients.length + ' destinatarios.');
      };

      const delBtn = document.createElement('button');
      delBtn.className = 'boton-secundario';
      delBtn.textContent = 'Eliminar';
      delBtn.style.padding = '6px 12px';
      delBtn.style.fontSize = '12px';
      delBtn.onclick = () => {
        if (!confirm('¿Eliminar notificación?')) return;
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

  if ($tipo) {
      $tipo.addEventListener('change', populateRecipients);
  }

  if ($btnPreview) {
      $btnPreview.addEventListener('click', () => {
        const titulo = $titulo.value.trim();
        const mensaje = $mensaje.value.trim();
        const recs = getSelectedRecipients();
        if (!titulo || !mensaje) return alert('Rellena título y mensaje para previsualizar.');
        const nombreDest = recs.length ? recs.map(r => r.name).join(', ') : 'Ninguno seleccionado';
        const text = `TÍTULO: ${titulo}\n\nMENSAJE:\n${mensaje}\n\nDESTINATARIOS: ${nombreDest}`;
        alert(text);
      });
  }

  if ($form) {
      $form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const titulo = $titulo.value.trim();
        const mensaje = $mensaje.value.trim();
        const tipo = $tipo.value;
        const recipients = getSelectedRecipients();
        
        if (!titulo || !mensaje) return alert('Título y mensaje son obligatorios.');
        if (recipients.length === 0) return alert('Selecciona al menos un destinatario.');

        const obj = {
          id: 'n' + Date.now(),
          title: titulo,
          message: mensaje,
          tipo,
          recipients,
          createdAt: new Date().toISOString(),
          sent: true, // Enviamos directamente
          sentAt: new Date().toISOString()
        };
        saveNotification(obj);
        $form.reset();
        populateRecipients();
        alert('Notificación enviada correctamente.');
      });
  }

  // Inicialización
  if ($tipo && $listaNotifs) {
      populateRecipients();
      renderNotifications();
  }
})();
