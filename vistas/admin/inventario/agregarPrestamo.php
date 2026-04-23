<?php
session_start();
$titulo_pagina = "Nuevo Préstamo - Super Admin";
$seccion = 'prestamos';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

$articulos_disponibles = listarArticulos();
$todos_los_estudiantes = listarEstudiantes();
$todos_los_ciclos = listarTodosLosCiclos();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_prestamo'])) { $datos = $_SESSION['datos_prestamo']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_prestamo']);
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Préstamo</h1>
    <a href="/pfc/vistas/admin/inventario/gestionarPrestamos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/inventario/prestar.php">
        <div class="formulario-cuadricula">
            
            <div class="campo-formulario">
                <label>Recurso (Solo disponibles) *</label>
                <select name="idArticulo">
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($articulos_disponibles as $art) { 
                        if ($art['estado'] == 'disponible') {
                            ?>
                            <option value="<?php echo $art['idArticulo']; ?>" <?php if(isset($datos['idArticulo']) && $datos['idArticulo'] == $art['idArticulo']) echo "selected"; ?>>
                                <?php echo $art['nombreArticulo']; ?> (<?php echo $art['numeroSerie']; ?>)
                            </option>
                            <?php
                        }
                    } ?>
                </select>
                <?php if (isset($lista_de_errores['idArticulo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idArticulo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) { ?>
                        <option value="<?php echo $est['idEstudiante']; ?>" <?php if(isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) echo "selected"; ?>>
                            <?php echo $est['nombreEstudiante']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Préstamo *</label>
                <input type="date" name="fechaPrestamo" value="<?php if(isset($datos['fechaPrestamo'])) echo $datos['fechaPrestamo']; ?>">
                <?php if (isset($lista_de_errores['fechaPrestamo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaPrestamo']; ?></p>
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

<?php include '../comunes/footer.php'; ?>
