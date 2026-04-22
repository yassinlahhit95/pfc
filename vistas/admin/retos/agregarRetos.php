<?php
session_start();
require_once "../../../modelos/modulos.php";

$listaModulos = listarModulos();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_reto'])) {
    $datos = $_SESSION['datos_reto'];
}
unset($_SESSION['errores'], $_SESSION['datos_reto']);

$titulo_pagina = "Nuevo Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Crear Nuevo Reto</h1>
        <p class="subtitulo-encabezado">Defina un nuevo reto y vincule los módulos correspondientes</p>
    </div>
    <a href="/pfc/vistas/admin/retos/verRetos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/retos/insertar.php" method="POST" style="max-width: 800px;">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Reto *</label>
                <?php 
                $nombreReto = '';
                if (isset($datos['nombreReto'])) {
                    $nombreReto = $datos['nombreReto'];
                }
                ?>
                <input type="text" name="nombreReto" placeholder="Nombre del reto" value="<?php echo $nombreReto; ?>">
            </div>

            <div class="campo-formulario">
                <label>Horas del Reto *</label>
                <?php 
                $horasReto = '';
                if (isset($datos['horasReto'])) {
                    $horasReto = $datos['horasReto'];
                }
                ?>
                <input type="text" name="horasReto" placeholder="40" value="<?php echo $horasReto; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <?php 
                $fechaInicio = date('Y-m-d');
                if (isset($datos['fechaInicio'])) {
                    $fechaInicio = $datos['fechaInicio'];
                }
                ?>
                <input type="date" name="fechaInicio" value="<?php echo $fechaInicio; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha de Finalización *</label>
                <?php 
                $fechaFin = date('Y-m-d', strtotime('+1 month'));
                if (isset($datos['fechaFin'])) {
                    $fechaFin = $datos['fechaFin'];
                }
                ?>
                <input type="date" name="fechaFin" value="<?php echo $fechaFin; ?>">
            </div>
        </div>

        <div class="margen-arriba-grande">
            <h4 class="margen-abajo">Vincular Módulos (Subproyectos)</h4>
            <div class="lista-checkboxes-columna" style="display: flex; flex-direction: column; gap: 10px; background: #f4f8ff; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                <?php foreach ($listaModulos as $modulo) { ?>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px; border-bottom: 1px solid #eee;">
                        <?php 
                        $checked = '';
                        if (isset($datos['modulos']) && is_array($datos['modulos'])) {
                            if (in_array($modulo['idModulo'], $datos['modulos'])) {
                                $checked = 'checked';
                            }
                        }
                        ?>
                        <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>" <?php echo $checked; ?>>
                        <span><strong><?php echo $modulo['nombreModulo']; ?></strong> (<?php echo $modulo['nombreCiclo']; ?>)</span>
                    </label>
                <?php } ?>
            </div>
            <?php if (isset($errores['modulos'])) { ?>
                <p class="error-campo"><?php echo $errores['modulos']; ?></p>
            <?php } ?>
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="insertarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>