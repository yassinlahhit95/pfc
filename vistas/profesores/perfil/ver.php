<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($id);

$tituloDelPagina = "AULAPRO | MI PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Mi Perfil</h1>
        <p class="subtitulo">InformaciÃ³n de tu cuenta de profesor</p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> EDITAR INFORMACIÃ“N
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= strtoupper($profesor['nombreProfesor']) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email Corporativo</div>
        <div class="valor-detalle"><?= $profesor['emailProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">TelÃ©fono</div>
        <div class="valor-detalle"><?= $profesor['telefonoProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI / IdentificaciÃ³n</div>
        <div class="valor-detalle"><?= $profesor['dniProfesor'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DirecciÃ³n</div>
        <div class="valor-detalle"><?= $profesor['direccionProfesor'] ?></div>
    </div>
    
    <?php if(!empty($profesor['observacionesProfesor'])) { ?>
        <div class="fila-detalle">
            <div class="etiqueta-detalle">Observaciones</div>
            <div class="valor-detalle"><?= $profesor['observacionesProfesor'] ?></div>
        </div>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>

