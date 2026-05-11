<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$estudiantes = listarEstudiantesDeProfesor($idProfesor);
$listaDeCiclosParaFiltro = obtenerCiclosDeProfesor($idProfesor);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "AULAPRO | LISTA DE ESTUDIANTES";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE ESTUDIANTES</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label for="selectFiltroCicloProf"><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCicloProf" name="selectFiltroCicloProf" onchange="filtrarTabla('selectFiltroCicloProf', 'tablaEstudiantesProf')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?= strtoupper($cicloFiltro['nombreCiclo']) ?>">
                    <?= strtoupper($cicloFiltro['nombreCiclo']) ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Estudiantes Registrados</h3>
    </div>

    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantesProf">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Ciclo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($estudiantes) { ?>
                    <?php foreach ($estudiantes as $est) { ?>
                        <tr>
                            <td class="texto-negrita"><?= $est['nombreEstudiante'] ?></td>
                            <td><?= $est['emailEstudiante'] ?></td>
                            <td><?= $est['dniEstudiante'] ?></td>
                            <td><?= $est['nombreCiclo'] ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="detalles.php?idEstudiante=<?= $est['idEstudiante'] ?>" class="btn-accion btn-ver" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay estudiantes registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>




