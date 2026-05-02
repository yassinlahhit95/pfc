<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
$idModulo = intval($_GET['idModulo'] ?? 0);

// Filtros disponibles para el profesor
$mis_ciclos = obtenerCiclosDeProfesor($idProfesor);
$mis_modulos = [];
if ($idCiclo > 0) :
    $mis_modulos = obtenerModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
else :
    $mis_modulos = obtenerModulosDeProfesor($idProfesor);
endif;

$calificaciones = listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo, $idModulo);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Notas de Alumnos</h1>
    <a href="agregar.php" class="boton-primario">Asignar Nota</a>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>
<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="" class="disposicion-flexible alinear-fin separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="0">-- Todos mis Ciclos --</option>
                <?php foreach ($mis_ciclos as $c) : ?>
                    <option value="<?= $c['idCiclo'] ?>" <?= $idCiclo == $c['idCiclo'] ? 'selected' : '' ?>>
                        <?= $c['nombreCiclo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()">
                <option value="0">-- Todos mis Módulos --</option>
                <?php foreach ($mis_modulos as $m) : ?>
                    <option value="<?= $m['idModulo'] ?>" <?= $idModulo == $m['idModulo'] ? 'selected' : '' ?>>
                        <?= $m['nombreModulo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-15">
            <a href="lista.php" class="boton-secundario">Limpiar</a>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaNotasProf">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Módulo</th>
                    <th>1ª Ev</th>
                    <th>1ª Final</th>
                    <th>2ª Ev</th>
                    <th>2ª Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($calificaciones) : ?>
                    <?php foreach ($calificaciones as $nota) : ?>
                        <tr>
                            <td><?= $nota['nombreEstudiante'] ?></td>
                            <td class="texto-negrita"><?= $nota['nombreModulo'] ?></td>
                            <td><?= $nota['nota_1ev'] ?></td>
                            <td class="texto-negrita"><?= $nota['nota_1final'] ?></td>
                            <td><?= $nota['nota_2ev'] ?></td>
                            <td class="texto-negrita"><?= $nota['nota_2final'] ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="editar.php?id=<?= $nota['idCalificacion'] ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="sin-datos">No hay calificaciones que coincidan con los filtros.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
