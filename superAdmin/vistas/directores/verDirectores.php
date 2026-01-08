<?php
$titulo_pagina = "Ver Directores - Super Admin";
require_once "../comunes/nav.php";

require_once "../../modelo/conexion.php";
require_once "../../modelo/directores.php";

$con = new Conexion();
$conexion = $con->conectar(); // mysqli object válido

$directores = new director($conexion);
$directores = $directores->listarDirectoresModelo();

?>
<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Directores</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Gestión de directores del sistema
      </p>
    </div>
    <div class="acciones-pagina">
      <div class="caja-busqueda">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar director..." />
      </div>
      <a href="agregarDirectores.php" class="boton-primario">
        <i class="fas fa-plus"></i>
        Agregar Director
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
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        require_once "../../modelo/directores.php";
        $director = new director($conexion);
        $directores = $director->listarDirectoresModelo();
        foreach ($directores as $director) {
        ?>
          <tr>
            <td><?= $director['idDirector']; ?></td>
            <td><?= $director['nombreDirector']; ?></td>
            <td><?= $director['emailDirector']; ?></td>
            <td><?= $director['telefonoDirector']; ?></td>
            <?php
            // نجيب الاسم من قاعدة البيانات
            $estado = $director['nombreEstado'];

            // نحدد class المناسب باستخدام array mapping
            $tiposEstados = [
              'activo' => 'estado-activo',
              'inactivo' => 'estado-inactivo',
              'pendiente' => 'estado-pendiente'
            ];

            // نستعمل القيمة مباشرة للـ class
            $estiloEstado = $tiposEstados[$estado] ?? ''; // إذا القيمة غير موجودة يعطي فارغ
            ?>
            <td>
              <span class="insignia-estado <?= $estiloEstado; ?>">
                <?= ucfirst($estado); ?>
              </span>
            </td>
            <td>
              <div class="botones-accion">
                <a
                  href="verDetallesEstudiantes.php"
                  class="boton-icono boton-ver"
                  title="Ver detalles">
                  <i class="fas fa-eye"></i>
                </a>
                <a
                  href="modificarEstudiantes.php"
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