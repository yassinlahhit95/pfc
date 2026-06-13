<?php
	header('Content-type:application/xls;charset=utf8_spanish_ci');
	header('Content-Disposition: attachment; filename=formulario_ciclo.xls');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();

	$subs_dni = utf8_decode($_POST['dni']);

	mysqli_set_charset( $link, 'utf8');
	

	$query="SELECT ID , DNI , Nombre , Apellidos , Ciclo FROM admisiones WHERE DNI ='" .utf8_encode($subs_dni). "'";

	$result=mysqli_query($link, $query);
?>

<table border="1">
	<tr>
		<th style="background-color:lightblue">ID</th>
		<th style="background-color:lightblue">DNI</th>
		<th style="background-color:lightblue">Nombre</th>
		<th style="background-color:lightblue">Apellidos</th>
		<th style="background-color:lightblue">Ciclo</th>
	</tr>
	<?php
		while ($row=mysqli_fetch_assoc($result)) {
			?>
				<tr>
					<td><?php echo $row['ID']; ?></td>
					<td><?php echo $row['DNI']; ?></td>
					<td><?php echo $row['Nombre']; ?></td>
					<td><?php echo $row['Apellidos']; ?></td>
					<td>
						<?php 
						          switch ($row['Ciclo']) {
						    case 0:
						        echo "Cuidados Auxiliares de Enfermería";
						        break;
						    case 1:
						        echo "Laboratorio Clínico y Biomédico";
						        break;
						    case 2:
						        echo "Imagen para el Diagnóstico y Medicina Nuclear";
						        break;
						    case 3:
						        echo "Farmacia y Parafarmacia";
						        break;
						    case 4:
						        echo "Documentación y Administración Sanitaria";
						        break;
						    case 5:
						        echo "Formación Profesional Básica";
						        break;
						    case 6:
						        echo "Atención a Personas en Situación de Dependencia";
						        break;
						    case 7:
						        echo "Integración Social";
						        break;
						    case 8:
						        echo "Educación Infantil";
						        break;
						    case 9:
						        echo "Gestión Administrativa";
						        break;
						    case 10:
						        echo "Administración y Finanzas";
						        break;
						    case 11:
						        echo "Marketing y Publicidad";
						        break;
							case 12:
						        echo "Radioterapia y dosimetría";
						        break;
							case 13:
						        echo "Emergencias Sanitarias";
						        break;
						          } ?> 
					</td>
				</tr>	

			<?php
		}

	?>
</table>