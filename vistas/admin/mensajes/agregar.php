<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

// Filtros para la selecciÃ³n de destinatarios
$tipoDeDestinatario = $_GET['tipoDestinatario'] ?? "profesor"; // 'profesor' o 'estudiante'
$idCicloSeleccionado = $_GET['idCiclo'] ?? "";

$listaDeProfesores = [];
$listaDeEstudiantes = [];
$listaDeCiclos = listarTodosLosCiclos();

if ($tipoDeDestinatario == 'profesor') {
    $listaDeProfesores = listarProfesores();
} else {
    // Si queremos escribir a estudiantes, podemos filtrar por ciclo
    if (!empty($idCicloSeleccionado)) {
        $listaDeEstudiantes = listarEstudiantesPorCiclo($idCicloSeleccionado);
    } else {
        $listaDeEstudiantes = listarEstudiantes();
    }
}

$titulo_pagina = "Redactar Mensaje Oficial - Super Admin";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Redactar Nuevo Mensaje</h1>
    <a href="/pfc/vistas/admin/mensajes/lista.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver al BuzÃ³n
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible separacion-grande margen-abajo alinear-centro">
        <div class="campo-formulario">
            <label>1. Seleccionar Grupo de Destino:</label>
            <div class="disposicion-flexible separacion-pequena mt-5">
                <a href="?tipoDestinatario=profesor" class="boton-<?php echo ($tipoDeDestinatario == 'profesor' ? 'primario' : 'secundario'); ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Profesores
                </a>
                <a href="?tipoDestinatario=estudiante" class="boton-<?php echo ($tipoDeDestinatario == 'estudiante' ? 'primario' : 'secundario'); ?>">
                    <i class="fas fa-user-graduate"></i> Estudiantes
                </a>
            </div>
        </div>

        <?php if ($tipoDeDestinatario == 'estudiante') { ?>
        <form method="GET" class="flexible-rellenar">
            <input type="hidden" name="tipoDestinatario" value="estudiante">
            <div class="campo-formulario">
                <label>2. Filtrar Estudiantes por Ciclo (Opcional):</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los ciclos y estudiantes --</option>
                    <?php foreach ($listaDeCiclos as $cicloItem) { ?>
                        <option value="<?php echo $cicloItem['idCiclo']; ?>" <?php echo ($idCicloSeleccionado == $cicloItem['idCiclo'] ? 'selected' : ''); ?>>
                            <?php echo $cicloItem['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>
        <?php } ?>
    </div>

    <hr class="margen-abajo">

    <form action="/pfc/controladores/admin/mensajes/insertar.php" method="POST">
        <input type="hidden" name="emisor_rol" value="admin">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>3. Destinatario EspecÃ­fico *</label>
                <select name="<?php echo ($tipoDeDestinatario == 'profesor' ? 'idProfesor' : 'idEstudiante'); ?>">
                    <option value="">-- Seleccionar Nombre --</option>
                    
                    <?php if ($tipoDeDestinatario == 'profesor') { ?>
                        <?php foreach ($listaDeProfesores as $profesorItem) { ?>
                            <option value="<?php echo $profesorItem['idProfesor']; ?>">
                                <?php echo $profesorItem['nombreProfesor']; ?>
                            </option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($listaDeEstudiantes as $estudianteItem) { ?>
                            <option value="<?php echo $estudianteItem['idEstudiante']; ?>">
                                <?php echo $estudianteItem['nombreEstudiante']; ?> (<?php echo $estudianteItem['nombreCiclo']; ?>)
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Asunto del Mensaje *</label>
                <input type="text" name="asunto" placeholder="Ej: Convocatoria de reuniÃ³n, Aviso importante...">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Cuerpo del Mensaje *</label>
                <textarea name="descripcion" rows="6" placeholder="Escribe aquÃ­ el contenido detallado del mensaje..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> Enviar Mensaje Oficial
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
