<?php
		class Conexion{
			public $ruta;
			public $usuario;
			public $contrasena;
			public $baseDatos;

			function __construct(){
				$this->ruta       ="localhost"; // Servidor
				$this->usuario    ="admisiones"; // Usuario que tengas definido
				$this->contrasena ="2?62Pnce4"; // Contraseña que tengas definidad
				$this->baseDatos  ="admisiones"; // Base de datos (no el nombre de la tabla)
			}

			function conectarse(){
				//---------------------------TIPO DE CONEXION 1-----------------------------------
				/*$conectarse= mysql_connect($this->ruta,$this->usuario, $this->contrasena) or die(mysql_error()); //conexion al BD
				if($conectarse){
					mysql_select_db($this->baseDatos);
					return($conectarse);
				}else{
					return ("Error");
					}*/
				//------------------------TIPO DE CONEXION 2 - RECOMENDADA---------------------------------------------
				$enlace = mysqli_connect($this->ruta, $this->usuario, $this->contrasena, $this->baseDatos);
				if($enlace){
					// echo "Conexion exitosa";	//si la conexion fue exitosa nos muestra este mensaje como prueba, despues lo puedes poner comentarios de nuevo: //
				}else{
					die('Error de Conexión (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
				}
				return($enlace);
				//mysqli_close($enlace); //cierra la conexion a nuestra base de datos, un punto de seguridad importante.
			}
		}

?>
