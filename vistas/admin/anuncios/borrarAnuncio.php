<?php
require_once __DIR__ . "/../../../include/Security.php";
if (empty($_SESSION['idAdmin'])) { header("Location: ../../login.php"); exit; }
$id = $_GET['id'] ?? '';
require_once __DIR__ . '/../../../modelos/anuncios.php';
$registro = obtenerAnuncioPorId($id);
$titulo_pagina = 'AULAPRO | CONFIRMAR';
$seccion = '';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIRMAR ELIMINACIÓN</h1>
</div>

<div class="panel" style="max-width:500px;">
    <p>Quieres eliminar el anuncio "<?= $registro['tituloAnuncio'] ?>"!</p>
    <div class="acciones" style="margin-top:20px;">
        <form method="POST" action="../../../controladores/admin/anuncios/borrar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idAnuncio" value="<?= $id ?>">
            <button type="submit" class="boton-primario" style="background:#f87171;border-color:#f87171;min-width:160px;">Sí, eliminar</button>
        </form>
        <a href="gestionAnuncios.php" class="boton-secundario" style="min-width:160px;">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
