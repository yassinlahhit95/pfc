<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/secretarias.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);

$titulo_pagina = "AULAPRO | MI PERFIL";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MI PERFIL</h1>
    <a href="editar.php" class="boton-primario"><i class="fas fa-pen"></i> EDITAR PERFIL</a>
</div>

<div class="panel">
    <?php if ($secretaria): ?>
    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label">Nombre</span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['nombreSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label">Email</span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['emailSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label">Estado</span>
            <span class="dato-valor">
                <?php if ($secretaria['activoSecretaria']): ?>
                <span class="texto-estado verde">Activo</span>
                <?php else: ?>
                <span class="texto-estado rojo">Inactivo</span>
                <?php endif; ?>
            </span>
        </div>
    </div>
    <?php else: ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-user-slash"></i></div>
        <div class="panel-vacio-titulo">Perfil no encontrado</div>
    </div>
    <?php endif; ?>
</div>

<?php include '../comunes/footer.php'; ?>
