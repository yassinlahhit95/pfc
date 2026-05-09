<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$tipoDeDestinatario = $_GET['tipoDestinatario'] ?? "profesor"; // 'profesor' o 'estudiante'
$idCicloSeleccionado = $_GET['idCiclo'] ?? "";

$listaDeProfesores = [];
$listaDeEstudiantes = [];
$listaDeCiclos = listarTodosLosCiclos();

if ($tipoDeDestinatario == 'profesor') {
    $listaDeProfesores = listarProfesores();
} else {
    if (!empty($idCicloSeleccionado)) {
        $listaDeEstudiantes = listarEstudiantesPorCiclo($idCicloSeleccionado);
    } else {
        $listaDeEstudiantes = listarEstudiantes();
    }
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos_form = $_SESSION['datos_mensaje'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_mensaje']);

$titulo_pagina = "AULAPRO | REDACTAR MENSAJE OFICIAL";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Redactar Nuevo Mensaje</h1>
    <a href="lista.php" class="boton-secundario">â† Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca p-25">
    <div class="disposicion-flexible separacion-grande margen-abajo alinear-centro pv-10">
        <div class="campo-formulario">
            <label class="texto-negrita">1. Seleccionar Grupo de Destino:</label>
            <div class="disposicion-flexible separacion-pequena mt-10">
                <a href="?tipoDestinatario=profesor" class="boton-<?= ($tipoDeDestinatario == 'profesor' ? 'primario' : 'secundario') ?> py-10 px-20">
                    <i class="fas fa-chalkboard-teacher"></i> Profesores
                </a>
                <a href="?tipoDestinatario=estudiante" class="boton-<?= ($tipoDeDestinatario == 'estudiante' ? 'primario' : 'secundario') ?> py-10 px-20">
                    <i class="fas fa-user-graduate"></i> Estudiantes
                </a>
            </div>
        </div>

        <?php if ($tipoDeDestinatario == 'estudiante') { ?>
        <form method="GET" class="flexible-rellenar mt-10">
            <input type="hidden" name="tipoDestinatario" value="estudiante">
            <div class="campo-formulario">
                <label class="texto-negrita">2. Filtrar Estudiantes por Ciclo (Opcional):</label>
                <select name="idCiclo" onchange="this.form.submit()" class="mt-5">
                    <option value="">-- Todos los ciclos y estudiantes --</option>
                    <?php foreach ($listaDeCiclos as $cicloItem) { ?>
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?= ($idCicloSeleccionado == $cicloItem['idCiclo'] ? 'selected' : '') ?>>
                            <?= $cicloItem['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>
        <?php } ?>
    </div>

    <hr class="margen-abajo opacity-20">

    <form action="../../../controladores/admin/mensajes/insertar.php" method="POST" class="p-10">
        <input type="hidden" name="emisor_rol" value="admin">

        <div class="disposicion-flexible direccion-columna separacion-grande">
            <div class="campo-formulario">
                <label class="texto-negrita">3. Destinatario Específico *</label>
                <select name="<?= ($tipoDeDestinatario == 'profesor' ? 'idProfesor' : 'idEstudiante') ?>" class="mt-5 ancho-total">
                    <option value="">-- Seleccionar Nombre --</option>

                    <?php if ($tipoDeDestinatario == 'profesor') { ?>
                        <?php foreach ($listaDeProfesores as $profesorItem) { 
                            $selected = (isset($datos_form['idProfesor']) && $datos_form['idProfesor'] == $profesorItem['idProfesor']) ? 'selected' : '';
                        ?>
                            <option value="<?= $profesorItem['idProfesor'] ?>" <?= $selected ?>>
                                <?= $profesorItem['nombreProfesor'] ?>
                            </option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($listaDeEstudiantes as $estudianteItem) { 
                            $selected = (isset($datos_form['idEstudiante']) && $datos_form['idEstudiante'] == $estudianteItem['idEstudiante']) ? 'selected' : '';
                        ?>
                            <option value="<?= $estudianteItem['idEstudiante'] ?>" <?= $selected ?>>
                                <?= $estudianteItem['nombreEstudiante'] ?> (<?= $estudianteItem['nombreCiclo'] ?>)
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?php if (isset($errores['destinatario'])) { ?>
                    <strong class="error-campo"><?= $errores['destinatario'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Asunto del Mensaje *</label>
                <input type="text" name="asunto" class="mt-5 ancho-total" placeholder="Ej: Convocatoria de reunión, Aviso importante..." value="<?= $datos_form['asunto'] ?? '' ?>">
                <?php if (isset($errores['asunto'])) { ?>
                    <strong class="error-campo"><?= $errores['asunto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Cuerpo del Mensaje *</label>
                <textarea name="descripcion" rows="6" class="mt-5 ancho-total" placeholder="Escribe aquí el contenido detallado del mensaje..."><?= $datos_form['descripcion'] ?? '' ?></textarea>
                <?php if (isset($errores['descripcion'])) { ?>
                    <strong class="error-campo"><?= $errores['descripcion'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba-grande disposicion-flexible justify-end gap-15">
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
            <button type="submit" name="enviarMensaje" class="boton-primario px-30">
                <i class="fas fa-paper-plane"></i> Enviar Mensaje Oficial
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

