<?php
session_start();
$titulo_pagina = "Notas de Módulos - Super Admin";
$seccion = 'notas_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";

$id_modulo_elegido = 0;
if (isset($_GET['idModulo'])) { $id_modulo_elegido = $_GET['idModulo']; }

$todos_los_modulos = listarModulos();
$estudiantes_calificados = [];

if ($id_modulo_elegido != 0) {
    $estudiantes_calificados = listarCalificacionesPorModulo($id_modulo_elegido);
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="calificacionesModulos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="flexible-rellenar">
            <label>Seleccione un Módulo para Calificar:</label>
            <select name="idModulo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($todos_los_modulos as $mod) { ?>
                    <option value="<?php echo $mod['idModulo']; ?>" <?php if($id_modulo_elegido == $mod['idModulo']) echo "selected"; ?>>
                        <?php echo $mod['nombreModulo']; ?> (<?php echo $mod['nombreCiclo']; ?>)
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<?php if ($id_modulo_elegido != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/admin/academico/calificarModulos.php" method="POST">
            <input type="hidden" name="idModulo" value="<?php echo $id_modulo_elegido; ?>">
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Calificación (0-10)</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes_calificados)) { ?>
                            <tr><td colspan="3" class="sin-datos">No hay estudiantes matriculados en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_calificados as $cal) { ?>
                            <tr>
                                <td>
                                    <strong><?php echo $cal['nombreEstudiante']; ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $cal['idEstudiante']; ?>">
                                </td>
                                <td>
                                    <input type="number" name="notas[]" step="0.1" min="0" max="10" 
                                           value="<?php echo $cal['calificacion']; ?>" style="width: 80px;">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?php echo $cal['observaciones']; ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($estudiantes_calificados)) { ?>
                <div class="margen-arriba">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar Todas las Notas
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
