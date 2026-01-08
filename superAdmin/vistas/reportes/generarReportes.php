<?php
$titulo_pagina = "Generar Reportes - Super Admin";
include_once "../comunes/nav.php";
?>


      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Generar Reportes</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Generación de informes del sistema
            </p>
          </div>
          <div class="acciones-pagina">
            <div class="caja-busqueda">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Buscar reporte..." />
            </div>
            <a href="#" class="boton-primario">
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
                <th>Tipo Reporte</th>
                <th>Fecha Generación</th>
                <th>Usuario</th>
                <th>Formato</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#R001</td>
                <td>Rendimiento Académico</td>
                <td>01/12/2023</td>
                <td>Admin</td>
                <td>PDF</td>
                <td>
                  <span class="insignia-estado estado-activo">Completado</span>
                </td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesReportes.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarReportes.php"
                      class="boton-icono boton-editar"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button class="boton-icono boton-editar" title="Descargar">
                      <i class="fas fa-download"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>#R002</td>
                <td>Asistencia Mensual</td>
                <td>30/11/2023</td>
                <td>Admin</td>
                <td>Excel</td>
                <td>
                  <span class="insignia-estado estado-activo">Completado</span>
                </td>
                <td>
                  <div class="botones-accion">
                    <a
                      href="verDetallesReportes.php"
                      class="boton-icono boton-ver"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a
                      href="modificarReportes.php"
                      class="boton-icono boton-editar"
                      title="Editar"
                    >
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

