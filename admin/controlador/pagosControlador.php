<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/pagos.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloPago = new pago($conexionBD);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores'], $_SESSION['datos_viejos']);

    if ($accion == 'insertar' || $accion == 'actualizar') {
        $errores = [];
        
        $idEstudiante = $_POST['idEstudiante'] ?? '';
        $concepto = trim($_POST['concepto']);
        $monto = trim($_POST['monto']);
        $tipoPago = $_POST['tipoPago'];
        $estadoPago = $_POST['estadoPago'];
        $fechaInput = trim($_POST['fechaPago']); // DD-MM-YYYY

        // --- VALIDACIONES ---
        if (empty($idEstudiante)) { $errores['idEstudiante'] = "Debe seleccionar un alumno."; }
        
        if (empty($concepto)) {
            $errores['concepto'] = "El concepto es obligatorio.";
        }

        if (empty($monto)) {
            $errores['monto'] = "El monto es obligatorio.";
        } else if (!is_numeric($monto)) {
            $errores['monto'] = "El monto debe ser un número.";
        }

        if (!empty($fechaInput) && !preg_match("/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/", $fechaInput)) {
            $errores['fechaPago'] = "Formato de fecha inválido (DD-MM-YYYY).";
        }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_viejos'] = $_POST;
            $url = ($accion == 'insertar') ? "agregarPagos.php" : "modificarPagos.php?id=" . $_POST['idPago'];
            header("Location: ../vistas/pagos/" . $url);
            exit;
        }

        // Convertir fecha para BD si existe
        $fechaBD = null;
        if (!empty($fechaInput)) {
            $partes = explode("-", $fechaInput);
            $fechaBD = $partes[2] . "-" . $partes[1] . "-" . $partes[0];
        }

        $datos = [
            'idEstudiante' => $idEstudiante,
            'concepto' => $concepto,
            'monto' => $monto,
            'tipoPago' => $tipoPago,
            'estadoPago' => $estadoPago,
            'fechaPago' => $fechaBD
        ];

        if ($accion == 'insertar') {
            if ($modeloPago->insertarPagoModelo($datos)) {
                $_SESSION['exito'] = "Pago registrado correctamente.";
            }
        }
        header("Location: ../vistas/pagos/verPagosGeneral.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idPago'];
        if ($modeloPago->eliminarPagoModelo($id)) {
            $_SESSION['exito'] = "Registro de pago eliminado.";
        }
        header("Location: ../vistas/pagos/verPagosGeneral.php");
        exit;
    }
}
?>