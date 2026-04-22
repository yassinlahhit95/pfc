<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id);

$tituloDelPagina = "Mi Perfil - Portal Estudiantes";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mi Perfil</h1>
    <a href="/pfc/vistas/estudiantes/dashboard.php" class="boton-secundario">← Inicio</a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro">
        <div class="avatar-perfil margen-derecha">
            <i class="fas fa-graduation-cap fa-5x"></i>
        </div>
        <div class="flexible-rellenar">
            <h2><?php echo $estudiante['nombreEstudiante']; ?></h2>
            <p class="texto-secundario"><?php echo $estudiante['nombreCiclo']; ?></p>
        </div>
    </div>

    <div class="margen-arriba-grande">
        <div class="formulario-cuadricula">
            <div class="item-perfil">
                <label>Email</label>
                <p><?php echo $estudiante['emailEstudiante']; ?></p>
            </div>
            <div class="item-perfil">
                <label>Teléfono</label>
                <?php 
                $tel = $estudiante['telefonoEstudiante'];
                if (empty($tel)) { $tel = 'No registrado'; }
                ?>
                <p><?php echo $tel; ?></p>
            </div>
            <div class="item-perfil">
                <label>DNI</label>
                <p><?php echo $estudiante['dniEstudiante']; ?></p>
            </div>
            <div class="item-perfil">
                <label>Ciudad</label>
                <?php 
                $ciu = $estudiante['ciudadEstudiante'];
                if (empty($ciu)) { $ciu = 'No registrada'; }
                ?>
                <p><?php echo $ciu; ?></p>
            </div>
        </div>
    </div>

    <div class="margen-arriba">
        <a href="/pfc/vistas/estudiantes/perfil/editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> Editar mi Perfil
        </a>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
