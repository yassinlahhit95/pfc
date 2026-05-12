<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | SOBRE EL PROYECTO";
$seccion = 'sobre';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>SOBRE EL PROYECTO</h1>
</div>

<div class="tarjeta-blanca">

    <p style="font-size: 14px; color: #4a5568; line-height: 1.8; margin-bottom: 20px;">
        <strong>AulaPro</strong> es una aplicación web de gestión académica desarrollada como Trabajo de Fin de Grado (TFG) del ciclo DAW en CPS IBAIONDO, Bilbao. Permite gestionar estudiantes, notas, pagos, mensajería y generar documentos oficiales.
    </p>

    <table style="font-size: 14px; color: #4a5568; line-height: 2; border-collapse: collapse; width: 100%; max-width: 500px;">
        <tr>
            <td style="font-weight: 600; padding-right: 20px; white-space: nowrap;">Autor</td>
            <td>Yassin Lahhit</td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Centro</td>
            <td>CPS IBAIONDO, Bilbao</td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Ciclo</td>
            <td>Desarrollo de Aplicaciones Web (DAW)</td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Curso</td>
            <td>2025 / 2026</td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Email</td>
            <td><a href="mailto:yassin.lahhit@gmail.com" style="color: #667eea;">yassin.lahhit@gmail.com</a></td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Web</td>
            <td><a href="https://yassin.agency" target="_blank" style="color: #667eea;">yassin.agency</a></td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">GitHub</td>
            <td><a href="https://github.com/yassinlahhit" target="_blank" style="color: #667eea;">github.com/yassinlahhit</a></td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">LinkedIn</td>
            <td><a href="https://linkedin.com/in/yassinlahhit" target="_blank" style="color: #667eea;">linkedin.com/in/yassinlahhit</a></td>
        </tr>
        <tr>
            <td style="font-weight: 600; padding-right: 20px;">Instagram</td>
            <td><a href="https://instagram.com/yassinlahhit" target="_blank" style="color: #667eea;">@yassinlahhit</a></td>
        </tr>
    </table>

    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
        <a href="#" download class="boton-primario">
            <i class="fas fa-download"></i> DESCARGAR MEMORIA TFG (PDF)
        </a>
    </div>

</div>

<?php include '../comunes/footer.php'; ?>
