<?php
session_start();
$titulo_pagina = "Nuevo Préstamo - Super Admin";
$seccion = 'prestamos';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

$listaArticulos = listarArticulos();
$listaEstudiantes = listarEstudiantes();
$listaCiclos = listarTodosLosCiclos();


$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_inventario'])) {
    $datos = $_SESSION['datos_inventario'];
}
unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Registrar Nuevo Préstamo</h1>
    <a href="/pfc/vistas/admin/inventario/gestionarPrestamos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/inventario/prestar.php">
        <div class="formulario-cuadricula">
            
            <div class="campo-formulario">
                <label>Recurso *</label>
                <select name="idArticulo">
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($listaArticulos as $art) { 
                        if ($art['estado'] == 'disponible') {
                            $selected = '';
                            if (isset($datos['idArticulo'])) {
                                if ($datos['idArticulo'] == $art['idArticulo']) {
                                    $selected = 'selected';
                                }
                            }
                            echo "<option value='{$art['idArticulo']}' {$selected}>{$art['nombreArticulo']} ({$art['numeroSerie']})</option>";
                        }
                    } ?>
                </select>
                <?php if (isset($errores['idArticulo'])) { ?>
                    <p class='error-campo'><?php echo $errores['idArticulo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Filtrar Estudiantes por Ciclo</label>
                <select onchange="filtrarAlumnosPorCiclo(this.value)">
                    <option value="todos">Todos los ciclos</option>
                    <?php foreach ($listaCiclos as $c) { ?>
                        <option value="<?php echo $c['idCiclo']; ?>"><?php echo $c['nombreCiclo']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante" id="selectorEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($listaEstudiantes as $est) {
                        $selected = '';
                        if (isset($datos['idEstudiante'])) {
                            if ($datos['idEstudiante'] == $est['idEstudiante']) {
                                $selected = 'selected';
                            }
                        }
                        echo "<option value='{$est['idEstudiante']}' data-ciclo='{$est['idCiclo']}' {$selected}>{$est['nombreEstudiante']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) { ?>
                    <p class='error-campo'><?php echo $errores['idEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Préstamo *</label>
                <?php 
                $fechaActual = date('Y-m-d');
                if (isset($datos['fechaPrestamo'])) {
                    $fechaActual = $datos['fechaPrestamo'];
                }
                ?>
                <input type="date" name="fechaPrestamo" value="<?php echo $fechaActual; ?>">
                <?php if (isset($errores['fechaPrestamo'])) { ?>
                    <p class='error-campo'><?php echo $errores['fechaPrestamo']; ?></p>
                <?php } ?>
            </div>

        </div>

        <div class="margen-arriba">
            <button type="submit" name="registrarPrestamo" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Préstamo
            </button>
        </div>
    </form>
</div>

<script>
function filtrarAlumnosPorCiclo(cicloId) {
    var opciones = document.getElementById('selectorEstudiante').options;
    for (var i = 1; i < opciones.length; i++) {
        var ciclo = opciones[i].getAttribute('data-ciclo');
        var mostrar = false;
        if (cicloId === "todos") {
            mostrar = true;
        } else if (cicloId === ciclo) {
            mostrar = true;
        }
        
        if (mostrar) {
            opciones[i].style.display = "block";
        } else {
            opciones[i].style.display = "none";
        }
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
