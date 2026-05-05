<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$todos_los_retos = listarRetos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "Gestión de Retos - Admin";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Retos / Proyectos</h1>
    <a href="agregarRetos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO RETO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
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
                        <td><strong><?= $reto['nombreReto'] ?></strong></td>
                        <td><?= $textoModulos ?></td>
                        <td><?= $reto['horasReto'] ?>h</td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarRetos.php?idReto=<?= $reto['idReto'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/retos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este reto?')">
                                    <input type="hidden" name="idReto" value="<?= $reto['idReto'] ?>">
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


