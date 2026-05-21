<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$retos = listarRetosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | RETOS";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTION DE RETOS</h1>
    <div class="acciones-pagina">
        <a href="agregar.php" class="boton-primario">NUEVO RETO</a>
    </div>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($retos) { ?>
                    <?php foreach ($retos as $reto) { ?>
                        <tr>
                            <td class="texto-negrita"><?= $reto['nombreReto'] ?></td>
                            <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                            <td><?= $reto['horasReto'] ?> h</td>
                            <td>
                                <div class="botones-accion">
                                    <a href="editar.php?id=<?= $reto['idReto'] ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <form action="../../../controladores/profesores/retos/borrar.php" method="POST" onsubmit="return confirm('Estas seguro de que desea eliminar este reto?')">
                                        <input type="hidden" name="idReto" value="<?= $reto['idReto'] ?>">
                                        <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay retos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

