<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/estudiantes.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new estudiante($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    // Limpiar errores anteriores
    unset($_SESSION['error_nombre']);
    unset($_SESSION['error_email']);
    unset($_SESSION['error_dni']);
    unset($_SESSION['datos']);

    if ($accion == 'insertar' || $accion == 'actualizar') {
        
        // 1. Recoger datos y quitar espacios con trim
        $nombre = trim($_POST['nombreEstudiante']);
        $email = trim($_POST['emailEstudiante']);
        $dni = trim($_POST['dniEstudiante']);
        $telefono = trim($_POST['telefonoEstudiante']);
        $direccion = trim($_POST['direccionEstudiante']);
        $fecha = trim($_POST['fechaNacimientoEstudiante']);
        $curso = $_POST['idCurso'];
        $estado = $_POST['idEstado'];

        $hayError = false;

        // 2. Validaciones muy simples (Nivel Principiante)
        if ($nombre == "") {
            $_SESSION['error_nombre'] = "Debes escribir el nombre";
            $hayError = true;
        }

        if ($email == "") {
            $_SESSION['error_email'] = "Debes escribir el email";
            $hayError = true;
        }

        if ($dni == "") {
            $_SESSION['error_dni'] = "Debes escribir el DNI";
            $hayError = true;
        }

        // 3. Si hay errores, volver al formulario
        if ($hayError == true) {
            $_SESSION['datos'] = $_POST; // Guardar lo que escribió
            if ($accion == 'insertar') {
                header("Location: ../vistas/estudiantes/agregarEstudiantes.php");
            } else {
                header("Location: ../vistas/estudiantes/modificarEstudiantes.php?id=" . $_POST['idEstudiante']);
            }
            exit;
        }

        // 4. Preparar datos para el modelo
        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'dni' => $dni,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'fechaNac' => $fecha,
            'idCurso' => $curso,
            'idEstado' => $estado
        ];

        // 5. Guardar
        if ($accion == 'insertar') {
            $modelo->insertarEstudianteModelo($datos);
            $_SESSION['exito'] = "Estudiante guardado";
        } else {
            $datos['id'] = $_POST['idEstudiante'];
            $modelo->actualizarEstudianteModelo($datos);
            $_SESSION['exito'] = "Estudiante actualizado";
        }

        header("Location: ../vistas/estudiantes/verEstudiantes.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idEstudiante'];
        $modelo->eliminarEstudianteModelo($id);
        $_SESSION['exito'] = "Estudiante borrado";
        header("Location: ../vistas/estudiantes/verEstudiantes.php");
        exit;
    }
}
?>