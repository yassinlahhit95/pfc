<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$con = obtenerConexion();
$sql = "SELECT s.*, m.nombreModulo, p.nombreProfesor
        FROM aula_sesiones s
        JOIN modulos m ON s.idModulo = m.idModulo
        JOIN profesores p ON s.idProfesor = p.idProfesor
        ORDER BY s.fechaSesion DESC, s.horaSesion DESC";
$result = mysqli_query($con, $sql);
$sesiones = [];
while ($row = mysqli_fetch_assoc($result)) {
    $sesiones[] = $row;
}
mysqli_close($con);

$titulo_pagina = 'AULAPRO | AULA DIGITAL';
$seccion = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave">Monitorea todas las sesiones en vivo y gestiona el sistema académico</p>
</div>

<?php if (empty($sesiones)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay sesiones vivas en el sistema.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>SESIÓN</th>
                    <th>MÓDULO</th>
                    <th>PROFESOR</th>
                    <th>FECHA Y HORA</th>
                    <th>ESTADO</th>
                    <th>ASISTENCIAS</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sesiones as $sesion) {
                    $fechaSesion = strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']);
                    $ahora = time();

                    if ($ahora < $fechaSesion) {
                        $estado = '<span class="badge badge-azul">PRÓXIMA</span>';
                    } elseif ($ahora > $fechaSesion + 3600) {
                        $estado = '<span class="badge badge-gris">FINALIZADA</span>';
                    } else {
                        $estado = '<span class="badge badge-verde">EN DIRECTO</span>';
                    }

                    $con = obtenerConexion();
                    $sqlAsist = "SELECT COUNT(*) as total FROM aula_asistencias WHERE idSesion = ?";
                    $stmtAsist = mysqli_prepare($con, $sqlAsist);
                    mysqli_stmt_bind_param($stmtAsist, "i", $sesion['idSesion']);
                    mysqli_stmt_execute($stmtAsist);
                    $resAsist = mysqli_stmt_get_result($stmtAsist);
                    $totalAsist = mysqli_fetch_assoc($resAsist);
                    mysqli_close($con);
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($sesion['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($sesion['nombreModulo']) ?></td>
                    <td><?= htmlspecialchars($sesion['nombreProfesor']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion'])) ?></td>
                    <td><?= $estado ?></td>
                    <td>
                        <strong><?= $totalAsist['total'] ?? 0 ?></strong> estudiantes
                    </td>
                    <td>
                        <button type="button" class="boton-secundario btn-pequeno" title="Copiar enlace" onclick="AulaDigital.copyToClipboard('<?= htmlspecialchars($sesion['enlaceReunion']) ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3>Información del Sistema</h3>
        <ul>
            <li><strong>Total Sesiones:</strong> <?= count($sesiones) ?></li>
            <li><strong>Próximas:</strong> Sesiones que comenzarán en los próximos días</li>
            <li><strong>En Directo:</strong> Sesiones activas en este momento</li>
            <li><strong>Finalizadas:</strong> Sesiones que ya han terminado hace más de 1 hora</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
