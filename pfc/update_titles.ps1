$files = @(
    "pfc\vistas\admin\academico\calificacionesModulos.php",
    "pfc\vistas\admin\academico\calificacionesRetos.php",
    "pfc\vistas\admin\academico\resultadosFinales.php",
    "pfc\vistas\admin\anuncios\agregarAnuncios.php",
    "pfc\vistas\admin\anuncios\detallesAnuncio.php",
    "pfc\vistas\admin\anuncios\gestionAnuncios.php",
    "pfc\vistas\admin\anuncios\modificarAnuncios.php",
    "pfc\vistas\admin\aulas\agregarAulas.php",
    "pfc\vistas\admin\aulas\modificarAulas.php",
    "pfc\vistas\admin\aulas\verAulas.php",
    "pfc\vistas\admin\ciclos\agregarCiclos.php",
    "pfc\vistas\admin\ciclos\modificarCiclos.php",
    "pfc\vistas\admin\ciclos\verCiclos.php",
    "pfc\vistas\admin\comunes\creditos.php",
    "pfc\vistas\admin\comunes\sobreelproyecto.php",
    "pfc\vistas\admin\directores\agregarDirectores.php",
    "pfc\vistas\admin\directores\modificarDirectores.php",
    "pfc\vistas\admin\directores\perfil.php",
    "pfc\vistas\admin\directores\verDetallesDirectores.php",
    "pfc\vistas\admin\directores\verDirectores.php",
    "pfc\vistas\admin\estudiantes\agregarEstudiantes.php",
    "pfc\vistas\admin\estudiantes\modificarEstudiantes.php",
    "pfc\vistas\admin\estudiantes\verDetallesEstudiantes.php",
    "pfc\vistas\admin\estudiantes\verEstudiantes.php",
    "pfc\vistas\admin\eventos\agregarEvento.php",
    "pfc\vistas\admin\eventos\gestionEventos.php",
    "pfc\vistas\admin\eventos\modificarEvento.php",
    "pfc\vistas\admin\inicio\dashboard.php",
    "pfc\vistas\admin\inventario\agregarArticulo.php",
    "pfc\vistas\admin\inventario\agregarPrestamo.php",
    "pfc\vistas\admin\inventario\gestionarPrestamos.php",
    "pfc\vistas\admin\inventario\modificarArticulo.php",
    "pfc\vistas\admin\inventario\verInventario.php",
    "pfc\vistas\admin\mensajes\agregar.php",
    "pfc\vistas\admin\mensajes\detalles.php",
    "pfc\vistas\admin\mensajes\lista.php",
    "pfc\vistas\admin\mensajes\modificarReclamacion.php",
    "pfc\vistas\admin\modulos\agregarModulos.php",
    "pfc\vistas\admin\modulos\asignarProfesorModulo.php",
    "pfc\vistas\admin\modulos\modificarModulos.php",
    "pfc\vistas\admin\modulos\verModulos.php",
    "pfc\vistas\admin\pagos\agregarPagos.php",
    "pfc\vistas\admin\pagos\historialEstudiante.php",
    "pfc\vistas\admin\pagos\modificarPagos.php",
    "pfc\vistas\admin\pagos\verPagosGeneral.php",
    "pfc\vistas\admin\pfc\verTFGs.php",
    "pfc\vistas\admin\profesores\agregarProfesores.php",
    "pfc\vistas\admin\profesores\asignarModulos.php",
    "pfc\vistas\admin\profesores\modificarProfesores.php",
    "pfc\vistas\admin\profesores\verDetallesProfesores.php",
    "pfc\vistas\admin\profesores\verProfesores.php",
    "pfc\vistas\admin\retos\agregarRetos.php",
    "pfc\vistas\admin\retos\calificarReto.php",
    "pfc\vistas\admin\retos\modificarRetos.php",
    "pfc\vistas\admin\retos\verRetos.php",
    "pfc\vistas\estudiantes\academico\resultadosFinales.php",
    "pfc\vistas\estudiantes\anuncios\lista.php",
    "pfc\vistas\estudiantes\calificaciones\lista.php",
    "pfc\vistas\estudiantes\calificaciones\retos.php",
    "pfc\vistas\estudiantes\comunes\sobreelproyecto.php",
    "pfc\vistas\estudiantes\eventos\lista.php",
    "pfc\vistas\estudiantes\inicio\dashboard.php",
    "pfc\vistas\estudiantes\mensajes\detalles.php",
    "pfc\vistas\estudiantes\pagos\lista.php",
    "pfc\vistas\estudiantes\perfil\ver.php",
    "pfc\vistas\estudiantes\pfc\lista.php",
    "pfc\vistas\estudiantes\pfc\subir.php",
    "pfc\vistas\estudiantes\retos\lista.php",
    "pfc\vistas\profesores\anuncios\lista.php",
    "pfc\vistas\profesores\calificaciones\editar.php",
    "pfc\vistas\profesores\calificaciones\lista.php",
    "pfc\vistas\profesores\ciclos\lista.php",
    "pfc\vistas\profesores\comunes\sobreelproyecto.php",
    "pfc\vistas\profesores\estudiantes\detalles.php",
    "pfc\vistas\profesores\estudiantes\lista.php",
    "pfc\vistas\profesores\eventos\lista.php",
    "pfc\vistas\profesores\inicio\dashboard.php",
    "pfc\vistas\profesores\mensajes\agregar.php",
    "pfc\vistas\profesores\mensajes\detalles.php",
    "pfc\vistas\profesores\modulos\lista.php",
    "pfc\vistas\profesores\perfil\ver.php",
    "pfc\vistas\profesores\pfc\lista.php",
    "pfc\vistas\profesores\retos\lista.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        $newContent = [regex]::Replace($content, '(\$titulo(?:_pagina|DelPagina)\s*=\s*)(["''])(.*?)(["'']);', {
            param($match)
            $prefix = $match.Groups[1].Value
            $quote = $match.Groups[2].Value
            $title = $match.Groups[3].Value
            
            # Remove suffixes
            $cleanTitle = $title
            $suffixes = @(" - Admin", " - Estudiante", " - Profesor", " - Portal Profesores", " - Portal Estudiantes", " - Administración", " - Yassin Lahhit", " - Portal Profesores", " - Portal Estudiantes")
            
            # Sort suffixes by length descending to avoid partial matches
            $suffixes = $suffixes | Sort-Object -Descending -Property Length

            foreach ($suffix in $suffixes) {
                if ($cleanTitle.ToLower().EndsWith($suffix.ToLower())) {
                    $cleanTitle = $cleanTitle.Substring(0, $cleanTitle.Length - $suffix.Length)
                    break
                }
            }
            
            # General cleanup for any remaining " - ..." that might be missed
            # but be careful not to remove parts of legitimate titles
            # The rule says: Remove suffixes like '- Admin', '- Estudiante', or '- Profesor'
            
            $upperTitle = $cleanTitle.ToUpper().Trim()
            $finalTitle = "AULAPRO | " + $upperTitle
            
            return $prefix + $quote + $finalTitle + $quote + ";"
        })
        $newContent | Set-Content $file -Encoding UTF8
    }
}
