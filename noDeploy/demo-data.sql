-- AulaPro Demo Data — Minimal, Verified Setup
-- Import AFTER running database.sql schema
-- Safe for re-import (DELETE clears before INSERT)

-- ================================================================
-- CLEAR EXISTING DEMO DATA (comprehensive)
-- ================================================================
DELETE FROM `horarios` WHERE `idCiclo` IN (1,2,3,4,5,6);
DELETE FROM `calificaciones_modulos` WHERE `idEstudiante` > 0;
DELETE FROM `estudiante_tutor` WHERE `idEstudiante` > 0;
DELETE FROM `estudiantes` WHERE `emailEstudiante` LIKE 'est%@student.edu' OR `emailEstudiante` LIKE '%@student.edu';
DELETE FROM `ciclo_profesor` WHERE `idProfesor` BETWEEN 1 AND 12;
DELETE FROM `profesores` WHERE `dniProfesor` LIKE '456789%';
DELETE FROM `grupos` WHERE `idCiclo` IN (1,2,3,4,5,6);
DELETE FROM `modulos` WHERE `idCiclo` IN (1,2,3,4,5,6);
DELETE FROM `cursos_academicos` WHERE `idCiclo` IN (1,2,3,4,5,6);
DELETE FROM `ciclos` WHERE `idCiclo` IN (1,2,3,4,5,6);
DELETE FROM `aulas` WHERE `idAula` BETWEEN 1 AND 4;

-- ================================================================
-- 1. ADMIN DIRECTOR ACCOUNT
-- ================================================================
INSERT INTO `directores` (
  `idDirector`,
  `nombreDirector`,
  `emailDirector`,
  `password`,
  `dniDirector`
) VALUES (
  1,
  'Admin Aulapro',
  'admin@aulapro.com',
  '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  '12345678A'
) ON DUPLICATE KEY UPDATE
  `nombreDirector` = 'Admin Aulapro',
  `emailDirector` = 'admin@aulapro.com';

-- ================================================================
-- 2. CENTER CONFIGURATION
-- ================================================================
UPDATE `configuracion_centro` SET
  `nombreCentro` = 'Instituto de Formación Profesional San Miguel',
  `codigoCentro` = 'IES-SM-2024',
  `nifCifCentro` = 'Q9876543A',
  `direccionCentro` = 'Calle Principal 123, 28001',
  `ciudadCentro` = 'Madrid',
  `cpCentro` = '28001',
  `telefonoCentro` = '91-555-0123',
  `emailCentro` = 'info@ifpsanmiguel.edu',
  `cursoEscolar` = '2024-2025',
  `nombreDirectorFirmante` = 'Admin Aulapro'
WHERE `idConfig` = 1;

-- ================================================================
-- 3. VOCATIONAL PROGRAMS (6 CICLOS)
-- ================================================================
INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `activo`) VALUES
(1, 'Técnico en Informática (Grado Medio)', 'TIF-GM', 1500.00, 1),
(2, 'Técnico en Administración y Finanzas (Grado Superior)', 'TAF-GS', 2000.00, 1),
(3, 'Técnico en Comercio Electrónico (Grado Superior)', 'TCE-GS', 2000.00, 1),
(4, 'Técnico en Mantenimiento de Sistemas (Grado Superior)', 'TMSI-GS', 2200.00, 1),
(5, 'Técnico en Asistencia a la Dirección (Grado Superior)', 'TAD-GS', 1800.00, 1),
(6, 'Técnico en Electricidad (Grado Medio)', 'TEL-GM', 1600.00, 1);

-- ================================================================
-- 4. ACADEMIC COURSES (per program, 2 years each)
-- ================================================================
INSERT INTO `cursos_academicos` (`idCiclo`, `nombre`, `orden`) VALUES
(1, '1º Informática', 1),
(1, '2º Informática', 2),
(2, '1º Administración', 1),
(2, '2º Administración', 2),
(3, '1º Comercio Electrónico', 1),
(3, '2º Comercio Electrónico', 2),
(4, '1º Mantenimiento de Sistemas', 1),
(4, '2º Mantenimiento de Sistemas', 2),
(5, '1º Asistencia a la Dirección', 1),
(5, '2º Asistencia a la Dirección', 2),
(6, '1º Electricidad', 1),
(6, '2º Electricidad', 2);

-- ================================================================
-- 5. CLASSROOMS (AULAS)
-- ================================================================
INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`, `activa`) VALUES
(1, 1, 1, 'Aula 101 - Teoría', 'teoria', 30, 1),
(2, 1, 2, 'Aula 102 - Laboratorio', 'laboratorio', 20, 1),
(3, 1, 3, 'Aula 103 - Taller', 'taller', 25, 1),
(4, 1, 4, 'Aula 104 - Teoría', 'teoria', 35, 1);

-- ================================================================
-- 5. MODULES (MODULOS) — 4-5 modules per program
-- ================================================================
-- Ciclo 1: Informática
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(1, 'Sistemas Operativos', 'SO', 160, 1, '1º'),
(2, 'Bases de Datos', 'BD', 140, 1, '1º'),
(3, 'Programación Web', 'PW', 180, 1, '2º'),
(4, 'Redes e Internet', 'RI', 120, 1, '2º');

-- Ciclo 2: Administración
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(5, 'Contabilidad General', 'CG', 200, 2, '1º'),
(6, 'Administración Fiscal', 'AF', 180, 2, '1º'),
(7, 'Gestión Financiera', 'GF', 160, 2, '2º'),
(8, 'Recursos Humanos', 'RH', 140, 2, '2º'),
(9, 'Auditoría', 'AU', 120, 2, '2º');

-- Ciclo 3: Comercio Electrónico
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(10, 'Plataformas de Comercio Electrónico', 'PCE', 160, 3, '1º'),
(11, 'Marketing Digital', 'MD', 150, 3, '1º'),
(12, 'Gestión de Contenidos Web', 'GCW', 140, 3, '2º'),
(13, 'Logística y Distribución', 'LD', 130, 3, '2º');

-- Ciclo 4: Mantenimiento de Sistemas
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(14, 'Sistemas de Información', 'SI', 190, 4, '1º'),
(15, 'Ciberseguridad', 'CS', 160, 4, '1º'),
(16, 'Mantenimiento de Hardware', 'MH', 140, 4, '2º'),
(17, 'Soporte Técnico', 'ST', 130, 4, '2º'),
(18, 'Virtualización y Nube', 'VN', 120, 4, '2º');

-- Ciclo 5: Asistencia a la Dirección
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(19, 'Oficina Moderna', 'OM', 150, 5, '1º'),
(20, 'Gestión Administrativa', 'GA', 180, 5, '1º'),
(21, 'Comunicación Empresarial', 'CE', 140, 5, '2º'),
(22, 'Lengua Extranjera (Inglés)', 'LE', 120, 5, '2º');

-- Ciclo 6: Electricidad
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `cursoAnio`) VALUES
(23, 'Circuitos Eléctricos', 'CE', 180, 6, '1º'),
(24, 'Instalaciones Eléctricas', 'IE', 200, 6, '1º'),
(25, 'Máquinas Eléctricas', 'ME', 160, 6, '2º'),
(26, 'Automatismos', 'AU', 140, 6, '2º');

-- ================================================================
-- 6. TEACHERS (PROFESORES) — 12 teachers with passwords (test123)
-- ================================================================
INSERT INTO `profesores` (
  `idProfesor`,
  `nombreProfesor`,
  `emailProfesor`,
  `password`,
  `dniProfesor`
) VALUES
(1, 'María García López', 'maria.garcia@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678901B'),
(2, 'Carlos Rodríguez Martín', 'carlos.rodriguez@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678902C'),
(3, 'Elena Sánchez Ruiz', 'elena.sanchez@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678903D'),
(4, 'Fernando Díaz González', 'fernando.diaz@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678904E'),
(5, 'Ana Moreno Jiménez', 'ana.moreno@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678905F'),
(6, 'Luis Fernández Pérez', 'luis.fernandez@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678906G'),
(7, 'Rosa Gutiérrez López', 'rosa.gutierrez@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678907H'),
(8, 'Juan Pablo Navarro', 'juanpablo.navarro@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678908I'),
(9, 'Patricia Flores Castillo', 'patricia.flores@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678909J'),
(10, 'Miguel Ángel Ramos', 'miguelangel.ramos@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678910K'),
(11, 'Beatriz Castillo Méndez', 'beatriz.castillo@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678911L'),
(12, 'David Herrera Jiménez', 'david.herrera@ifpsanmiguel.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '45678912M');

-- ================================================================
-- 7. ASSIGN TEACHERS TO PROGRAMS (ciclo_profesor)
-- ================================================================
INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES
(1, 1), (1, 2), (1, 6),
(2, 3), (2, 4),
(3, 5), (3, 3),
(4, 6), (4, 7), (4, 8),
(5, 9), (5, 4),
(6, 10), (6, 11), (6, 12);

-- ================================================================
-- 8. GROUPS (GRUPOS)
-- ================================================================
INSERT INTO `grupos` (`idGrupo`, `nombreGrupo`, `idCiclo`, `anioEstudio`) VALUES
(1, '1º Informática - Grupo A', 1, '1º'),
(2, '1º Informática - Grupo B', 1, '1º'),
(3, '2º Informática - Grupo A', 1, '2º'),
(4, '1º Administración - Grupo A', 2, '1º'),
(5, '1º Administración - Grupo B', 2, '1º'),
(6, '2º Administración - Grupo A', 2, '2º'),
(7, '1º Comercio - Grupo A', 3, '1º'),
(8, '1º Comercio - Grupo B', 3, '1º'),
(9, '2º Comercio - Grupo A', 3, '2º'),
(10, '1º Mantenimiento - Grupo A', 4, '1º'),
(11, '1º Mantenimiento - Grupo B', 4, '1º'),
(12, '2º Mantenimiento - Grupo A', 4, '2º'),
(13, '1º Asistencia - Grupo A', 5, '1º'),
(14, '1º Asistencia - Grupo B', 5, '1º'),
(15, '2º Asistencia - Grupo A', 5, '2º'),
(16, '1º Electricidad - Grupo A', 6, '1º'),
(17, '1º Electricidad - Grupo B', 6, '1º'),
(18, '2º Electricidad - Grupo A', 6, '2º'),
(19, '2º Electricidad - Grupo B', 6, '2º');

-- ================================================================
-- 9. SAMPLE STUDENTS (optional)
-- ================================================================
-- Add students via the admin UI or with a bulk import script
-- Sample: 3 students per group to verify setup works (password: test123)
INSERT INTO `estudiantes` (
  `nombreEstudiante`,
  `emailEstudiante`,
  `password`,
  `dniEstudiante`,
  `idCiclo`,
  `anioEstudio`,
  `idGrupo`
) VALUES
('Juan Martínez Pérez', 'est001@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345679A', 1, '1º', 1),
('María González García', 'est002@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345680B', 1, '1º', 1),
('Carlos López Ruiz', 'est003@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345681C', 1, '1º', 1),
('Ana Rodríguez García', 'est004@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345682D', 1, '1º', 2),
('Luis Fernández López', 'est005@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345683E', 1, '1º', 2),
('Sandra Díaz Sánchez', 'est006@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345684F', 1, '1º', 2),
('David García Martín', 'est007@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345685G', 1, '2º', 3),
('Patricia Moreno López', 'est008@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345686H', 1, '2º', 3),
('Roberto Jiménez García', 'est009@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345687I', 1, '2º', 3),
('Elena Castillo Pérez', 'est010@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345688J', 2, '1º', 4),
('Miguel Ángel Sánchez', 'est011@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345689K', 2, '1º', 4),
('Beatriz Gómez López', 'est012@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345690L', 2, '1º', 4),
('José María Rodríguez', 'est013@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345691M', 2, '1º', 5),
('Soledad Herrera García', 'est014@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345692N', 2, '1º', 5),
('Fernando López Sánchez', 'est015@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345693O', 2, '1º', 5),
('Raquel Martínez García', 'est016@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345694P', 2, '2º', 6),
('Óscar Navarro López', 'est017@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345695Q', 2, '2º', 6),
('Verónica Flores Díaz', 'est018@student.edu', '$2y$10$WMvRiSBvk5uh1M5OY8yH6ORMqxGBFBWMfqJF6.bmaA3Gipm7S4Wnj2', '12345696R', 2, '2º', 6);

-- ================================================================
-- 10. SCHEDULES (HORARIOS) — Sample Mon-Fri timetables
-- ================================================================
-- Ciclo 1: Informática
INSERT INTO `horarios` (`idCiclo`, `diaSemana`, `horaInicio`, `horaFin`, `idModulo`, `idAula`, `idProfesor`) VALUES
(1, 'Lunes', '08:00', '09:30', 1, 1, 1),
(1, 'Lunes', '10:00', '11:30', 2, 2, 2),
(1, 'Martes', '08:00', '09:30', 3, 3, 6),
(1, 'Martes', '10:00', '11:30', 4, 4, 6),
(1, 'Miércoles', '08:00', '09:30', 1, 1, 1),
(1, 'Jueves', '08:00', '09:30', 3, 3, 6),
(1, 'Viernes', '08:00', '09:30', 1, 1, 1);

-- Ciclo 2: Administración (different times to avoid aula conflicts)
INSERT INTO `horarios` (`idCiclo`, `diaSemana`, `horaInicio`, `horaFin`, `idModulo`, `idAula`, `idProfesor`) VALUES
(2, 'Lunes', '12:00', '13:30', 5, 1, 3),
(2, 'Lunes', '14:00', '15:30', 6, 2, 4),
(2, 'Martes', '12:00', '13:30', 7, 3, 3),
(2, 'Martes', '14:00', '15:30', 8, 4, 4),
(2, 'Miércoles', '12:00', '13:30', 9, 1, 3),
(2, 'Jueves', '12:00', '13:30', 6, 3, 3),
(2, 'Viernes', '12:00', '13:30', 5, 1, 3);

-- ================================================================
-- DEMO DATA COMPLETE
-- Ready for import: database.sql → demo-data.sql
-- Login: use your directores account or install wizard
-- ================================================================
