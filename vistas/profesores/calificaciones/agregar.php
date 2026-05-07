<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
$idModulo = intval($_GET['idModulo'] ?? 0);

$listaDeCiclos = obtenerCiclosDeProfesor($idProfesor);
$listaDeModulos = [];

if ($idCiclo) {
    $todosLosModulosDelCiclo = obtenerModulosPorCiclo($idCiclo);
    $misModulosAsignados = obtenerIdsModulosDeProfesor($idProfesor);
    
    foreach ($todosLosModulosDelCiclo as $moduloItem) {
        if (in_array($moduloItem['idModulo'], $misModulosAsignados)) {
            $listaDeModulos[] = $moduloItem;
        }
    }
}

$listaDeEstudiantes = [];
if ($idModulo) {
    $listaDeEstudiantes = listarCalificacionesPorModulo($idModulo);
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

$tituloPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="../../../vistas/profesores/calificaciones/agregar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
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

        <div class="campo-formulario flexible-rellenar">
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
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<?php if ($idModulo) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="../../../controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
            <input type="hidden" name="idCiclo" value="<?= $idCiclo ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>1ª Ev</th>
                            <th>1ª Final</th>
                            <th>2ª Ev</th>
                            <th>2ª Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaDeEstudiantes)) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay alumnos en este ciclo todavía.</td></tr>
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
                                    <strong><?= $estudiante['nombreEstudiante'] ?></strong>
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
                <div class="form-acciones">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> GUARDAR CALIFICACIONES
                    </button>
                    <button type="button" class="boton-secundario" onclick="window.location.reload();"><i class="fas fa-eraser"></i> LIMPIAR</button>
                    <label class="etiqueta-notificacion ml-auto">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> Enviar aviso por email
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

