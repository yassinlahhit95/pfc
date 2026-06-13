<?php
	header('Content-type:application/xls;charset=utf8_spanish_ci');
	header('Content-Disposition: attachment; filename=formulario_ciclo.xls');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();

	$subs_ciclo = utf8_decode($_POST['ciclo']);

	mysqli_set_charset( $link, 'utf8');
	

	$query="SELECT ID , Nombre , Apellidos , DNI , Telefono , Email , Estudios , Ciclo_1 , Ciclo_2 , Ciclo_3, Preinscripcion, Registro FROM espera WHERE Ciclo_1 ='" .utf8_encode($subs_ciclo). "'";



	$result=mysqli_query($link, $query);
?>

<table border="1">
	<tr>
		<th style="background-color:lightblue">ID</th>
		<th style="background-color:lightblue">Nombre</th>
		<th style="background-color:lightblue">Apellidos</th>
		<th style="background-color:lightblue">DNI</th>
		<th style="background-color:lightblue">Telefono</th>
		<th style="background-color:lightblue">Email</th>
		<th style="background-color:lightblue">Estudios</th>
		<th style="background-color:lightblue">Primera opción</th>
		<th style="background-color:lightblue">Segunda opción</th>
		<th style="background-color:lightblue">Tercera opción</th>
		<th style="background-color:lightblue">Preinscripción</th>
		<th style="background-color:lightblue">Fecha de registro</th>
	</tr>
	<?php
		while ($row=mysqli_fetch_assoc($result)) {
			?>
				<tr>
					<td><?php echo $row['ID']; ?></td>
					<td><?php echo $row['Nombre']; ?></td>
					<td><?php echo $row['Apellidos']; ?></td>
					<td><?php echo $row['DNI']; ?></td>
					<td><?php echo $row['Telefono']; ?></td>
					<td><?php echo $row['Email']; ?></td>
					<td><?php echo $row['Estudios']; ?></td>
					<td><?php echo $row['Ciclo_1']; ?></td>
					<td><?php echo $row['Ciclo_2']; ?></td>
					<td><?php echo $row['Ciclo_3']; ?></td>
					<td><?php echo $row['Preinscripcion']; ?></td>
					<td><?php echo $row['Registro']; ?></td>
				</tr>	

			<?php
		}

	?>
</table>