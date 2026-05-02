<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";

$ciclos = obtenerCiclosDeProfesor($idProfesor);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "Mis Ciclos Formativos - Portal Profesores";
$seccionActual = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Ciclos Formativos</h1>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>
<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

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
                <?php if (!empty($ciclos)) : ?>
                    <?php foreach ($ciclos as $ciclo) : ?>
                        <tr>
                            <td class="texto-negrita"><?= $ciclo['nombreCiclo'] ?></td>
                            <td><?= $ciclo['abreviaturaCiclo'] ?></td>
                            <td><?= $ciclo['nombreNivel'] ?? 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" class="sin-datos">No tiene ciclos asignados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
