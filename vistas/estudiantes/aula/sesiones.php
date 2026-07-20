<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = listarModulosPorCiclo($idCiclo);
$todasLasSesiones = [];

foreach ($listaModulos as $modulo) {
    $sesiones = listarSesionesPorModulo($modulo['idModulo']);
    foreach ($sesiones as $sesion) {
        $sesion['nombreModulo'] = $modulo['nombreModulo'];
        $todasLasSesiones[] = $sesion;
    }
}

usort($todasLasSesiones, function($a, $b) {
    return strtotime($b['fechaSesion'] . ' ' . $b['horaSesion']) - strtotime($a['fechaSesion'] . ' ' . $a['horaSesion']);
});

$tituloDelPagina = 'AULAPRO | AULA DIGITAL';
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULA DIGITAL</h1>
    <p class="subtitulo-encabezado">Accede a las clases en vivo y gestiona tu aprendizaje</p>
</div>

<?php if (empty($todasLasSesiones)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay sesiones vivas programadas en tus módulos.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>MÓDULO</th>
                    <th>SESIÓN</th>
                    <th>PROFESOR</th>
                    <th>FECHA Y HORA</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todasLasSesiones as $sesion) {
                    $fechaSesion = strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']);
                    $ahora = time();

                    if ($ahora < $fechaSesion) {
                        $estado = '<span class="badge badge-azul">PRÓXIMA</span>';
                        $tiempoRestante = $fechaSesion - $ahora;
                        $dias = floor($tiempoRestante / 86400);
                        $horas = floor(($tiempoRestante % 86400) / 3600);
                        $detalleEstado = "$dias días, $horas horas";
                    } elseif ($ahora > $fechaSesion + 3600) {
                        $estado = '<span class="badge badge-gris">FINALIZADA</span>';
                        $detalleEstado = 'Hace ' . round(($ahora - $fechaSesion) / 3600) . 'h';
                    } else {
                        $estado = '<span class="badge badge-verde">EN DIRECTO</span>';
                        $detalleEstado = 'En curso ahora';
                    }
                ?>
                <tr>
                    <td><strong><?= Security::escapeHtml($sesion['nombreModulo']) ?></strong></td>
                    <td><?= Security::escapeHtml($sesion['titulo']) ?></td>
                    <td><?= Security::escapeHtml($sesion['nombreProfesor']) ?></td>
                    <td>
                        <div><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']))) ?></div>
                        <span class="texto-pequeno texto-suave"><?= Security::escapeHtml($detalleEstado ) ?></span>
                    </td>
                    <td><?= Security::escapeHtml($estado ) ?></td>
                    <td>
                        <?php if ($ahora >= $fechaSesion && $ahora <= $fechaSesion + 3600) { ?>
                            <a href="<?= Security::escapeHtml($sesion['enlaceReunion']) ?>" target="_blank" class="boton-primario btn-pequeno" title="Acceder a la sesión">
                                <i class="fas fa-sign-in-alt"></i> ENTRAR
                            </a>
                        <?php } else { ?>
                            <a href="detalles.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-secundario btn-pequeno" title="Ver detalles">
                                <i class="fas fa-eye"></i> VER
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3>Información</h3>
        <ul>
            <li><strong>Próximas:</strong> Sesiones que comenzarán en los próximos días</li>
            <li><strong>En Directo:</strong> Sesiones activas en este momento. Haz clic en ENTRAR para acceder.</li>
            <li><strong>Finalizadas:</strong> Sesiones que ya han terminado</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


