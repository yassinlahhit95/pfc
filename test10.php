<?php
require 'modelos/conectar.php';
$c = obtenerConexion();
$sql = "CREATE TABLE IF NOT EXISTS historial_secretarias (
    idHistorial INT AUTO_INCREMENT PRIMARY KEY,
    idSecretaria INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    entidad VARCHAR(100) NOT NULL,
    detalles TEXT,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idSecretaria) REFERENCES secretarias(idSecretaria) ON DELETE CASCADE
);";
if (mysqli_query($c, $sql)) {
    echo "Table created successfully\n";
} else {
    echo "Error: " . mysqli_error($c) . "\n";
}
