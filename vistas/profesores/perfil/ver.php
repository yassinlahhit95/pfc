<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$tituloDelPagina = "Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mi Perfil</h1>
    <a href="../dashboard.php" class="boton-secundario">â† Inicio</a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro margen-abajo">
        <div class="flexible-rellenar">
            <h2><?= $profesor['nombreProfesor'] ?></h2>
        </div>
        <div>
            <a href="editar.php" class="boton-primario">
                <i class="fas fa-edit"></i> Editar InformaciÃ³n
            </a>
        </div>
    </div>

    <div class="titulo-tarjeta mt-20">
        <h3>Datos Personales</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email Corporativo</div>
        <div class="valor-detalle"><?= $profesor['emailProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">TelÃ©fono de Contacto</div>
        <div class="valor-detalle"><?= $profesor['telefonoProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $profesor['dniProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DirecciÃ³n</div>
        <div class="valor-detalle"><?= $profesor['direccionProfesor'] ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


