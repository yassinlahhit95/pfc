<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if ($esTutor && $idCicloTutor) {
    $estudiantes             = listarEstudiantesPorCiclo($idCicloTutor);
    $cicloTutor              = obtenerCicloPorId($idCicloTutor);
    $listaDeCiclosParaFiltro = $cicloTutor ? [$cicloTutor] : [];
} else {
    $estudiantes             = listarEstudiantesDeProfesor($idProfesor);
    $listaDeCiclosParaFiltro = listarCiclosDeProfesor($idProfesor);
}

$tituloDelPagina = "AULAPRO | LISTA DE ESTUDIANTES";
$seccionActual   = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN DE ESTUDIANTES</h1>
    <a href="agregar.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO ESTUDIANTE</a>
</div>


<div class="panel margen-abajo">
    <div class="campo">
        <label for="selectFiltroCicloProf">FILTRAR POR CICLO:</label>
        <select id="selectFiltroCicloProf" onchange="filtrarTabla('selectFiltroCicloProf', 'tablaEstudiantesProf')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?= Security::escapeHtml(strtoupper($cicloFiltro['nombreCiclo'])) ?>">
                    <?= Security::escapeHtml(strtoupper($cicloFiltro['nombreCiclo'])) ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta"><h3>Estudiantes Registrados</h3></div>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantesProf">
            <thead>
                <tr>
                    <th>Nivel</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Ciclo</th>
                    <th style="text-align:right;width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($estudiantes) { ?>
                    <?php foreach ($estudiantes as $est) { ?>
                        <tr>
                            <td>
                                <span class="texto-estado <?= Security::escapeHtml($est['idNivel'] == 1 ? 'azul' : 'verde') ?>">
                                    <?= Security::escapeHtml($est['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior') ?>
                                </span>
                            </td>
                            <td class="texto-negrita"><?= Security::escapeHtml($est['nombreEstudiante']) ?></td>
                            <td><?= Security::escapeHtml($est['emailEstudiante']) ?></td>
                            <td><?= Security::escapeHtml($est['dniEstudiante']) ?></td>
                            <td><?= Security::escapeHtml($est['nombreCiclo']) ?></td>
                            <td style="text-align:right;">
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <a class="recurso-menu-item" href="detalles.php?idEstudiante=<?= (int)$est['idEstudiante'] ?>"><i class="fas fa-eye"></i> Ver perfil</a>
                                        <a class="recurso-menu-item" href="editar.php?idEstudiante=<?= (int)$est['idEstudiante'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$est['idEstudiante'] ?>"
                                           data-tipo="Estudiante"
                                           data-nombre="<?= Security::escapeHtml($est['nombreEstudiante']) ?>"
                                           data-url="/controladores/profesores/estudiantes/borrar.php"
                                           data-campo="idEstudiante"><i class="fas fa-trash"></i> Eliminar</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay estudiantes registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaEstudiantesProf', 15);
</script>
