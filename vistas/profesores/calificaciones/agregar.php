<?php
session_start();

// Validar que sea profesor
$idProfLogueado = $_SESSION['idProfesor'] ?? '';
if (!$idProfLogueado) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

// Coger filtros de la URL
$idCicloSeleccionado = intval($_GET['idCiclo'] ?? 0);
$idModuloSeleccionado = intval($_GET['idModulo'] ?? 0);

// Listas para los selects
$listaMisCiclos = obtenerCiclosDeProfesor($idProfLogueado);
$listaModulosFiltrados = [];

if ($idCicloSeleccionado) :
    $todosLosModulosDelCiclo = obtenerModulosPorCiclo($idCicloSeleccionado);
    $misModulosAsignados = obtenerIdsModulosDeProfesor($idProfLogueado);
    
    foreach ($todosLosModulosDelCiclo as $moduloItem) :
        if (in_array($moduloItem['idModulo'], $misModulosAsignados)) :
            $listaModulosFiltrados[] = $moduloItem;
        endif;
    endforeach;
endif;

// Sacar alumnos si ya eligio modulo
$listaAlumnos = [];
if ($idModuloSeleccionado) :
    $listaAlumnos = listarCalificacionesPorModulo($idModuloSeleccionado);
endif;

// Mensajes de la sesion
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
    <p class="subtitulo">Aqui puedes poner las notas de todos tus alumnos a la vez</p>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="agregar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Selecciona el Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Elige un ciclo --</option>
                <?php foreach ($listaMisCiclos as $c) : ?>
                    <option value="<?= $c['idCiclo'] ?>" <?= $idCicloSeleccionado == $c['idCiclo'] ? 'selected' : '' ?>>
                        <?= $c['nombreCiclo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Selecciona el Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($idCicloSeleccionado) ? 'disabled' : '' ?>>
                <option value="">-- Elige un modulo --</option>
                <?php foreach ($listaModulosFiltrados as $m) : ?>
                    <option value="<?= $m['idModulo'] ?>" <?= $idModuloSeleccionado == $m['idModulo'] ? 'selected' : '' ?>>
                        <?= $m['nombreModulo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>
<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<?php if ($idModuloSeleccionado) : ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="../../../controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?= $idModuloSeleccionado ?>">
            <input type="hidden" name="idCiclo" value="<?= $idCicloSeleccionado ?>">
            
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
                        <?php if (empty($listaAlumnos)) : ?>
                            <tr><td colspan="6" class="sin-datos">No hay alumnos en este ciclo todavia.</td></tr>
                        <?php else : ?>
                            <?php foreach ($listaAlumnos as $alu) : 
                                $idA = $alu['idEstudiante'];
                                $notas = obtenerNotasModulo($idA, $idModuloSeleccionado);
                                
                                $v1 = $notas['nota_1ev'] ?? '';
                                $v1f = $notas['nota_1final'] ?? '';
                                $v2 = $notas['nota_2ev'] ?? '';
                                $v2f = $notas['nota_2final'] ?? '';
                                $vobs = $notas['observaciones'] ?? '';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= $alu['nombreEstudiante'] ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?= $idA ?>">
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
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($listaAlumnos)) : ?>
                <div class="margen-arriba disposicion-flexible alinear-centro">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar todas las Notas
                    </button>
                    <label class="etiqueta-notificacion">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> Enviar aviso por email
                    </label>
                </div>
            <?php endif; ?>
        </form>
    </div>
<?php endif; ?>

<?php include '../comunes/footer.php'; ?>
