<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$tituloDelPagina = "Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mi Perfil</h1>
    <a href="/pfc/vistas/profesores/dashboard.php" class="boton-secundario">← Inicio</a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro">
        <div class="avatar-perfil margen-derecha">
            <i class="fas fa-user-circle fa-5x"></i>
        </div>
        <div class="flexible-rellenar">
            <h2><?php echo $profesor['nombreProfesor']; ?></h2>
            <p class="texto-secundario"><?php echo $profesor['especialidad']; ?></p>
        </div>
    </div>

    <div class="margen-arriba-grande">
        <div class="formulario-cuadricula">
            <div class="item-perfil">
                <label>Email Corporativo</label>
                <p><?php echo $profesor['emailProfesor']; ?></p>
            </div>
            <div class="item-perfil">
                <label>Teléfono de Contacto</label>
                <?php 
                $tel = $profesor['telefonoProfesor'];
                if (empty($tel)) { $tel = 'No registrado'; }
                ?>
                <p><?php echo $tel; ?></p>
            </div>
            <div class="item-perfil">
                <label>DNI</label>
                <p><?php echo $profesor['dniProfesor']; ?></p>
            </div>
            <div class="item-perfil">
                <label>Dirección</label>
                <?php 
                $dir = $profesor['direccionProfesor'];
                if (empty($dir)) { $dir = 'No registrada'; }
                ?>
                <p><?php echo $dir; ?></p>
            </div>
        </div>
    </div>

    <div class="margen-arriba">
        <a href="/pfc/vistas/profesores/perfil/editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Información
        </a>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
