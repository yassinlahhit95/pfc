<?php
$titulo_pagina = "Ver Calificaciones - Super Admin";
include_once "../comunes/nav.php";
?>



  <main class="contenido-principal">
    <!-- Page Header -->
    <div class="encabezado-pagina">
      <div>
        <h1>Calificaciones por Curso</h1>
        <p style="color: #8f9bba; margin-top: 5px">
          Consulta las calificaciones de los estudiantes por curso
        </p>
      </div>
    </div>

    <!-- Seleccionar Curso -->
    <p style="margin-bottom: 20px; color: #718096">
      Selecciona un curso para ver las calificaciones.
    </p>

    <!-- Grid de Cursos -->
    <div class="cuadricula-clases">
      <div class="tarjeta-clase" onclick="mostrarCalificaciones('DAW', this)">
        <div class="icono-clase"><i class="fas fa-laptop-code"></i></div>
        <div class="titulo-clase">DAW</div>
        <div class="subtitulo-clase">Desarrollo de Aplicaciones Web</div>
      </div>
      <div class="tarjeta-clase" onclick="mostrarCalificaciones('ASIR', this)">
        <div class="icono-clase"><i class="fas fa-server"></i></div>
        <div class="titulo-clase">ASIR</div>
        <div class="subtitulo-clase">Admin. Sistemas Informaticos</div>
      </div>
      <div class="tarjeta-clase" onclick="mostrarCalificaciones('GA', this)">
        <div class="icono-clase"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="titulo-clase">GA</div>
        <div class="subtitulo-clase">Gestion Administrativa</div>
      </div>
      <div class="tarjeta-clase" onclick="mostrarCalificaciones('SMR', this)">
        <div class="icono-clase"><i class="fas fa-microchip"></i></div>
        <div class="titulo-clase">SMR</div>
        <div class="subtitulo-clase">Sistemas Microinformaticos</div>
      </div>
      <div class="tarjeta-clase" onclick="mostrarCalificaciones('AYF', this)">
        <div class="icono-clase"><i class="fas fa-chart-line"></i></div>
        <div class="titulo-clase">AYF</div>
        <div class="subtitulo-clase">Administracion y Finanzas</div>
      </div>
    </div>

    <!-- Lista de Estudiantes -->
    <!-- Seccion de Calificaciones (oculta inicialmente) -->
    <div id="seccionCalificaciones" class="seccion-archivos">
      <div class="encabezado-tarjeta">
        <h3 id="tituloCursoSeleccionado">Calificaciones - DAW</h3>
        <div class="caja-busqueda">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Buscar estudiante..." />
        </div>
      </div>

      <!-- Lista de Estudiantes -->
      <div id="listaEstudiantes">
        <!-- Estudiante 1 -->
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <!-- Cabecera del Estudiante -->
          <div class="cabecera-estudiante">
            <div class="info-estudiante">
              <div class="avatar-estudiante avatar-azul">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <h3 class="nombre-estudiante">Juan Pérez García</h3>
                <p class="datos-estudiante">
                  <strong>DIE:</strong> 2406286D | <strong>Curso:</strong> 3º
                  ESO A
                </p>
              </div>
            </div>
            <button
              class="boton-primario"
              onclick="window.location.href='verDetallesCalificaciones.php'">
              <i class="fas fa-eye"></i> Ver Detalles
            </button>
          </div>

          <!-- Título de Calificaciones -->
          <div class="titulo-calificaciones">
            <h4>Calificaciones</h4>
          </div>

          <!-- Tabla de Materias -->
          <div class="contenedor-tabla-calificaciones">
            <table class="tabla-datos tabla-calificaciones">
              <thead>
                <tr>
                  <th class="encabezado-materia">MATERIA</th>
                  <th class="columna-nota">1EV</th>
                  <th class="columna-nota">R/E</th>
                  <th class="columna-nota">2EV</th>
                  <th class="columna-nota">3EV</th>
                  <th class="columna-nota">FINAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Matemáticas</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">8.5</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Lengua y Literatura</td>
                  <td class="celda-nota">7</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota-final">7.5</td>
                </tr>
                <tr>
                  <td>Inglés</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9.5</td>
                  <td class="celda-nota-final">9</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Ciencias Naturales</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota-final">8</td>
                </tr>
                <tr>
                  <td>Historia</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota-final">7.8</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Educación Física</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">9</td>
                </tr>
                <tr class="fila-promedio">
                  <td style="color: #1f2937">PROMEDIO GENERAL</td>
                  <td colspan="4" class="celda-nota">-</td>
                  <td class="celda-promedio">8.3</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Estudiante 2 -->
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="cabecera-estudiante">
            <div class="info-estudiante">
              <div class="avatar-estudiante avatar-naranja">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <h3 class="nombre-estudiante">María López Sánchez</h3>
                <p class="datos-estudiante">
                  <strong>DIE:</strong> 2406287E | <strong>Curso:</strong> 3º
                  ESO A
                </p>
              </div>
            </div>
            <button
              class="boton-primario"
              onclick="window.location.href='verDetallesCalificaciones.php'">
              <i class="fas fa-eye"></i> Ver Detalles
            </button>
          </div>

          <div class="titulo-calificaciones">
            <h4>Calificaciones</h4>
          </div>

          <div class="contenedor-tabla-calificaciones">
            <table class="tabla-datos tabla-calificaciones">
              <thead>
                <tr>
                  <th class="encabezado-materia">MATERIA</th>
                  <th class="columna-nota">1EV</th>
                  <th class="columna-nota">R/E</th>
                  <th class="columna-nota">2EV</th>
                  <th class="columna-nota">3EV</th>
                  <th class="columna-nota">FINAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Matemáticas</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9.5</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota-final">9.5</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Lengua y Literatura</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">8.8</td>
                </tr>
                <tr>
                  <td>Inglés</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota-final">10</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Ciencias Naturales</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9.5</td>
                  <td class="celda-nota-final">9</td>
                </tr>
                <tr>
                  <td>Historia</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">8.8</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Educación Física</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota">10</td>
                  <td class="celda-nota-final">10</td>
                </tr>
                <tr class="fila-promedio">
                  <td style="color: #1f2937">PROMEDIO GENERAL</td>
                  <td colspan="4" class="celda-nota">-</td>
                  <td class="celda-promedio">9.3</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Estudiante 3 -->
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="cabecera-estudiante">
            <div class="info-estudiante">
              <div class="avatar-estudiante avatar-verde">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <h3 class="nombre-estudiante">Carlos Rodríguez Martín</h3>
                <p class="datos-estudiante">
                  <strong>DIE:</strong> 2406288F | <strong>Curso:</strong> 3º
                  ESO A
                </p>
              </div>
            </div>
            <button
              class="boton-primario"
              onclick="window.location.href='verDetallesCalificaciones.php'">
              <i class="fas fa-eye"></i> Ver Detalles
            </button>
          </div>

          <div class="titulo-calificaciones">
            <h4>Calificaciones</h4>
          </div>

          <div class="contenedor-tabla-calificaciones">
            <table class="tabla-datos tabla-calificaciones">
              <thead>
                <tr>
                  <th class="encabezado-materia">MATERIA</th>
                  <th class="columna-nota">1EV</th>
                  <th class="columna-nota">R/E</th>
                  <th class="columna-nota">2EV</th>
                  <th class="columna-nota">3EV</th>
                  <th class="columna-nota">FINAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Matemáticas</td>
                  <td class="celda-nota">7</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota-final">7.5</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Lengua y Literatura</td>
                  <td class="celda-nota">6.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">7</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota-final">7</td>
                </tr>
                <tr>
                  <td>Inglés</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">8.5</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Ciencias Naturales</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota">8</td>
                  <td class="celda-nota-final">7.8</td>
                </tr>
                <tr>
                  <td>Historia</td>
                  <td class="celda-nota">7</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">7</td>
                  <td class="celda-nota">7.5</td>
                  <td class="celda-nota-final">7</td>
                </tr>
                <tr style="background: #f9fafb">
                  <td>Educación Física</td>
                  <td class="celda-nota">8.5</td>
                  <td class="celda-nota">-</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota">9</td>
                  <td class="celda-nota-final">8.8</td>
                </tr>
                <tr class="fila-promedio">
                  <td style="color: #1f2937">PROMEDIO GENERAL</td>
                  <td colspan="4" class="celda-nota">-</td>
                  <td class="celda-promedio">7.8</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- cierra seccionCalificaciones -->
  </main>
</div>
<!-- Scripts -->
<script src="../../js/menu.js"></script>
<script src="../../js/deleteModal.js"></script>
<script src="../../js/main.js"></script>
<script>
  function mostrarCalificaciones(nombreCurso, elementoTarjeta) {
    document.querySelectorAll(".tarjeta-clase").forEach(c => c.classList.remove("activa"));
    elementoTarjeta.classList.add("activa");
    const seccion = document.getElementById("seccionCalificaciones");
    const titulo = document.getElementById("tituloCursoSeleccionado");
    titulo.textContent = "Calificaciones - " + nombreCurso;
    seccion.classList.remove("visible");
    void seccion.offsetWidth;
    seccion.classList.add("visible");
  }
</script>
</body>

</html>