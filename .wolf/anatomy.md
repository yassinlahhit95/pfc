# anatomy.md

> Auto-maintained by OpenWolf. Last scanned: 2026-05-14T10:00:01.815Z
> Files: 223 tracked | Anatomy hits: 0 | Misses: 0

## ./

- `.gitignore` — Git ignore rules (~107 tok)
- `CLAUDE.md` — AulaPro – Directrices del Proyecto (~617 tok)
- `database.sql` — DB: pfc (~5408 tok)
- `diagramas.md` — Diagramas de AulaPro (~1382 tok)
- `escribir.md` — Guía de Redacción de la Memoria del TFG - DAW (~820 tok)
- `features.txt` — # Características del Proyecto - Sistema de Gestión Académica (~323 tok)
- `firebase-messaging-sw.js` — Declares messaging (~241 tok)
- `IMPLEMENTACION_COMPLETA.md` — IMPLEMENTACIÓN COMPLETA - Sistema de Gestión Escolar en Tres Portales (~3775 tok)
- `index.html` — AulaPro — Gestión académica para centros formativos (~6295 tok)
- `prompt_casos_uso.md` — Diagrama de Casos de Uso — AulaPro (TFG) (~963 tok)
- `prompt_entidad_relacion.md` — Modelo de Datos — AulaPro (TFG) (~873 tok)
- `README.md` — Project documentation (~2614 tok)
- `TFG_Proyecto.txt` — TFG - PROYECTO DE DESARROLLO DE VIDEOJUEGOS EDUCATIVOS (~1693 tok)

## .claude/

- `settings.json` (~441 tok)
- `settings.local.json` — Declares f (~1251 tok)

## .claude/rules/

- `openwolf.md` (~313 tok)

## config/

- `secrets.php` (~33 tok)
- `service-account.json` (~678 tok)

## controladores/

- `contacto_landing.php` (~936 tok)
- `logout.php` (~28 tok)
- `validacion.php` — VALIDACIÓN DE LOGIN (~519 tok)

## controladores/admin/academico/

- `calificarModulos.php` (~833 tok)
- `calificarRetos.php` (~383 tok)
- `enviarNotasMasivo.php` (~348 tok)

## controladores/admin/anuncios/

- `actualizar.php` (~374 tok)
- `borrar.php` (~120 tok)
- `insertar.php` (~590 tok)

## controladores/admin/ciclos/

- `actualizar.php` (~466 tok)
- `borrar.php` (~103 tok)
- `insertar.php` (~459 tok)

## controladores/admin/directores/

- `actualizar.php` (~703 tok)
- `borrar.php` (~118 tok)
- `insertar.php` (~662 tok)

## controladores/admin/estudiantes/

- `actualizar.php` (~810 tok)
- `borrar.php` (~115 tok)
- `insertar.php` (~868 tok)
- `subirTFG.php` (~387 tok)

## controladores/admin/eventos/

- `actualizar.php` (~316 tok)
- `borrar.php` (~107 tok)
- `insertar.php` (~252 tok)

## controladores/admin/inventario/

- `actualizar.php` (~402 tok)
- `borrar.php` (~110 tok)
- `devolver.php` (~143 tok)
- `insertar.php` (~335 tok)
- `prestar.php` (~327 tok)

## controladores/admin/mensajes/

- `actualizar.php` (~235 tok)
- `borrar.php` (~135 tok)
- `insertar.php` (~1094 tok)

## controladores/admin/modulos/

- `actualizar.php` (~491 tok)
- `actualizarProfesores.php` (~216 tok)
- `borrar.php` (~122 tok)
- `insertar.php` (~465 tok)

## controladores/admin/pagos/

- `actualizar.php` (~314 tok)
- `borrar.php` (~112 tok)
- `insertar.php` (~652 tok)
- `obtenerEstadoFinanciero.php` (~87 tok)

## controladores/admin/pfc/

- `borrar.php` (~234 tok)
- `calificar.php` (~416 tok)
- `gestionar.php` (~310 tok)

## controladores/admin/profesores/

- `actualizar.php` (~849 tok)
- `actualizarModulos.php` (~203 tok)
- `borrar.php` (~129 tok)
- `insertar.php` (~810 tok)

## controladores/admin/retos/

- `actualizar.php` (~903 tok)
- `borrar.php` (~138 tok)
- `calificar.php` (~188 tok)
- `insertar.php` (~844 tok)

## controladores/comunes/

- `email_helper.php` — Declares sendEmail (~458 tok)
- `notificaciones_grades.php` — generarTablaNotasHTML: enviarEmailNotasEstudiante, enviarEmailNotasClase, generarEmailCalificacionTFGHTML, enviarEmailCalificacionTFG (~2801 tok)

## controladores/estudiantes/mensajes/

- `insertar.php` (~510 tok)
- `marcar_visto.php` (~150 tok)

## controladores/estudiantes/perfil/

- `actualizar.php` (~610 tok)

## controladores/estudiantes/pfc/

- `eliminar.php` (~196 tok)
- `subir.php` (~596 tok)

## controladores/firebase/

- `firebase_helper.php` — obtenerAccessToken: enviarNotificacionFirebase, obtenerTokenUsuario (~1312 tok)
- `guardar_token.php` (~340 tok)

## controladores/profesores/calificaciones/

- `actualizar.php` (~502 tok)
- `borrar.php` (~142 tok)
- `calificarModulos_prof.php` (~576 tok)
- `calificarRetos.php` (~360 tok)
- `insertar.php` (~362 tok)

## controladores/profesores/estudiantes/

- `actualizar.php` (~656 tok)
- `borrar.php` (~181 tok)
- `insertar.php` (~730 tok)

## controladores/profesores/mensajes/

- `actualizar.php` (~233 tok)
- `borrar.php` (~119 tok)
- `insertar.php` (~521 tok)
- `marcar_visto.php` (~156 tok)

## controladores/profesores/perfil/

- `actualizar.php` (~634 tok)

## controladores/profesores/pfc/

- `actualizar.php` (~244 tok)
- `borrar.php` (~114 tok)
- `calificar.php` (~456 tok)

## controladores/profesores/retos/

- `actualizar.php` (~493 tok)
- `borrar.php` (~109 tok)
- `insertar.php` (~463 tok)

## modelos/

- `anuncios.php` — listarTodosLosAnuncios: insertarAnuncio, eliminarAnuncio, obtenerAnuncioPorId + 4 more (~1107 tok)
- `calificaciones.php` — obtenerNotasModulo: listarCalificacionesGeneral, obtenerCalificacionPorId, eliminarCalificacion + 6 more (~3126 tok)
- `ciclos.php` — listarTodosLosCiclos: obtenerCiclosDeProfesor, checkCicloExistente, insertarNuevoCiclo + 5 more (~1508 tok)
- `conectar.php` — Declares obtenerConexion (~132 tok)
- `directores.php` — listarDirectores: checkDirectorExistente, insertarDirector, actualizarDirector + 8 more (~1584 tok)
- `estudiantes.php` — listarEstudiantes: checkEstudianteExistente, insertarEstudiante, actualizarEstudiante + 10 more (~2050 tok)
- `eventos.php` — listarEventosProximos: insertarEvento, eliminarEvento, obtenerEventoPorId, actualizarEvento (~597 tok)
- `inventario.php` — listarTodosLosPrestamos: listarArticulos, listarPrestamosActivos, checkArticuloExistente + 6 more (~1681 tok)
- `modulos.php` — listarModulos: obtenerModulosDeProfesor, obtenerModulosDeProfesorPorCiclo, obtenerModulosPorCiclo + 10 more (~1953 tok)
- `niveles.php` — Declares listarNiveles (~101 tok)
- `pagos.php` — listarTodosLosPagos: listarPagosFiltrados, obtenerPagosPorEstudiante, insertarPagoCompleto + 6 more (~1366 tok)
- `panelDeControl.php` — contarEstudiantes: contarProfesores, contarDirectores, contarAnuncios + 13 more (~1583 tok)
- `profesores.php` — listarProfesores: checkProfesorExistente, insertarProfesor, actualizarProfesor + 15 more (~2353 tok)
- `reclamaciones.php` — listarTodosLosMensajes: obtenerMensajePorId, marcarMensajeComoLeido, responderMensaje + 9 more (~1881 tok)
- `retos.php` — listarRetos: listarRetosFiltrados, obtenerRetosDeProfesor, insertarReto + 12 more (~2690 tok)
- `tfg.php` — listarTodosLosTFGs: listarTFGsFiltrados, obtenerTFGporEstudiante, actualizarTFG + 10 more (~2782 tok)

## public/css/

- `estilo.css` — Styles: 106 rules (~5510 tok)
- `landing.css` — Styles: 111 rules (~4232 tok)
- `login.css` — Styles: 45 rules, 10 vars, 3 media queries, 5 animations (~2843 tok)
- `notificaciones.css` — contenedor-notificaciones { (~535 tok)
- `responsive.css` — Styles: 2 rules, 2 media queries (~2935 tok)

## public/imagenes/

- `fondo.webp` (~34118 tok)

## public/js/

- `filtros.js` — Filtra las filas de una tabla según el texto del select o input (~162 tok)
- `menu.js` — Declares toggleMenu (~264 tok)
- `paginacion.js` — iniciarPaginacion: _mostrarPaginaTabla, _renderControles, irAPagina, resetearPaginacion (~744 tok)
- `profesores-form.js` — Declares actualizarModulos (~189 tok)
- `retos.js` (~393 tok)

## public/js/firebase/

- `firebase-init.js` — Declares userData (~116 tok)
- `firebase.js` — Exports requestPermissionAndGetToken (~799 tok)
- `notificaciones-ui.js` — Exports mostrarNotificacionUI (~503 tok)

## vistas/

- `login.php` — Declares togglePassword (~1573 tok)

## vistas/admin/academico/

- `calificacionesModulos.php` — Declares filtrarCiclosModulos (~2001 tok)
- `calificacionesRetos.php` — Declares filtrarCiclosRetos (~1884 tok)
- `calificacionesTFG.php` — filtrarCiclosTFG: toggleFormCalificar (~2252 tok)
- `resultadosFinales.php` — Declares filtrarCiclosFinales (~1881 tok)

## vistas/admin/anuncios/

- `agregarAnuncios.php` (~857 tok)
- `detallesAnuncio.php` (~665 tok)
- `gestionAnuncios.php` (~823 tok)
- `modificarAnuncios.php` (~668 tok)

## vistas/admin/ciclos/

- `agregarCiclos.php` (~1157 tok)
- `modificarCiclos.php` (~1295 tok)
- `verCiclos.php` (~999 tok)

## vistas/admin/comunes/

- `footer.php` (~54 tok)
- `index.php` (~15 tok)
- `nav.php` (~2485 tok)

## vistas/admin/directores/

- `agregarDirectores.php` (~1419 tok)
- `modificarDirectores.php` (~1530 tok)
- `verDetallesDirectores.php` (~1007 tok)
- `verDirectores.php` (~824 tok)

## vistas/admin/estudiantes/

- `agregarEstudiantes.php` — Declares alCambiarNivel (~2438 tok)
- `modificarEstudiantes.php` — Declares alCambiarNivel (~2241 tok)
- `verDetallesEstudiantes.php` (~1006 tok)
- `verEstudiantes.php` — Declares aplicarFiltrosEstudiantes (~1844 tok)

## vistas/admin/eventos/

- `agregarEvento.php` (~656 tok)
- `gestionEventos.php` (~886 tok)
- `modificarEvento.php` (~750 tok)

## vistas/admin/inicio/

- `dashboard.php` (~1774 tok)

## vistas/admin/inventario/

- `agregarArticulo.php` (~671 tok)
- `agregarPrestamo.php` (~1218 tok)
- `gestionarPrestamos.php` (~956 tok)
- `modificarArticulo.php` (~525 tok)
- `verInventario.php` (~833 tok)

## vistas/admin/mensajes/

- `agregar.php` (~1861 tok)
- `detalles.php` (~838 tok)
- `lista.php` (~1258 tok)
- `modificarReclamacion.php` (~825 tok)

## vistas/admin/modulos/

- `agregarModulos.php` — Declares alCambiarNivel (~1456 tok)
- `asignarProfesorModulo.php` (~600 tok)
- `modificarModulos.php` — Declares alCambiarNivel (~1540 tok)
- `verModulos.php` — Declares filtrarNivelModulos (~1986 tok)

## vistas/admin/pagos/

- `agregarPagos.php` — Declares actualizarMontoRapido (~2040 tok)
- `historialEstudiante.php` (~597 tok)
- `modificarPagos.php` (~1185 tok)
- `verPagosGeneral.php` (~1452 tok)

## vistas/admin/profesores/

- `agregarProfesores.php` (~2382 tok)
- `asignarModulos.php` (~697 tok)
- `modificarProfesores.php` (~1593 tok)
- `verDetallesProfesores.php` (~1774 tok)
- `verProfesores.php` (~1070 tok)

## vistas/admin/retos/

- `agregarRetos.php` (~1181 tok)
- `calificarReto.php` (~830 tok)
- `modificarRetos.php` (~1310 tok)
- `verRetos.php` — Declares aplicarFiltrosRetos (~1508 tok)

## vistas/estudiantes/academico/

- `resultadosFinales.php` (~1232 tok)

## vistas/estudiantes/anuncios/

- `lista.php` (~458 tok)

## vistas/estudiantes/calificaciones/

- `lista.php` (~769 tok)
- `retos.php` (~616 tok)

## vistas/estudiantes/comunes/

- `footer.php` (~39 tok)
- `nav.php` (~1843 tok)

## vistas/estudiantes/eventos/

- `lista.php` (~540 tok)

## vistas/estudiantes/inicio/

- `dashboard.php` (~1560 tok)

## vistas/estudiantes/mensajes/

- `agregar.php` (~933 tok)
- `detalles.php` (~734 tok)
- `lista.php` (~1025 tok)

## vistas/estudiantes/pagos/

- `lista.php` (~1007 tok)

## vistas/estudiantes/perfil/

- `editar.php` (~1082 tok)
- `ver.php` (~697 tok)

## vistas/estudiantes/pfc/

- `lista.php` (~948 tok)
- `subir.php` (~1386 tok)

## vistas/estudiantes/retos/

- `lista.php` (~712 tok)

## vistas/profesores/academico/

- `resultadosFinales.php` (~2413 tok)

## vistas/profesores/anuncios/

- `lista.php` (~446 tok)

## vistas/profesores/calificaciones/

- `agregar.php` (~1968 tok)
- `editar.php` (~1480 tok)
- `lista.php` (~1408 tok)
- `retos.php` (~1675 tok)
- `tfg.php` — abrirModalCalificar: cerrarModal (~2021 tok)

## vistas/profesores/ciclos/

- `lista.php` (~506 tok)

## vistas/profesores/comunes/

- `footer.php` (~39 tok)
- `nav.php` (~2016 tok)

## vistas/profesores/estudiantes/

- `agregar.php` — Declares alCambiarNivel (~2339 tok)
- `detalles.php` (~1218 tok)
- `editar.php` — Declares alCambiarNivel (~2233 tok)
- `lista.php` (~1297 tok)

## vistas/profesores/eventos/

- `lista.php` (~557 tok)

## vistas/profesores/inicio/

- `dashboard.php` — Declares f (~1934 tok)

## vistas/profesores/mensajes/

- `agregar.php` (~1436 tok)
- `detalles.php` (~861 tok)
- `editar.php` (~840 tok)
- `lista.php` (~1274 tok)

## vistas/profesores/modulos/

- `lista.php` (~418 tok)

## vistas/profesores/perfil/

- `editar.php` (~1216 tok)
- `ver.php` (~578 tok)

## vistas/profesores/pfc/

- `editar.php` (~632 tok)
- `lista.php` — Declares toggleFormCalificar (~1778 tok)

## vistas/profesores/retos/

- `agregar.php` (~1278 tok)
- `editar.php` (~1265 tok)
- `lista.php` (~800 tok)
