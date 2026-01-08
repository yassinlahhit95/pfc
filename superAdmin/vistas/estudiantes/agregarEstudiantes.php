<?php

session_start(); // مهم باش نقدر نستعمل $_SESSION

$titulo_pagina = "Agregar Estudiante - Super Admin";
include_once "../comunes/nav.php";

require_once "../../modelo/conexion.php";

$conexion = new Conexion();
$db = $conexion->conectar();

// Traer todos los cursos
$queryCursos = $db->query("SELECT idCurso, nombreCurso FROM cursos ORDER BY nombreCurso ASC");
$cursos = [];
while($row = $queryCursos->fetch_assoc()) {
    $cursos[] = $row;
}

// Traer todos los estados
$queryEstados = $db->query("SELECT idEstado, nombreEstado FROM estados ORDER BY nombreEstado ASC");
$estados = [];
while($row = $queryEstados->fetch_assoc()) {
    $estados[] = $row;
}

// Obtener errores y valores antiguos de la sesión
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<main class="contenido-principal">
  <div class="encabezado-pagina">
    <div>
      <h1>Agregar Estudiante</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Complete el formulario para registrar un nuevo estudiante
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
  </div>

  <div class="contenedor-formulario">
    <form method="POST" action="../../controlador/estudianteControlador.php">

      <div class="cuadricula-formulario">
        <div class="grupo-formulario">
          <label>Nombre completo<span class="requerido">*</span></label>
          <input type="text" name="nombreEstudiante" placeholder="Ingrese el nombre"
                 value="<?= $old['nombreEstudiante'] ?? '' ?>" requerido />
          <?php if(isset($errors['nombreEstudiante'])): ?>
              <small style="color:red;"><?= $errors['nombreEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>Email <span class="requerido">*</span></label>
          <input type="email" name="emailEstudiante" placeholder="ejemplo@email.com"
                 value="<?= $old['emailEstudiante'] ?? '' ?>" requerido />
          <?php if(isset($errors['emailEstudiante'])): ?>
              <small style="color:red;"><?= $errors['emailEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>Teléfono</label>
          <input type="tel" name="telefonoEstudiante" placeholder="+34 600 000 000"
                 value="<?= $old['telefonoEstudiante'] ?? '' ?>" />
          <?php if(isset($errors['telefonoEstudiante'])): ?>
              <small style="color:red;"><?= $errors['telefonoEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>Fecha de Nacimiento <span class="requerido">*</span></label>
          <input type="date" name="fechaNacimientoEstudiante"
                 value="<?= $old['fechaNacimientoEstudiante'] ?? '' ?>" requerido />
          <?php if(isset($errors['fechaNacimientoEstudiante'])): ?>
              <small style="color:red;"><?= $errors['fechaNacimientoEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>DNI/NIE <span class="requerido">*</span></label>
          <input type="text" name="dniEstudiante" placeholder="12345678A"
                 value="<?= $old['dniEstudiante'] ?? '' ?>" requerido />
          <?php if(isset($errors['dniEstudiante'])): ?>
              <small style="color:red;"><?= $errors['dniEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>Curso <span class="requerido">*</span></label>
          <select name="idCurso" requerido>
            <option value="">Seleccione un curso</option>
            <?php foreach($cursos as $curso): ?>
                <option value="<?= $curso['idCurso'] ?>"
                  <?= (isset($old['idCurso']) && $old['idCurso']==$curso['idCurso'])?'selected':'' ?>>
                  <?= $curso['nombreCurso'] ?>
                </option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['idCurso'])): ?>
              <small style="color:red;"><?= $errors['idCurso'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario">
          <label>Fecha de Ingreso <span class="requerido">*</span></label>
          <input type="date" name="fechaAltaEstudiante"
                 value="<?= $old['fechaAltaEstudiante'] ?? '' ?>" requerido />
          <?php if(isset($errors['fechaAltaEstudiante'])): ?>
              <small style="color:red;"><?= $errors['fechaAltaEstudiante'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario ancho-completo">
          <label>Dirección</label>
          <input type="text" name="direccionEstudiante" placeholder="Calle, número, piso..."
                 value="<?= $old['direccionEstudiante'] ?? '' ?>" />
        </div>

        <div class="grupo-formulario">
          <label>Ciudad</label>
          <input type="text" name="ciudadEstudiante" placeholder="Ciudad"
                 value="<?= $old['ciudadEstudiante'] ?? '' ?>" />
        </div>

        <div class="grupo-formulario">
          <label>Código Postal</label>
          <input type="text" name="codigoPostal" placeholder="28001"
                 value="<?= $old['codigoPostal'] ?? '' ?>" />
        </div>

        <div class="grupo-formulario">
          <label>Nombre del Tutor/a</label>
          <input type="text" name="nombreTutor" placeholder="Nombre completo del tutor"
                 value="<?= $old['nombreTutor'] ?? '' ?>" />
        </div>

        <div class="grupo-formulario">
          <label>Teléfono del Tutor/a</label>
          <input type="tel" name="telefonoTutor" placeholder="+34 600 000 000"
                 value="<?= $old['telefonoTutor'] ?? '' ?>" />
        </div>

        <div class="grupo-formulario">
          <label>Estado <span class="requerido">*</span></label>
          <select name="idEstado" requerido>
            <option value="">Seleccione un estado</option>
            <?php foreach($estados as $estado): ?>
                <option value="<?= $estado['idEstado'] ?>"
                  <?= (isset($old['idEstado']) && $old['idEstado']==$estado['idEstado'])?'selected':'' ?>>
                  <?= $estado['nombreEstado'] ?>
                </option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['idEstado'])): ?>
              <small style="color:red;"><?= $errors['idEstado'] ?></small>
          <?php endif; ?>
        </div>

        <div class="grupo-formulario ancho-completo">
          <label>Observaciones</label>
          <textarea name="observaciones" placeholder="Notas adicionales sobre el estudiante..."><?= $old['observaciones'] ?? '' ?></textarea>
        </div>
      </div>

      <div class="acciones-formulario">
        <button
          type="button"
          class="boton-cancelar"
          onclick="window.location.href='verEstudiantes.php'"
        >
          Cancelar
        </button>
        <button type="submit" name="submit" class="boton-enviar">
          <i class="fas fa-save"></i>
          Guardar Estudiante
        </button>
      </div>
    </form>
  </div>
</main>

<script src="../../js/menu.js"></script>
<script src="../../js/main.js"></script>
</body>
</html>