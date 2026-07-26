-- =====================================================================
-- Demo Data Insertion Script for AulaPro
-- Emails: *@aulapro.com
-- Password for all accounts: 123456
-- (Bcrypt Hash: $2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. CLEANING UP PREVIOUS DEMO DATA (Optional but recommended for clean state)
-- ---------------------------------------------------------------------
TRUNCATE TABLE `configuracion_centro`;
TRUNCATE TABLE `niveles`;
TRUNCATE TABLE `ciclos`;
TRUNCATE TABLE `cursos_academicos`;
TRUNCATE TABLE `modulos`;
TRUNCATE TABLE `aulas`;
TRUNCATE TABLE `directores`;
TRUNCATE TABLE `secretarias`;
TRUNCATE TABLE `profesores`;
TRUNCATE TABLE `ciclo_profesor`;
TRUNCATE TABLE `modulo_profesor`;
TRUNCATE TABLE `estudiantes`;
TRUNCATE TABLE `tutores`;
TRUNCATE TABLE `estudiante_tutor`;
TRUNCATE TABLE `anuncios`;
TRUNCATE TABLE `eventos`;
TRUNCATE TABLE `aula_tareas`;
TRUNCATE TABLE `aula_entregas`;
TRUNCATE TABLE `aula_sesiones_vivas`;
TRUNCATE TABLE `chat_conversaciones`;
TRUNCATE TABLE `chat_mensajes`;
TRUNCATE TABLE `asistencias`;
TRUNCATE TABLE `categorias_gasto`;
TRUNCATE TABLE `gastos`;
TRUNCATE TABLE `fp_empresas`;
TRUNCATE TABLE `fct`;

-- ---------------------------------------------------------------------
-- 2. SCHOOL CENTER CONFIGURATION
-- ---------------------------------------------------------------------
INSERT INTO `configuracion_centro` (
  `idConfig`, `nombreCentro`, `codigoCentro`, `nifCifCentro`, `direccionCentro`, `ciudadCentro`, 
  `cpCentro`, `telefonoCentro`, `emailCentro`, `cursoEscolar`, `logoCentro`, `logoGobierno1`, 
  `logoGobierno2`, `textoLegal`, `nombreDirectorFirmante`, `feature_prematricula`, `feature_chat`, 
  `feature_inventario`, `feature_subida_tfg`, `instance_status`, `feature_horario`, 
  `feature_anuncios`, `feature_eventos`, `feature_retos`, `feature_mensajes`, `feature_pagos`, 
  `feature_gastos`, `feature_informes`, `feature_geoblock_admin`, `feature_ra_ce`, `feature_fp_dual`, 
  `feature_landing`, `prematricula_filtrar_niveles`, `feature_academico_config`, `feature_fct`
) VALUES (
  1, 'AulaPro Formación Profesional', 'CENTRO001', 'B12345678', 'Av. de la Innovación 42', 'Madrid', 
  '28042', '912345678', 'info@aulapro.com', '2026-2027', '', '', 
  '', 'Aviso legal: Este es un entorno de demostración de AulaPro.', 'Carlos Mendoza', 1, 1, 
  1, 1, 'active', 1, 
  1, 1, 1, 1, 1, 
  1, 1, 1, 1, 1, 
  1, 0, 0, 1
);

-- ---------------------------------------------------------------------
-- 3. STUDY LEVELS
-- ---------------------------------------------------------------------
INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES 
(1, 'Grado Superior'), 
(2, 'Grado Medio');

-- ---------------------------------------------------------------------
-- 4. CYCLES / DEGREES
-- ---------------------------------------------------------------------
INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`, `activo`) VALUES 
(1, 'Desarrollo de Aplicaciones Web', 'DAW', 1200.00, 1, 1), 
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'DAM', 1200.00, 1, 1), 
(3, 'Sistemas Microinformáticos y Redes', 'SMR', 900.00, 2, 1);

-- ---------------------------------------------------------------------
-- 5. ACADEMIC COURSES PER CYCLE
-- ---------------------------------------------------------------------
INSERT INTO `cursos_academicos` (`idCurso`, `idCiclo`, `nombre`, `orden`) VALUES 
(1, 1, '1º DAW', 1), 
(2, 1, '2º DAW', 2), 
(3, 2, '1º DAM', 1), 
(4, 2, '2º DAM', 2), 
(5, 3, '1º SMR', 1), 
(6, 3, '2º SMR', 2);

-- ---------------------------------------------------------------------
-- 6. SUBJECTS / MODULES
-- ---------------------------------------------------------------------
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `idCurso`, `tipoModulo`, `cursoAnio`, `creditosECTS`) VALUES 
(1, 'Programación', 'PRG', 240, 1, 1, 'Específico', '1º', 10), 
(2, 'Bases de Datos', 'BD', 180, 1, 1, 'Específico', '1º', 8), 
(3, 'Desarrollo Web en Entorno Servidor', 'DWES', 180, 1, 2, 'Específico', '2º', 9), 
(4, 'Desarrollo Web en Entorno Cliente', 'DWEC', 140, 1, 2, 'Específico', '2º', 7), 
(5, 'Diseño de Interfaces Web', 'DIW', 120, 1, 2, 'Específico', '2º', 6), 
(6, 'Entornos de Desarrollo', 'ED', 90, 1, 1, 'Específico', '1º', 4), 
(7, 'Programación Multimedia y Dispositivos Móviles', 'PMDM', 120, 2, 4, 'Específico', '2º', 6), 
(8, 'Montaje y Mantenimiento de Equipos', 'MME', 150, 3, 5, 'Específico', '1º', 8);

-- ---------------------------------------------------------------------
-- 7. CLASSROOMS
-- ---------------------------------------------------------------------
INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`, `activa`) VALUES 
(1, 1, 1, 'Laboratorio Informática I', 'laboratorio', 25, 1), 
(2, 1, 2, 'Laboratorio Informática II', 'laboratorio', 25, 1), 
(3, 2, 1, 'Aula de Teoría 201', 'teoria', 30, 1), 
(4, 2, 2, 'Taller de Hardware', 'taller', 20, 1);

-- ---------------------------------------------------------------------
-- 8. USERS: DIRECTORS (ADMINS)
-- ---------------------------------------------------------------------
INSERT INTO `directores` (
  `idDirector`, `nombreDirector`, `emailDirector`, `password`, `telefonoDirector`, 
  `dniDirector`, `fechaNacimientoDirector`, `fechaAltaDirector`, `direccionDirector`, 
  `ciudadDirector`, `codigoPostalDirector`, `observacionesDirector`
) VALUES (
  1, 'Carlos Mendoza', 'carlos.mendoza@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600111222', 
  '12345678A', '1980-05-15', '2024-09-01', 'Calle Mayor 1', 
  'Madrid', '28001', 'Director General de AulaPro'
);

-- ---------------------------------------------------------------------
-- 9. USERS: SECRETARIES
-- ---------------------------------------------------------------------
INSERT INTO `secretarias` (
  `idSecretaria`, `nombreSecretaria`, `emailSecretaria`, `password`, 
  `activoSecretaria`, `must_change_password`, `fechaAltaSecretaria`
) VALUES (
  1, 'Laura Gómez', 'laura.gomez@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', 
  1, 0, NOW()
);

-- ---------------------------------------------------------------------
-- 10. USERS: PROFESSORS
-- ---------------------------------------------------------------------
INSERT INTO `profesores` (
  `idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, 
  `dniProfesor`, `fechaNacimientoProfesor`, `fechaAltaProfesor`, `direccionProfesor`, 
  `ciudadProfesor`, `codigoPostalProfesor`, `observacionesProfesor`, `esTutor`, `idCicloTutor`
) VALUES 
(
  1, 'Juan Pérez', 'juan.perez@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600333444', 
  '23456789B', '1985-10-20', '2024-09-01', 'Calle Secundaria 2', 
  'Madrid', '28002', 'Profesor especialista en Backend. Tutor de 2º DAW.', 1, 1
),
(
  2, 'María Rodríguez', 'maria.rodriguez@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600444555', 
  '34567890C', '1988-03-12', '2024-09-01', 'Avenida Principal 3', 
  'Madrid', '28003', 'Profesora de programación e iniciación al desarrollo.', 0, NULL
),
(
  3, 'Pedro Martínez', 'pedro.martinez@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600555666', 
  '45678901D', '1982-07-25', '2024-09-01', 'Paseo del Prado 4', 
  'Madrid', '28004', 'Profesor de multiplataforma y hardware. Tutor de 2º DAM.', 1, 2
);

-- ---------------------------------------------------------------------
-- 11. CYCLE & MODULE ASSIGNMENTS TO PROFESSORS
-- ---------------------------------------------------------------------
INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES 
(1, 1), 
(1, 2), 
(2, 3), 
(3, 3);

INSERT INTO `modulo_profesor` (`idModulo`, `idProfesor`) VALUES 
(1, 2), 
(2, 2), 
(3, 1), 
(4, 1), 
(5, 3), 
(6, 2), 
(7, 3), 
(8, 3);

-- ---------------------------------------------------------------------
-- 12. USERS: STUDENTS
-- ---------------------------------------------------------------------
INSERT INTO `estudiantes` (
  `idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `telefonoEstudiante`, 
  `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `direccionEstudiante`, 
  `ciudadEstudiante`, `codigoPostalEstudiante`, `observacionesEstudiante`, `idCiclo`, `curso`, 
  `anioEstudio`, `idCurso`
) VALUES 
(
  1, 'Ana Silva', 'ana.silva@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600666777', 
  '56789012E', '2005-04-10', '2024-09-01', 'Calle Verde 5', 
  'Madrid', '28005', 'Delegada de clase. Excelente rendimiento académico.', 1, 'Grado Superior', 
  '2º', 2
),
(
  2, 'David Ortiz', 'david.ortiz@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600777888', 
  '67890123F', '2005-09-18', '2024-09-01', 'Calle Azul 6', 
  'Madrid', '28006', 'Participativo y muy interesado en diseño Frontend.', 1, 'Grado Superior', 
  '2º', 2
),
(
  3, 'Elena Pastor', 'elena.pastor@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600888999', 
  '78901234G', '2006-01-22', '2025-09-01', 'Calle Roja 7', 
  'Madrid', '28007', 'Interés en frameworks modernos y diseño UI/UX.', 1, 'Grado Superior', 
  '1º', 1
),
(
  4, 'Javier Ruiz', 'javier.ruiz@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600999000', 
  '89012345H', '2006-05-30', '2025-09-01', 'Calle Amarilla 8', 
  'Madrid', '28008', 'Tiene conocimientos previos de programación autodidacta.', 1, 'Grado Superior', 
  '1º', 1
),
(
  5, 'Lucía Mendez', 'lucia.mendez@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600000111', 
  '90123456I', '2005-11-05', '2024-09-01', 'Calle Naranja 9', 
  'Madrid', '28009', 'Estudiante de 2º DAM. Interesada en desarrollo de videojuegos.', 2, 'Grado Superior', 
  '2º', 4
),
(
  6, 'Sergio Abad', 'sergio.abad@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600111000', 
  '01234567J', '2005-02-14', '2024-09-01', 'Calle Violeta 10', 
  'Madrid', '28010', 'Interés en administración de servidores y redes.', 2, 'Grado Superior', 
  '2º', 4
);

-- ---------------------------------------------------------------------
-- 13. USERS: PARENTS / TUTORS
-- ---------------------------------------------------------------------
INSERT INTO `tutores` (
  `idTutor`, `nombreTutor`, `emailTutor`, `password`, `telefonoTutor`, 
  `dniTutor`, `must_change_password`, `idEstudiante`, `fechaAltaTutor`
) VALUES 
(
  1, 'Pedro Silva', 'pedro.silva@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655111222', 
  'A1234567B', 0, 1, NOW()
),
(
  2, 'Marta Ortiz', 'marta.ortiz@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655222333', 
  'B2345678C', 0, 2, NOW()
),
(
  3, 'Carmen Pastor', 'carmen.pastor@aulapro.com', 
  '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655333444', 
  'C3456789D', 0, 3, NOW()
);

-- Map junction table
INSERT INTO `estudiante_tutor` (`idEstudiante`, `idTutor`, `parentesco`) VALUES 
(1, 1, 'Padre'), 
(2, 2, 'Madre'), 
(3, 3, 'Madre');

-- ---------------------------------------------------------------------
-- 14. ANNOUNCEMENTS
-- ---------------------------------------------------------------------
INSERT INTO `anuncios` (`idAnuncio`, `titulo`, `mensaje`, `fechaAnuncio`, `fechaExpiracion`, `dirigidoA`) VALUES 
(1, 'Bienvenida al Año Académico 2026/2027', 'Les damos la más cordial bienvenida a todos los estudiantes y profesores a este nuevo año académico. Las clases comienzan el 15 de Septiembre a las 8:30.', NOW(), '2026-10-31', 'todos'), 
(2, 'Entrega de Proyectos TFG', 'Se recuerda a los estudiantes de 2º año que el plazo máximo para la subida del TFG y su documentación al Aula Virtual es el 15 de Junio.', NOW(), '2027-06-15', 'estudiantes'), 
(3, 'Reunión Extraordinaria de Claustro', 'Estimados docentes, se convoca una reunión extraordinaria de claustro para tratar las nuevas normativas de FP Dual el lunes 2 de Agosto a las 16:30.', NOW(), '2026-08-03', 'profesores');

-- ---------------------------------------------------------------------
-- 15. EVENTS
-- ---------------------------------------------------------------------
INSERT INTO `eventos` (`idEvento`, `tituloEvento`, `descripcionEvento`, `fechaEvento`, `horaEvento`, `ubicacionEvento`) VALUES 
(1, 'Exposición de Proyectos de Fin de Grado', 'Defensa de los Proyectos TFG/Proyectos Integradores ante el comité evaluador.', '2026-06-20', '09:00:00', 'Salón de Actos - Edificio Central'), 
(2, 'Jornada Informativa: Inicio de FCT', 'Charla obligatoria sobre el proceso, documentación y pautas a seguir durante el periodo de prácticas FCT.', '2026-02-15', '12:30:00', 'Laboratorio Informática I'), 
(3, 'Conferencia: Salidas Laborales en el Ámbito Web', 'Charla tecnológica a cargo de directores de desarrollo y talento de Tech Solutions.', '2026-07-30', '16:00:00', 'Salón de Actos');

-- ---------------------------------------------------------------------
-- 16. VIRTUAL CLASSROOM TASKS
-- ---------------------------------------------------------------------
INSERT INTO `aula_tareas` (`idTarea`, `titulo`, `descripcion`, `idModulo`, `idProfesor`, `archivoAdjunto`, `publicado`) VALUES 
(1, 'Estructuras de Control en PHP', 'Desarrollar una biblioteca básica de validación de datos utilizando sentencias condicionales, bucles anidados y arrays asociativos.', 3, 1, NULL, 1), 
(2, 'Diseño e Implementación de API REST', 'Crear una API RESTful para la gestión de productos con soporte para operaciones CRUD y respuestas estructuradas en formato JSON.', 3, 1, NULL, 1), 
(3, 'Manipulación Dinámica del DOM', 'Desarrollar una aplicación interactiva simple en JavaScript que agregue, elimine y filtre elementos de una tabla usando eventos y selectores nativos.', 4, 1, NULL, 1), 
(4, 'Maquetación Avanzada con CSS Grid', 'Crear un dashboard de administración responsive utilizando exclusivamente CSS Grid y Flexbox para organizar la cuadrícula.', 5, 3, NULL, 1);

-- ---------------------------------------------------------------------
-- 17. TASK SUBMISSIONS (ENTREGAS) & GRADES
-- ---------------------------------------------------------------------
INSERT INTO `aula_entregas` (
  `idEntrega`, `idTarea`, `idEstudiante`, `archivoEntrega`, `respuesta`, 
  `version`, `fechaEntrega`, `nota`, `estado`, `comentarioCalificacion`
) VALUES 
(
  1, 1, 1, 'practica_1_ana_silva.zip', 
  'Profesor, adjunto la práctica resuelta. He añadido como extra una vista HTML básica para probar las validaciones.', 
  1, DATE_SUB(NOW(), INTERVAL 2 DAY), 8.80, 'corregida', 
  'Excelente código, muy limpio y estructurado. Los extras están muy bien implementados.'
), 
(
  2, 1, 2, 'practica_1_david_ortiz.zip', 
  'Hola Juan, aquí tiene mi entrega de PHP. Un saludo.', 
  1, DATE_SUB(NOW(), INTERVAL 2 DAY), 7.20, 'corregida', 
  'Buen trabajo en general. Ten cuidado con los nombres de variables y la indentación.'
), 
(
  3, 2, 1, 'api_rest_ana_silva.zip', 
  'He diseñado los endpoints según los estándares REST. Se incluye archivo OpenAPI (Swagger) de documentación.', 
  1, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, 'enviada', 
  NULL
), 
(
  4, 3, 1, 'dom_ana_silva.zip', 
  'Adjunto código JS listo. He implementado delegación de eventos en la tabla para optimizar rendimiento.', 
  1, DATE_SUB(NOW(), INTERVAL 12 HOUR), 9.60, 'corregida', 
  'Fantástico uso de delegación de eventos y modularización del script JS. ¡Enhorabuena!'
);

-- ---------------------------------------------------------------------
-- 18. LIVE SESSION MEETINGS
-- ---------------------------------------------------------------------
INSERT INTO `aula_sesiones_vivas` (`idSesion`, `idModulo`, `idProfesor`, `titulo`, `descripcion`, `fechaSesion`, `horaSesion`, `enlaceReunion`, `plataforma`, `estado`) VALUES 
(1, 3, 1, 'Resolución de Dudas: API REST', 'Revisión grupal y solución de dudas sobre cómo diseñar e integrar los verbos y códigos de respuesta en endpoints.', DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), '11:00:00', 'https://meet.google.com/xyz-pdq-abc', 'Google Meet', 'programada'), 
(2, 4, 1, 'Taller JavaScript: Programación Asíncrona', 'Explicación detallada y práctica sobre el flujo con Event Loop, Promises, Fetch API y Async/Await.', DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), '10:00:00', 'https://meet.google.com/uvw-xyz-rst', 'Google Meet', 'programada');

-- ---------------------------------------------------------------------
-- 19. CHAT CONVERSATIONS & MESSAGES
-- ---------------------------------------------------------------------
INSERT INTO `chat_conversaciones` (`id`, `user_a_rol`, `user_a_id`, `user_b_rol`, `user_b_id`, `last_message_at`) VALUES 
(1, 'profesor', 1, 'estudiante', 1, DATE_SUB(NOW(), INTERVAL 10 MINUTE)), 
(2, 'profesor', 1, 'estudiante', 2, DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO `chat_mensajes` (`id`, `conversacion_id`, `emisor_rol`, `emisor_id`, `contenido`, `leido`, `fecha`) VALUES 
(1, 1, 'estudiante', 1, 'Hola profesor, ¿el lunes es festivo o hay entrega normal de la práctica 2?', 1, DATE_SUB(NOW(), INTERVAL 30 MINUTE)), 
(2, 1, 'profesor', 1, 'Hola Ana. Es día lectivo normal, por lo tanto la entrega se mantiene para las 23:59 de ese día.', 1, DATE_SUB(NOW(), INTERVAL 25 MINUTE)), 
(3, 1, 'estudiante', 1, 'Perfecto, ya la tengo casi lista. Muchas gracias por la aclaración.', 0, DATE_SUB(NOW(), INTERVAL 10 MINUTE)), 
(4, 2, 'estudiante', 2, 'Hola Juan, tengo un fallo al validar el token en la práctica de REST. ¿Me podría guiar un poco?', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)), 
(5, 2, 'profesor', 1, 'Hola David. Revisa la cabecera "Authorization" en tu middleware. Asegúrate de separar el prefijo "Bearer " del token propiamente dicho.', 0, DATE_SUB(NOW(), INTERVAL 45 MINUTE));

-- ---------------------------------------------------------------------
-- 20. ATTENDANCE LOGS (ASISTENCIAS)
-- ---------------------------------------------------------------------
INSERT INTO `asistencias` (`idAsistencia`, `idEstudiante`, `idModulo`, `idProfesor`, `fecha`, `estado`, `observacion`) VALUES 
(1, 1, 3, 1, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), 'presente', 'Llegó a la hora correcta'), 
(2, 2, 3, 1, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), 'retraso', 'Llegó 15 minutos tarde por tráfico'), 
(3, 3, 1, 2, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), 'presente', NULL), 
(4, 4, 1, 2, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), 'ausente', 'No comunicó la falta'), 
(5, 1, 4, 1, CURRENT_DATE, 'presente', NULL), 
(6, 2, 4, 1, CURRENT_DATE, 'justificado', 'Tiene justificante de cita médica');

-- ---------------------------------------------------------------------
-- 21. EXPENSE CATEGORIES & LOGGED EXPENSES
-- ---------------------------------------------------------------------
INSERT INTO `categorias_gasto` (`idCategoria`, `nombre`, `presupuestoAnual`, `color`, `activo`) VALUES 
(1, 'Licencias de Software', 5000.00, '#0ea5e9', 1), 
(2, 'Material e Instrumentos de Laboratorio', 10000.00, '#10b981', 1), 
(3, 'Material de Oficina e Imprenta', 2000.00, '#f59e0b', 1), 
(4, 'Infraestructura, Servidores y Cableado', 8000.00, '#ef4444', 1);

INSERT INTO `gastos` (`idGasto`, `idCategoria`, `idCiclo`, `concepto`, `importe`, `fecha`, `tipoJustificante`, `numeroReferencia`, `observaciones`) VALUES 
(1, 1, 1, 'Licencias JetBrains IDE (25 licencias académicas)', 450.00, DATE_SUB(CURRENT_DATE, INTERVAL 15 DAY), 'Factura', 'JET-2026-001', 'Uso exclusivo para DAW/DAM'), 
(2, 2, 3, 'Componentes de red (Switches administrables y Cat 6)', 1200.00, DATE_SUB(CURRENT_DATE, INTERVAL 12 DAY), 'Factura', 'NET-2026-104', 'Para laboratorio del ciclo SMR'), 
(3, 3, NULL, 'Lote de folios, bolígrafos, tóners y carpetas de secretaría', 185.50, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), 'Ticket', 'OFF-9923', 'Material de oficina general');

-- ---------------------------------------------------------------------
-- 22. DUAL EDUCATION PARTNERSHIP (COMPANIES & INTERNSHIPS FCT)
-- ---------------------------------------------------------------------
INSERT INTO `fp_empresas` (`idEmpresa`, `nombre`, `cif`, `direccion`, `persona_contacto`, `telefono`, `email`, `activo`) VALUES 
(1, 'Tech Solutions S.L.', 'B12345678', 'Parque Tecnológico, Edificio A', 'Marta García', '600123456', 'marta.garcia@techsolutions.com', 1), 
(2, 'Global Web Developers', 'B87654321', 'Avenida de la Informática 10', 'Luis Naranjo', '600987654', 'luis.naranjo@globalweb.com', 1);

INSERT INTO `fct` (
  `idFCT`, `idEstudiante`, `idCiclo`, `empresa`, `idEmpresa`, `tutorEmpresa`, 
  `emailTutorEmpresa`, `telefonoEmpresa`, `ciudadEmpresa`, `fechaInicio`, `fechaFin`, 
  `horasTotales`, `horasRealizadas`, `nota`, `apto`, `observaciones`, `idProfesorTutor`, `fase`
) VALUES 
(
  1, 1, 1, 'Tech Solutions S.L.', 1, 'Ramón Gómez', 
  'ramon.gomez@techsolutions.com', '655987654', 'Madrid', '2026-03-01', '2026-06-30', 
  400, 400, 9.20, 1, 'Excelente desempeño en el stack de desarrollo backend con PHP.', 1, 1
), 
(
  2, 2, 1, 'Global Web Developers', 2, 'Sofía Martínez', 
  'sofia.martinez@globalweb.com', '655654321', 'Madrid', '2026-03-01', '2026-06-30', 
  400, 260, NULL, NULL, 'Buen ritmo en maquetación. En progreso continuo.', 1, 1
);

SET FOREIGN_KEY_CHECKS = 1;
