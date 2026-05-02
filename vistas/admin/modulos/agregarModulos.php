<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

$titulo_pagina = "Registrar Módulo - Super Admin";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Módulo</h1>
    <a href="verModulos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="form-estandar">

        <div class="campo-formulario">
            <label>Nombre del Módulo *</label>
            <input type="text" name="nombreModulo" value="<?= $datos['nombreModulo'] ?? '' ?>">
            <?php if (isset($lista_de_errores['nombreModulo'])) : ?>
                <span class="error-campo"><?= $lista_de_errores['nombreModulo'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Ciclo Formativo Asociado *</label>
            <select name="idCiclo">
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($todos_los_ciclos as $ciclo) : ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($lista_de_errores['idCiclo'])) : ?>
                <span class="error-campo"><?= $lista_de_errores['idCiclo'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Horas Máximas *</label>
            <input type="text" name="horasMaximas" value="<?= $datos['horasMaximas'] ?? '' ?>">
            <?php if (isset($lista_de_errores['horasMaximas'])) : ?>
                <span class="error-campo"><?= $lista_de_errores['horasMaximas'] ?></span>
            <?php endif; ?>
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarModulo" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Registrar Módulo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
