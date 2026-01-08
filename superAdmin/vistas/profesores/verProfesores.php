<?php
session_start();
$titulo_pagina = "Ver Profesor - Super Admin";
include_once "../comunes/nav.php";

require_once "../../modelo/profesores.php";
$conexionObj = new Conexion();
$conexion = $conexionObj->conectar(); // mysqli object válido

$profs = new profesor($conexion);
$listaProfesores = $profs->listarProfesoresModelo();

?>
<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Profesores</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Gestión de profesores del sistema
      </p>
    </div>
    <div class="acciones-pagina">
      <div class="caja-busqueda">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar profesor..." />
      </div>
      <a href="agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i>
        Agregar Profesor
      </a>
    </div>
  </div>

  <!-- Data Table -->
  <div class="contenedor-tabla">
    <table class="tabla-datos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre Completo</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Dirección</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listaProfesores as $profesor) { ?>

          <tr>
            <td><?= $profesor['idProfesor']; ?></td>
            <td><?= $profesor['nombreProfesor']; ?></td>
            <td><?= $profesor['correoProfesor']; ?></td>
            <td><?= $profesor['telefonoProfesor']; ?></td>
            <td><?= $profesor['direccionProfesor']; ?></td>
            <?php
            $estado = $profesor['nombreEstado'];
            $tiposEstados = [
              'activo' => 'estado-activo',
              'inactivo' => 'estado-inactivo',
              'pendiente' => 'estado-pendiente'
            ];
            $estiloEstado = $tiposEstados[$estado] ?? '';
            ?>
            <td>
              <span class="insignia-estado <?= $estiloEstado; ?>">
                <?= ucfirst($estado); ?>
              </span>
            </td>
            <td>
              <div class="botones-accion">
                <a
                  href="verDetallesProfesores.php"
                  class="boton-icono boton-ver"
                  title="Ver detalles">
                  <i class="fas fa-eye"></i>
                </a>
                <a
                  href="modificarProfesores.php"
                  class="boton-icono boton-editar"
                  title="Editar">
                  <i class="fas fa-edit"></i>
                </a>
                <button class="boton-icono boton-eliminar" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php } ?>

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