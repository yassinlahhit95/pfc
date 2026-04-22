<?php
session_start();
require_once "../../../modelos/ciclos.php";

$listaCiclos = listarTodosLosCiclos();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['error']);

$titulo_pagina = "Registrar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Módulo</h1>
    <a href="/pfc/vistas/admin/modulos/verModulos.php" class="boton-secundario">← Volver</a>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/modulos/insertar.php" method="POST" style="max-width: 600px; margin: 0 auto;">
        
        <div class="campo-formulario">
            <label>Nombre del Módulo *</label>
            <input type="text" name="nombreModulo" placeholder="Programación PHP">
        </div>

        <div class="campo-formulario">
            <label>Ciclo Formativo Asociado *</label>
            <select name="idCiclo">
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?php echo $ciclo['idCiclo']; ?>">
                        <?php echo $ciclo['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario">
            <label>Horas Máximas *</label>
            <input type="text" name="horasMaximas" placeholder="180">
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarModulo" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Registrar Módulo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>