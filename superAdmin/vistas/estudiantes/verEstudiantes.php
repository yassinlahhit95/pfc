<?php
$titulo_pagina = "Ver Estudiantes - Super Admin";
include_once "../comunes/nav.php";

// Conexión a la base de datos
require_once "../../modelo/estudiantes.php";

require_once "../../modelo/cursos.php"; // زيد هاد السطر



$conexionObj = new Conexion();
$conexion = $conexionObj->conectar(); // mysqli object válido
// جلب الكورسات من الموديل ديالك
$cursoObj = new curso($conexion); // إنشاء كائن من كلاس curso
$listaCursos = $cursoObj->listarCursosModelo(); // جلب البيانات


$estudiante = new estudiante($conexion);
$listaEstudiantes = $estudiante->listarEstudiantesModelo();
?>

<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Estudiantes</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Gestión de estudiantes del sistema
      </p>
    </div>
    <div class="acciones-pagina">
      <div class="caja-busqueda">
        <i class="fas fa-search"></i>
        <input
          type="text"
          placeholder="Buscar estudiante..."
          id="inputBusqueda" />
      </div>
      
<select id="filtroCurso" class="selector-filtro" style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; margin-right: 10px;">
    <option value="">Todos los cursos</option>
    
    <?php foreach ($listaCursos as $c) { ?>
        <option value="<?= htmlspecialchars($c['nombreCurso']); ?>">
            <?= htmlspecialchars($c['nombreCurso']); ?>
        </option>
    <?php } ?>
    
</select>


      <a href="agregarEstudiantes.php" class="boton-primario">
        <i class="fas fa-plus"></i>
        Agregar Estudiante
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
          <th>Curso</th>
          <th>Telefono</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listaEstudiantes as $alumno) { ?>
          <tr>
            <td><?= $alumno['idEstudiante']; ?></td>
            <td><?= $alumno['nombreEstudiante']; ?></td>
            <td><?= $alumno['emailEstudiante']; ?></td>
            <td><?= $alumno['nombreCurso']; ?></td>
            <td><?= $alumno['telefonoEstudiante']; ?></td>
            <?php
            $estado = $alumno['nombreEstado'];
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
                <a href="verDetallesEstudiantes.php" class="boton-icono boton-ver" title="Ver detalles">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="modificarEstudiantes.php" class="boton-icono boton-editar" title="Editar">
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputBusqueda = document.getElementById("inputBusqueda");
    const filtroCurso = document.getElementById("filtroCurso");
    const tablaFilas = document.querySelectorAll(".tabla-datos tbody tr");

    function cleanText(text) {
        return text.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    // دالة Levenshtein لحساب الفرق بين كلمتين
    function levenshtein(a, b) {
        const matrix = [];
        for (let i = 0; i <= b.length; i++) matrix[i] = [i];
        for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
        for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1, matrix[i][j - 1] + 1, matrix[i - 1][j] + 1);
                }
            }
        }
        return matrix[b.length][a.length];
    }

    function matchSmart(fullName, query) {
        if (!query) return true;
        
        // 1. جرب البحث المباشر (إيلا كانت الكلمة كاينة وسط الاسم)
        if (fullName.includes(query)) return true;

        // 2. تقسيم الاسم لأجزاء (مثلا Kepa و Arrizabalaga)
        const parts = fullName.split(/\s+/); 
        
        // 3. مقارنة كلمة البحث مع كل جزء من الاسم
        return parts.some(part => {
            // إيلا كان الجزء طويل (أكثر من 3 حروف) كنسمحو بغلط ديال حرف واحد
            if (part.length > 3) {
                const distance = levenshtein(part, query);
                return distance <= 1; // مسموح بغلط ديال حرف واحد (مثلا r ناقصة)
            }
            return part === query;
        });
    }

    function filtrarEstudiantes() {
        const searchText = cleanText(inputBusqueda.value);
        const cursoSeleccionado = filtroCurso.value.toLowerCase();

        tablaFilas.forEach((fila) => {
            const nombreOriginal = cleanText(fila.children[1].textContent);
            const emailOriginal = cleanText(fila.children[2].textContent);
            const cursoOriginal = fila.children[3].textContent.toLowerCase();

            // تطبيق البحث الذكي
            const coincideTexto = matchSmart(nombreOriginal, searchText) || emailOriginal.includes(searchText);
            const coincideCurso = cursoSeleccionado === "" || cursoOriginal === cursoSeleccionado;

            fila.style.display = (coincideTexto && coincideCurso) ? "" : "none";
        });
    }

    inputBusqueda.addEventListener("input", filtrarEstudiantes);
    filtroCurso.addEventListener("change", filtrarEstudiantes);
});
</script>

</body>
</html>
