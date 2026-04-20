<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$reclamaciones = listarReclamacionesPorEstudiante($idEstudiante);

$tituloDelPagina = "Mis Reclamaciones - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mis Reclamaciones Enviadas</h1>
    <a href="/pfc/estudiantes/vistas/reclamaciones/agregar.php" class="boton-primario">Nueva Reclamación</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Dirigida a</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Gravedad</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reclamaciones) { ?>
                    <?php foreach ($reclamaciones as $rec) { ?>
                        <tr>
                            <td>
                                <?php 
                                $nomProf = $rec['nombreProfesor'];
                                if (empty($nomProf)) {
                                    echo '<span class="etiqueta-gris">General / Otros</span>';
                                } else {
                                    echo $nomProf;
                                }
                                ?>
                            </td>
                            <td class="texto-negrita"><?php echo $rec['asunto']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rec['fecha'])); ?></td>
                            <td>
                                <?php 
                                $estRec = $rec['estadoReclamacion'];
                                if ($estRec == 'atendido') {
                                    $claseEstado = 'verde';
                                } else {
                                    $claseEstado = 'naranja';
                                }
                                ?>
                                <span class="etiqueta-estado <?php echo $claseEstado; ?>">
                                    <?php echo ucfirst($estRec); ?>
                                </span>
                            </td>
                            <td><?php echo ucfirst($rec['gravedad']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No has realizado ninguna reclamación aún.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>