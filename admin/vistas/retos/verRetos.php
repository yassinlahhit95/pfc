<?php
session_start();
$titulo_pagina = "Gestión de Retos - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";

$listaRetos = listarRetos();

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Retos</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/agregarRetos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Crear Nuevo Reto
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Reto</th>
                    <th>Módulos</th>
                    <th>Fechas</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaRetos)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay retos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaRetos as $reto) { 
                        $modulos = obtenerModulosDeReto($reto['idReto']);
                    ?>
                    <tr>
                        <td><?php echo $reto['idReto']; ?></td>
                        <td><strong><?php echo $reto['nombreReto']; ?></strong></td>
                        <td>
                            <?php foreach ($modulos as $m) { ?>
                                <span class="etiqueta-estado azul"><?php echo $m['nombreModulo']; ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="texto-pequeno">
                                <div><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($reto['fechaInicio'])); ?></div>
                                <div class="texto-atenuado"><i class="far fa-calendar-check"></i> <?php echo date('d/m/Y', strtotime($reto['fechaFin'])); ?></div>
                            </div>
                        </td>
                        <td><?php echo $reto['horasReto']; ?>h</td>
                        <td>
                            <div class="botones-accion">
                                <a href="vistas/retos/modificarRetos.php?idReto=<?php echo $reto['idReto']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="controladores/retos/borrar.php" class="d-inline" onsubmit="return confirm('¿Eliminar reto?');">
                                    <input type="hidden" name="idReto" value="<?php echo $reto['idReto']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
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