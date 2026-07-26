<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta"><h3><i class="fas fa-shield-halved"></i> Mis datos (RGPD)</h3></div>
    <div class="fila-datos">
        <div class="nombre-detalle">Exportar mis datos</div>
        <div class="valor-detalle">
            <form method="GET" action="/controladores/comunes/rgpd/exportar_propio.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml(Security::generateCSRFToken()) ?>">
                <button type="submit" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;">
                    <i class="fas fa-download"></i> Descargar mis datos (JSON)
                </button>
            </form>
        </div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Solicitar eliminación de mis datos</div>
        <div class="valor-detalle">
            <form method="POST" action="/controladores/comunes/rgpd/solicitar_baja.php">
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml(Security::generateCSRFToken()) ?>">
                <textarea name="motivo" rows="2" required placeholder="Motivo de tu solicitud…"
                          style="width:100%;padding:8px;border:1px solid var(--border-2);border-radius:8px;background:var(--surface);color:var(--text);margin-bottom:8px;"></textarea>
                <button type="submit" class="boton-peligro" style="font-size:.85rem;padding:6px 14px;">
                    <i class="fas fa-user-slash"></i> Solicitar eliminación
                </button>
            </form>
        </div>
    </div>
</div>
