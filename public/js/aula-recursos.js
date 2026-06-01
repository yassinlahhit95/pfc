(function () {
  'use strict';

  const CTRL = '../../../controladores/profesores/aula/';
  const CTRL_EST = '../../../controladores/estudiantes/aula/';

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
    mover(id) {
      this._moverId = id;
      this.abrirModal('modalMover');
    },
    confirmarMover(idModulo) {
      const destino = document.getElementById('mvCarpeta').value;
      postRecarga(CTRL + 'moverArchivo.php', {
        id: this._moverId,
        carpeta: destino,
        modulo: idModulo,
        regresar: carpetaActual()
      });
    },
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
    menu(btn) {
      if (btn._menu === undefined) {
        btn._menu = btn.nextElementSibling;
      }
      const m = btn._menu;
      if (!m) return;
      const yaAbierto = m.classList.contains('abierto');
      cerrarMenus();
      if (yaAbierto) return;
      if (m.parentNode !== document.body) {
        document.body.appendChild(m);
      }
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
        this.toast(fijado
          ? '<i class="fas fa-thumbtack"></i> Fijado'
          : '<i class="fas fa-thumbtack"></i> Desfijado');
      });
    },

    // Eliminación DEFINITIVA (POST con recarga: robusto, sin depender de AJAX/JSON)
    borrar(tipo, id, btn) {
      const msg = tipo === 'carpeta'
        ? '¿Eliminar definitivamente esta carpeta y TODO su contenido? Esta acción no se puede deshacer.'
        : '¿Eliminar definitivamente este archivo? Esta acción no se puede deshacer.';
      if (!window.confirm(msg)) return;
      const url = tipo === 'carpeta' ? CTRL + 'borrarCarpeta.php' : CTRL + 'borrarArchivo.php';
      postRecarga(url, { id: id, modulo: moduloActual(), carpeta: carpetaActual() });
    },

    // Marca / desmarca un recurso como favorito (estudiante) in situ
    favorito(idArchivo, btn, origen) {
      origen = origen || 'recursos';
      this.ajax(CTRL_EST + 'toggleFavorito.php', { idArchivo: idArchivo, origen: origen })
        .then(res => {
          if (!res || !res.ok) {
            this.toast('<i class="fas fa-triangle-exclamation"></i> No se pudo actualizar favoritos');
            return;
          }
          cerrarMenus();
          const esFav = (res.favorito === 1 || res.favorito === '1');
          if (origen === 'favoritos' && !esFav) {
            // Ya no es favorito → quitar la fila de la lista de favoritos
            const tr = document.querySelector('tr[data-archivo-id="' + idArchivo + '"]');
            if (tr) {
              tr.style.transition = 'opacity .25s ease';
              tr.style.opacity = '0';
              setTimeout(() => { tr.remove(); comprobarVacio(); }, 250);
            }
            this.toast('<i class="fas fa-star"></i> Quitado de favoritos');
          } else {
            // En la lista de recursos → actualizar la estrella y la etiqueta del menú
            const icono = btn.querySelector('.fa-star');
            if (icono) icono.style.color = esFav ? '#f59e0b' : '';
            actualizarTextoBoton(btn, esFav ? 'Quitar de favoritos' : 'Añadir a favoritos');
            this.toast(esFav
              ? '<i class="fas fa-star"></i> Añadido a favoritos'
              : '<i class="fas fa-star"></i> Quitado de favoritos');
          }
        });
    },

    toast(html) {
      const t = document.getElementById('recursoToast');
      if (!t) return;
      t.innerHTML = html;
      t.classList.add('visible');
      clearTimeout(this._toastT);
      this._toastT = setTimeout(() => t.classList.remove('visible'), 2600);
    },
    loader(mostrar) {
      const l = document.getElementById('recursoLoader');
      if (l) l.classList.toggle('activo', !!mostrar);
    },
    loaderGo() { this.loader(true); return true; }
  };

  // Localiza la tarjeta de carpeta o la fila de archivo por su id
  function localizarElemento(tipo, id) {
    return tipo === 'carpeta'
      ? document.querySelector('.recurso-carpeta[data-drop-carpeta="' + id + '"]')
      : document.querySelector('tr[data-drag-tipo="archivo"][data-drag-id="' + id + '"]');
  }

  // Añade o quita el indicador de "fijado" de una tarjeta/fila
  function marcarFijado(el, tipo, fijado) {
    el.classList.toggle('fijado', fijado); // color distinto para fijados (carpeta o fila de archivo)
    const span = (tipo === 'carpeta')
      ? el.querySelector('.recurso-carpeta-nombre')
      : el.querySelector('.recurso-archivo-nombre > span:last-child');
    if (!span) return;
    let ind = span.querySelector('.recurso-pin-ind');
    if (fijado && !ind) {
      ind = document.createElement('i');
      ind.className = 'fas fa-thumbtack recurso-pin-ind';
      ind.title = 'Fijado';
      span.insertBefore(document.createTextNode(' '), span.firstChild);
      span.insertBefore(ind, span.firstChild);
    } else if (!fijado && ind) {
      ind.remove();
    }
  }

  // Reemplaza el texto de un botón conservando sus iconos hijos
  function actualizarTextoBoton(btn, texto) {
    let nodoTexto = null;
    btn.childNodes.forEach(n => { if (n.nodeType === 3 && n.textContent.trim()) nodoTexto = n; });
    if (nodoTexto) nodoTexto.textContent = ' ' + texto;
    else btn.appendChild(document.createTextNode(' ' + texto));
  }

  // Si ya no quedan carpetas ni archivos, recarga para mostrar el estado vacío
  function comprobarVacio() {
    const hayCarpetas = document.querySelector('.recurso-carpetas-grid .recurso-carpeta');
    const hayArchivos = document.querySelector('.recurso-lista tbody tr');
    if (!hayCarpetas && !hayArchivos) window.location.reload();
  }

  // POST oculto con token CSRF + recarga (operaciones que cambian de contexto: mover)
  function postRecarga(url, datos) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;
    f.style.display = 'none';
    const todo = Object.assign({ csrf_token: tokenCSRF() }, datos);
    for (const k in todo) {
      const i = document.createElement('input');
      i.type = 'hidden';
      i.name = k;
      i.value = todo[k];
      f.appendChild(i);
    }
    document.body.appendChild(f);
    AulaRecursos.loader(true);
    f.submit();
  }

  function cerrarMenus() {
    document.querySelectorAll('.recurso-menu.abierto').forEach(m => m.classList.remove('abierto'));
  }
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
    if (e.target.classList.contains('recurso-visor-overlay')) {
      const id = e.target.id;
      if (id) AulaRecursos.cerrarModal(id);
    }
    // Si pulsan un elemento del menú, ciérralo (con margen para procesar el evento)
    if (e.target.closest('.recurso-menu-item')) {
      setTimeout(cerrarMenus, 300);
    } else if (e.target.closest('.recurso-menu-btn')) {
      return;
    } else if (!e.target.closest('.recurso-menu')) {
      cerrarMenus();
    }
  });

  window.addEventListener('resize', cerrarMenus);
  document.addEventListener('submit', function () { AulaRecursos.loader(true); });
  window.addEventListener('pageshow', function () { AulaRecursos.loader(false); });

  // ── Arrastrar y soltar ─────────────────────────────────────────────────────
  let _arrastrado = null;

  // Overlay animado al arrastrar archivos desde el equipo (sistema operativo)
  let _dropContador = 0;
  function esArrastreArchivos(e) {
    return !!(e.dataTransfer && e.dataTransfer.types && e.dataTransfer.types.includes && e.dataTransfer.types.includes('Files'));
  }
  function mostrarDropZone(mostrar) {
    const dz = document.getElementById('recursoDropZone');
    if (dz) dz.classList.toggle('activo', !!mostrar);
  }
  function ocultarDropZone() { _dropContador = 0; mostrarDropZone(false); }
  document.addEventListener('dragenter', function (e) {
    if (_arrastrado || !esArrastreArchivos(e)) return; // ignora el arrastre interno
    _dropContador++;
    mostrarDropZone(true);
  });
  window.addEventListener('dragend', ocultarDropZone);
  window.addEventListener('blur', ocultarDropZone);
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
  document.addEventListener('dragleave', function (e) {
    const t = e.target.closest('[data-drop-carpeta]'); if (t) t.classList.remove('soltar-aqui');
    if (!_arrastrado && esArrastreArchivos(e)) {
      _dropContador--;
      if (_dropContador <= 0) ocultarDropZone();
    }
  });
  document.addEventListener('drop', function (e) {
    if (_arrastrado) {
      // Mover interno (arrastrar archivo/carpeta sobre una carpeta destino)
      const t = e.target.closest('[data-drop-carpeta]');
      if (!t) return;
      e.preventDefault();
      const destino = t.getAttribute('data-drop-carpeta');
      if (_arrastrado.tipo === 'carpeta' && destino === _arrastrado.id) return;
      const modulo = moduloActual();
      if (_arrastrado.tipo === 'archivo') {
        postRecarga(CTRL + 'moverArchivo.php', {
          id: _arrastrado.id, carpeta: destino, modulo: modulo, regresar: carpetaActual()
        });
      } else {
        postRecarga(CTRL + 'moverCarpeta.php', {
          id: _arrastrado.id, destino: destino, modulo: modulo, regresar: carpetaActual()
        });
      }
    } else {
      // Soltar archivos desde el sistema operativo → abre el modal de subida
      ocultarDropZone();
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        e.preventDefault();
        const uploadModal = document.getElementById('modalSubir');
        if (uploadModal && uploadModal.querySelector('form')) {
          const fileInput = uploadModal.querySelector('input[type="file"]');
          if (fileInput) {
            fileInput.files = e.dataTransfer.files;
            AulaRecursos.abrirModal('modalSubir');
          }
        }
      }
    }
  });
  document.addEventListener('dragover', function (e) {
    if (_arrastrado) {
      const t = e.target.closest('[data-drop-carpeta]');
      if (!t) return;
      if (_arrastrado.tipo === 'carpeta' && t.getAttribute('data-drop-carpeta') === _arrastrado.id) return;
      e.preventDefault();
      if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
      t.classList.add('soltar-aqui');
    } else if (e.dataTransfer && e.dataTransfer.types && e.dataTransfer.types.includes('Files')) {
      e.preventDefault();
      if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy';
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const ok = document.querySelector('.alerta-exito');
    if (ok) setTimeout(function () { ok.classList.add('ocultar'); }, 4500);
  });
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
