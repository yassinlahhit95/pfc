<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/planificacion.php";

$tareas = listarPlanificacion();
$rolBase = 'secretaria';

$titulo_pagina = "Planificación";
$seccion = 'planificacion';
include_once __DIR__ . "/../comunes/nav.php";

require __DIR__ . '/../../comunes/planificacion/_planificacion.php';
