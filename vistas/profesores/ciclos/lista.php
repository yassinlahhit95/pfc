<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/ciclos.php";

$ciclos = listarCiclosDeProfesor($idProfesor);

$titulo_pagina = "Mis Ciclos Formativos";
$seccionActual = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Mis Ciclos Formativos</h1>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tabla-mis-ciclos-prof">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Abreviatura</th>
                    <th>Nivel</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ciclos)) { ?>
                    <?php foreach ($ciclos as $ciclo) { ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml($ciclo['nombreCiclo'] ) ?></td>
                            <td><?= Security::escapeHtml($ciclo['abreviaturaCiclo'] ) ?></td>
                            <td><?= Security::escapeHtml($ciclo['nombreNivel'] ?? 'N/A') ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="vacio">No tiene ciclos asignados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iniciarPaginacion('tabla-mis-ciclos-prof', 15);
});
</script>
