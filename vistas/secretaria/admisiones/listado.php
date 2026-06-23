<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/admisiones.php";
$admisiones = listarPreMatriculas();

$pendientes = array_filter($admisiones, fn($a) => in_array($a['estado'], ['PENDIENTE','EN_REVISION']));
$admitidos  = array_filter($admisiones, fn($a) => $a['estado'] === 'ADMITIDO');

$titulo_pagina = 'AULAPRO | ADMISIONES';
$seccion = 'admisiones';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-graduation-cap"></i> Solicitudes de Admisión</h1>
        <p class="subtitulo-encabezado">Gestiona las solicitudes de acceso al centro</p>
    </div>
    <button onclick="window.location.reload()" class="boton-secundario">
        <i class="fas fa-sync-alt"></i> Actualizar
    </button>
</div>

<!-- Stat cards -->
<div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <?php
    $stats = [
        ['label'=>'Total recibidas', 'value'=>count($admisiones),  'icon'=>'fa-file-signature', 'color'=>'var(--accent)'],
        ['label'=>'Pendientes',      'value'=>count($pendientes),  'icon'=>'fa-clock',          'color'=>'#f59e0b'],
        ['label'=>'Admitidos',       'value'=>count($admitidos),   'icon'=>'fa-user-check',     'color'=>'#10b981'],
    ];
    foreach ($stats as $s): ?>
    <div class="panel" style="flex:1;min-width:160px;display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:color-mix(in srgb,<?= $s['color'] ?> 12%,transparent);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:<?= $s['color'] ?>;flex-shrink:0;">
            <i class="fas <?= $s['icon'] ?>"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;"><?= $s['label'] ?></div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $s['value'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="panel">
    <?php if (empty($admisiones)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-graduation-cap"></i></div>
            <div class="panel-vacio-titulo">Sin solicitudes</div>
            <div class="panel-vacio-desc">No se han recibido solicitudes de admisión todavía.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAdmisiones">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Solicitante</th>
                    <th>Email</th>
                    <th>Ciclo</th>
                    <th>Estado</th>
                    <th style="width:160px;">Cambiar estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admisiones as $a):
                    $estadoClase = match($a['estado']) {
                        'PENDIENTE'   => 'naranja',
                        'EN_REVISION' => 'azul',
                        'ADMITIDO'    => 'verde',
                        'RECHAZADO'   => 'rojo',
                        default       => 'gris'
                    };
                ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($a['fechaSolicitud'])) ?></td>
                    <td><b><?= Security::escapeHtml($a['nombre'] . ' ' . $a['apellidos']) ?></b></td>
                    <td><?= Security::escapeHtml($a['email']) ?></td>
                    <td><?= Security::escapeHtml($a['nombreCiclo']) ?></td>
                    <td><span class="texto-estado <?= $estadoClase ?>"><?= Security::escapeHtml($a['estado']) ?></span></td>
                    <td>
                        <form class="form-estado-admision" data-id="<?= (int)$a['idPreMatricula'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <select name="estado" class="select-estado-admision" style="font-size:.8rem;padding:4px 8px;">
                                <option value="EN_REVISION"  <?= $a['estado']==='EN_REVISION' ? 'selected':'' ?>>En revisión</option>
                                <option value="ADMITIDO"     <?= $a['estado']==='ADMITIDO'    ? 'selected':'' ?>>Admitido</option>
                                <option value="RECHAZADO"    <?= $a['estado']==='RECHAZADO'   ? 'selected':'' ?>>Rechazado</option>
                                <option value="SUBSANACION"  <?= $a['estado']==='SUBSANACION' ? 'selected':'' ?>>Subsanación</option>
                            </select>
                            <button type="submit" class="boton-primario" style="padding:4px 10px;font-size:.8rem;">Aplicar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaAdmisiones', 15);

document.querySelectorAll('.form-estado-admision').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var id     = this.dataset.id;
        var estado = this.querySelector('select').value;
        var csrf   = this.querySelector('[name="csrf_token"]').value;
        $.ajax({
            url: '../../../controladores/secretaria/admisiones/acciones.php?action=update_status',
            method: 'POST',
            data: { idPreMatricula: id, estado: estado, observaciones: '', csrf_token: csrf },
            dataType: 'json',
            success: function(res) {
                if (window.Toast) Toast.show(res.msg, res.ok ? 'success' : 'error');
                if (res.ok) setTimeout(() => location.reload(), 1200);
            }
        });
    });
});
</script>
