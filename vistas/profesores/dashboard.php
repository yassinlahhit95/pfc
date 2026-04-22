<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";

$idProfesor = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);
$anuncios = listarTodosLosAnuncios();
$reclamaciones = listarReclamacionesPorProfesor($idProfesor);
$estudiantes = listarEstudiantesPorProfesor($idProfesor);

$tituloDelPagina = "Dashboard Profesor";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Bienvenido/a, <?php echo $profesor['nombreProfesor']; ?></h1>
    <p class="subtitulo-encabezado">Resumen de gestión y avisos del centro.</p>
</div>

<div class="disposicion-flexible separacion-grande">
    <div class="flexible-rellenar">
        
        <div class="tarjeta-blanca margen-abajo">
            <div class="titulo-tarjeta"><h3><i class="fas fa-bullhorn"></i> Avisos Recientes</h3></div>
            <div class="contenido-anuncios">
                <?php if (empty($anuncios)) { ?>
                    <p class="sin-datos">No hay anuncios publicados.</p>
                <?php } else { ?>
                    <?php 
                    $cont = 0;
                    foreach ($anuncios as $anuncio) { 
                        if ($cont < 3) {
                    ?>
                        <div class="item-anuncio-dashboard">
                            <h4><?php echo $anuncio['titulo']; ?></h4>
                            <p><?php echo substr($anuncio['mensaje'], 0, 120); ?>...</p>
                        </div>
                    <?php 
                        }
                        $cont++;
                    } ?>
                <?php } ?>
            </div>
        </div>

        
        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta"><h3><i class="fas fa-exclamation-circle"></i> Mis Reportes Recientes</h3></div>
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reclamaciones)) { ?>
                            <tr><td colspan="3" class="sin-datos">No has realizado reportes.</td></tr>
                        <?php } else { ?>
                            <?php 
                            $contR = 0;
                            foreach ($reclamaciones as $r) { 
                                if ($contR < 5) {
                                    $estRec = $r['estadoReclamacion'];
                                    if ($estRec == 'atendido') {
                                        $clase = 'verde';
                                    } else {
                                        $clase = 'naranja';
                                    }
                            ?>
                                <tr>
                                    <td><?php echo $r['nombreEstudiante']; ?></td>
                                    <td><?php echo $r['asunto']; ?></td>
                                    <td><span class="etiqueta-estado <?php echo $clase; ?>"><?php echo ucfirst($estRec); ?></span></td>
                                </tr>
                            <?php 
                                }
                                $contR++;
                            } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Resumen de Actividad</h3></div>
        <div class="resumen-metricas">
            <div class="metrica-circular">
                <h2><?php echo count($estudiantes); ?></h2>
                <p class="texto-gris">Alumnos a cargo</p>
            </div>
            <hr>
            <div class="info-adicional-perfil">
                <?php 
                $esp = $profesor['especialidad'];
                if (empty($esp)) { $esp = 'No definida'; }
                ?>
                <p><strong>Especialidad:</strong><br><?php echo $esp; ?></p>
                <p><strong>Email:</strong><br><?php echo $profesor['emailProfesor']; ?></p>
            </div>
            <a href="/pfc/vistas/profesores/perfil/ver.php" class="boton-secundario ancho-total center-text">Editar Perfil</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>
