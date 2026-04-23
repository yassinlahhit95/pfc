<?php
session_start();
$titulo_pagina = "Nuevo Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";

$listaCiclos = listarTodosLosCiclos();
$listaModulos = listarModulos();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Profesor</h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">Volver</a>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/profesores/insertar.php" method="POST" style="max-width: 600px; margin: 0 auto;">
        
        <div class="campo-formulario">
            <label>Nombre Completo *</label>
            <input type="text" name="nombreProfesor" placeholder="Introduce el nombre">
        </div>

        <div class="campo-formulario">
            <label>Email *</label>
            <input type="email" name="emailProfesor" placeholder="correo@ejemplo.com">
        </div>

        <div class="campo-formulario">
            <label>DNI *</label>
            <input type="text" name="dniProfesor" placeholder="12345678X">
        </div>

        <div class="campo-formulario">
            <label>Teléfono</label>
            <input type="text" name="telefonoProfesor" placeholder="600000000">
        </div>

        <div class="campo-formulario">
            <label>Dirección</label>
            <input type="text" name="direccionProfesor" placeholder="Calle, número, ciudad">
        </div>

        <div class="campo-formulario margen-arriba">
            <label>1. Seleccionar Ciclos</label>
            <div class="lista-checkboxes-columna" style="display: flex; flex-direction: column; gap: 10px; background: #fdffdf; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="ciclos[]" value="<?php echo $ciclo['idCiclo']; ?>" class="check-ciclo" onchange="filtrarModulos()">
                        <span><?php echo $ciclo['nombreCiclo']; ?></span>
                    </label>
                <?php } ?>
            </div>
        </div>

        <div class="campo-formulario margen-arriba" id="contenedorModulos" style="display: none;">
            <label>2. Seleccionar Módulos del Ciclo</label>
            <div class="lista-checkboxes-columna" style="display: flex; flex-direction: column; gap: 10px; background: #f4f8ff; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                <?php foreach ($listaModulos as $modulo) { ?>
                    <label class="item-modulo" data-ciclo="<?php echo $modulo['idCiclo']; ?>" style="display: none; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>">
                        <span><?php echo $modulo['nombreModulo']; ?></span>
                    </label>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarProfesor" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Registrar Profesor
            </button>
        </div>
    </form>
</div>

<script>
function filtrarModulos() {
    // 1. Obtener todos los ciclos seleccionados
    const checksCiclo = document.querySelectorAll('.check-ciclo');
    let ciclosSeleccionados = [];
    
    checksCiclo.forEach(check => {
        if (check.checked) {
            ciclosSeleccionados.push(check.value);
        }
    });

    // 2. Mostrar/Ocultar el contenedor de módulos
    const contenedor = document.getElementById('contenedorModulos');
    if (ciclosSeleccionados.length > 0) {
        contenedor.style.display = 'block';
    } else {
        contenedor.style.display = 'none';
    }

    // 3. Filtrar los módulos individualmente
    const modulos = document.querySelectorAll('.item-modulo');
    modulos.forEach(mod => {
        const idCicloModulo = mod.getAttribute('data-ciclo');
        
        // Si el ciclo del módulo está entre los seleccionados, se muestra
        if (ciclosSeleccionados.includes(idCicloModulo)) {
            mod.style.display = 'flex';
        } else {
            mod.style.display = 'none';
            // Opcional: desmarcar si se oculta
            mod.querySelector('input').checked = false;
        }
    });
}
</script>

<?php include '../comunes/footer.php'; ?>