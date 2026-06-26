<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);
$nombre = $secretaria['nombreSecretaria'] ?? 'Secretaria';

$con = obtenerConexion();

$r = mysqli_query($con, "SELECT COUNT(*) AS n FROM estudiantes");
$totalEstudiantes = $r ? (int)(mysqli_fetch_assoc($r)['n'] ?? 0) : 0;

$r = mysqli_query($con, "SELECT COUNT(*) AS n FROM pre_matriculas WHERE estado = 'PENDIENTE'");
$admisionesPendientes = $r ? (int)(mysqli_fetch_assoc($r)['n'] ?? 0) : 0;

$r = mysqli_query($con, "SELECT COUNT(*) AS n FROM reclamaciones WHERE leido = 0 AND id_parent IS NULL AND ((emisor_rol='estudiante' AND idProfesor IS NULL) OR (emisor_rol='profesor' AND idEstudiante IS NULL))");
$mensajesSinLeer = $r ? (int)(mysqli_fetch_assoc($r)['n'] ?? 0) : 0;

$r = mysqli_query($con, "SELECT COUNT(*) AS n FROM anuncios WHERE fechaExpiracion >= CURDATE()");
$anunciosActivos = $r ? (int)(mysqli_fetch_assoc($r)['n'] ?? 0) : 0;

$eventos = listarEventosProximos();
$proximosEventos = array_slice($eventos, 0, 5);

$hora = (int)date('H');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

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
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--naranja,#f59e0b);flex-shrink:0;">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Admisiones Pendientes</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $admisionesPendientes ?></div>
        </div>
    </div>
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--rojo,#ef4444);flex-shrink:0;">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Mensajes Sin Leer</div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $mensajesSinLeer ?></div>
        </div>
    </div>
    <div class="panel" style="display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--verde,#10b981);flex-shrink:0;">
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
            <a href="../admisiones/listado.php" class="boton-secundario" style="text-align:center;justify-content:center;">
                <i class="fas fa-graduation-cap"></i> Admisiones
            </a>
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
                <?php foreach ($proximosEventos as $ev): ?>
                <li style="display:flex;align-items:flex-start;gap:10px;">
                    <div style="min-width:36px;height:36px;border-radius:8px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:.8rem;">
                        <?= date('d', strtotime($ev['fechaEvento'])) ?><br><small><?= strtoupper(substr(date('M', strtotime($ev['fechaEvento'])), 0, 3)) ?></small>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.9rem;"><?= Security::escapeHtml($ev['tituloEvento']) ?></div>
                        <?php if (!empty($ev['ubicacionEvento'])): ?>
                            <div class="texto-suave" style="font-size:.78rem;"><i class="fas fa-map-marker-alt"></i> <?= Security::escapeHtml($ev['ubicacionEvento']) ?></div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
