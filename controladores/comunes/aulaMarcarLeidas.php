<?php
session_start();
require_once __DIR__ . "/../../modelos/aula.php";

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(200);
    exit;
}
if (!empty($_SESSION['idProfesor'])) {
    marcarTodasLeidasAula($_SESSION['idProfesor'], 'profesor');
} elseif (!empty($_SESSION['idEstudiante'])) {
    marcarTodasLeidasAula($_SESSION['idEstudiante'], 'estudiante');
}
http_response_code(200);
