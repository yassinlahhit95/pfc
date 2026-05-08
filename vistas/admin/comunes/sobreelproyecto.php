<?php
session_start();
$titulo_pagina = "Sobre el Proyecto - Yassin Lahhit";
$seccion = 'creditos';
include_once __DIR__ . "/nav.php";
?>

<div class="seccion-about-me">
    <div class="contenedor-about">
        <div class="about-imagen-izq">
            <div class="silhueta-contenedor">
                <img src="../../../public/imagenes/aulapro.png" alt="AulaPro" class="silhueta-imagen">
            </div>
        </div>

        <div class="about-contenido">
            <h2 class="about-titulo">ABOUT ME</h2>
            <h3 class="about-subtitulo">Yassin Lahhit — DAW · CPS Ibaiondo</h3>
            <p class="about-descripcion">
                Soy estudiante de segundo de DAW en CPS Ibaiondo. AulaPro es mi TFG:
                lo he montado de cero, base de datos, backend y frontend, sin frameworks externos.
                El backend en PHP con arquitectura MVC propia, el frontend en CSS puro.
                Me gusta que el código sea limpio y que las cosas funcionen sin complicarse.
            </p>

            <div class="about-stack">
                <span>PHP</span>
                <span>MySQL</span>
                <span>JavaScript</span>
                <span>CSS</span>
                <span>MVC</span>
            </div>

            <div class="about-acciones mt-25">
                <a href="../../../public/uploads/pfc/memoria_tfg.pdf" download class="boton-primario">
                    <i class="fas fa-download"></i> Memoria TFG
                </a>
                <a href="#" target="_blank" class="boton-secundario">
                    <i class="fab fa-github"></i> GitHub
                </a>
            </div>

            <div class="about-redes-sociales">
                <a href="#" target="_blank" class="enlace-red-social" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" target="_blank" class="enlace-red-social" title="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
            </div>

            <div class="separador-huella mt-30">
                <p class="texto-negrita mb-5">© <?= date('Y') ?> Yassin Lahhit</p>
                <p class="texto-atenuado">Desarrollado con PHP, MySQL y Brevo API.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
