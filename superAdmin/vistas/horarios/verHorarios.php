<?php
$titulo_pagina = "Ver Horario - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Horarios</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Gestión de horarios de clases
            </p>
          </div>
          <div class="acciones-pagina">
            <div class="caja-busqueda">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Buscar horario..." />
            </div>
            <a href="configurarHorarios.php" class="boton-primario">
              <i class="fas fa-cog"></i>
              Configurar Horario
            </a>
          </div>
        </div>

        <!-- Data Table -->
        <div class="contenedor-tabla">
          <table class="tabla-datos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Curso</th>
                <th>Día</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Materia</th>
                <th>Profesor</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#H001</td>
                <td>1º ESO A</td>
                <td>Lunes</td>
                <td>08:00</td>
                <td>09:00</td>
                <td>Matemáticas</td>
                <td>Laura Martínez</td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesHorarios.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarHorarios.php"
                      class="boton-icono boton-editar"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button class="boton-icono boton-eliminar" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>#H002</td>
                <td>1 ESO A</td>
                <td>Lunes</td>
                <td>09:00</td>
                <td>10:00</td>
                <td>Historia</td>
                <td>Roberto Sánchez</td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesHorarios.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarHorarios.php"
                      class="boton-icono boton-editar"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button class="boton-icono boton-eliminar" title="Eliminar">
                      <i class="fas fa-trash"></i>
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
