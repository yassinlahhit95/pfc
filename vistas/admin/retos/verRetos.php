<?php
session_start();
$titulo_pagina = "Gestión de Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";

$todos_los_retos = listarRetos();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Retos / Proyectos</h1>
    <a href="/pfc/vistas/admin/retos/agregarRetos.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Reto
    </a>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaRetos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Módulos</th>
                    <th>Horas</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_retos)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay retos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_retos as $reto) { 
                        $modulos = obtenerModulosDeReto($reto['idReto']);
                        $nombresModulos = array_column($modulos, 'nombreModulo');
                        $textoModulos = !empty($nombresModulos) ? implode(", ", $nombresModulos) : "<em>Sin módulos</em>";
                    ?>
                    <tr>
                        <td><strong><?php echo $reto['nombreReto']; ?></strong></td>
                        <td><?php echo $textoModulos; ?></td>
                        <td><?php echo $reto['horasReto']; ?>h</td>
                        <td><?php echo date('d/m/Y', strtotime($reto['fechaInicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reto['fechaFin'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/retos/modificarRetos.php?idReto=<?php echo $reto['idReto']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/retos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este reto?')">
                                    <input type="hidden" name="idReto" value="<?php echo $reto['idReto']; ?>">
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

