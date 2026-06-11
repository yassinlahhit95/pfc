<?php
require_once __DIR__ . "/../../../include/Security.php";

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$sesiones = listarSesionesPorProfesor($idProfesor);

usort($sesiones, function($a, $b) {
    return strtotime($b['fechaSesion'] . ' ' . $b['horaSesion']) - strtotime($a['fechaSesion'] . ' ' . $a['horaSesion']);
});

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

$tituloDelPagina = 'AULAPRO | AULA DIGITAL';
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave">Gestiona tus sesiones en vivo y conecta con tus estudiantes</p>
</div>

<?php if (!empty($exito)) { ?>
    <div class="alerta-exito" style="margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i>
        <p><?= Security::escapeHtml($exito) ?></p>
    </div>
<?php } ?>

<?php if (!empty($errores)) { ?>
    <div class="alerta-error" style="margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i>
        <p><?= Security::escapeHtml($errores) ?></p>
    </div>
<?php } ?>

<div style="margin-bottom: 20px; text-align: right;">
    <a href="crear.php" class="boton-primario">
        <i class="fas fa-plus-circle"></i> CREAR SESIÓN
    </a>
</div>

<?php if (empty($sesiones)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No tienes sesiones vivas programadas. <a href="crear.php">Crea una nueva</a></p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>SESIÓN</th>
                    <th>MÓDULO</th>
                    <th>FECHA Y HORA</th>
                    <th>PLATAFORMA</th>
                    <th>ESTADO</th>
                    <th>ASISTENCIA</th>
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

                    $totalAsistencias = contarAsistenciaPorSesion($sesion['idSesion']);
                ?>
                <tr>
                    <td><strong><?= Security::escapeHtml($sesion['titulo']) ?></strong></td>
                    <td><?= Security::escapeHtml($sesion['nombreModulo']) ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']))) ?></td>
                    <td>
                        <span class="badge badge-gris">
                            <i class="fas fa-link"></i> <?= Security::escapeHtml(ucfirst($sesion['plataforma'])) ?>
                        </span>
                    </td>
                    <td><?= Security::escapeHtml($estado ) ?></td>
                    <td>
                        <strong><?= Security::escapeHtml($totalAsistencias ) ?></strong> estudiantes
                    </td>
                    <td>
                        <button type="button" class="boton-secundario btn-pequeno" title="Copiar enlace" onclick="AulaDigital.copyToClipboard('<?= Security::escapeHtml($sesion['enlaceReunion']) ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                        <a href="../../../controladores/aula/enviar_sesion_brevo.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-secundario btn-pequeno" title="Enviar a estudiantes del ciclo" onclick="return confirm('¿Enviar el enlace de esta sesión a todos los estudiantes del ciclo?')">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="editar.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-secundario btn-pequeno" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="../../../controladores/aula/borrar_sesion.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-peligro btn-pequeno" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sesión?')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


