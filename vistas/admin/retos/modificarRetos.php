<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_reto = $_GET['idReto'] ?? '';
$reto = obtenerRetoPorId($id_reto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulos_del_reto = obtenerModulosDeReto($id_reto);
$ids_modulos_viculados = [];
foreach ($modulos_del_reto as $m) {
    $ids_modulos_viculados[] = $m['idModulo'];
}

if (isset($_SESSION['datos_reto'])) {
    $reto = $_SESSION['datos_reto'];
    if (isset($reto['modulosReto'])) {
        $ids_modulos_viculados = $reto['modulosReto'];
    } else {
        $ids_modulos_viculados = [];
    }
}

$todos_los_modulos = listarModulos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reto']);

$titulo_pagina = "AULAPRO | MODIFICAR RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Reto</h1>
    <a href="verRetos.php" class="boton-secundario">â† Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/retos/actualizar.php" id="formReto">
        <input type="hidden" name="idReto" value="<?= $id_reto ?>">
        
        <div class="formulario-cuadricula" style="grid-template-columns: 1fr;">
            <div class="campo-formulario">
                <label for="nombreReto">Nombre del Reto *</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= $reto['nombreReto'] ?>">
                <?php if (isset($errores['nombreReto'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="horasReto">Horas Totales Estimadas *</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= $reto['horasReto'] ?>">
                <?php if (isset($errores['horasReto'])) { ?>
                    <strong class="error-campo"><?= $errores['horasReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaInicioReto">Fecha de Inicio *</label>
                <input type="date" name="fechaInicioReto" id="fechaInicioReto" value="<?= $reto['fechaInicio'] ?>">
                <?php if (isset($errores['fechaInicioReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaInicioReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaFinReto">Fecha de Fin *</label>
                <input type="date" name="fechaFinReto" id="fechaFinReto" value="<?= $reto['fechaFin'] ?>">
                <?php if (isset($errores['fechaFinReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaFinReto'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="campo-formulario margen-arriba">
            <label><strong>Vincular Módulos *</strong></label>
            <div class="tarjeta-gris-suave scroll-vertical mt-5">
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <div class="item-seleccionable">
                        <input type="checkbox" name="modulosReto[]" id="modulo_<?= $modulo['idModulo'] ?>" value="<?= $modulo['idModulo'] ?>" 
                            <?= in_array($modulo['idModulo'], $ids_modulos_viculados) ? 'checked' : '' ?>>
                        <label for="modulo_<?= $modulo['idModulo'] ?>"><?= $modulo['nombreModulo'] ?> (<?= $modulo['nombreCiclo'] ?>)</label>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($errores['modulosReto'])) { ?>
                <strong class="error-campo"><?= $errores['modulosReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarReto" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<script>
document.getElementById('formReto').addEventListener('submit', function(e) {
    const fechaInicioInput = document.getElementById('fechaInicioReto').value;
    const fechaFinInput = document.getElementById('fechaFinReto').value;
    const horasInput = document.getElementById('horasReto').value;

    if (!fechaInicioInput || !fechaFinInput || !horasInput) return;

    const fechaInicio = new Date(fechaInicioInput);
    const fechaFin = new Date(fechaFinInput);
    const horas = parseInt(horasInput);
    
    if (fechaInicio > fechaFin) {
        e.preventDefault();
        alert('La fecha de inicio no puede ser posterior a la fecha de fin.');
        return;
    }

    // Calcular días laborables (Lunes a Viernes)
    let diasLaborables = 0;
    let current = new Date(fechaInicio);
    while (current <= fechaFin) {
        const day = current.getDay();
        if (day !== 0 && day !== 6) { // 0 es Domingo, 6 es Sábado
            diasLaborables++;
        }
        current.setDate(current.getDate() + 1);
    }

    const maxHoras = diasLaborables * 6;
    if (horas > maxHoras) {
        e.preventDefault();
        alert(`Las horas estimadas (${horas}) superan el máximo permitido para este rango de fechas (${maxHoras} horas, basadas en 6h/día de Lunes a Viernes).`);
    }
});
</script>

<?php include '../comunes/footer.php'; ?>




