// Aula Digital (recursos): modales de carpeta/archivo, menú contextual,
// y llamadas AJAX a controladores/profesores|estudiantes/aula/. Expone
// window.AulaRecursos para los onclick inline de las vistas.
(function () {
  'use strict';

  // Determine the root path (3 levels up from public/js/aula-recursos.js).
  // document.currentScript is reliable here because the IIFE runs synchronously at parse time.
  const scriptUrl = new URL((document.currentScript || {src: location.href}).src);
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
      const modal = document.getElementById(id);
      if (modal) {
        cerrarMenus();
        modal.classList.add('abierto');
      }
    },
    cerrarModal(id) {
      const modal = document.getElementById(id);
      if (modal) modal.classList.remove('abierto');
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
      const menu = btn._menu;
      if (!menu || !menu.classList.contains('recurso-menu')) return;
      const yaAbierto = menu.classList.contains('abierto');
      cerrarMenus();
      if (yaAbierto) return;
      // Mover al <body> para que no lo recorte el overflow de la tabla
      if (menu.parentNode !== document.body) document.body.appendChild(menu);
      menu.classList.add('abierto');
      const btnRect = btn.getBoundingClientRect();
      const ancho = menu.offsetWidth || 200;
      let izq = btnRect.right - ancho;
      if (izq < 8) izq = 8;
      let top = btnRect.bottom + 6;
      const alto = menu.offsetHeight || 180;
      if (top + alto > window.innerHeight) {
        top = btnRect.top - alto - 6;
        if (top < 8) top = 8;
      }
      menu.style.top = top + 'px';
      menu.style.left = izq + 'px';
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
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = CTRL + 'moverArchivo.php';
      for (const campoNombre in campos) {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = campoNombre;
        inp.value = campos[campoNombre];
        form.appendChild(inp);
      }
      document.body.appendChild(form);
      form.submit();
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
      const textarea = document.createElement('textarea');
      textarea.value = texto;
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      try { document.execCommand('copy'); ok(); } catch (e) { /* noop */ }
      document.body.removeChild(textarea);
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
        const targetEl = localizarElemento(tipo, id);
        if (targetEl) {
          marcarFijado(targetEl, tipo, fijado);
          if (fijado) {
            // Subir el elemento al principio de su lista, sin recargar la página
            const cont = (tipo === 'carpeta') ? targetEl.closest('.recurso-carpetas-grid') : targetEl.closest('tbody');
            if (cont && cont.firstElementChild !== targetEl) cont.insertBefore(targetEl, cont.firstElementChild);
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

    toast(msg, type) {
        if (window.Toast) {
            window.Toast.show(msg, type);
        }
    },

    // ── Confirmación de borrado ──────────────────────────
    confirmar(form, msg) {
      document.getElementById('modalConfirmarTexto').textContent = msg;
      document.getElementById('modalConfirmarBtn').onclick = () => {
        this.cerrarModal('modalConfirmar');
        form.submit();
      };
      this.abrirModal('modalConfirmar');
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

  function seleccionarValor(hiddenId, attr, val) {
    const container = document.querySelector('[data-target="' + hiddenId + '"]');
    if (!container) return;
    container.querySelectorAll('[' + attr + ']').forEach(i => {
      i.classList.toggle('activo', i.getAttribute(attr) === val);
    });
    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = val;
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

  // ── Selectores de color e icono ──────────────────────
  document.addEventListener('click', function(e) {
    // Color
    var swatch = e.target.closest('.swatch[data-color]');
    if (swatch) {
      var colorGrid = swatch.closest('.selector-colores');
      if (!colorGrid) return;
      colorGrid.querySelectorAll('.swatch').forEach(function(sw) { sw.classList.remove('activo'); });
      swatch.classList.add('activo');
      var colorInput = document.getElementById(colorGrid.getAttribute('data-target'));
      if (colorInput) colorInput.value = swatch.getAttribute('data-color');
      return;
    }
    // Icono
    var ico = e.target.closest('.icono-op[data-icono]');
    if (ico) {
      var iconGrid = ico.closest('.selector-iconos');
      if (!iconGrid) return;
      iconGrid.querySelectorAll('.icono-op').forEach(function(opt) { opt.classList.remove('activo'); });
      ico.classList.add('activo');
      var iconInput = document.getElementById(iconGrid.getAttribute('data-target'));
      if (iconInput) iconInput.value = ico.getAttribute('data-icono');
      return;
    }
    // Borrado con confirmación
    var delBtn = e.target.closest('[data-confirmar]');
    if (delBtn && delBtn.type === 'submit') {
      e.preventDefault();
      AulaRecursos.confirmar(delBtn.closest('form'), delBtn.getAttribute('data-confirmar'));
    }
  });

  // ── Drag-and-drop desde el sistema de archivos ──────────────
  (function() {
    var dropZone = document.getElementById('recursoDropZone');
    if (!dropZone) return;

    var depth = 0;

    function tieneArchivos(e) {
      if (!e.dataTransfer || !e.dataTransfer.types) return false;
      return Array.prototype.indexOf.call(e.dataTransfer.types, 'Files') !== -1;
    }

    document.addEventListener('dragenter', function(e) {
      if (!tieneArchivos(e)) return;
      depth++;
      dropZone.classList.add('activo');
    });

    document.addEventListener('dragleave', function(e) {
      if (!tieneArchivos(e) && depth <= 0) return;
      depth--;
      if (depth <= 0) { depth = 0; dropZone.classList.remove('activo'); }
    });

    document.addEventListener('dragover', function(e) {
      if (tieneArchivos(e)) e.preventDefault();
    });

    document.addEventListener('drop', function(e) {
      depth = 0;
      dropZone.classList.remove('activo');
      if (!tieneArchivos(e)) return;
      e.preventDefault();

      var files = e.dataTransfer.files;
      if (!files || !files.length) return;

      var loader = document.getElementById('recursoLoader');
      if (loader) loader.classList.add('activo');

      var fd = new FormData();
      fd.append('subirArchivos', '1');
      fd.append('ajax', '1');
      fd.append('csrf_token', tokenCSRF());
      fd.append('idModulo', moduloActual());
      fd.append('idCarpeta', carpetaActual() || '0');
      for (var i = 0; i < files.length; i++) {
        fd.append('archivos[]', files[i], files[i].name);
      }

      fetch(CTRL + 'subirArchivos.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (loader) loader.classList.remove('activo');
          if (data.ok) {
            AulaRecursos.toast('<i class="fas fa-check-circle"></i> ' + data.msg, 'success');
            setTimeout(function() { window.location.reload(); }, 900);
          } else {
            AulaRecursos.toast('<i class="fas fa-exclamation-triangle"></i> ' + (data.msg || 'No se pudo subir.'), 'error');
          }
        })
        .catch(function() {
          if (loader) loader.classList.remove('activo');
          AulaRecursos.toast('<i class="fas fa-exclamation-triangle"></i> Error al subir. Inténtalo de nuevo.', 'error');
        });
    });
  })();

})();
