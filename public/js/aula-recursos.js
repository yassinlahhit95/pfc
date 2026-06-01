/* ============================================================
   AULA DIGITAL · GESTIÓN DE RECURSOS
   Interacciones de UI: visor de documentos, apertura de modales
   (renombrar, versión, mover) y selectores de color/icono.
   ============================================================ */
(function () {
  'use strict';

  const AulaRecursos = {

    // ── Modales ──────────────────────────────────────────
    abrirModal(id) {
      const m = document.getElementById(id);
      if (m) {
        cerrarMenus();
        m.classList.add('abierto');
      }
    },
    cerrarModal(id) {
      const m = document.getElementById(id);
      if (m) m.classList.remove('abierto');
      if (id === 'modalVisor') document.getElementById('visorCuerpo').innerHTML = '';
    },

    // ── Editar carpeta (rellena el modal) ────────────────
    editarCarpeta(id, nombre, color, icono) {
      document.getElementById('edCarpetaId').value = id;
      document.getElementById('edCarpetaNombre').value = nombre;
      seleccionarValor('edColorCarpeta', 'data-color', color);
      seleccionarValor('edIconoCarpeta', 'data-icono', icono);
      this.abrirModal('modalEditarCarpeta');
    },

    // ── Renombrar archivo ────────────────────────────────
    renombrar(id, nombreSinExt) {
      document.getElementById('rnId').value = id;
      document.getElementById('rnNombre').value = nombreSinExt;
      this.abrirModal('modalRenombrar');
    },

    // ── Nueva versión ────────────────────────────────────
    nuevaVersion(id, nombre) {
      document.getElementById('verId').value = id;
      document.getElementById('verNombre').textContent = nombre;
      this.abrirModal('modalVersion');
    },

    // ── Mover archivo ────────────────────────────────────
    mover(id) {
      this._moverId = id;
      this.abrirModal('modalMover');
    },
    confirmarMover(idModulo) {
      const destino = document.getElementById('mvCarpeta').value;
      window.location.href = '../../../controladores/profesores/aula/moverArchivo.php?id=' +
        this._moverId + '&carpeta=' + destino + '&modulo=' + idModulo;
    },

    // ── Visor de documentos ──────────────────────────────
    verDocumento(url, nombre, ext) {
      const cuerpo = document.getElementById('visorCuerpo');
      document.getElementById('visorTitulo').textContent = nombre;
      document.getElementById('visorDescargar').href = url.replace('modo=ver', 'modo=descarga');
      ext = (ext || '').toLowerCase();
      if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
        cuerpo.innerHTML = '<img src="' + url + '" alt="' + nombre + '">';
      } else {
        cuerpo.innerHTML = '<iframe src="' + url + '"></iframe>';
      }
      this.abrirModal('modalVisor');
    },

    // ── Menú contextual "…" (estilo Teams) ───────────────
    menu(btn) {
      if (!btn._menu) {
        btn._menu = btn.nextElementSibling; // .recurso-menu
      }
      const m = btn._menu;
      if (!m) return;
      
      const yaAbierto = m.classList.contains('abierto');
      cerrarMenus();
      if (yaAbierto) return;

      // Teletransportar al body para evitar recortes (overflow) y problemas de posición (transforms)
      if (m.parentNode !== document.body) {
        document.body.appendChild(m);
      }

      m.classList.add('abierto');
      
      // Posición fija respecto al botón (viewport coordinates)
      const r = btn.getBoundingClientRect();
      const ancho = m.offsetWidth;
      let izq = r.right - ancho;
      if (izq < 8) izq = 8;
      
      // Ajustar si se sale por abajo
      let top = r.bottom + 6;
      const alto = m.offsetHeight;
      if (top + alto > window.innerHeight - 8) {
        top = r.top - alto - 6;
      }

      m.style.top  = top + 'px';
      m.style.left = izq + 'px';
    },

    // ── Copiar enlace (respeta permisos en el servidor) ──
    copiarEnlace(rel) {
      cerrarMenus();
      const url = new URL(rel, window.location.href).href;
      const ok = () => AulaRecursos.toast('<i class="fas fa-check"></i> Enlace copiado al portapapeles');
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(ok).catch(() => window.prompt('Copia el enlace:', url));
      } else {
        window.prompt('Copia el enlace:', url);
      }
    },

    // ── Aviso flotante (toast) ───────────────────────────
    toast(html) {
      const t = document.getElementById('recursoToast');
      if (!t) return;
      t.innerHTML = html;
      t.classList.add('visible');
      clearTimeout(this._toastT);
      this._toastT = setTimeout(() => t.classList.remove('visible'), 2600);
    },

    // ── Indicador de carga ───────────────────────────────
    loader(mostrar) {
      const l = document.getElementById('recursoLoader');
      if (l) l.classList.toggle('activo', !!mostrar);
    },
    loaderGo() { this.loader(true); return true; }
  };

  // Cierra todos los menús contextuales abiertos
  function cerrarMenus() {
    document.querySelectorAll('.recurso-menu.abierto').forEach(m => m.classList.remove('abierto'));
  }

  // Selecciona visualmente un valor en un grupo selector y vuelca al input oculto
  function seleccionarValor(inputId, attr, valor) {
    const grupo = document.querySelector('[data-target="' + inputId + '"]');
    if (!grupo) return;
    grupo.querySelectorAll('[' + attr + ']').forEach(el => {
      const activo = el.getAttribute(attr) === valor;
      el.classList.toggle('activo', activo);
    });
    const input = document.getElementById(inputId);
    if (input) input.value = valor;
  }

  // Inicializa los selectores de color e icono (delegación de clics)
  document.addEventListener('click', function (e) {
    const sw = e.target.closest('.selector-colores .swatch');
    if (sw) {
      const grupo = sw.closest('.selector-colores');
      grupo.querySelectorAll('.swatch').forEach(s => s.classList.remove('activo'));
      sw.classList.add('activo');
      const input = document.getElementById(grupo.getAttribute('data-target'));
      if (input) input.value = sw.getAttribute('data-color');
    }
    const ic = e.target.closest('.selector-iconos .icono-op');
    if (ic) {
      e.preventDefault();
      const grupo = ic.closest('.selector-iconos');
      grupo.querySelectorAll('.icono-op').forEach(s => s.classList.remove('activo'));
      ic.classList.add('activo');
      const input = document.getElementById(grupo.getAttribute('data-target'));
      if (input) input.value = ic.getAttribute('data-icono');
    }
    // Cerrar overlay al pulsar el fondo
    if (e.target.classList.contains('recurso-visor-overlay')) {
      const id = e.target.id;
      if (id) AulaRecursos.cerrarModal(id);
    }
    // Cerrar los menús contextuales si se hace clic fuera del botón "…" y fuera del propio menú
    if (!e.target.closest('.recurso-menu-btn') && !e.target.closest('.recurso-menu')) {
      cerrarMenus();
    }
  });

  // Cerrar menús al hacer scroll o redimensionar (su posición es fija)
  document.addEventListener('scroll', cerrarMenus, true);
  window.addEventListener('resize', cerrarMenus);

  // Mostrar el loader al enviar cualquier formulario (subida, crear, editar, mover…)
  document.addEventListener('submit', function () { AulaRecursos.loader(true); });
  // Ocultar el loader al volver a la página (incluido el botón Atrás / bfcache)
  window.addEventListener('pageshow', function () { AulaRecursos.loader(false); });

  // ── Arrastrar y soltar (mover archivos y carpetas) ───
  let _arrastrado = null;
  document.addEventListener('dragstart', function (e) {
    const el = e.target.closest('[data-drag-id]');
    if (!el) return;
    _arrastrado = { tipo: el.getAttribute('data-drag-tipo'), id: el.getAttribute('data-drag-id') };
    el.classList.add('arrastrando');
    if (e.dataTransfer) { e.dataTransfer.effectAllowed = 'move'; try { e.dataTransfer.setData('text/plain', _arrastrado.id); } catch (_) {} }
    cerrarMenus();
  });
  document.addEventListener('dragend', function (e) {
    const el = e.target.closest('[data-drag-id]'); if (el) el.classList.remove('arrastrando');
    document.querySelectorAll('.soltar-aqui').forEach(x => x.classList.remove('soltar-aqui'));
    _arrastrado = null;
  });
  document.addEventListener('dragover', function (e) {
    if (!_arrastrado) return;
    const t = e.target.closest('[data-drop-carpeta]');
    if (!t) return;
    // No permitir soltar una carpeta sobre sí misma
    if (_arrastrado.tipo === 'carpeta' && t.getAttribute('data-drop-carpeta') === _arrastrado.id) return;
    e.preventDefault();
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
    t.classList.add('soltar-aqui');
  });
  document.addEventListener('dragleave', function (e) {
    const t = e.target.closest('[data-drop-carpeta]'); if (t) t.classList.remove('soltar-aqui');
  });
  document.addEventListener('drop', function (e) {
    if (!_arrastrado) return;
    const t = e.target.closest('[data-drop-carpeta]');
    if (!t) return;
    e.preventDefault();
    const destino = t.getAttribute('data-drop-carpeta'); // "0" = raíz
    if (_arrastrado.tipo === 'carpeta' && destino === _arrastrado.id) return;
    const bc = document.querySelector('.recurso-breadcrumb');
    const modulo = bc ? (bc.getAttribute('data-modulo') || '') : '';
    const ctrl = _arrastrado.tipo === 'archivo'
      ? '../../../controladores/profesores/aula/moverArchivo.php?id=' + _arrastrado.id + '&carpeta=' + destino + '&modulo=' + modulo
      : '../../../controladores/profesores/aula/moverCarpeta.php?id=' + _arrastrado.id + '&destino=' + destino + '&modulo=' + modulo;
    AulaRecursos.loader(true);
    window.location.href = ctrl;
  });

  // Auto-ocultar los avisos de éxito a los pocos segundos
  document.addEventListener('DOMContentLoaded', function () {
    const ok = document.querySelector('.alerta-exito');
    if (ok) setTimeout(function () { ok.classList.add('ocultar'); }, 4500);
  });

  // Enter dentro de un modal abierto envía su formulario (excepto en textarea/file)
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    const campo = e.target;
    if (campo.tagName === 'TEXTAREA' || (campo.tagName === 'INPUT' && campo.type === 'file')) return;
    if (!campo.closest('.recurso-visor-overlay.abierto')) return;
    const form = campo.closest('form');
    if (form) {
      e.preventDefault();
      if (typeof form.requestSubmit === 'function') form.requestSubmit();
      else form.submit();
    }
  });

  window.AulaRecursos = AulaRecursos;
})();
