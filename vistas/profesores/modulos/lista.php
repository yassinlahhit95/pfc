<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = $_SESSION['idProfesor'];
$modulos = listarModulosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | MODULOS";
$seccionActual = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>LISTA DE MODULOS</h1>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulosProf">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Horas</th>
                    <th>Ciclo</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($modulos) { ?>
                    <?php foreach ($modulos as $moduloItem) { ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml($moduloItem['nombreModulo'] ) ?></td>
                            <td><?= Security::escapeHtml($moduloItem['horasMaximas'] ) ?> h</td>
                            <td><?= Security::escapeHtml($moduloItem['nombreCiclo'] ) ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="vacio">No hay módulos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>






