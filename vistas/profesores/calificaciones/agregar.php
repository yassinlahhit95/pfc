<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idCiclo = $_GET['idCiclo'] ?? 0;
$idModulo = $_GET['idModulo'] ?? 0;

$listaDeCiclos = listarCiclosDeProfesor($idProfesor);
$listaDeModulos = [];

if ($idCiclo) {
    $todosLosModulosDelCiclo = listarModulosPorCiclo($idCiclo);
    $misModulosAsignados = listarIdsModulosDeProfesor($idProfesor);
    $mapaModulosAsignados = [];
    foreach ($misModulosAsignados as $idM) { $mapaModulosAsignados[$idM] = true; }

    foreach ($todosLosModulosDelCiclo as $moduloItem) {
        if (isset($mapaModulosAsignados[$moduloItem['idModulo']])) {
            $listaDeModulos[] = $moduloItem;
        }
    }
}

$listaDeEstudiantes = [];
if ($idModulo) {
    $listaDeEstudiantes = listarCalificacionesPorModulo($idModulo);
}

$tituloDelPagina = "AULAPRO | AGREGAR CALIFICACIÓN";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICACIONES POR MÓDULO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="GET" action="../../../vistas/profesores/calificaciones/agregar.php" class="caja alinear-centro espacio-grande">
        <div class="campo relleno">
            <label for="idCiclo">1. Selecciona el Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">-- Elige un ciclo --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= $idCiclo == $ciclo['idCiclo'] ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label for="idModulo">2. Selecciona el Módulo:</label>
            <select name="idModulo" id="idModulo" onchange="this.form.submit()" <?= empty($idCiclo) ? 'disabled' : '' ?>>
                <option value="">-- Elige un módulo --</option>
                <?php foreach ($listaDeModulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= $idModulo == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= $modulo['nombreModulo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<?php if ($idModulo) { ?>
    <div class="panel margen-arriba">
        <form action="../../../controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
            <input type="hidden" name="idCiclo" value="<?= $idCiclo ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>1º Ev</th>
                            <th>1º Final</th>
                            <th>2º Ev</th>
                            <th>2º Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaDeEstudiantes)) { ?>
                            <tr><td colspan="6" class="vacio">No hay alumnos en este ciclo todavia.</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaDeEstudiantes as $estudiante) { 
                                $idEstudiante = $estudiante['idEstudiante'];
                                $notas = obtenerNotasModulo($idEstudiante, $idModulo);
                                
                                $v1 = $notas['nota_1ev'] ?? '';
                                $v1f = $notas['nota_1final'] ?? '';
                                $v2 = $notas['nota_2ev'] ?? '';
                                $v2f = $notas['nota_2final'] ?? '';
                                $vobs = $notas['observaciones'] ?? '';
                            ?>
                            <tr>
                                <td>
                                    <?= $estudiante['nombreEstudiante'] ?>
                                    <input type="hidden" name="estudiantes[]" value="<?= $idEstudiante ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas_1ev[]" value="<?= $v1 ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_1final[]" value="<?= $v1f ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2ev[]" value="<?= $v2 ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2final[]" value="<?= $v2f ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?= $vobs ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($listaDeEstudiantes)) { ?>
                <div class="acciones">
                    <input type="submit" name="guardarNotas" class="boton-primario" value="GUARDAR CALIFICACIONES">
                    <button type="button" class="boton-secundario" onclick="window.location.reload();"><i class="fas fa-eraser"></i> LIMPIAR</button>
                    <label class="texto-aviso ml-auto">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> Enviar aviso por email
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

