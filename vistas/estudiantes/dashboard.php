<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($idEstudiante);
$anuncios = listarTodosLosAnuncios();
$notas = listarCalificacionesPorEstudiante($idEstudiante);

$tituloDelPagina = "Dashboard Estudiante";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Bienvenido/a, <?php echo $estudiante['nombreEstudiante']; ?></h1>
    <p class="subtitulo-encabezado">Aquí tienes un resumen de tu actividad académica.</p>
</div>

<div class="disposicion-flexible separacion-grande">
    <div class="flexible-rellenar">
        
        <div class="tarjeta-blanca margen-abajo">
            <div class="titulo-tarjeta"><h3><i class="fas fa-bullhorn"></i> Últimos Anuncios</h3></div>
            <div class="contenido-anuncios">
                <?php if (empty($anuncios)) { ?>
                    <p class="sin-datos">No hay avisos recientes.</p>
                <?php } else { ?>
                    <?php 
                    $contador = 0;
                    foreach ($anuncios as $anuncio) { 
                        if ($contador < 3) {
                    ?>
                        <div class="item-anuncio-dashboard">
                            <h4><?php echo $anuncio['titulo']; ?></h4>
                            <small class="texto-gris">Expira el: <?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?></small>
                            <p><?php echo substr($anuncio['mensaje'], 0, 150); ?>...</p>
                        </div>
                    <?php 
                        }
                        $contador++;
                    } ?>
                    <a href="/pfc/vistas/estudiantes/anuncios/lista.php" class="enlace-pequeno">Ver todos los anuncios</a>
                <?php } ?>
            </div>
        </div>

        
        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta"><h3><i class="fas fa-graduation-cap"></i> Calificaciones Recientes</h3></div>
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th>1ª Final</th>
                            <th>2ª Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($notas)) { ?>
                            <tr><td colspan="3" class="sin-datos">No hay notas registradas.</td></tr>
                        <?php } else { ?>
                            <?php 
                            $contadorNotas = 0;
                            foreach ($notas as $n) { 
                                if ($contadorNotas < 5) {
                                    $nota1 = $n['nota_1final'];
                                    if (empty($nota1)) { $nota1 = '-'; }
                                    
                                    $nota2 = $n['nota_2final'];
                                    if (empty($nota2)) { $nota2 = '-'; }
                            ?>
                                <tr>
                                    <td><?php echo $n['nombreModulo']; ?></td>
                                    <td class="texto-negrita"><?php echo $nota1; ?></td>
                                    <td class="texto-negrita"><?php echo $nota2; ?></td>
                                </tr>
                            <?php 
                                }
                                $contadorNotas++;
                            } ?>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="/pfc/vistas/estudiantes/calificaciones/lista.php" class="enlace-pequeno margen-arriba d-block">Ver boletín completo</a>
            </div>
        </div>
    </div>

    
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Mi Perfil</h3></div>
        <div class="detalle-perfil-corto">
            <p><strong>DNI:</strong> <?php echo $estudiante['dniEstudiante']; ?></p>
            <p><strong>Email:</strong> <?php echo $estudiante['emailEstudiante']; ?></p>
            <p><strong>Ciclo:</strong> <?php echo $estudiante['nombreCiclo']; ?></p>
            <p><strong>Fecha Alta:</strong> <?php echo date('d/m/Y', strtotime($estudiante['fechaAltaEstudiante'])); ?></p>
            <hr>
            <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario ancho-total center-text">Ver Perfil Completo</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>
