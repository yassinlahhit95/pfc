<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = $_SESSION['idProfesor'];
$modulos = listarModulosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | MÓDULOS";
$seccionActual = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>LISTA DE MÓDULOS</h1>
</div>

<div class="panel">
    <div class="tcont">
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
                    <?php foreach ($modulos as $mod) { ?>
                        <tr>
                            <td class="texto-negrita"><?= $mod['nombreModulo'] ?></td>
                            <td><?= $mod['horasMaximas'] ?> h</td>
                            <td><?= $mod['nombreCiclo'] ?></td>
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




