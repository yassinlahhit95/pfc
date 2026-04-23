<?php
session_start();
require_once "../../../modelos/modulos.php";

$listaModulos = listarModulos();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

$datos = [];
if (isset($_SESSION['datos_reto'])) {
    $datos = $_SESSION['datos_reto'];
}
unset($_SESSION['error'], $_SESSION['datos_reto']);

$titulo_pagina = "Nuevo Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

// Recuperar variables simples si existen tras el error
$nombreReto = '';
if (isset($datos['nombreReto'])) { $nombreReto = $datos['nombreReto']; }

$horasReto = '';
if (isset($datos['horasReto'])) { $horasReto = $datos['horasReto']; }

$fechaInicio = date('Y-m-d');
if (isset($datos['fechaInicio'])) { $fechaInicio = $datos['fechaInicio']; }

$fechaFin = date('Y-m-d', strtotime('+1 month'));
if (isset($datos['fechaFin'])) { $fechaFin = $datos['fechaFin']; }

$modulosSeleccionados = [];
if (isset($datos['modulos']) && is_array($datos['modulos'])) {
    $modulosSeleccionados = $datos['modulos'];
}
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

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/retos/insertar.php" method="POST" style="max-width: 800px;">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" placeholder="Nombre del reto" value="<?php echo $nombreReto; ?>">
            </div>

            <div class="campo-formulario">
                <label>Horas del Reto *</label>
                <input type="text" name="horasReto" placeholder="40" value="<?php echo $horasReto; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <input type="date" name="fechaInicio" value="<?php echo $fechaInicio; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha de Finalización *</label>
                <input type="date" name="fechaFin" value="<?php echo $fechaFin; ?>">
            </div>
        </div>

        <div class="margen-arriba-grande">
            <h4 class="margen-abajo">Vincular Módulos (Subproyectos)</h4>
            <div class="aviso-info-claro margen-abajo">
                <i class="fas fa-info-circle"></i> Solo se permiten 6h por día laborable (Lunes a Viernes). El sistema validará que el total de horas encaje en el periodo elegido.
            </div>
            
            <div class="lista-checkboxes-columna" style="display: flex; flex-direction: column; gap: 10px; background: #f4f8ff; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                <?php foreach ($listaModulos as $modulo) { 
                    $horasUsadas = obtenerHorasTotalesRetosModulo($modulo['idModulo']);
                    $disponibles = $modulo['horasMaximas'] - $horasUsadas;
                    
                    $claseTexto = "texto-verde";
                    if ($disponibles <= 0) { $claseTexto = "texto-rojo"; }
                ?>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px; border-bottom: 1px solid #eee;">
                        <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>" 
                            <?php if ($disponibles <= 0) { echo 'disabled'; } ?>
                            <?php if (in_array($modulo['idModulo'], $modulosSeleccionados)) { echo 'checked'; } ?>>
                        <div style="flex: 1;">
                            <strong><?php echo $modulo['nombreModulo']; ?></strong> 
                            <small class="texto-gris">(<?php echo $modulo['nombreCiclo']; ?>)</small>
                        </div>
                        <div class="<?php echo $claseTexto; ?>" style="font-size: 0.85em; font-weight: bold;">
                            <?php echo $disponibles; ?>h disponibles
                        </div>
                    </label>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="insertarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>