<?php
// Widget de calendario mensual para dashboards (admin + secretaría)
// Poblado dinámicamente por public/js/features/calendario.js
?>

<div id="cal-widget-mensual" class="dash-panel" data-rol-base="<?= htmlspecialchars($_SESSION['rol'] ?? 'admin') ?>">
    <div class="cal-encabezado">
        <button data-cal-prev aria-label="Mes anterior">❮</button>
        <div style="flex: 1; text-align: center;">
            <h3 class="cal-mes-titulo">Cargando...</h3>
        </div>
        <button data-cal-next aria-label="Próximo mes">❯</button>
        <button class="cal-crear-btn" data-nuevo-evento>+ Evento</button>
    </div>
    <div class="cal-mes"></div>
</div>
