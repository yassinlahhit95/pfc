USE yassjjzw_pfc;

-- ========================================
-- LEVELS (Niveles)
-- ========================================
INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'Grado Superior'),
(2, 'Grado Medio'),
(3, 'Grado Básico'),
(4, 'Colegio (Primaria/ESO/Bachillerato)');

-- ========================================
-- ACADEMIC CYCLES (Ciclos Formativos)
-- ========================================
INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`, `activo`, `fechaArchivado`) VALUES
(1, 'Desarrollo de Aplicaciones Web', 'DAW', 1200.00, 1, 1, NULL),
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'DAM', 1200.00, 1, 1, NULL),
(3, 'Sistemas Microinformáticos y Redes', 'SMR', 900.00, 2, 1, NULL),
(4, 'Administración de Sistemas Informáticos en Red', 'ASIR', 1500.00, 1, 1, NULL),
(5, 'Técnico en Informática', 'TI', 1100.00, 2, 1, NULL),
(6, 'Electrónica Industrial', 'EI', 950.00, 2, 1, NULL);

-- ========================================
-- DIRECTORS
-- ========================================
INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `password`, `telefonoDirector`, `dniDirector`, `fechaNacimientoDirector`, `fechaAltaDirector`, `direccionDirector`, `ciudadDirector`, `codigoPostalDirector`, `observacionesDirector`, `fcm_token`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Carlos Mendoza', 'admin@aulapro.com', '$2y$12$8BcEEoY4aFlVujZhRQnHgOcJO7fL.XGQsFtdngE5w9ER2//EphGbm', '600111222', '12345678A', '1980-05-15', '2024-09-01', 'Calle Mayor 1', 'Madrid', '28001', 'Director General de AulaPro', 'c4Xm0un3T7OF3Ys5Grz_TX:APA91bEl195en19HYRJjyopzQElzWDWhjcuAfAmJ431c1RVzQpUvIGHtnZxgAq_ZhSuOFK5hJsooJD_M8ld0pnIiWeZrSxFogXIZZaeqG7pZp31jA6BqEO0', 0, NULL, NULL),
(2, 'Isabel González', 'isabel@aulapro.com', '$2y$12$8BcEEoY4aFlVujZhRQnHgOcJO7fL.XGQsFtdngE5w9ER2//EphGbm', '600222333', '87654321B', '1975-08-20', '2024-09-01', 'Avenida Secundaria 2', 'Madrid', '28002', 'Subdirectora Académica', NULL, 0, NULL, NULL);

-- ========================================
-- TEACHERS (Profesores)
-- ========================================
INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, `dniProfesor`, `fechaNacimientoProfesor`, `fechaAltaProfesor`, `direccionProfesor`, `ciudadProfesor`, `codigoPostalProfesor`, `observacionesProfesor`, `fcm_token`, `esTutor`, `idCicloTutor`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Juan Pérez', 'juan.perez@aulapro.com', '$2y$12$NoDoFaNeZT43YYAR1XnAGOEhZHc9NGdJXxGc.JOceS21paDUZnQRq', '600333444', '23456789B', '1985-10-20', '2024-09-01', 'Calle Secundaria 2', 'Madrid', '28002', 'Profesor especialista en Backend. Tutor de 2º DAW.', NULL, 1, 1, 0, NULL, NULL),
(2, 'María Rodríguez', 'maria.rodriguez@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600444555', '34567890C', '1988-03-12', '2024-09-01', 'Avenida Principal 3', 'Madrid', '28003', 'Profesora de programación e iniciación al desarrollo.', NULL, 0, NULL, 0, NULL, NULL),
(3, 'Pedro Martínez', 'pedro.martinez@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600555666', '45678901D', '1982-07-25', '2024-09-01', 'Paseo del Prado 4', 'Madrid', '28004', 'Profesor de multiplataforma y hardware. Tutor de 2º DAM.', NULL, 1, 2, 0, NULL, NULL),
(4, 'Laura Gómez', 'laura.gomez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '600666777', '56789012E', '1990-01-15', '2024-09-01', 'Calle Tercera 5', 'Madrid', '28005', 'Profesora de sistemas y redes. Tutor de 2º SMR.', NULL, 1, 3, 0, NULL, NULL),
(5, 'Miguel Torres', 'miguel.torres@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '600777888', '67890123F', '1987-06-10', '2024-09-01', 'Calle Cuarta 6', 'Madrid', '28006', 'Profesor de administración de sistemas', NULL, 1, 4, 0, NULL, NULL),
(6, 'Sofía Navarro', 'sofia.navarro@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '600888999', '78901234G', '1989-09-22', '2024-09-01', 'Calle Quinta 7', 'Madrid', '28007', 'Profesora de programación frontend y diseño', NULL, 0, NULL, 0, NULL, NULL),
(7, 'Carlos Sánchez', 'carlos.sanchez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '600999000', '89012345H', '1984-11-05', '2024-09-01', 'Calle Sexta 8', 'Madrid', '28008', 'Profesor de bases de datos', NULL, 0, NULL, 0, NULL, NULL),
(8, 'Raquel López', 'raquel.lopez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '601000111', '90123456I', '1991-04-18', '2024-09-01', 'Calle Séptima 9', 'Madrid', '28009', 'Profesora de electrónica', NULL, 0, NULL, 0, NULL, NULL),
(9, 'Javier Fernández', 'javier.fernandez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '601111222', '01234567J', '1986-02-28', '2024-09-01', 'Calle Octava 10', 'Madrid', '28010', 'Profesor de formación empresarial', NULL, 0, NULL, 0, NULL, NULL),
(10, 'Beatriz García', 'beatriz.garcia@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '601222333', '12345678K', '1983-07-12', '2024-09-01', 'Calle Novena 11', 'Madrid', '28011', 'Profesora de inglés técnico', NULL, 0, NULL, 0, NULL, NULL);

-- ========================================
-- CYCLE-PROFESSOR RELATIONSHIPS
-- ========================================
INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES
(1, 1),
(1, 2),
(1, 6),
(2, 3),
(2, 7),
(3, 4),
(3, 8),
(4, 1),
(4, 5),
(5, 8),
(6, 8);

-- ========================================
-- STUDENTS (Estudiantes) - 50 students across cycles
-- ========================================
INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `telefonoEstudiante`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `direccionEstudiante`, `ciudadEstudiante`, `codigoPostalEstudiante`, `observacionesEstudiante`, `idCiclo`, `curso`, `anioEstudio`, `idCurso`, `archivoTFG`, `tituloTFG`, `fechaSubidaTFG`, `fcm_token`, `eliminado`, `fecha_eliminacion`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
-- DAW 1º (8 students)
(1, 'Juan García López', 'juan.garcia@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655123456', '13456789A', '2005-03-15', '2024-09-01', 'Calle A 1', 'Madrid', '28001', 'Estudiante aplicado', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(2, 'María Jiménez García', 'maria.jimenez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655234567', '24567890B', '2005-07-22', '2024-09-01', 'Calle B 2', 'Madrid', '28002', 'Estudiante destacada', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(3, 'Carlos Pérez Rodríguez', 'carlos.perez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655345678', '35678901C', '2005-11-08', '2024-09-01', 'Calle C 3', 'Madrid', '28003', 'Estudiante regular', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(4, 'Elena Martínez Díaz', 'elena.martinez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655456789', '46789012D', '2006-01-30', '2024-09-01', 'Calle D 4', 'Madrid', '28004', 'Estudiante con potencial', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(5, 'Pablo González Fernández', 'pablo.gonzalez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655567890', '57890123E', '2005-09-14', '2024-09-01', 'Calle E 5', 'Madrid', '28005', 'Estudiante interesado', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(6, 'Sofía Ruiz López', 'sofia.ruiz@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655678901', '68901234F', '2006-02-25', '2024-09-01', 'Calle F 6', 'Madrid', '28006', 'Estudiante dedicada', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(7, 'Andrés Sánchez García', 'andres.sanchez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655789012', '79012345G', '2005-05-19', '2024-09-01', 'Calle G 7', 'Madrid', '28007', 'Estudiante emprendedor', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(8, 'Natalia Romero Moreno', 'natalia.romero@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655890123', '80123456H', '2006-04-11', '2024-09-01', 'Calle H 8', 'Madrid', '28008', 'Estudiante colaboradora', 1, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- DAW 2º (6 students)
(9, 'Ricardo Herrera Sánchez', 'ricardo.herrera@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '655901234', '91234567I', '2004-06-28', '2023-09-01', 'Calle I 9', 'Madrid', '28009', 'Estudiante avanzado', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(10, 'Martina Campos López', 'martina.campos@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656012345', '02345678J', '2004-12-03', '2023-09-01', 'Calle J 10', 'Madrid', '28010', 'Estudiante sobresaliente', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(11, 'Óscar Núñez Flores', 'oscar.nunez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656123456', '13456789K', '2004-08-17', '2023-09-01', 'Calle K 11', 'Madrid', '28011', 'Estudiante profesional', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(12, 'Victoria Ramos Delgado', 'victoria.ramos@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656234567', '24567890L', '2004-10-09', '2023-09-01', 'Calle L 12', 'Madrid', '28012', 'Estudiante comprometida', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(13, 'Diego Iglesias Aroca', 'diego.iglesias@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656345678', '35678901M', '2004-05-12', '2023-09-01', 'Calle M 13', 'Madrid', '28013', 'Estudiante responsable', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(14, 'Roxana Pérez Carmona', 'roxana.perez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656456789', '46789012N', '2004-03-20', '2023-09-01', 'Calle N 14', 'Madrid', '28014', 'Estudiante técnica', 1, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- DAM 1º (8 students)
(15, 'Sergio López Martínez', 'sergio.lopez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656567890', '57890123O', '2005-04-12', '2024-09-01', 'Calle O 15', 'Madrid', '28015', 'Estudiante de móviles', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(16, 'Irene García Castro', 'irene.garcia@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656678901', '68901234P', '2005-09-05', '2024-09-01', 'Calle P 16', 'Madrid', '28016', 'Estudiante programadora', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(17, 'Alejandro Rodríguez Medina', 'alejandro.rodriguez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656789012', '79012345Q', '2005-02-21', '2024-09-01', 'Calle Q 17', 'Madrid', '28017', 'Estudiante ambicioso', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(18, 'Raquel Vázquez Hernández', 'raquel.vazquez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656890123', '80123456R', '2005-07-14', '2024-09-01', 'Calle R 18', 'Madrid', '28018', 'Estudiante disciplinada', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(19, 'Lorenzo Acosta Rivera', 'lorenzo.acosta@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '656901234', '91234567S', '2005-11-30', '2024-09-01', 'Calle S 19', 'Madrid', '28019', 'Estudiante creativo', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(20, 'Valentina Pino Santana', 'valentina.pino@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657012345', '02345678T', '2006-03-08', '2024-09-01', 'Calle T 20', 'Madrid', '28020', 'Estudiante innovadora', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(21, 'Xavier Gallegos Mena', 'xavier.gallegos@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657123456', '13456789U', '2005-06-03', '2024-09-01', 'Calle U 21', 'Madrid', '28021', 'Estudiante versátil', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(22, 'Yara Campos León', 'yara.campos@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657234567', '24567890V', '2005-08-15', '2024-09-01', 'Calle V 22', 'Madrid', '28022', 'Estudiante analítica', 2, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- DAM 2º (5 students)
(23, 'Adrián Ochoa Mederos', 'adrian.ochoa@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657345678', '35678901W', '2004-05-19', '2023-09-01', 'Calle W 23', 'Madrid', '28023', 'Estudiante senior', 2, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(24, 'Lucía Santana Aroca', 'lucia.santana@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657456789', '46789012X', '2004-09-26', '2023-09-01', 'Calle X 24', 'Madrid', '28024', 'Estudiante meritoria', 2, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(25, 'Ángel Rivas Álvarez', 'angel.rivas@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657567890', '57890123Y', '2004-01-13', '2023-09-01', 'Calle Y 25', 'Madrid', '28025', 'Estudiante con liderazgo', 2, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(26, 'Sandra Valenzuela López', 'sandra.valenzuela@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657678901', '68901234Z', '2004-11-07', '2023-09-01', 'Calle Z 26', 'Madrid', '28026', 'Estudiante equilibrada', 2, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(27, 'Felipe Romero Quintero', 'felipe.romero@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657789012', '79012346A', '2004-07-08', '2023-09-01', 'Calle AA 27', 'Madrid', '28027', 'Estudiante persistente', 2, 'Grado Superior', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- SMR 1º (6 students)
(28, 'Miguel Gutiérrez Ramírez', 'miguel.gutierrez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657890123', '80123457B', '2005-06-03', '2024-09-01', 'Calle AB 28', 'Madrid', '28028', 'Estudiante de redes', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(29, 'Gabriela Romero Quintero', 'gabriela.romero@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '657901234', '91234568C', '2005-08-15', '2024-09-01', 'Calle AC 29', 'Madrid', '28029', 'Estudiante técnica sistemas', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(30, 'Fernando Soto Molina', 'fernando.soto@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658012345', '02345679D', '2005-12-22', '2024-09-01', 'Calle AD 30', 'Madrid', '28030', 'Estudiante práctico', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(31, 'Coral Miranda Fuentes', 'coral.miranda@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658123456', '13456790E', '2006-02-14', '2024-09-01', 'Calle AE 31', 'Madrid', '28031', 'Estudiante ordenada', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(32, 'Tomás Oliva Ruiz', 'tomas.oliva@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658234567', '24567891F', '2005-04-27', '2024-09-01', 'Calle AF 32', 'Madrid', '28032', 'Estudiante curioso', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(33, 'Marta Quirós García', 'marta.quiros@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658345678', '35678902G', '2005-10-31', '2024-09-01', 'Calle AG 33', 'Madrid', '28033', 'Estudiante competente', 3, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- SMR 2º (5 students)
(34, 'Hugo Soria Parada', 'hugo.soria@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658456789', '46789013H', '2004-07-08', '2023-09-01', 'Calle AH 34', 'Madrid', '28034', 'Estudiante principalista', 3, 'Grado Medio', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(35, 'Antonia Torres Robles', 'antonia.torres@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658567890', '57890124I', '2004-03-20', '2023-09-01', 'Calle AI 35', 'Madrid', '28035', 'Estudiante trabajadora', 3, 'Grado Medio', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(36, 'Guillermo Ramos Iglesias', 'guillermo.ramos@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658678901', '68901235J', '2004-09-16', '2023-09-01', 'Calle AJ 36', 'Madrid', '28036', 'Estudiante consistente', 3, 'Grado Medio', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(37, 'Leticia Pérez Carmona', 'leticia.perez@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658789012', '79012347K', '2004-12-11', '2023-09-01', 'Calle AK 37', 'Madrid', '28037', 'Estudiante reflexiva', 3, 'Grado Medio', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(38, 'Néstor Calderón Alarcón', 'nestor.calderon@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658890123', '80123458L', '2005-02-09', '2024-09-01', 'Calle AL 38', 'Madrid', '28038', 'Estudiante estructurado', 3, 'Grado Medio', '2º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- ASIR 1º (4 students)
(39, 'Miriam Quintana Reyes', 'miriam.quintana@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '658901234', '91234569M', '2005-11-04', '2024-09-01', 'Calle AM 39', 'Madrid', '28039', 'Estudiante sistemática', 4, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(40, 'Ismael Llano Carmona', 'ismael.llano@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659012345', '02345680N', '2006-01-25', '2024-09-01', 'Calle AN 40', 'Madrid', '28040', 'Estudiante riguroso', 4, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(41, 'Silvia Muñiz Vallés', 'silvia.muniz@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659123456', '13456791O', '2005-08-13', '2024-09-01', 'Calle AO 41', 'Madrid', '28041', 'Estudiante clara', 4, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(42, 'Everardo Roca Montes', 'everardo.roca@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659234567', '24567892P', '2006-05-30', '2024-09-01', 'Calle AP 42', 'Madrid', '28042', 'Estudiante enfocado', 4, 'Grado Superior', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- TI 1º (4 students)
(43, 'Tatiana Vega Ponce', 'tatiana.vega@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659345678', '35678903Q', '2005-07-22', '2024-09-01', 'Calle AQ 43', 'Madrid', '28043', 'Estudiante moderna', 5, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(44, 'Marcelo Ríos Huerta', 'marcelo.rios@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659456789', '46789014R', '2006-03-11', '2024-09-01', 'Calle AR 44', 'Madrid', '28044', 'Estudiante joven', 5, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(45, 'Renata Parra Conejo', 'renata.parra@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659567890', '57890125S', '2005-09-18', '2024-09-01', 'Calle AS 45', 'Madrid', '28045', 'Estudiante positiva', 5, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(46, 'Víctor Fuentes Aguirre', 'victor.fuentes@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659678901', '68901236T', '2005-04-07', '2024-09-01', 'Calle AT 46', 'Madrid', '28046', 'Estudiante resolutivo', 5, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
-- EI 1º (4 students)
(47, 'Yolanda Sepúlveda Urbina', 'yolanda.sepulveda@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659789012', '79012348U', '2005-10-29', '2024-09-01', 'Calle AU 47', 'Madrid', '28047', 'Estudiante eléctrica', 6, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(48, 'Zacarías Valdes Fernández', 'zacarias.valdes@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659890123', '80123459V', '2005-05-16', '2024-09-01', 'Calle AV 48', 'Madrid', '28048', 'Estudiante manual', 6, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(49, 'Olga Roldán Salazar', 'olga.roldan@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '659901234', '91234570W', '2006-01-03', '2024-09-01', 'Calle AW 49', 'Madrid', '28049', 'Estudiante precisa', 6, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(50, 'Wendell Fuentes Iglesias', 'wendell.fuentes@student.aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '660012345', '02345681X', '2006-03-21', '2024-09-01', 'Calle AX 50', 'Madrid', '28050', 'Estudiante industrial', 6, 'Grado Medio', '1º', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL);

COMMIT;
