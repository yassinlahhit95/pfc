<?php
$titulo_pagina = "Ver Notas - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Notas</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Gestión de notas y evaluaciones
            </p>
          </div>
          <div class="acciones-pagina">
            <div class="caja-busqueda">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Buscar nota..." />
            </div>
            <a href="ingresarNotas.php" class="boton-primario">
              <i class="fas fa-plus"></i>
              Ingresar Notas
            </a>
          </div>
        </div>

        <!-- Data Table -->
        <div class="contenedor-tabla">
          <table class="tabla-datos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Materia</th>
                <th>Evaluación</th>
                <th>Nota</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#N001</td>
                <td>Juan Pérez García</td>
                <td>Matemáticas</td>
                <td>Examen Parcial</td>
                <td>8.5</td>
                <td>15/10/2023</td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesNotas.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarNotas.php"
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
                <td>#N002</td>
                <td>María López Sánchez</td>
                <td>Historia</td>
                <td>Trabajo Final</td>
                <td>9.0</td>
                <td>20/10/2023</td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesNotas.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarNotas.php"
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

