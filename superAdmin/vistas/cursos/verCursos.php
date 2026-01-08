<?php
$titulo_pagina = "Ver Cursos - Super Admin";
include_once "../comunes/nav.php";
?>
<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Cursos</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Gestión de cursos académicos
      </p>
    </div>
    <div class="acciones-pagina">
      <div class="caja-busqueda">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar curso..." />
      </div>
      <a href="agregarCursos.php" class="boton-primario">
        <i class="fas fa-plus"></i>
        Agregar Curso
      </a>
    </div>
  </div>

  <!-- Data Table -->
  <div class="contenedor-tabla">
    <table class="tabla-datos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre Curso</th>
          <th>Nivel</th>
          <th>Tutor</th>
          <th>Aula</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <!-- Curso: Desarrollo de Aplicaciones Web (DAW) -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Desarrollo de Aplicaciones Web (DAW)</td>
        </tr>
        <tr>
          <td>#DAW1</td>
          <td>1º DAW</td>
          <td>Grado Superior</td>
          <td>Juan Pérez</td>
          <td>A-201</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#DAW2</td>
          <td>2º DAW</td>
          <td>Grado Superior</td>
          <td>Juan Pérez</td>
          <td>A-202</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>

        <!-- Curso: ASIR -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Admin. Sistemas Informáticos en Red (ASIR)</td>
        </tr>
        <tr>
          <td>#ASIR1</td>
          <td>1º ASIR</td>
          <td>Grado Superior</td>
          <td>María López</td>
          <td>A-203</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#ASIR2</td>
          <td>2º ASIR</td>
          <td>Grado Superior</td>
          <td>María López</td>
          <td>A-204</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>

        <!-- Curso: Gestión Administrativa -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Gestión Administrativa (GA)</td>
        </tr>
        <tr>
          <td>#GA1</td>
          <td>1º GA</td>
          <td>Grado Medio</td>
          <td>Carlos Ruiz</td>
          <td>B-101</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#GA2</td>
          <td>2º GA</td>
          <td>Grado Medio</td>
          <td>Carlos Ruiz</td>
          <td>B-102</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>

        <!-- Curso: 3D y Juegos -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Animaciones 3D, Juegos y Entornos (3D)</td>
        </tr>
        <tr>
          <td>#3D1</td>
          <td>1º 3D y Juegos</td>
          <td>Grado Superior</td>
          <td>Laura Gómez</td>
          <td>C-301</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#3D2</td>
          <td>2º 3D y Juegos</td>
          <td>Grado Superior</td>
          <td>Laura Gómez</td>
          <td>C-302</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>

        <!-- Curso: SMR -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Sistemas Microinformáticos y Redes (SMR)</td>
        </tr>
        <tr>
          <td>#SMR1</td>
          <td>1º SMR</td>
          <td>Grado Medio</td>
          <td>Pedro Sánchez</td>
          <td>B-103</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#SMR2</td>
          <td>2º SMR</td>
          <td>Grado Medio</td>
          <td>Pedro Sánchez</td>
          <td>B-104</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>

        <!-- Curso: AYF -->
        <tr class="encabezado-grupo-curso">
          <td colspan="7">Administración y Finanzas (AYF)</td>
        </tr>
        <tr>
          <td>#AYF1</td>
          <td>1º AYF</td>
          <td>Grado Superior</td>
          <td>Ana Martínez</td>
          <td>B-201</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
              <button class="boton-icono boton-eliminar" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>#AYF2</td>
          <td>2º AYF</td>
          <td>Grado Superior</td>
          <td>Ana Martínez</td>
          <td>B-202</td>
          <td>
            <span class="insignia-estado estado-activo">Activo</span>
          </td>
          <td>
            <div class="botones-accion">
              <a
                href="verDetallesCursos.php"
                class="boton-icono boton-ver"
                title="Ver detalles"><i class="fas fa-eye"></i></a>
              <a
                href="modificarCursos.php"
                class="boton-icono boton-editar"
                title="Editar"><i class="fas fa-edit"></i></a>
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
<script src="../../js/main.js"></script>
<script>
  // ... same scripts if any
</script>
</body>

</html>