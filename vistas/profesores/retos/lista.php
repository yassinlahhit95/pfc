<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$retos = obtenerRetosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | RETOS";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE RETOS</h1>
    <div class="acciones-pagina">
        <a href="agregar.php" class="boton-primario">NUEVO RETO</a>
    </div>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
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
                            <td><?= $reto['fechaInicio'] ?></td>
                            <td><?= $reto['fechaFin'] ?></td>
                            <td><?= $reto['horasReto'] ?> h</td>
                            <td>
                                <div class="botones-accion">
                                    <a href="editar.php?id=<?= $reto['idReto'] ?>" class="btn-accion btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form action="../../../controladores/profesores/retos/borrar.php" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este reto?');" class="display-inline">
                                        <input type="hidden" name="idReto" value="<?= $reto['idReto'] ?>">
                                        <button type="submit" class="btn-accion btn-eliminar" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay retos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>





