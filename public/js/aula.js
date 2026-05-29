/* ═══════════════════════════════════════
   AULA DIGITAL — JS principal
═══════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  // ── Carpetas toggle ────────────────────
  document.querySelectorAll('.aula-carpeta-titulo').forEach(function (titulo) {
    titulo.addEventListener('click', function () {
      var carpeta = this.closest('.aula-carpeta');
      carpeta.classList.toggle('abierta');
    });
  });

  // Abrir primera carpeta automáticamente
  var primera = document.querySelector('.aula-carpeta');
  if (primera) primera.classList.add('abierta');

  // ── Drag & Drop Upload ─────────────────
  var zona = document.getElementById('aulaUploadZona');
  var input = document.getElementById('aulaInputArchivos');
  var preview = document.getElementById('aulaPreviewLista');

  if (zona && input) {
    zona.addEventListener('click', function () { input.click(); });

    ['dragenter', 'dragover'].forEach(function (ev) {
      zona.addEventListener(ev, function (e) {
        e.preventDefault();
        zona.classList.add('drag-sobre');
      });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      zona.addEventListener(ev, function (e) {
        e.preventDefault();
        zona.classList.remove('drag-sobre');
      });
    });
    zona.addEventListener('drop', function (e) {
      var dt = e.dataTransfer;
      actualizarInputConArchivos(dt.files);
    });
    input.addEventListener('change', function () {
      actualizarInputConArchivos(this.files);
    });
  }

  function actualizarInputConArchivos(files) {
    if (!preview) return;
    preview.innerHTML = '';
    var permitidos = ['pdf', 'docx', 'txt'];
    var validos = 0;

    Array.from(files).forEach(function (file) {
      var ext = file.name.split('.').pop().toLowerCase();
      var ok  = permitidos.indexOf(ext) !== -1;
      if (ok) validos++;

      var item = document.createElement('div');
      item.className = 'aula-preview-item aula-fade-in';

      var icono = iconoPorExtension(ext);
      item.innerHTML = '<span class="' + (ok ? 'valido' : 'invalido') + '">' +
        icono + ' ' + htmlEscape(file.name) + '</span>' +
        '<span style="color:#94a3b8;font-size:0.7rem;margin-left:auto;">' +
        formatBytes(file.size) + '</span>' +
        (ok ? '' : '<span style="color:#dc2626;font-size:0.7rem;"> ✕ No permitido</span>');
      preview.appendChild(item);
    });

    var aviso = document.getElementById('aulaUploadAviso');
    if (aviso) {
      aviso.textContent = validos + ' archivo(s) listo(s) para subir';
      aviso.style.color = validos > 0 ? '#16a34a' : '#dc2626';
    }
  }

  // ── Notificaciones dropdown ────────────
  var bellBtn = document.getElementById('aulaBell');
  var dropdown = document.getElementById('aulaNotifDropdown');

  if (bellBtn && dropdown) {
    bellBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.classList.toggle('visible');
      if (dropdown.classList.contains('visible')) {
        marcarLeidasAjax();
      }
    });
    document.addEventListener('click', function () {
      dropdown.classList.remove('visible');
    });
    dropdown.addEventListener('click', function (e) { e.stopPropagation(); });
  }

  function marcarLeidasAjax() {
    fetch('/controladores/comunes/aulaMarcarLeidas.php', { method: 'POST' })
      .then(function () {
        var badge = document.querySelector('.aula-notif-badge .contador');
        if (badge) badge.style.display = 'none';
        document.querySelectorAll('.aula-notif-item.no-leida').forEach(function (n) {
          n.classList.remove('no-leida');
        });
      }).catch(function () {});
  }

  // ── Viewer de archivos ─────────────────
  document.querySelectorAll('[data-ver-archivo]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url  = this.dataset.verArchivo;
      var ext  = this.dataset.ext;
      var nombre = this.dataset.nombre || 'archivo';
      abrirViewerAula(url, ext, nombre);
    });
  });

  // ── Utilidades ─────────────────────────
  function iconoPorExtension(ext) {
    if (ext === 'pdf')  return '<i class="fas fa-file-pdf" style="color:#dc2626;"></i>';
    if (ext === 'docx') return '<i class="fas fa-file-word" style="color:#2563eb;"></i>';
    return '<i class="fas fa-file-alt" style="color:#64748b;"></i>';
  }

  function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  function htmlEscape(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
});

// Abre el viewer inline (PDF via pdf.js, TXT inline, DOCX → descarga)
function abrirViewerAula(url, ext, nombre) {
  var modal = document.getElementById('aulaViewerModal');
  var contenedor = document.getElementById('aulaViewerContenedor');
  var titulo = document.getElementById('aulaViewerNombre');

  if (!modal) return;
  titulo.textContent = nombre;
  contenedor.innerHTML = '<p style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>';
  modal.style.display = 'flex';

  if (ext === 'txt') {
    fetch(url)
      .then(function (r) { return r.text(); })
      .then(function (texto) {
        contenedor.innerHTML = '<div class="aula-viewer-txt">' +
          texto.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') +
          '</div>';
      }).catch(function () {
        contenedor.innerHTML = '<p style="padding:20px;color:#dc2626;">No se pudo cargar el archivo.</p>';
      });

  } else if (ext === 'pdf' && typeof pdfjsLib !== 'undefined') {
    abrirPDF(url, nombre);
    modal.style.display = 'none';

  } else {
    contenedor.innerHTML = '<div style="text-align:center;padding:40px;">' +
      '<i class="fas fa-file-word" style="font-size:3rem;color:#2563eb;"></i>' +
      '<p style="margin-top:12px;color:#64748b;">Los archivos DOCX no se pueden previsualizar.</p>' +
      '<a href="' + url + '" download class="boton-primario" style="display:inline-flex;margin-top:12px;">' +
      '<i class="fas fa-download"></i> Descargar</a></div>';
  }
}
