<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];
$ciclos = obtenerCiclosDeProfesor($idProfesor);

$tituloDelPagina = "Mis Ciclos Formativos - Portal Profesores";
$seccionActual = 'ciclos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Ciclos Formativos</h1>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
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
                            <td class="texto-negrita"><?php echo $ciclo['nombreCiclo']; ?></td>
                            <td><?php echo $ciclo['abreviaturaCiclo']; ?></td>
                            <td><?php echo (isset($ciclo['nombreNivel']) ? $ciclo['nombreNivel'] : 'N/A'); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="sin-datos">No tiene ciclos asignados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
