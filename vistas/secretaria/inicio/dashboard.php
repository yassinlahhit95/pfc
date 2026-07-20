<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);
$nombre = $secretaria['nombreSecretaria'] ?? 'Secretaria';

require_once __DIR__ . "/../../../include/Cache.php";

$stats = Cache::remember('secretaria_dashboard_stats_' . $_SESSION['idSecretaria'], 60, function () {
    $con = obtenerConexion();

    $resultado = mysqli_query($con, "SELECT COUNT(*) AS n FROM estudiantes");
    $totalEstudiantes = $resultado ? (int)(mysqli_fetch_assoc($resultado)['n'] ?? 0) : 0;

    $resultado = mysqli_query($con, "SELECT COUNT(*) AS n FROM pre_matriculas WHERE estado = 'PENDIENTE'");
    $admisionesPendientes = $resultado ? (int)(mysqli_fetch_assoc($resultado)['n'] ?? 0) : 0;

    $resultado = mysqli_query($con, "SELECT COUNT(*) AS n FROM reclamaciones WHERE leido = 0 AND id_parent IS NULL AND ((emisor_rol='estudiante' AND idProfesor IS NULL) OR (emisor_rol='profesor' AND idEstudiante IS NULL))");
    $mensajesSinLeer = $resultado ? (int)(mysqli_fetch_assoc($resultado)['n'] ?? 0) : 0;

    $resultado = mysqli_query($con, "SELECT COUNT(*) AS n FROM anuncios WHERE fechaExpiracion >= CURDATE()");
    $anunciosActivos = $resultado ? (int)(mysqli_fetch_assoc($resultado)['n'] ?? 0) : 0;

    return compact('totalEstudiantes', 'admisionesPendientes', 'mensajesSinLeer', 'anunciosActivos');
});
$totalEstudiantes     = $stats['totalEstudiantes'];
$admisionesPendientes = $stats['admisionesPendientes'];
$mensajesSinLeer      = $stats['mensajesSinLeer'];
$anunciosActivos      = $stats['anunciosActivos'];

$eventos = listarEventosProximos();
$proximosEventos = array_slice($eventos, 0, 5);
$estudiantesPendientes = listarEstudiantesConPagosPendientes();

$con = obtenerConexion(); // reutilizado más abajo para las estadísticas financieras

require_once __DIR__ . "/../../../include/dashboard_helpers.php";
$saludo = saludoHorario();

$titulo_pagina = 'AulaPro — Secretaría';
$seccion = 'inicio';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><?= Security::escapeHtml($saludo) ?>, <?= Security::escapeHtml($nombre) ?></h1>
        <p class="subtitulo-encabezado">Panel de Secretaría</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(79,70,229,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--accent);flex-shrink:0;">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Estudiantes</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $totalEstudiantes ?></div>
        </div>
    </div>
    <?php if (FeatureGuard::check('feature_prematricula')): ?>
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--naranja-suave);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--naranja);flex-shrink:0;">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Admisiones Pendientes</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $admisionesPendientes ?></div>
        </div>
    </div>
    <?php endif; ?>
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--rojo-suave);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--rojo);flex-shrink:0;">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Mensajes Sin Leer</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $mensajesSinLeer ?></div>
        </div>
    </div>
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--verde-suave);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--verde);flex-shrink:0;">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Avisos Activos</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $anunciosActivos ?></div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;flex-wrap:wrap;">
    <!-- Accesos rápidos -->
    <div class="panel">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:16px;color:var(--text);">Accesos rápidos</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <a href="../estudiantes/verEstudiantes.php" class="boton-secundario" style="text-align:center;justify-content:center;">
                <i class="fas fa-user-graduate"></i> Estudiantes
            </a>
            <?php if (FeatureGuard::check('feature_prematricula')): ?>
            <a href="../admisiones/listado.php" class="boton-secundario" style="text-align:center;justify-content:center;">
                <i class="fas fa-graduation-cap"></i> Admisiones
            </a>
            <?php endif; ?>
            <a href="../pagos/verPagos.php" class="boton-secundario" style="text-align:center;justify-content:center;">
                <i class="fas fa-euro-sign"></i> Pagos
            </a>
            <a href="../mensajes/lista.php" class="boton-secundario" style="text-align:center;justify-content:center;">
                <?php if ($mensajesSinLeer > 0): ?>
                    <span class="texto-estado rojo" style="margin-right:4px;"><?= $mensajesSinLeer ?></span>
                <?php endif; ?>
                <i class="fas fa-envelope"></i> Mensajes
            </a>
        </div>
    </div>

    <!-- Próximos eventos -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--text);">Próximos eventos</h2>
            <a href="../eventos/gestionEventos.php" style="font-size:.8rem;color:var(--accent);">Ver todos</a>
        </div>
        <?php if (empty($proximosEventos)): ?>
            <p class="texto-suave" style="font-size:.9rem;">No hay eventos próximos.</p>
        <?php else: ?>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                <?php foreach ($proximosEventos as $evento): ?>
                <li style="display:flex;align-items:flex-start;gap:10px;">
                    <div style="min-width:36px;height:36px;border-radius:8px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:.8rem;">
                        <?= date('d', strtotime($evento['fechaEvento'])) ?><br><small><?= strtoupper(substr(date('M', strtotime($evento['fechaEvento'])), 0, 3)) ?></small>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.9rem;"><?= Security::escapeHtml($evento['tituloEvento']) ?></div>
                        <?php if (!empty($evento['ubicacionEvento'])): ?>
                            <div class="texto-suave" style="font-size:.78rem;"><i class="fas fa-map-marker-alt"></i> <?= Security::escapeHtml($evento['ubicacionEvento']) ?></div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php
// Obtener estadísticas financieras
$rPagos = mysqli_query($con, "
    SELECT 
        SUM(CASE WHEN p.fechaProximoPago >= CURDATE() THEN 1 ELSE 0 END) AS al_dia,
        SUM(CASE WHEN p.fechaProximoPago BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS proximo,
        SUM(CASE WHEN p.fechaProximoPago < CURDATE() AND (p.prorrogaHasta IS NULL OR p.prorrogaHasta < CURDATE()) THEN 1 ELSE 0 END) AS vencido
    FROM pagos p
    INNER JOIN (
        SELECT idEstudiante, MAX(idPago) as max_id
        FROM pagos
        GROUP BY idEstudiante
    ) latest ON p.idPago = latest.max_id
");
$statsPagos = mysqli_fetch_assoc($rPagos);

$rVencidos = mysqli_query($con, "
    SELECT p.*, e.nombreEstudiante, e.emailEstudiante 
    FROM pagos p 
    JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
    INNER JOIN (
        SELECT idEstudiante, MAX(idPago) as max_id
        FROM pagos
        GROUP BY idEstudiante
    ) latest ON p.idPago = latest.max_id
    WHERE p.fechaProximoPago < CURDATE() AND (p.prorrogaHasta IS NULL OR p.prorrogaHasta < CURDATE())
");
$vencidos = [];
if ($rVencidos) {
    while($filaPago = mysqli_fetch_assoc($rVencidos)) {
        $vencidos[] = $filaPago;
    }
}
?>
<div class="panel" style="margin-top:24px;">
    <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:8px;">
        <i class="fas fa-wallet" style="color:var(--verde);"></i> Panel Financiero y Pagos
    </h2>
    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; background:var(--verde-suave); border-left:4px solid var(--verde); padding:16px; border-radius:8px;">
            <div style="font-size:0.8rem; font-weight:700; color:var(--verde-ink); text-transform:uppercase;">Al Día</div>
            <div style="font-size:1.5rem; font-weight:700; color:var(--verde-ink);"><?= (int)$statsPagos['al_dia'] ?> estudiantes</div>
        </div>
        <div style="flex:1; background:var(--naranja-suave); border-left:4px solid var(--naranja); padding:16px; border-radius:8px;">
            <div style="font-size:0.8rem; font-weight:700; color:var(--naranja-ink); text-transform:uppercase;">Próximo a Vencer</div>
            <div style="font-size:1.5rem; font-weight:700; color:var(--naranja-ink);"><?= (int)$statsPagos['proximo'] ?> estudiantes</div>
        </div>
        <div style="flex:1; background:var(--rojo-suave); border-left:4px solid var(--rojo); padding:16px; border-radius:8px;">
            <div style="font-size:0.8rem; font-weight:700; color:var(--rojo-ink); text-transform:uppercase;">Vencidos (Bloqueados)</div>
            <div style="font-size:1.5rem; font-weight:700; color:var(--rojo-ink);"><?= (int)$statsPagos['vencido'] ?> estudiantes</div>
        </div>
    </div>
    
    <?php if (count($vencidos) > 0): ?>
    <h3 style="font-size:0.95rem; font-weight:600; margin-bottom:12px; color:var(--rojo);"><i class="fas fa-exclamation-triangle"></i> Acciones Requeridas: Alumnos Morosos</h3>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
            <thead>
                <tr style="background:var(--surface-2); border-bottom:2px solid var(--border-2); text-align:left;">
                    <th style="padding:10px;">Estudiante</th>
                    <th style="padding:10px;">Fecha Vencimiento</th>
                    <th style="padding:10px;">Comprobante</th>
                    <th style="padding:10px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($vencidos as $pagoVencido): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:10px; font-weight:600;"><?= Security::escapeHtml($pagoVencido['nombreEstudiante']) ?><br><small style="font-weight:normal; color:var(--dim);"><?= Security::escapeHtml($pagoVencido['emailEstudiante']) ?></small></td>
                    <td style="padding:10px; color:var(--rojo); font-weight:600;"><?= date('d/m/Y', strtotime($pagoVencido['fechaProximoPago'])) ?></td>
                    <td style="padding:10px;">
                        <?php if ($pagoVencido['estadoComprobante'] === 'verificando'): ?>
                            <span class="badge badge-ambar"><i class="fas fa-search"></i> Verificando</span>
                        <?php else: ?>
                            <span class="texto-suave">Sin comprobante</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px;">
                        <button onclick="otorgarProrroga(<?= $pagoVencido['idPago'] ?>)" class="boton-secundario btn-pequeno" style="background:var(--rojo-suave); border-color:var(--rojo); color:var(--rojo);">
                            <i class="fas fa-unlock"></i> Otorgar Prórroga (7 Días)
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Panel: Estudiantes con Deuda Pendiente -->
<div class="panel" style="margin-top:24px;">
    <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:8px;">
        <i class="fas fa-file-invoice-dollar" style="color:var(--accent);"></i> Estudiantes con Deuda Pendiente
    </h2>
    <?php if (!empty($estudiantesPendientes)) { ?>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
            <thead>
                <tr style="background:var(--surface-2); border-bottom:2px solid var(--border-2); text-align:left;">
                    <th style="padding:10px;">Estudiante</th>
                    <th style="padding:10px;">Ciclo</th>
                    <th style="padding:10px;">Pagado</th>
                    <th style="padding:10px;">Deuda</th>
                    <th style="padding:10px;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contadorPagos = 0;
                foreach ($estudiantesPendientes as $estudiantePendiente) {
                    if ($contadorPagos >= 8) break;
                ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:10px; font-weight:500;"><?= Security::escapeHtml($estudiantePendiente['nombreEstudiante'] . ' ' . ($estudiantePendiente['apellidosEstudiante'] ?? '')) ?></td>
                    <td style="padding:10px;"><span style="background:color-mix(in oklab, var(--accent) 14%, var(--surface)); color:var(--accent); padding:2px 8px; border-radius:8px; font-size:0.78rem; font-weight:600;"><?= Security::escapeHtml($estudiantePendiente['nombreCiclo']) ?></span></td>
                    <td style="padding:10px; color:var(--verde);"><?= number_format($estudiantePendiente['totalPagado'], 2) ?> €</td>
                    <td style="padding:10px; color:var(--rojo); font-weight:600;"><?= number_format($estudiantePendiente['deuda'], 2) ?> €</td>
                    <td style="padding:10px;">
                        <a href="../pagos/agregarPago.php?idEstudiante=<?= $estudiantePendiente['idEstudiante'] ?>" style="display:inline-block; padding:4px 12px; background:var(--accent); color:var(--accent-ink); border-radius:6px; font-size:0.82rem; text-decoration:none;">Cobrar</a>
                    </td>
                </tr>
                <?php
                $contadorPagos++;
                } ?>
            </tbody>
        </table>
    </div>
    <?php if (count($estudiantesPendientes) > 8) { ?>
        <p style="padding:10px; text-align:center; font-size:0.85rem; color:var(--dim);">Mostrando 8 de <?= count($estudiantesPendientes) ?> estudiantes. <a href="../pagos/verPagos.php">Ver todos →</a></p>
    <?php } ?>
    <?php } else { ?>
        <p style="padding:20px; text-align:center; color:var(--dim);">✅ No hay estudiantes con pagos pendientes. ¡Todos están al día!</p>
    <?php } ?>
</div>

<script>
function otorgarProrroga(idPago) {
    if (!confirm('¿Seguro que deseas otorgar 7 días de prórroga? Esto desbloqueará el acceso del estudiante.')) return;
    
    const formData = new FormData();
    formData.append('idPago', idPago);
    formData.append('csrf_token', $('[name="modal_csrf"]').val());

    fetch('../../../controladores/secretaria/ajax_prorroga.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            alert('Prórroga otorgada exitosamente.');
            window.location.reload();
        } else {
            alert(res.msg || 'Error al otorgar prórroga.');
        }
    });
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
