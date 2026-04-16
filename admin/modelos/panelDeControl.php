<?php
require_once "conectar.php";

class panelDeControl
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = getConnection();
    }

    public function __get($propiedad) {
        if (property_exists($this, $propiedad)) { return $this->$propiedad; }
    }

    public function contadorEstudiantes()
    {
        $sql = "SELECT COUNT(idEstudiante) AS total FROM estudiantes";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorProfesores()
    {
        $sql = "SELECT COUNT(idProfesor) AS total FROM profesores";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorDirectores()
    {
        $sql = "SELECT COUNT(idDirector) AS total FROM directores";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function totalRecaudado()
    {
        $sql = "SELECT SUM(monto) AS total FROM pagos WHERE estadoPago = 'pagado'";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'] ?? 0;
    }

    public function pagosPendientesContador()
    {
        $sql = "SELECT COUNT(idPago) AS total FROM pagos WHERE estadoPago = 'pendiente'";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorAulas()
    {
        $sql = "SELECT COUNT(idAula) AS total FROM aulas";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorCiclos()
    {
        $sql = "SELECT COUNT(idCiclo) AS total FROM ciclos";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorModulos()
    {
        $sql = "SELECT COUNT(idModulo) AS total FROM modulos";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public function contadorRetos()
    {
        $sql = "SELECT COUNT(idReto) AS total FROM retos";
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }
}
