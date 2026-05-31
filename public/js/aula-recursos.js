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
      if (m) m.classList.add('abierto');
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
    }
  };

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
      e.target.classList.remove('abierto');
    }
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
