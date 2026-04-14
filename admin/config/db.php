<?php
	$bd = new mysqli("localhost", "root", "", "pfc");
   	if ($bd->connect_error) {
		die ( '<br>Imposible conectar con la base de datos ' . $bd->connect_errno );
	}
?>
