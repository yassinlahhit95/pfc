<?php
/**
 * Vista de Detalles del Estudiante para Profesores (Solo Lectura)
 */
session_start();

// Seguridad: Verificar sesión de profesor
if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_GET['idEstudiante'] ?? 0;
$idEstudiante = intval($idEstudiante);

// Obtener los datos del estudiante
$estudiante = obtenerEstudiantePorId($idEstudiante);

// Verificar si el estudiante existe
if (!$estudiante) {
    $_SESSION['error'] = "ESTUDIANTE NO ENCONTRADO.";
    header("Location: lista.php");
    exit;
}

// Opcional: Podríamos verificar que el estudiante pertenece a un ciclo del profesor
// Pero por ahora permitimos la visualización si existe el ID

$tituloDelPagina = "Detalles del Estudiante - Portal Profesores";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Ficha de Estudiante</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-graduate"></i> INFORMACIÓN PERSONAL</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= strtoupper($estudiante['nombreEstudiante']) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciclo Formativo</div>
        <div class="valor-detalle">
            <span class="estado-bolita activo-verde"><?= $estudiante['nombreCiclo'] ?></span>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad / Dirección</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?= !empty($estudiante['observacionesEstudiante']) ? nl2br($estudiante['observacionesEstudiante']) : '<span class="texto-atenuado">Sin observaciones registradas.</span>' ?>
        </div>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-file-pdf"></i> SITUACIÓN DEL TFG</h3>
    </div>
    <div class="disposicion-flexible alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="estado-bolita activo-verde">ENTREGADO</span>
                <p class="texto-pequeno texto-atenuado mt-5">Subido el: <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?></p>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">PENDIENTE / NO SUBIDO</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
