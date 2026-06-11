<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";

$ciclos = listarCiclosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | MIS CICLOS FORMATIVOS";
$seccionActual = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS CICLOS FORMATIVOS</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>

<div class="panel">
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



