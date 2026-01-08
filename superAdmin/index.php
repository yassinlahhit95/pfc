<?php

session_start();
require_once "./modelo/panelDeControl.php";
require_once "./modelo/conexion.php";

$con = new Conexion();
$conexion = $con->conectar();

$panelDeControl = new panelDeControl($conexion);
$contadorEstudiantes = $panelDeControl->contadorEstudiantes();
$contadorProfesores = $panelDeControl->contadorProfesores();
$contadorDirectores = $panelDeControl->contadorDirectores();
?>

<!DOCTYPE php>
<php lang="es">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Super Admin</title>
    <link rel="stylesheet" href="estiloSuperAdmin/estiloSuperAdmin.css" />
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
              <div class="titulo-proyecto">Projet</div>
              <button class="boton-menu" id="botonMenu">
                <span>&#9776;</span>
              </button>
            </div>
            <div class="subtitulo-admin">Super Admin</div>
          </div>
          <ul class="lista-navegacion">
            <li>
              <a href="index.php" class="enlace-menu activo">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="tiene-submenu">
              <a href="#" class="enlace-menu">
                <i class="fas fa-user-graduate"></i>
                <span>Estudiantes</span>
                <span class="flecha">&#9662;</span>
              </a>
              <ul class="submenu">
                <li>
                  <a href="vistas/estudiantes/verEstudiantes.php">Ver Estudiantes</a>
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
                  <a href="vistas/profesores/verProfesores.php">Ver Profesores</a>
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
                  <a href="vistas/directores/verDirectores.php">Ver Directores</a>
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
                <li><a href="vistas/cursos/verCursos.php">Ver Cursos</a></li>
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
                  <a href="vistas/materias/verMaterias.php">Ver Materias</a>
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
                  <a href="vistas/horarios/verHorarios.php">Ver Horarios</a>
                </li>
                <li>
                  <a href="vistas/horarios/generarHorarios.php">Generar Horario</a>
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
                  <a href="vistas/asistencia/gestionAsistencia.php">Gestionar Ausencias</a>
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
                <li><a href="vistas/notas/verNotas.php">Ver Notas</a></li>
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
                  <a href="vistas/calificaciones/verCalificaciones.php">Ver Calificaciones</a>
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
                  <a href="vistas/reportes/generarReportes.php">Generar Reporte</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="vistas/configuracion.php" class="enlace-menu">
                <i class="fas fa-cog"></i><span>Configuración</span>
              </a>
            </li>
            <li>
              <a href="vistas/notificaciones.php" class="enlace-menu">
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

      <main class="contenido-principal">
        <!-- Header del Dashboard -->
        <div class="encabezado-panel">
          <div>
            <h1>Panel de Control</h1>
            <p class="subtitulo-panel">Bienvenido de nuevo, Super Admin</p>
          </div>
          <div class="acciones-encabezado">
            <button class="boton-notificaciones">
              <i class="fas fa-bell"></i>
              <span class="insignia-notificacion">3</span>
            </button>
          </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="cuadricula-estadisticas">
          <div class="tarjeta-estadistica tarjeta-estadistica-azul">
            <div class="icono-estadistica">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div class="info-estadistica">

              <h3><?php echo $contadorEstudiantes; ?></h3>
              <p>Estudiantes</p>
              <span class="cambio-estadistica positivo">+12% este mes</span>
            </div>
          </div>

          <div class="tarjeta-estadistica tarjeta-estadistica-verde">
            <div class="icono-estadistica">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="info-estadistica">
              <h3><?php echo $contadorProfesores; ?></h3>
              <p>Profesores</p>
              <span class="cambio-estadistica positivo">+5% este mes</span>
            </div>
          </div>

          <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
            <div class="icono-estadistica">
              <i class="fas fa-book"></i>
            </div>
            <div class="info-estadistica">
              <h3>45</h3>
              <p>Cursos Activos</p>
              <span class="cambio-estadistica neutral">Sin cambios</span>
            </div>
          </div>

          <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
            <div class="icono-estadistica">
              <i class="fas fa-calendar-check"></i>
            </div>
            <div class="info-estadistica">
              <h3>98.5%</h3>
              <p>Asistencia</p>
              <span class="cambio-estadistica positivo">+2.3% este mes</span>
            </div>
          </div>
        </div>

        <!-- Sección Inferior: Notificaciones y Tareas -->
        <div class="cuadricula-secundaria">
          <!-- Notificaciones Simplificadas -->
          <div class="tarjeta-panel tarjeta-notificaciones">
            <div class="encabezado-tarjeta">
              <h3>Notificaciones Recientes</h3>
              <a href="vistas/notificaciones.php" class="ver-todo">Ver todas</a>
            </div>
            <div class="lista-notificaciones" id="listaNotificacionesDashboard">
              <!-- Las notificaciones se cargarán desde PHP -->
              <div class="elemento-notificacion">
                <div class="contenido-notificacion">
                  <p class="titulo-notificacion">Reunión de profesores</p>
                  <span class="tiempo-notificacion">Hace 10 minutos</span>
                </div>
              </div>
              <div class="elemento-notificacion">
                <div class="contenido-notificacion">
                  <p class="titulo-notificacion">Actualización del sistema</p>
                  <span class="tiempo-notificacion">Hace 1 hora</span>
                </div>
              </div>
              <div class="elemento-notificacion">
                <div class="contenido-notificacion">
                  <p class="titulo-notificacion">Reporte mensual generado</p>
                  <span class="tiempo-notificacion">Hace 5 horas</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Acciones Rápidas -->
          <div class="tarjeta-panel tarjeta-acciones-rapidas">
            <div class="encabezado-tarjeta">
              <h3>Acciones Rápidas</h3>
            </div>
            <div class="cuadricula-acciones-rapidas">
              <a
                href="vistas/estudiantes/agregarEstudiantes.php"
                class="accion-rapida">
                <i class="fas fa-user-plus"></i>
                <span>Agregar Estudiante</span>
              </a>
              <a
                href="vistas/profesores/agregarProfesores.php"
                class="accion-rapida">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Agregar Profesor</span>
              </a>
              <a href="vistas/cursos/agregarCursos.php" class="accion-rapida">
                <i class="fas fa-book"></i>
                <span>Crear Curso</span>
              </a>
              <a
                href="vistas/reportes/generarReportes.php"
                class="accion-rapida">
                <i class="fas fa-chart-bar"></i>
                <span>Generar Reporte</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Sección: Actividad Reciente y Próximos Eventos -->
        <div class="cuadricula-secundaria" style="margin-top: 30px">
          <!-- Actividad Reciente -->
          <div class="tarjeta-panel">
            <div class="encabezado-tarjeta">
              <h3>
                <i
                  class="fas fa-history"
                  style="color: #667eea; margin-right: 10px"></i>Actividad Reciente
              </h3>
            </div>
            <div class="lista-actividad">
              <div
                class="elemento-actividad"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div
                  style="
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #dbeafe;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #2563eb;
    ">
                  <i class="fas fa-user-plus"></i>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Nuevo estudiante registrado
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    María García López - DAW
                  </p>
                </div>
                <span style="font-size: 12px; color: #94a3b8">Hace 2h</span>
              </div>
              <div
                class="elemento-actividad"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div
                  style="
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #d1fae5;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #059669;
    ">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Notas actualizadas
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    ASIR - 1ª Evaluación
                  </p>
                </div>
                <span style="font-size: 12px; color: #94a3b8">Hace 4h</span>
              </div>
              <div
                class="elemento-actividad"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div
                  style="
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #fef3c7;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #d97706;
    ">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Horario modificado
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    GA - Cambio de aula
                  </p>
                </div>
                <span style="font-size: 12px; color: #94a3b8">Hace 6h</span>
              </div>
              <div
                class="elemento-actividad"
                style="display: flex; gap: 15px; padding: 12px 0">
                <div
                  style="
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #ede9fe;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #7c3aed;
    ">
                  <i class="fas fa-file-alt"></i>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Reporte generado
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    Informe mensual - Noviembre 2024
                  </p>
                </div>
                <span style="font-size: 12px; color: #94a3b8">Ayer</span>
              </div>
            </div>
          </div>

          <!-- Próximos Eventos -->
          <div class="tarjeta-panel">
            <div class="encabezado-tarjeta">
              <h3>
                <i
                  class="fas fa-calendar-week"
                  style="color: #10b981; margin-right: 10px"></i>Próximos Eventos
              </h3>
            </div>
            <div class="lista-eventos">
              <div
                class="elemento-evento"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div style="text-align: center; min-width: 50px">
                  <div
                    style="font-size: 24px; font-weight: 700; color: #667eea">
                    10
                  </div>
                  <div
                    style="
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
      ">
                    DIC
                  </div>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Exámenes 1ª Evaluación
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    <i class="fas fa-clock" style="margin-right: 5px"></i>Todo
                    el día
                  </p>
                </div>
              </div>
              <div
                class="elemento-evento"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div style="text-align: center; min-width: 50px">
                  <div
                    style="font-size: 24px; font-weight: 700; color: #10b981">
                    15
                  </div>
                  <div
                    style="
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
      ">
                    DIC
                  </div>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Reunión con padres
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    <i class="fas fa-clock" style="margin-right: 5px"></i>17:00
                    - 19:00
                  </p>
                </div>
              </div>
              <div
                class="elemento-evento"
                style="
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  ">
                <div style="text-align: center; min-width: 50px">
                  <div
                    style="font-size: 24px; font-weight: 700; color: #f59e0b">
                    20
                  </div>
                  <div
                    style="
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
      ">
                    DIC
                  </div>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Entrega de notas
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    <i class="fas fa-clock" style="margin-right: 5px"></i>09:00
                    - 14:00
                  </p>
                </div>
              </div>
              <div
                class="elemento-evento"
                style="display: flex; gap: 15px; padding: 12px 0">
                <div style="text-align: center; min-width: 50px">
                  <div
                    style="font-size: 24px; font-weight: 700; color: #dc2626">
                    22
                  </div>
                  <div
                    style="
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
      ">
                    DIC
                  </div>
                </div>
                <div style="flex: 1">
                  <p
                    style="font-weight: 600; color: #2d3748; margin-bottom: 3px">
                    Vacaciones de Navidad
                  </p>
                  <p style="font-size: 13px; color: #64748b">
                    <i class="fas fa-calendar" style="margin-right: 5px"></i>Hasta 7 de Enero
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer del Creador -->
        <footer
          style="
margin-top: 50px;
padding: 40px 0;
border-top: 1px solid #e2e8f0;
">
          <div
            style="
display: flex;
flex-wrap: wrap;
gap: 40px;
justify-content: space-between;
align-items: center;
">
            <!-- Info del Creador -->
            <div style="flex: 1; min-width: 300px">
              <div
                style="
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
  ">
                <div
                  style="
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(
        135deg,
        #667eea 0%,
        #764ba2 100%
      );
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      font-weight: 700;
    ">
                  YL
                </div>
                <div>
                  <h3
                    style="font-size: 20px; color: #1f2937; margin-bottom: 3px">
                    YASSIN LAHHIT
                  </h3>
                  <p style="color: #64748b; font-size: 14px">
                    Full Stack Developer
                  </p>
                </div>
              </div>
              <p
                style="
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
    max-width: 400px;
  ">
                Proyecto de Fin de Ciclo - Sistema de Gestión Educativa.
                Desarrollado con pasión para facilitar la administración
                académica.
              </p>
            </div>

            <!-- Enlaces de Contacto -->
            <div style="display: flex; gap: 30px; flex-wrap: wrap">
              <div>
                <h4
                  style="
      color: #1f2937;
      font-size: 14px;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
    ">
                  Contacto
                </h4>
                <div style="display: flex; flex-direction: column; gap: 8px">
                  <a
                    href="mailto:yassin.lahhit@gmail.com"
                    style="
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
      ">
                    <i
                      class="fas fa-envelope"
                      style="color: #667eea; width: 16px"></i>
                    yassin.lahhit@gmail.com
                  </a>

                  <a
                    href="mailto:yassin.lahhit@outlook.com"
                    style="
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
      ">
                    <i
                      class="fas fa-envelope"
                      style="color: #667eea; width: 16px"></i>
                    yassin.lahhit@outlook.com
                  </a>
                  <a
                    href="tel:+34632104011"
                    style="
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
      ">
                    <i
                      class="fas fa-phone"
                      style="color: #667eea; width: 16px"></i>
                    +34 632 104 011
                  </a>
                </div>
              </div>
              <div>
                <h4
                  style="
      color: #1f2937;
      font-size: 14px;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
    ">
                  Redes
                </h4>
                <div style="display: flex; gap: 12px">
                  <a
                    href="https://github.com/yassinlahhit"
                    target="_blank"
                    style="
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #1f2937;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform 0.3s, box-shadow 0.3s;
      "
                    title="GitHub">
                    <i class="fab fa-github"></i>
                  </a>
                  <a
                    href="https://linkedin.com/in/yassinlahhit"
                    target="_blank"
                    style="
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0077b5;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform 0.3s, box-shadow 0.3s;
      "
                    title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                  </a>
                  <a
                    href="https://twitter.com/yassinlahhit"
                    target="_blank"
                    style="
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #1da1f2;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform 0.3s, box-shadow 0.3s;
      "
                    title="Twitter">
                    <i class="fab fa-twitter"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Copyright -->
          <div
            style="
margin-top: 30px;
padding-top: 20px;
border-top: 1px solid #e2e8f0;
text-align: center;
">
            <p style="color: #94a3b8; font-size: 13px">
              © 2025 - 2026 <strong style="color: #667eea">YASSIN LAHHIT</strong> -
              Sistema de Gestión Educativa | Proyecto de Fin de Curso DAW
            </p>

          </div>
        </footer>
      </main>
    </div>

    <!-- Scripts -->
    <script src="js/menu.js"></script>

    <script src="js/main.js"></script>
  </body>
</php>