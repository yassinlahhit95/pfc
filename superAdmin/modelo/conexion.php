<?php
class Conexion
{
    public function conectar()
    {
        $conexion = new mysqli("localhost", "root", "", "pfc");
        if ($conexion->connect_error) {
            echo "Fallo al conectar a base de datos: " . $conexion->connect_error;
            die();
        }
        $conexion->set_charset("utf8mb4");
        return $conexion;
    }
}