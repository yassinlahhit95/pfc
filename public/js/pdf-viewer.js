document.addEventListener('DOMContentLoaded', function () {
  if (typeof pdfjsLib === 'undefined') return;

  pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  // Inject modal HTML once
  const modal = document.createElement('div');
  modal.id = 'modalPDF';
  modal.className = 'modal-pdf-overlay';
  modal.style.display = 'none';
  modal.innerHTML = `
    <div class="modal-pdf-contenedor">
      <div class="modal-pdf-cabecera">
        <span class="modal-pdf-nombre" id="pdfNombre">documento.pdf</span>
        <div class="modal-pdf-controles">
          <button id="pdfAnterior" title="Página anterior"><i class="fas fa-chevron-left"></i></button>
          <span id="pdfInfo">— / —</span>
          <button id="pdfSiguiente" title="Página siguiente"><i class="fas fa-chevron-right"></i></button>
        </div>
        <button class="modal-pdf-cerrar" id="pdfCerrar" title="Cerrar"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-pdf-cuerpo" id="pdfCuerpo">
        <p class="modal-pdf-cargando"><i class="fas fa-spinner fa-spin"></i> Cargando PDF...</p>
      </div>
    </div>`;
  document.body.appendChild(modal);

  let pdfDoc = null;
  let pdfPagActual = 1;

  function renderPagina(num) {
    pdfDoc.getPage(num).then(function (page) {
      const cuerpo = document.getElementById('pdfCuerpo');
      cuerpo.innerHTML = '';
      const canvas = document.createElement('canvas');
      cuerpo.appendChild(canvas);

      const vp = page.getViewport({ scale: 1.5 });
      canvas.width  = vp.width;
      canvas.height = vp.height;

      page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });
      pdfPagActual = num;
      document.getElementById('pdfInfo').textContent =
        `${num} / ${pdfDoc.numPages}`;
    });
  }

  window.abrirPDF = function (url, nombre) {
    document.getElementById('pdfNombre').textContent = nombre || 'documento.pdf';
    document.getElementById('pdfCuerpo').innerHTML =
      '<p class="modal-pdf-cargando"><i class="fas fa-spinner fa-spin"></i> Cargando PDF...</p>';
    modal.style.display = 'flex';
    pdfDoc = null;
    pdfPagActual = 1;

    pdfjsLib.getDocument(url).promise.then(function (doc) {
      pdfDoc = doc;
      renderPagina(1);
    }).catch(function () {
      document.getElementById('pdfCuerpo').innerHTML =
        '<p class="modal-pdf-cargando">No se pudo cargar el PDF.</p>';
    });
  };

  document.getElementById('pdfAnterior').addEventListener('click', function () {
    if (pdfDoc && pdfPagActual > 1) renderPagina(pdfPagActual - 1);
  });

  document.getElementById('pdfSiguiente').addEventListener('click', function () {
    if (pdfDoc && pdfPagActual < pdfDoc.numPages) renderPagina(pdfPagActual + 1);
  });

  document.getElementById('pdfCerrar').addEventListener('click', function () {
    modal.style.display = 'none';
    pdfDoc = null;
  });

  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      modal.style.display = 'none';
      pdfDoc = null;
    }
  });
});
