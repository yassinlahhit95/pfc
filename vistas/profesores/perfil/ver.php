<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/profesores.php";

$idProfesor = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);

$tituloDelPagina = "AULAPRO | MI PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI PERFIL</h1>
        <p class="subtitulo">Informacion de tu cuenta de profesor</p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> EDITAR INFORMACION
        </a>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml(mb_strtoupper($profesor['nombreProfesor'], 'UTF-8')) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email Corporativo</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['emailProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Telefono</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['telefonoProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI / Identificacion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['dniProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Direccion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['direccionProfesor'] ) ?></div>
    </div>

    <?php if(!empty($profesor['observacionesProfesor'])) { ?>
        <div class="fila-datos">
            <div class="nombre-detalle">Observaciones</div>
            <div class="valor-detalle"><?= Security::escapeHtml($profesor['observacionesProfesor'] ) ?></div>
        </div>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>
