<?php
session_start();
require_once "../modelos/conectar.php";

if (isset($_POST["enviar"])) {
    if (!isset($_POST["usuario"])) {
        $_SESSION["error"] = "Error en el campo email.";
        header("Location: /pfc/index.php");
        exit;
    } else {
        $email = strip_tags(trim($_POST["usuario"]));
        if (empty($email)) {
            $_SESSION["error"] = "Email vacio.";
            header("Location: /pfc/index.php");
            exit;
        } else {
            if (!isset($_POST["contrasena"])) {
                $_SESSION["error"] = "Error en el campo contraseña.";
                header("Location: /pfc/index.php");
                exit;
            } else {
                $pass = strip_tags(trim($_POST["contrasena"]));
                if (empty($pass)) {
                    $_SESSION["error"] = "Contraseña vacia.";
                    header("Location: /pfc/index.php");
                    exit;
                } else {
                    $con = obtenerConexion();

                    // Limpiar roles previos para evitar conflictos
                    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

                    // 1. Intentamos buscar en DIRECTORES (Admin)
                    $sqlAdmin = "SELECT idDirector FROM directores WHERE emailDirector = '$email' AND password = '$pass'";
                    $resAdmin = mysqli_query($con, $sqlAdmin);
                    $fAdmin = mysqli_fetch_assoc($resAdmin);

                    if ($fAdmin) {
                        $_SESSION["idAdmin"] = $fAdmin['idDirector'];
                        header("Location: /pfc/vistas/admin/dashboard.php");
                        exit;
                    } else {
                        // 2. Intentamos buscar en PROFESORES
                        $sqlProf = "SELECT idProfesor FROM profesores WHERE emailProfesor = '$email' AND password = '$pass'";
                        $resProf = mysqli_query($con, $sqlProf);
                        $fProf = mysqli_fetch_assoc($resProf);

                        if ($fProf) {
                            $_SESSION["idProfesor"] = $fProf['idProfesor'];
                            header("Location: /pfc/vistas/profesores/dashboard.php");
                            exit;
                        } else {
                            // 3. Intentamos buscar en ESTUDIANTES
                            $sqlEst = "SELECT idEstudiante FROM estudiantes WHERE emailEstudiante = '$email' AND password = '$pass'";
                            $resEst = mysqli_query($con, $sqlEst);
                            $fEst = mysqli_fetch_assoc($resEst);

                            if ($fEst) {
                                $_SESSION["idEstudiante"] = $fEst['idEstudiante'];
                                header("Location: /pfc/vistas/estudiantes/dashboard.php");
                                exit;
                            } else {
                                $_SESSION["error"] = "Credenciales incorrectas.";
                                header("Location: /pfc/index.php");
                                exit;
                            }
                        }
                    }
                    mysqli_close($con);
                }
            }
        }
    }
}
?>