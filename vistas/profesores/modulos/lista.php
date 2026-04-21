<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";

$modulos = listarModulos();

$tituloDelPagina = "Módulos - Portal Profesores";
$seccionActual = 'modulos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Lista de Módulos</h1>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
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
                            <td class="texto-negrita"><?php echo $mod['nombreModulo']; ?></td>
                            <td><?php echo $mod['horasMaximas']; ?> h</td>
                            <td><?php echo $mod['nombreCiclo']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="sin-datos">No hay módulos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>