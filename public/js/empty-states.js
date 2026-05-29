document.addEventListener('DOMContentLoaded', function () {
  const SVG_EMPTY = `
    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="10" y="20" width="60" height="46" rx="6" stroke="#a0aec0" stroke-width="2.5" fill="none"/>
      <path d="M10 34h60" stroke="#a0aec0" stroke-width="2.5"/>
      <path d="M28 34v32" stroke="#a0aec0" stroke-width="1.5" stroke-dasharray="3 3"/>
      <circle cx="40" cy="14" r="6" stroke="#a0aec0" stroke-width="2.5" fill="none"/>
      <path d="M34 52h12M34 44h12M34 60h8" stroke="#a0aec0" stroke-width="2" stroke-linecap="round"/>
    </svg>`;

  document.querySelectorAll('td.vacio').forEach(function (td) {
    const texto = td.textContent.trim();
    const colspan = td.getAttribute('colspan') || 1;
    td.innerHTML = `
      <div class="empty-state">
        ${SVG_EMPTY}
        <p class="empty-state-texto">${texto}</p>
      </div>`;
  });

  document.querySelectorAll('p.texto-suave').forEach(function (p) {
    const t = p.textContent.trim();
    if (
      (t.startsWith('No hay') || t.startsWith('No existen') || t.startsWith('Sin ')) &&
      p.closest('.panel') &&
      !p.closest('.empty-state')
    ) {
      const wrap = document.createElement('div');
      wrap.className = 'empty-state';
      wrap.innerHTML = SVG_EMPTY + `<p class="empty-state-texto">${t}</p>`;
      p.replaceWith(wrap);
    }
  });
});
