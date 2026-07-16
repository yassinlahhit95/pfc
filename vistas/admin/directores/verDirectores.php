<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/directores.php";

$todosLosDirectores = listarDirectores();

$titulo_pagina = "AULAPRO | GESTIÓN DE DIRECTORES";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>DIRECTORES DE CICLO</h1>
    <a href="agregarDirectores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO DIRECTOR
    </a>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaDirectores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todosLosDirectores)) { ?>
                    <tr><td colspan="5" class="vacio">No hay directores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todosLosDirectores as $director) { ?>
                    <tr>
                        <td><?= (int)$director['idDirector'] ?></td>
                        <td><b><?= Security::escapeHtml($director['nombreDirector']) ?></b></td>
                        <td><?= Security::escapeHtml($director['emailDirector']) ?></td>
                        <td><?= Security::escapeHtml($director['telefonoDirector']) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="verDetallesDirectores.php?id=<?= (int)$director['idDirector'] ?>"><i class="fas fa-search"></i> Ver detalles</a>
                                    <a class="recurso-menu-item" href="modificarDirectores.php?idDirector=<?= (int)$director['idDirector'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$director['idDirector'] ?>"
                                       data-tipo="Director"
                                       data-nombre="<?= Security::escapeHtml($director['nombreDirector']) ?>"
                                       data-url="/controladores/admin/directores/borrar.php"
                                       data-campo="idDirector"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
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
<script>
iniciarPaginacion('tablaDirectores', 15);
</script>

