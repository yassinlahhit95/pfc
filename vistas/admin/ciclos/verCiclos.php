<?php
session_start();
$titulo_pagina = "Gestión de Ciclos - Super Admin";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_ciclos'])) { $datos = $_SESSION['datos_ciclos']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_ciclos']);
?>

<div class="encabezado-pagina">
    <h1>Ciclos Formativos</h1>
    <a href="/pfc/vistas/admin/ciclos/agregarCiclos.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Ciclo
    </a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCiclos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Nivel</th>
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
                        <td><strong><?php echo $ciclo['nombreCiclo']; ?></strong></td>
                        <td><?php echo $ciclo['nombreNivel']; ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=<?php echo $ciclo['idCiclo']; ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/ciclos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este ciclo?')">
                                    <input type="hidden" name="idCiclo" value="<?php echo $ciclo['idCiclo']; ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
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

