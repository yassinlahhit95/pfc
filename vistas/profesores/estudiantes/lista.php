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
$listaDeCiclosParaFiltro = listarCiclosDeProfesor($idProfesor);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "AULAPRO | LISTA DE ESTUDIANTES";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN DE ESTUDIANTES</h1>
    <div class="acciones-pagina">
        <a href="agregar.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO ESTUDIANTE
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel margen-abajo">
    <div class="campo">
        <label for="selectFiltroCicloProf">FILTRAR POR CICLO:</label>
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

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Estudiantes Registrados</h3>
    </div>

    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantesProf">
            <thead>
                <tr>
                    <th>Nivel</th>
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
                            <td>
                                <span class="texto-estado <?= $est['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $est['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                                <span class="texto-estado gris"><?= $est['curso'] ?></span>
                            </td>
                            <td class="texto-negrita"><?= $est['nombreEstudiante'] ?></td>
                            <td><?= $est['emailEstudiante'] ?></td>
                            <td><?= $est['dniEstudiante'] ?></td>
                            <td><?= $est['nombreCiclo'] ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="detalles.php?idEstudiante=<?= $est['idEstudiante'] ?>" class="btn-accion btn-ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar.php?idEstudiante=<?= $est['idEstudiante'] ?>" class="btn-accion btn-editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="../../../controladores/profesores/estudiantes/borrar.php" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este estudiante?');" class="display-inline">
                                        <input type="hidden" name="idEstudiante" value="<?= $est['idEstudiante'] ?>">
                                        <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay estudiantes registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>




