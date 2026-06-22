<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor   = $_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$modulos = ($esTutor && $idCicloTutor)
    ? listarModulosDeCicloConNombre($idCicloTutor)
    : listarModulosDeProfesor($idProfesor);

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$tituloDelPagina = "AULAPRO | MODULOS";
$seccionActual = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>LISTA DE MÓDULOS</h1>
    <?php if ($esTutor && $idCicloTutor): ?>
    <a href="agregar.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO MÓDULO</a>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulosProf">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Horas</th>
                    <th>Ciclo</th>
                    <?php if ($esTutor): ?><th style="width:60px;"></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($modulos): ?>
                    <?php foreach ($modulos as $m): ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml($m['nombreModulo']) ?></td>
                            <td><?= Security::escapeHtml($m['horasMaximas']) ?> h</td>
                            <td><?= Security::escapeHtml($m['nombreCiclo']) ?></td>
                            <?php if ($esTutor): ?>
                            <td style="text-align:right;">
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <a class="recurso-menu-item" href="editar.php?idModulo=<?= (int)$m['idModulo'] ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$m['idModulo'] ?>"
                                           data-tipo="Módulo"
                                           data-nombre="<?= Security::escapeHtml($m['nombreModulo']) ?>"
                                           data-url="/controladores/profesores/modulos/borrar.php"
                                           data-campo="idModulo">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $esTutor ? 4 : 3 ?>" class="vacio">No hay módulos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaModulosProf', 15);
</script>





