<?php
session_start();
$titulo_pagina = "Nuevo Director";
$seccion = 'directores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_director'] ?? [];
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['datos_director'], $_SESSION['errores']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Director</h1>
    <a href="vistas/directores/verDirectores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../controladores/directores/insertar.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?php echo htmlspecialchars($datos['nombreDirector'] ?? ''); ?>">
                <?php if (isset($errores['nombreDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailDirector" value="<?php echo htmlspecialchars($datos['emailDirector'] ?? ''); ?>">
                <?php if (isset($errores['emailDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['emailDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?php echo htmlspecialchars($datos['dniDirector'] ?? ''); ?>">
                <?php if (isset($errores['dniDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['dniDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" value="<?php echo htmlspecialchars($datos['telefonoDirector'] ?? ''); ?>">
                <?php if (isset($errores['telefonoDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['telefonoDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionDirector" value="<?php echo htmlspecialchars($datos['direccionDirector'] ?? ''); ?>">
                <?php if (isset($errores['direccionDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['direccionDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadDirector" value="<?php echo htmlspecialchars($datos['ciudadDirector'] ?? ''); ?>">
                <?php if (isset($errores['ciudadDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['ciudadDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalDirector" value="<?php echo htmlspecialchars($datos['codigoPostalDirector'] ?? ''); ?>">
                <?php if (isset($errores['codigoPostalDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['codigoPostalDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaDirector" value="<?php echo htmlspecialchars($datos['fechaAltaDirector'] ?? date('Y-m-d')); ?>">
                <?php if (isset($errores['fechaAltaDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['fechaAltaDirector']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarDirector" class="boton-primario">Guardar Director</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
