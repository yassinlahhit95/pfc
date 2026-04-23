<?php
session_start();
require_once "../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$mensaje_error = "";
if (isset($_SESSION['error'])) { 
    $mensaje_error = $_SESSION['error']; 
}

$lista_de_errores = array();
if (isset($_SESSION['errores'])) { 
    $lista_de_errores = $_SESSION['errores']; 
}

$datos = array();
if (isset($_SESSION['datos_modulo'])) { 
    $datos = $_SESSION['datos_modulo']; 
}

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

$titulo_pagina = "Registrar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Módulo</h1>
    <a href="/pfc/vistas/admin/modulos/verModulos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/modulos/insertar.php" method="POST" style="max-width: 600px; margin: 0 auto;">
        
        <div class="campo-formulario">
            <label>Nombre del Módulo *</label>
            <input type="text" name="nombreModulo" value="<?php if(isset($datos['nombreModulo'])) { echo $datos['nombreModulo']; } ?>">
            <?php if (isset($lista_de_errores['nombreModulo'])) { ?>
                <span class="error-campo"><?php echo $lista_de_errores['nombreModulo']; ?></span>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Ciclo Formativo Asociado *</label>
            <select name="idCiclo">
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if(isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo "selected"; } ?>>
                        <?php echo $ciclo['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                <span class="error-campo"><?php echo $lista_de_errores['idCiclo']; ?></span>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Horas Máximas *</label>
            <input type="text" name="horasMaximas" value="<?php if(isset($datos['horasMaximas'])) { echo $datos['horasMaximas']; } ?>">
            <?php if (isset($lista_de_errores['horasMaximas'])) { ?>
                <span class="error-campo"><?php echo $lista_de_errores['horasMaximas']; ?></span>
            <?php } ?>
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarModulo" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Registrar Módulo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
