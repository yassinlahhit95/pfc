<?php
// Este archivo impide el listado de directorios en el servidor web
header("HTTP/1.0 403 Forbidden");
exit;
?>