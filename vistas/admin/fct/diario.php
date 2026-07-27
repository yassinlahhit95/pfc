<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$idFCT = (int)($_GET['idFCT'] ?? 0);
if ($idFCT <= 0) {
    die("Asignación de prácticas no especificada.");
}

$con = obtenerConexion();

// Fetch FCT details
$stmt = mysqli_prepare($con, "
    SELECT f.*, e.nombreEstudiante, c.nombreCiclo, p.nombreProfesor
    FROM fct f
    INNER JOIN estudiantes e ON f.idEstudiante = e.idEstudiante
    INNER JOIN ciclos c ON f.idCiclo = c.idCiclo
    LEFT JOIN profesores p ON f.idProfesorTutor = p.idProfesor
    WHERE f.idFCT = ? LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $idFCT);
mysqli_stmt_execute($stmt);
$fct = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$fct) {
    die("Asignación de prácticas no encontrada.");
}

// Fetch logs
$stmtD = mysqli_prepare($con, "SELECT * FROM fct_diarios WHERE idFCT = ? ORDER BY fecha DESC, idDiario DESC");
mysqli_stmt_bind_param($stmtD, "i", $idFCT);
mysqli_stmt_execute($stmtD);
$diarios = mysqli_fetch_all(mysqli_stmt_get_result($stmtD), MYSQLI_ASSOC);

$horasAprobadas = 0.0;
$horasPendientes = 0.0;
foreach ($diarios as $d) {
    if ($d['estado'] === 'aprobado') {
        $horasAprobadas += (float)$d['horas'];
    } elseif ($d['estado'] === 'pendiente') {
        $horasPendientes += (float)$d['horas'];
    }
}

$titulo_pagina = "AULAPRO | SEGUIMIENTO DIARIO FCT";
$seccion = 'fct';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-book"></i> DIARIO DE PRÁCTICAS</h1>
        <p class="subtitulo-encabezado">Estudiante: <strong><?= Security::escapeHtml($fct['nombreEstudiante']) ?></strong> | Empresa: <strong><?= Security::escapeHtml($fct['empresa']) ?></strong></p>
    </div>
    <a href="lista.php?idCiclo=<?= (int)$fct['idCiclo'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver al listado</a>
</div>

<!-- Stats Banner -->
<div class="columnas margen-abajo" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
    <div class="caja-estadistica panel">
        <div class="info">
            <span class="label">Horas Requeridas</span>
            <span class="numero"><?= (int)$fct['horasTotales'] ?> h</span>
        </div>
    </div>
    <div class="caja-estadistica panel">
        <div class="info">
            <span class="label">Horas Aprobadas</span>
            <span class="numero verde"><?= $horasAprobadas ?> h</span>
            <span class="tendencia verde"><i class="fas fa-check"></i> <?= round(($horasAprobadas / max(1, $fct['horasTotales'])) * 100) ?>% completado</span>
        </div>
    </div>
    <div class="caja-estadistica panel">
        <div class="info">
            <span class="label">Horas Pendientes</span>
            <span class="numero naranja"><?= $horasPendientes ?> h</span>
            <span class="tendencia naranja"><i class="fas fa-hourglass-half"></i> En espera de firma</span>
        </div>
    </div>
    <div class="caja-estadistica panel">
        <div class="info">
            <span class="label">Tutor de Empresa</span>
            <span class="numero" style="font-size:1.2rem;"><?= Security::escapeHtml($fct['tutorEmpresa'] ?? 'Sin indicar') ?></span>
            <span class="tendencia"><i class="fas fa-envelope"></i> <?= Security::escapeHtml($fct['emailTutorEmpresa'] ?? 'Sin email') ?></span>
        </div>
    </div>
</div>

<div class="panel">
    <h2>Registro diario de actividades</h2>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Horas</th>
                    <th>Actividades Realizadas</th>
                    <th>Estado de la Firma</th>
                    <th>Comentarios/Observaciones del Tutor de Empresa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($diarios)): ?>
                    <tr>
                        <td colspan="5" class="vacio">El estudiante no ha registrado ninguna jornada de prácticas todavía.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($diarios as $d): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                            <td><strong><?= (float)$d['horas'] ?> h</strong></td>
                            <td><?= nl2br(Security::escapeHtml($d['actividades'])) ?></td>
                            <td>
                                <?php if ($d['estado'] === 'aprobado'): ?>
                                    <span class="texto-estado verde"><i class="fas fa-check-circle"></i> Firmado (Aprobado)</span>
                                <?php elseif ($d['estado'] === 'rechazado'): ?>
                                    <span class="texto-estado rojo"><i class="fas fa-times-circle"></i> Rechazado</span>
                                <?php else: ?>
                                    <span class="texto-estado naranja"><i class="fas fa-hourglass-half"></i> Pendiente de Firma</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:0.9rem; color:var(--text-2);"><?= Security::escapeHtml($d['observacionesTutor'] ?? '—') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
