(function () {
  'use strict';

  // Determine the root path relative to this script (3 levels up from public/js/aula-recursos.js)
  const scriptUrl = new URL(import.meta.url || document.currentScript.src);
  const appRoot = new URL('../../../', scriptUrl).pathname;

  const CTRL = appRoot + 'controladores/profesores/aula/';
  const CTRL_EST = appRoot + 'controladores/estudiantes/aula/';

  // Lee atributos del breadcrumb (módulo actual, carpeta actual)
  function leerBreadcrumb(attr) {
    const bc = document.querySelector('.recurso-breadcrumb');
    return bc ? (bc.getAttribute(attr) || '') : '';
  }
  // El token CSRF puede estar en el breadcrumb (profesor) o en cualquier
  // elemento con data-csrf (estudiante: tabla de favoritos, etc.)
  function tokenCSRF() {
    const el = document.querySelector('[data-csrf]');
    return el ? (el.getAttribute('data-csrf') || '') : '';
  }
  function moduloActual() { return leerBreadcrumb('data-modulo'); }
  function carpetaActual(){ return leerBreadcrumb('data-carpeta'); }

  const AulaRecursos = {
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
    editarCarpeta(id, nombre, color, icono) {
      document.getElementById('edCarpetaId').value = id;
      document.getElementById('edCarpetaNombre').value = nombre;
      seleccionarValor('edColorCarpeta', 'data-color', color);
      seleccionarValor('edIconoCarpeta', 'data-icono', icono);
      this.abrirModal('modalEditarCarpeta');
    },
    renombrar(id, nombreSinExt) {
      document.getElementById('rnId').value = id;
      document.getElementById('rnNombre').value = nombreSinExt;
      this.abrirModal('modalRenombrar');
    },
    nuevaVersion(id, nombre) {
      document.getElementById('verId').value = id;
      document.getElementById('verNombre').textContent = nombre;
      this.abrirModal('modalVersion');
    },
    // ── Menú contextual de cada recurso ──────────────────
    menu(btn) {
      if (btn._menu === undefined) btn._menu = btn.nextElementSibling;
      const m = btn._menu;
      if (!m || !m.classList.contains('recurso-menu')) return;
      const yaAbierto = m.classList.contains('abierto');
      cerrarMenus();
      if (yaAbierto) return;
      // Mover al <body> para que no lo recorte el overflow de la tabla
      if (m.parentNode !== document.body) document.body.appendChild(m);
      m.classList.add('abierto');
      const r = btn.getBoundingClientRect();
      const ancho = m.offsetWidth || 200;
      let izq = r.right - ancho;
      if (izq < 8) izq = 8;
      let top = r.bottom + 6;
      const alto = m.offsetHeight || 180;
      if (top + alto > window.innerHeight) {
        top = r.top - alto - 6;
        if (top < 8) top = 8;
      }
      m.style.top = top + 'px';
      m.style.left = izq + 'px';
    },

    // ── Mover archivo ────────────────────────────────────
    mover(id) {
      this._moverId = id;
      this.abrirModal('modalMover');
    },
    confirmarMover(idModulo) {
      // moverArchivo.php requiere POST + token CSRF y termina con un redirect,
      // por eso enviamos un formulario real (no fetch/GET).
      const destino = document.getElementById('mvCarpeta').value;
      const campos = {
        csrf_token: tokenCSRF(),
        id: this._moverId,
        carpeta: destino,
        modulo: idModulo,
        regresar: carpetaActual()
      };
      const f = document.createElement('form');
      f.method = 'POST';
      f.action = CTRL + 'moverArchivo.php';
      for (const k in campos) {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = k;
        inp.value = campos[k];
        f.appendChild(inp);
      }
      document.body.appendChild(f);
      f.submit();
    },

    // ── Copiar enlace al portapapeles ────────────────────
    copiarEnlace(url) {
      const abs = new URL(url, window.location.href).href;
      const ok = () => this.toast('<i class="fas fa-check"></i> Enlace copiado');
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(abs).then(ok).catch(() => this._copiaManual(abs, ok));
      } else {
        this._copiaManual(abs, ok);
      }
      cerrarMenus();
    },
    _copiaManual(texto, ok) {
      const ta = document.createElement('textarea');
      ta.value = texto;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); ok(); } catch (e) { /* noop */ }
      document.body.removeChild(ta);
    },

    // ── Visor de documentos ──────────────────────────────
    verDocumento(url, nombre, ext) {
      const cuerpo = document.getElementById('visorCuerpo');
      document.getElementById('visorTitulo').textContent = nombre;
      document.getElementById('visorDescargar').href = url.replace('modo=ver', 'modo=descarga');
      ext = (ext || '').toLowerCase();
      if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
        cuerpo.innerHTML = '<img src="' + url + '" alt="">';
      } else {
        cuerpo.innerHTML = '<iframe src="' + url + '"></iframe>';
      }
      this.abrirModal('modalVisor');
    },

    // ── Acciones AJAX (sin recargar la página) ──────────────────────────────
    ajax(url, datos) {
      const cuerpo = new URLSearchParams();
      cuerpo.append('csrf_token', tokenCSRF());
      cuerpo.append('ajax', '1'); // marcador robusto (sobrevive a proxies que filtran X-Requested-With)
      for (const k in datos) cuerpo.append(k, datos[k]);
      return fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: cuerpo.toString()
      }).then(r => r.json()).catch(() => null);
    },

    // Fijar / desfijar un archivo o carpeta in situ
    pin(tipo, id, btn) {
      this.ajax(CTRL + 'togglePin.php', {
        tipo: tipo, id: id, modulo: moduloActual(), carpeta: carpetaActual()
      }).then(res => {
        if (!res || !res.ok) {
          this.toast('<i class="fas fa-triangle-exclamation"></i> No se pudo cambiar el estado');
          return;
        }
        const fijado = (res.fijado === 1 || res.fijado === '1');
        const label = btn.querySelector('.recurso-pin-label');
        if (label) label.textContent = fijado ? 'Quitar fijado' : 'Fijar';
        const el = localizarElemento(tipo, id);
        if (el) {
          marcarFijado(el, tipo, fijado);
          if (fijado) {
            // Subir el elemento al principio de su lista, sin recargar la página
            const cont = (tipo === 'carpeta') ? el.closest('.recurso-carpetas-grid') : el.closest('tbody');
            if (cont && cont.firstElementChild !== el) cont.insertBefore(el, cont.firstElementChild);
          }
        }
        cerrarMenus();
      });
    },

    favorito(id, btn) {
      this.ajax(CTRL_EST + 'toggleFavorito.php', { id: id }).then(res => {
        if (!res || !res.ok) {
          this.toast('<i class="fas fa-triangle-exclamation"></i> No se pudo actualizar favoritos');
          return;
        }
        const fav = (res.favorito === true || res.favorito === 1 || res.favorito === '1');
        btn.classList.toggle('activo', fav);
        const icon = btn.querySelector('i');
        if (icon) {
          icon.className = fav ? 'fas fa-star' : 'far fa-star';
        }
        this.toast(fav ? 'Añadido a favoritos' : 'Quitado de favoritos');
        cerrarMenus();
      });
    },

    toast(msg) {
        if (window.Toast) {
            window.Toast.show(msg);
        } else {
            // fallback si no está cargado toast.js
            console.log("Toast:", msg);
        }
    }
  };

  // Helper para encontrar el elemento en el DOM según tipo e ID
  function localizarElemento(tipo, id) {
    const sel = (tipo === 'carpeta') ? `[data-id-carpeta="${id}"]` : `[data-id-archivo="${id}"]`;
    return document.querySelector(sel);
  }

  function marcarFijado(el, tipo, fijado) {
    if (tipo === 'carpeta') {
      el.classList.toggle('carpeta-fijada', fijado);
      const pinIcon = el.querySelector('.pin-icon');
      if (pinIcon) pinIcon.style.display = fijado ? 'block' : 'none';
    } else {
      el.classList.toggle('archivo-fijado', fijado);
    }
  }

  function seleccionarValor(id, attr, val) {
    const grid = document.getElementById(id);
    if (!grid) return;
    grid.querySelectorAll('.opcion-item').forEach(i => {
      i.classList.toggle('seleccionado', i.getAttribute(attr) === val);
    });
  }

  function cerrarMenus() {
    document.querySelectorAll('.recurso-menu.abierto').forEach(m => m.classList.remove('abierto'));
  }

  // Eventos globales
  window.AulaRecursos = AulaRecursos;

  // Cerrar modales con clic fuera
  window.addEventListener('click', e => {
    if (e.target.classList.contains('modal-recurso')) {
      AulaRecursos.cerrarModal(e.target.id);
    }
  });

  // Cerrar menús contextuales al hacer clic fuera de ellos
  document.addEventListener('click', e => {
    if (e.target.closest('.recurso-menu-btn') || e.target.closest('.recurso-menu')) return;
    cerrarMenus();
  });
  window.addEventListener('resize', cerrarMenus);

})();
