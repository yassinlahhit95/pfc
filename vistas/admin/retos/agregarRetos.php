<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$todos_los_modulos = listarModulos();

$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reto'] ?? [];
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';

unset($_SESSION['errores'], $_SESSION['datos_reto'], $_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "Nuevo Reto - Admin";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Crear Nuevo Reto</h1>
    <a href="verRetos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/retos/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?= $datos['nombreReto'] ?? '' ?>">
                <?php if (isset($lista_de_errores['nombreReto'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales Estimadas *</label>
                <input type="text" name="horasReto" value="<?= $datos['horasReto'] ?? '' ?>">
                <?php if (isset($lista_de_errores['horasReto'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['horasReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <input type="date" name="fechaInicioReto" min="<?= date('Y-m-d') ?>" value="<?= $datos['fechaInicioReto'] ?? '' ?>">
                <?php if (isset($lista_de_errores['fechaInicioReto'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaInicioReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Fin *</label>
                <input type="date" name="fechaFinReto" min="<?= date('Y-m-d') ?>" value="<?= $datos['fechaFinReto'] ?? '' ?>">
                <?php if (isset($lista_de_errores['fechaFinReto'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaFinReto'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <label><strong>Vincular Módulos (Obligatorio seleccionar al menos uno) *</strong></label>
            <div class="tarjeta-gris-suave scroll-vertical mt-5">
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <div class="item-seleccionable">
                        <input type="checkbox" name="modulosReto[]" value="<?= $modulo['idModulo'] ?>" 
                            <?= (isset($datos['modulosReto']) && in_array($modulo['idModulo'], $datos['modulosReto'])) ? 'checked' : '' ?>>
                        <span><?= $modulo['nombreModulo'] ?> (<?= $modulo['nombreCiclo'] ?>)</span>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($lista_de_errores['modulosReto'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['modulosReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-estandar-botones">
            <button type="submit" name="guardarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


