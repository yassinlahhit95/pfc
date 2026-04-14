<?php
/**
 * Simple Database Connection
 */
require_once "C:/xampp/htdocs/pfc/admin/config/db.php";

class Conexion {
    public function conectar() {
        global $bd;
        return $bd;
    }
}
?>