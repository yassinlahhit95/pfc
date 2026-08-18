<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor   = $_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$sesiones = ($esTutor && $idCicloTutor)
    ? listarSesionesPorCiclo($idCicloTutor)
    : listarSesionesPorProfesor($idProfesor);

usort($sesiones, function($a, $b) {
    return strtotime($b['fechaSesion'] . ' ' . $b['horaSesion']) - strtotime($a['fechaSesion'] . ' ' . $a['horaSesion']);
});

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

$titulo_pagina = 'Aula Digital';
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Aula Digital</h1>
        <p class="subtitulo-encabezado">Gestiona tus sesiones en vivo y conecta con tus estudiantes</p>
    </div>
    <a href="crear.php" class="boton-primario">
        <i class="fas fa-plus-circle"></i> Crear Sesión
    </a>
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

<?php if (empty($sesiones)) { ?>
    <div class="panel">
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-video"></i></div>
            <div class="panel-vacio-titulo">Sin sesiones vivas</div>
            <div class="panel-vacio-desc">No tienes sesiones vivas programadas todavía.</div>
            <a href="crear.php" class="boton-primario" style="margin-top:16px;"><i class="fas fa-plus"></i> Crear sesión</a>
        </div>
    </div>
<?php } else { ?>
    <div class="panel margen-abajo">
        <input type="text" id="buscarSesion" class="buscador"
               autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
               data-lpignore="true" data-1p-ignore="true" data-form-type="other"
               placeholder="Buscar por título o módulo…"
               oninput="filtrarTabla('buscarSesion','tablaSesiones')">
    </div>
    <div class="panel">
        <div class="contenedor-tabla">
            <table class="tabla-datos" id="tablaSesiones">
                <thead>
                    <tr>
                        <th>Sesión</th>
                        <th>Módulo</th>
                        <th>Fecha y hora</th>
                        <th>Plataforma</th>
                        <th>Estado</th>
                        <th>Asistencia</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sesiones as $sesion) {
                        $fechaSesion = strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']);
                        $ahora = time();

                        if ($ahora < $fechaSesion) {
                            $estado = ['azul', 'Próxima'];
                        } elseif ($ahora > $fechaSesion + 3600) {
                            $estado = ['gris', 'Finalizada'];
                        } else {
                            $estado = ['verde', 'En directo'];
                        }

                        $totalAsistencias = contarAsistenciaPorSesion($sesion['idSesion']);
                    ?>
                    <tr>
                        <td><strong><?= Security::escapeHtml($sesion['titulo']) ?></strong></td>
                        <td><?= Security::escapeHtml($sesion['nombreModulo']) ?></td>
                        <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']))) ?></td>
                        <td>
                            <span class="texto-estado gris">
                                <i class="fas fa-link"></i> <?= Security::escapeHtml(ucfirst($sesion['plataforma'])) ?>
                            </span>
                        </td>
                        <td><span class="texto-estado <?= $estado[0] ?>"><?= $estado[1] ?></span></td>
                        <td>
                            <strong><?= Security::escapeHtml($totalAsistencias) ?></strong> estudiantes
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <button type="button" class="boton-secundario btn-pequeno" title="Copiar enlace" onclick="AulaDigital.copyToClipboard('<?= Security::escapeHtml($sesion['enlaceReunion']) ?>')">
                                <i class="fas fa-copy"></i>
                            </button>
                            <a href="../../../controladores/aula/enviar_sesion_brevo.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-secundario btn-pequeno" title="Enviar a estudiantes del ciclo" data-ajax-confirm="¿Enviar el enlace de esta sesión a todos los estudiantes del ciclo?">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="editar.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="boton-secundario btn-pequeno" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="../../../controladores/aula/borrar_sesion.php"
                                  data-ajax-confirm="¿Estás seguro de que deseas eliminar esta sesión?" style="display:inline;margin:0;">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <input type="hidden" name="id" value="<?= Security::escapeHtml($sesion['idSesion']) ?>">
                                <button type="submit" class="boton-peligro btn-pequeno" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<script>
if (typeof iniciarPaginacion === 'function' && document.getElementById('tablaSesiones')) {
    iniciarPaginacion('tablaSesiones', 15);
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
