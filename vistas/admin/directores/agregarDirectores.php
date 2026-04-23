<?php
session_start();
$titulo_pagina = "Registrar Director - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_director'])) { $datos = $_SESSION['datos_director']; }

unset($_SESSION['errores'], $_SESSION['datos_director']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Director de Ciclo</h1>
    <a href="/pfc/vistas/admin/directores/verDirectores.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?php if(isset($datos['nombreDirector'])) echo $datos['nombreDirector']; ?>">
                <?php if (isset($lista_de_errores['nombreDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailDirector" value="<?php if(isset($datos['emailDirector'])) echo $datos['emailDirector']; ?>">
                <?php if (isset($lista_de_errores['emailDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['emailDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?php if(isset($datos['dniDirector'])) echo $datos['dniDirector']; ?>">
                <?php if (isset($lista_de_errores['dniDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['dniDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" value="<?php if(isset($datos['telefonoDirector'])) echo $datos['telefonoDirector']; ?>">
                <?php if (isset($lista_de_errores['telefonoDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['telefonoDirector']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarDirector" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Director
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
