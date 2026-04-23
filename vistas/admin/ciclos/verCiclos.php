<?php
session_start();
$titulo_pagina = "Gestión de Ciclos - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$mensaje_exito = "";
if (isset($_SESSION['exito'])) { $mensaje_exito = $_SESSION['exito']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_ciclos'])) { $datos = $_SESSION['datos_ciclos']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_ciclos']);
?>

<div class="encabezado-pagina">
    <h1>Ciclos Formativos</h1>
</div>

<?php if ($mensaje_exito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
<?php } ?>
<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nuevo Ciclo Formativo</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/ciclos/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" value="<?php if(isset($datos['nombreCiclo'])) { echo $datos['nombreCiclo']; } ?>" placeholder="Ej: Desarrollo de Aplicaciones Web">
                <?php if (isset($lista_de_errores['nombreCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreCiclo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Grado *</label>
                <select name="gradoCiclo">
                    <option value="">-- Seleccionar --</option>
                    <option value="Medio" <?php if(isset($datos['gradoCiclo']) && $datos['gradoCiclo'] == 'Medio') { echo "selected"; } ?>>Grado Medio</option>
                    <option value="Superior" <?php if(isset($datos['gradoCiclo']) && $datos['gradoCiclo'] == 'Superior') { echo "selected"; } ?>>Grado Superior</option>
                </select>
                <?php if (isset($lista_de_errores['gradoCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['gradoCiclo']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Ciclo
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_ciclos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay ciclos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <tr>
                        <td><?php echo $ciclo['idCiclo']; ?></td>
                        <td><?php echo $ciclo['nombreCiclo']; ?></td>
                        <td>
                            <?php 
                            if (isset($ciclo['gradoCiclo'])) {
                                echo $ciclo['gradoCiclo']; 
                            }
                            ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=<?php echo $ciclo['idCiclo']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/ciclos/borrar.php" method="POST" class="d-inline">
                                    <input type="hidden" name="idCiclo" value="<?php echo $ciclo['idCiclo']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
