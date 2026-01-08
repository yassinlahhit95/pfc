<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $titulo_pagina ?></title>
  <link rel="stylesheet" href="../../estiloSuperAdmin/estiloSuperAdmin.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="contenedor-admin">
    <!-- Botón externo para cuando el menú está oculto -->
    <button class="boton-menu-externo" id="botonMenuExterno">
      <span>&#9776;</span>
    </button>

    <header class="barra-lateral" id="barraLateral">
      <nav>
        <div class="contenedor-logo">
          <div class="logo-titulo-wrapper">
            <div class="titulo-proyecto">PFC</div>
            <button class="boton-menu" id="botonMenu">
              <span>&#9776;</span>
            </button>
          </div>
          <div class="subtitulo-admin">Super Admin</div>
        </div>
        <ul class="lista-navegacion">
          <li>
            <a href="../../index.php" class="enlace-menu">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <!--<li class="tiene-submenu abierto">-->
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
            <!--<a href="#" class="enlace-menu activo">-->
              <i class="fas fa-user-graduate"></i>
              <span>Estudiantes</span>
              <span class="flecha">&#9662;</span>
            </a>
            <!--<ul class="submenu">-->
            <ul class="submenu">
              <li>
                <a href="../estudiantes/verEstudiantes.php">Ver Estudiantes</a>
               <!-- <a href="../estudiantes/verEstudiantes.php" class="activo">Ver Estudiantes</a>-->
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-chalkboard-teacher"></i>
              <span>Profesores</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../profesores/verProfesores.php">Ver Profesores</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-user-tie"></i>
              <span>Directores</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../directores/verDirectores.php">Ver Directores</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-book"></i>
              <span>Cursos</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li><a href="../cursos/verCursos.php">Ver Cursos</a></li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-book-open"></i>
              <span>Materias</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../materias/verMaterias.php">Ver Materias</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-calendar-alt"></i>
              <span>Horarios</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../horarios/verHorarios.php">Ver Horarios</a>
              </li>
              <li>
                <a href="../horarios/generarHorarios.php">Generar Horario</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-user-check"></i>
              <span>Asistencia</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../asistencia/gestionAsistencia.php">Gestionar Ausencias</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-clipboard-list"></i>
              <span>Notas</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li><a href="../notas/verNotas.php">Ver Notas</a></li>
            </ul>
          </li>
		            <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-clipboard-list"></i>
              <span>Solicitudes</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li><a href="../notas/verNotas.php">Ver Notas</a></li>
            </ul>
          </li>
		  
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-star"></i>
              <span>Calificaciones</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../calificaciones/verCalificaciones.php">Ver Calificaciones</a>
              </li>
            </ul>
          </li>
          <li class="tiene-submenu">
            <a href="#" class="enlace-menu">
              <i class="fas fa-chart-bar"></i>
              <span>Reportes</span>
              <span class="flecha">&#9662;</span>
            </a>
            <ul class="submenu">
              <li>
                <a href="../reportes/generarReportes.php">Generar Reporte</a>
              </li>
            </ul>
          </li>
          <li>
            <a href="../configuracion.php" class="enlace-menu">
              <i class="fas fa-cog"></i><span>Configuración</span>
            </a>
          </li>
          <li>
            <a href="../notificaciones.php" class="enlace-menu">
              <i class="fas fa-bell"></i>
              <span>Notificaciones</span>
            </a>
          </li>
          <li>
            <a href="#" class="enlace-menu">
              <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
            </a>
          </li>
        </ul>
      </nav>
    </header>
