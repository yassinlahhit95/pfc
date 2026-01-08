<?php

require_once "conexion.php";

class panelDeControl
{

    protected $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function contadorEstudiantes()
    {
        $sql1 = "SELECT COUNT(idEstudiante) AS contadorEstudiantes FROM estudiantes";
        $resultado = $this->conexion->query($sql1);
        $contadorEstudiantes = 0;
        $fila1 = $resultado->fetch_assoc();
        return $fila1['contadorEstudiantes'];
    }

    public function contadorProfesores()
    {
        $sql2 = "SELECT COUNT(idProfesor) AS contadorProfesores FROM profesores";
        $resultado2 = $this->conexion->query($sql2);
        $contadorProfesores = 0;
        $fila2 = $resultado2->fetch_assoc();
        return $fila2['contadorProfesores'];
    }

    public function contadorDirectores()
    {
        $sql3 = "SELECT COUNT(idDirector) AS contadorDirectores FROM directores";
        $resultado3 = $this->conexion->query($sql3);
        $contadorDirectores = 0;
        $fila3 = $resultado3->fetch_assoc();
        return $fila3['contadorDirectores'];
    }
}
