<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

$tituloDelPagina = "Notas de Retos - Portal Profesores";
$seccionActual = 'notas_retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/retos.php";
require_once "../../../modelos/estudiantes.php";

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo'])) { $id_ciclo_elegido = $_GET['idCiclo']; }

$id_modulo_elegido = 0;
if (isset($_GET['idModulo'])) { $id_modulo_elegido = $_GET['idModulo']; }

$id_reto_elegido = 0;
if (isset($_GET['idReto'])) { $id_reto_elegido = $_GET['idReto']; }

$todos_los_ciclos = listarTodosLosCiclos();

$modulos_filtrados = array();
if ($id_ciclo_elegido != 0) {
    $modulos_filtrados = obtenerModulosPorCiclo($id_ciclo_elegido);
}

$retos_filtrados = array();
if ($id_modulo_elegido != 0) {
    $retos_filtrados = listarRetosFiltrados($id_modulo_elegido);
}

$estudiantes_lista = array();
if ($id_reto_elegido != 0) {
    $estudiantes_lista = listarEstudiantesPorCiclo($id_ciclo_elegido);
}

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Reto</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="retos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Seleccione Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($todos_los_ciclos as $cic) { ?>
                    <option value="<?php echo $cic['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cic['idCiclo']) echo "selected"; ?>>
                        <?php echo $cic['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if($id_ciclo_elegido == 0) echo "disabled"; ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($modulos_filtrados as $mod) { ?>
                    <option value="<?php echo $mod['idModulo']; ?>" <?php if($id_modulo_elegido == $mod['idModulo']) echo "selected"; ?>>
                        <?php echo $mod['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>3. Seleccione Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?php if($id_modulo_elegido == 0) echo "disabled"; ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($retos_filtrados as $reto) { ?>
                    <option value="<?php echo $reto['idReto']; ?>" <?php if($id_reto_elegido == $reto['idReto']) echo "selected"; ?>>
                        <?php echo $reto['nombreReto']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<?php if ($id_reto_elegido != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/profesores/calificaciones/calificarRetos.php" method="POST">
            <input type="hidden" name="idReto" value="<?php echo $id_reto_elegido; ?>">
            <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo_elegido; ?>">
            <input type="hidden" name="idModulo" value="<?php echo $id_modulo_elegido; ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Nota Reto (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes_lista)) { ?>
                            <tr><td colspan="2" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_lista as $est) { 
                                $id_est = $est['idEstudiante'];
                                $nota_actual = obtenerCalificacion($id_est, $id_reto_elegido);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo $est['nombreEstudiante']; ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $id_est; ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas[]" value="<?php echo $nota_actual; ?>" style="width: 80px;">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($estudiantes_lista)) { ?>
                <div class="margen-arriba">
                    <button type="submit" name="guardarNotasReto" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar Notas del Reto
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
