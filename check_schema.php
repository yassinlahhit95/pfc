<?php
$c = mysqli_connect('localhost', 'root', '', 'pfc1');
if (!$c) die("Connect failed: " . mysqli_connect_error());
$res = mysqli_query($c, 'SHOW CREATE TABLE calificaciones_modulos');
if (!$res) die("Query failed: " . mysqli_error($c));
$row = mysqli_fetch_row($res);
echo $row[1];
