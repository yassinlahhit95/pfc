<?php
require_once 'c:/laragon/www/pfc/modelos/conectar.php';
$c = obtenerConexion();
if (mysqli_query($c, "ALTER TABLE gastos MODIFY archivoJustificante TEXT;")) {
    echo "Table altered successfully\n";
} else {
    echo "Error: " . mysqli_error($c) . "\n";
}
