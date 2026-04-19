<?php
session_start();
$titulo_pagina = "Modificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";
require_once "../../modelos/modulos.php";

// Usamos el nombre descriptivo de la variable y del parametro GET
$idDelReto = $_GET['idReto'] ?? null;

if (!$idDelReto) {
    header("Location: verRetos.php");
    exit;
}

$retoActual = obtenerRetoPorId($idDelReto);

if (!$retoActual) {
    header("Location: verRetos.php");
    exit;
}


$modulosDelRetoActual = obtenerModulosDeReto($idDelReto);
$idsModulosSeleccionados = array_column($modulosDelRetoActual, 'idModulo');

$listaDeModulos = listarModulos();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Reto</h1>
        <p class="subtitulo-encabezado">Editando: <strong><?php echo $retoActual['nombreReto']; ?></strong></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/retos/actualizar.php" method="POST">
        <input type="hidden" name="idReto" value="<?php echo $retoActual['idReto']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label for="nombreReto">Nombre del Reto *</label>
                <input type="text" id="nombreReto" name="nombreReto" 
                       placeholder="Ej: Reto Sostenibilidad"
                       value="<?php echo $retoActual['nombreReto']; ?>"
                       class="<?php if (isset($errores['nombre'])) { echo 'input-error'; } else { echo ''; } ?>">
            </div>

            <div class="campo-formulario">
                <label for="fechaInicio">Fecha de Inicio *</label>
                <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo $retoActual['fechaInicio']; ?>">
            </div>

            <div class="campo-formulario">
                <label for="fechaFin">Fecha de Fin *</label>
                <input type="date" id="fechaFin" name="fechaFin" value="<?php echo $retoActual['fechaFin']; ?>">
            </div>

            <div class="campo-formulario">
                <label for="horasReto">Horas Estimadas *</label>
                <input type="text" id="horasReto" name="horasReto" value="<?php echo $retoActual['horasReto']; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Módulos Asociados * <span class="texto-atenuado">(Selecciona uno o más)</span></label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <div class="formulario-cuadricula">
                        <?php foreach($listaDeModulos as $modulo) { 
                            $marcado = in_array($modulo['idModulo'], $idsModulosSeleccionados) ? 'checked' : '';
                        ?>
                            <label class="item-seleccionable tarjeta-blanca sin-margen p-0">
                                <div class="disposicion-flexible alinear-centro separacion-pequena p-10">
                                    <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>" <?php echo $marcado; ?>>
                                    <div>
                                        <p class="texto-pequeno sin-margen texto-negrita"><?php echo $modulo['nombreModulo']; ?></p>
                                        <p class="texto-pequeno texto-atenuado sin-margen"><?php echo $modulo['nombreCiclo']; ?></p>
                                    </div>
                                </div>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReto" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
