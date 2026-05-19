<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$tipoDeDestinatario = $_GET['tipoDestinatario'] ?? "profesor";
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

<div class="cabecera">
    <h1>REDACTAR NUEVO MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">

    <div class="campo margen-abajo">
        <label class="texto-negrita">1. Seleccionar Grupo de Destino:</label>
        <div class="caja espacio-pequeno" style="margin-top: 10px;">
            <a href="?tipoDestinatario=profesor" class="boton-<?= ($tipoDeDestinatario == 'profesor' ? 'primario' : 'secundario') ?>">
                <i class="fas fa-chalkboard-teacher"></i> Profesores
            </a>
            <a href="?tipoDestinatario=estudiante" class="boton-<?= ($tipoDeDestinatario == 'estudiante' ? 'primario' : 'secundario') ?>">
                <i class="fas fa-user-graduate"></i> Estudiantes
            </a>
        </div>
    </div>

    <hr class="margen-abajo" style="opacity: 0.2;">

    <form action="../../../controladores/admin/mensajes/insertar.php" method="POST">
        <input type="hidden" name="emisor_rol" value="admin">
        <input type="hidden" name="tipoDestinatario" value="<?= $tipoDeDestinatario ?>">
        <input type="hidden" name="idCicloMasivo" value="<?= $idCicloSeleccionado ?>">

        <?php if ($tipoDeDestinatario == 'estudiante') { ?>
        <div class="form-cols margen-abajo">
            <div class="campo">
                <label class="texto-negrita">2. Filtrar por Ciclo (Opcional):</label>
                <select onchange="window.location.href='?tipoDestinatario=estudiante&idCiclo='+this.value">
                    <option value="">-- Todos los estudiantes --</option>
                    <?php foreach ($listaDeCiclos as $cicloItem) { ?>
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?= ($idCicloSeleccionado == $cicloItem['idCiclo'] ? 'selected' : '') ?>>
                            <?= $cicloItem['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo">
                <label class="texto-negrita">3. Estudiante Específico: <?php if (!empty($idCicloSeleccionado)) { ?><span class="texto-suave">Deja en blanco para enviar a todo el ciclo.</span><?php } ?></label>
                <select name="idEstudiante" class="ancho-total">
                    <option value="">-- Todos los del ciclo seleccionado --</option>
                    <?php foreach ($listaDeEstudiantes as $estudianteItem) { ?>
                        <?php $selected = (isset($datos_form['idEstudiante']) && $datos_form['idEstudiante'] == $estudianteItem['idEstudiante']) ? 'selected' : ''; ?>
                        <option value="<?= $estudianteItem['idEstudiante'] ?>" <?= $selected ?>>
                            <?= $estudianteItem['nombreEstudiante'] ?> (<?= $estudianteItem['nombreCiclo'] ?>)
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['destinatario'])) { ?>
                    <strong class="error-campo"><?= $errores['destinatario'] ?></strong>
                <?php } ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="campo margen-abajo">
            <label class="texto-negrita">2. Destinatario Específico</label>
            <select name="idProfesor" class="ancho-total">
                <option value="">-- Seleccionar Nombre --</option>
                <?php foreach ($listaDeProfesores as $profesorItem) { ?>
                    <?php $selected = (isset($datos_form['idProfesor']) && $datos_form['idProfesor'] == $profesorItem['idProfesor']) ? 'selected' : ''; ?>
                    <option value="<?= $profesorItem['idProfesor'] ?>" <?= $selected ?>>
                        <?= $profesorItem['nombreProfesor'] ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['destinatario'])) { ?>
                <strong class="error-campo"><?= $errores['destinatario'] ?></strong>
            <?php } ?>
        </div>
        <?php } ?>

        <div class="campo">
            <label class="texto-negrita">Asunto del Mensaje</label>
            <input type="text" name="asunto" class="ancho-total" placeholder="Ej: Convocatoria de reunión, Aviso importante..." value="<?= $datos_form['asunto'] ?? '' ?>">
            <?php if (isset($errores['asunto'])) { ?>
                <strong class="error-campo"><?= $errores['asunto'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo margen-arriba">
            <label class="texto-negrita">Cuerpo del Mensaje</label>
            <textarea name="descripcion" rows="6" class="ancho-total" placeholder="Escribe aquí el contenido detallado del mensaje..."><?= $datos_form['descripcion'] ?? '' ?></textarea>
            <?php if (isset($errores['descripcion'])) { ?>
                <strong class="error-campo"><?= $errores['descripcion'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="reset" class="boton-secundario" value="Limpiar">
            <input type="submit" name="enviarMensaje" class="boton-primario" value="Enviar Mensaje Oficial">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
