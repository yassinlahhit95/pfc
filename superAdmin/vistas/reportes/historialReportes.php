<?php
$titulo_pagina = "Historial Reportes - Super Admin";
include_once "../comunes/nav.php";
?>
<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Historial de Reportes</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Historial de reportes generados
      </p>
    </div>
    <div class="acciones-pagina">
      <div class="caja-busqueda">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar historial..." />
      </div>
      <a href="generarReportes.php" class="boton-primario">
        <i class="fas fa-plus"></i>
        Nuevo Reporte
      </a>
    </div>
  </div>

  <!-- Data Table -->
  <div class="contenedor-tabla">
    <table class="tabla-datos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Reporte</th>
          <th>Fecha</th>
          <th>Generado Por</th>
          <th>Detalle</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#H001</td>
          <td>Reporte Anual 2022</td>
          <td>15/01/2023</td>
          <td>Admin</td>
          <td>Resumen general</td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesReportes.php"
                class="boton-icono boton-ver"
                title="Ver detalles">
                <i class="fas fa-eye"></i>
              </a>
              <a
                href="modificarReportes.php"
                class="boton-icono boton-editar"
                title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <button class="boton-icono boton-editar" title="Descargar">
                <i class="fas fa-download"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#H002</td>
          <td>Reporte Notas 1º Trimestre</td>
          <td>20/12/2022</td>
          <td>Admin</td>
          <td>Notas por curso</td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesReportes.php"
                class="boton-icono boton-ver"
                title="Ver detalles">
                <i class="fas fa-eye"></i>
              </a>
              <a
                href="modificarReportes.php"
                class="boton-icono boton-editar"
                title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <button class="boton-icono boton-editar" title="Descargar">
                <i class="fas fa-download"></i>
              </button>
            </div>
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
            <li class="tiene-submenu abierto">
              <a href="#" class="enlace-menu">
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
                <span class="flecha">&#9662;</span>
              </a>
              <ul class="submenu">
                <li>
                  <a href="generarReportes.php">Generar Reporte</a>
                </li>
                <li>
                  <a href="historialReportes.php" class="activo">Historial</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="../configuracion.php" class="enlace-menu">
                <i class="fas fa-cog"></i><span>Configuración</span>
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
              <!-- Page Header -->
              <div class="encabezado-pagina">
                <div>
                  <h1>Historial de Reportes</h1>
                  <p style="color: #8f9bba; margin-top: 5px">
                    Historial de reportes generados
                  </p>
                </div>
                <div class="acciones-pagina">
                  <div class="caja-busqueda">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar historial..." />
                  </div>
                  <a href="generarReportes.php" class="boton-primario">
                    <i class="fas fa-plus"></i>
                    Nuevo Reporte
                  </a>
                </div>
              </div>

              <!-- Data Table -->
              <div class="contenedor-tabla">
                <table class="tabla-datos">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Reporte</th>
                      <th>Fecha</th>
                      <th>Generado Por</th>
                      <th>Detalle</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>#H001</td>
                      <td>Reporte Anual 2022</td>
                      <td>15/01/2023</td>
                      <td>Admin</td>
                      <td>Resumen general</td>
                      <td>
                        <div class="botones-accion">
                          <a
                            href="verDetallesReportes.php"
                            class="boton-icono boton-ver"
                            title="Ver detalles">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a
                            href="modificarReportes.php"
                            class="boton-icono boton-editar"
                            title="Editar">
                            <i class="fas fa-edit"></i>
                          </a>
                          <button class="boton-icono boton-editar" title="Descargar">
                            <i class="fas fa-download"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>#H002</td>
                      <td>Reporte Notas 1º Trimestre</td>
                      <td>20/12/2022</td>
                      <td>Admin</td>
                      <td>Notas por curso</td>
                      <td>
                        <div class="botones-accion">
                          <a
                            href="verDetallesReportes.php"
                            class="boton-icono boton-ver"
                            title="Ver detalles">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a
                            href="modificarReportes.php"
                            class="boton-icono boton-editar"
                            title="Editar">
                            <i class="fas fa-edit"></i>
                          </a>
                          <button class="boton-icono boton-editar" title="Descargar">
                            <i class="fas fa-download"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>


              </div>
            </main>
  </div>
  <!-- Scripts -->
  <script src="../../js/menu.js"></script>
  <script src="../../js/deleteModal.js"></script>
  <script src="../../js/main.js"></script>

  </body>

  </html>