<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/planificacion.php";

$tareas = listarPlanificacion();
$rolBase = 'admin';

$titulo_pagina = "Planificación";
$seccion = 'planificacion';
include_once __DIR__ . "/../comunes/nav.php";

require __DIR__ . '/../../comunes/planificacion/_planificacion.php';
