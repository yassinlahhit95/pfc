<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notificaciones - Super Admin</title>
    <link rel="stylesheet" href="../estiloSuperAdmin/estiloSuperAdmin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="contenedor-admin">
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
              <a href="../index.php" class="enlace-menu">
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
                  <a href="estudiantes/verEstudiantes.php">Ver Estudiantes</a>
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
                  <a href="profesores/verProfesores.php">Ver Profesores</a>
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
                  <a href="directores/verDirectores.php">Ver Directores</a>
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
                <li><a href="cursos/verCursos.php">Ver Cursos</a></li>
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
                  <a href="materias/verMaterias.php">Ver Materias</a>
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
                  <a href="horarios/verHorarios.php">Ver Horarios</a>
                </li>
                <li>
                  <a href="horarios/generarHorarios.php">Generar Horario</a>
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
                  <a href="asistencia/gestionAsistencia.php"
                    >Gestionar Ausencias</a
                  >
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
                <li><a href="notas/verNotas.php">Ver Notas</a></li>
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
                  <a href="calificaciones/verCalificaciones.php"
                    >Ver Calificaciones</a
                  >
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
                  <a href="reportes/generarReportes.php">Generar Reporte</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="configuracion.php" class="enlace-menu">
                <i class="fas fa-cog"></i><span>Configuración</span>
              </a>
            </li>
            <li>
              <a href="notificaciones.php" class="enlace-menu activo">
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
        <div class="encabezado-pagina">
          <div>
            <h1>Notificaciones</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Gestión de avisos y notificaciones
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="../index.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
          </div>
        </div>

        <div class="tarjeta-panel">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-bell"></i> Crear Notificación</h3>
          </div>
          <form id="formularioNotificaciones">
            <div class="grupo-formulario">
              <label class="etiqueta-formulario">Título</label>
              <input
                id="titulo"
                type="text"
                placeholder="Asunto de la notificación"
                class="control-formulario"
                requerido
              />
            </div>
            <div class="grupo-formulario">
              <label class="etiqueta-formulario">Mensaje</label>
              <textarea
                id="mensaje"
                rows="3"
                placeholder="Escribe el mensaje"
                class="control-formulario"
                requerido
              ></textarea>
            </div>
            <div class="grupo-formulario">
              <label class="etiqueta-formulario">Enviar a</label>
              <select id="tipoDestinatario" class="control-formulario">
                <option value="profesores">Profesores</option>
                <option value="estudiantes">Estudiantes</option>
                <option value="ambos">Ambos</option>
              </select>
            </div>

            <!-- Contenedor para la lista dinámica de destinatarios -->
            <div
              id="listaDestinatarios"
              class="grupo-formulario"
              style="
                max-height: 200px;
                overflow-y: auto;
                padding: 10px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
              "
            >
              <!-- Se rellena via JS -->
            </div>

            <div class="botones-accion" style="margin-top: 20px">
              <button
                type="button"
                id="botonPrevisualizar"
                class="boton-secundario"
              >
                Previsualizar
              </button>
              <button type="submit" id="botonEnviar" class="boton-primario">
                Enviar
              </button>
            </div>
          </form>
        </div>

        <div class="tarjeta-panel" style="margin-top: 20px">
          <div class="encabezado-tarjeta">
            <h3>Notificaciones Guardadas</h3>
          </div>
          <div id="listaNotificaciones"></div>
        </div>
      </main>
    </div>

    <!-- Scripts -->
    <script src="../js/menu.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/main.js"></script>
  </body>
</html>

