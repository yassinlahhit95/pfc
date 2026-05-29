<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$sesiones = listarSesionesPorProfesor($idProfesor);

usort($sesiones, function($a, $b) {
    return strtotime($b['fechaSesion'] . ' ' . $b['horaSesion']) - strtotime($a['fechaSesion'] . ' ' . $a['horaSesion']);
});

$tituloDelPagina = 'AULAPRO | MIS SESIONES VIVAS';
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS SESIONES VIVAS</h1>
    <p class="texto-suave">Gestiona tus clases en vivo</p>
</div>

<div style="margin-bottom: 20px; text-align: right;">
    <a href="crear.php" class="boton-primario">
        <i class="fas fa-plus-circle"></i> NUEVA SESIÓN
    </a>
</div>

<?php if (empty($sesiones)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No tienes sesiones vivas programadas. <a href="crear.php">Crea una nueva</a></p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>SESIÓN</th>
                    <th>MÓDULO</th>
                    <th>FECHA Y HORA</th>
                    <th>PLATAFORMA</th>
                    <th>ESTADO</th>
                    <th>ASISTENCIA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sesiones as $sesion) {
                    $fechaSesion = strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']);
                    $ahora = time();

                    if ($ahora < $fechaSesion) {
                        $estado = '<span class="badge badge-azul">PRÓXIMA</span>';
                    } elseif ($ahora > $fechaSesion + 3600) {
                        $estado = '<span class="badge badge-gris">FINALIZADA</span>';
                    } else {
                        $estado = '<span class="badge badge-verde">EN DIRECTO</span>';
                    }

                    $totalAsistencias = contarAsistenciaPorSesion($sesion['idSesion']);
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($sesion['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($sesion['nombreModulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion'])) ?></td>
                    <td>
                        <span class="badge badge-gris">
                            <i class="fas fa-link"></i> <?= ucfirst($sesion['plataforma']) ?>
                        </span>
                    </td>
                    <td><?= $estado ?></td>
                    <td>
                        <strong><?= $totalAsistencias ?></strong> estudiantes
                    </td>
                    <td>
                        <a href="detalles.php?id=<?= $sesion['idSesion'] ?>" class="boton-secundario btn-pequeno" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="editar.php?id=<?= $sesion['idSesion'] ?>" class="boton-secundario btn-pequeno" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="../../../controladores/aula/borrar_sesion.php?id=<?= $sesion['idSesion'] ?>" class="boton-peligro btn-pequeno" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sesión?')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
