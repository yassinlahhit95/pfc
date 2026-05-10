<?php
session_start();
$titulo_pagina = "AULAPRO | SOBRE MÍ";
$seccion = 'creditos';
include_once __DIR__ . "/nav.php";
?><div class="seccion-about-me">
    <div class="contenedor-about">

        <div class="about-imagen-top">
            <div class="silhueta-contenedor">
                <img src="../../../public/imagenes/aulapro.png" alt="AulaPro" class="silhueta-imagen">
            </div>
        </div>

        <div class="about-contenido">
            <h2 class="about-titulo">SOBRE MÍ</h2>
            <h3 class="about-subtitulo">Yassin Lahhit — DAW · CPS Ibaiondo</h3>
            <p class="about-descripcion">
                ¡Hola! Soy <strong>Yassin Lahhit</strong>, desarrollador Full Stack apasionado por crear soluciones digitales que marquen la diferencia. 
                <strong>AulaPro</strong> es el resultado de mi Trabajo de Fin de Grado (TFG) en el <strong>CPS Ibaiondo</strong>, un proyecto nacido con la ambición de centralizar la gestión académica en una plataforma moderna, fluida y eficiente.
            </p>
            <p class="about-descripcion">
                He construido este ecosistema íntegramente desde sus cimientos, diseñando una arquitectura <strong>MVC personalizada</strong> en PHP, una base de datos MySQL robusta y una interfaz de usuario reactiva desarrollada con <strong>CSS puro</strong>. 
                Mi filosofía se basa en el dominio de las tecnologías base para garantizar un rendimiento óptimo y un código limpio, priorizando siempre la escalabilidad y una experiencia de usuario intuitiva.
            </p>

            <div class="about-stack">
                <span>PHP</span>
                <span>MySQL</span>
                <span>JavaScript</span>
                <span>CSS</span>
                <span>MVC</span>
                <span>jQuery</span>
                <span>Firebase</span>
                <span>Brevo API</span>
                <span>XAMPP</span>
                <span>Font Awesome</span>
            </div>

            <div class="about-acciones mt-25">
                <a href="#" class="boton-primario">
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
