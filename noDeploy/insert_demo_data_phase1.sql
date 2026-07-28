-- Phase 1 Calendar + Ausencias + Horarios demo data
-- Inserted: 2026-07-28
-- Scope of this run (checked against the live yassjjzw_pfc DB before writing):
--   - eventos/recordatorios/notificaciones_recordatorios/asistencias/horarios already existed
--     with SOME seed data, but:
--       * only 2 ACTIVE eventos had a future fechaEvento (id 3, id 17) -> added 5 more
--         spread across the next ~21 days, covering publica/roles/personalizado.
--       * horarios had 75 rows but 60 of them (idCiclo 2-5) referenced idProfesor/idModulo/
--         idCiclo/idAula values that DON'T EXIST in profesores/modulos/ciclos/aulas (the FK
--         constraints exist on the table but that data was inserted with
--         FOREIGN_KEY_CHECKS=0, silently violating them). Only idCiclo=1's 15 rows were
--         actually valid. This script deletes the orphaned 60 rows and replaces them with
--         a valid, complete weekly grid for the two other real ciclos (idCiclo 2 and 3),
--         adding the professors/modules needed to do that with real FK-valid ids.
--       * asistencias only had 6 rows across 2 dates -> added 14 more across ~9 days and
--         both real ciclos' students, for 20 total.

USE yassjjzw_pfc;
SET NAMES utf8mb4;

-- ── A. Supporting data needed for a real (non-orphaned) ciclo2/ciclo3 schedule ──

-- 2 more profesores (ids 4,5 — table was at 3), reusing the default demo password hash.
INSERT INTO profesores (nombreProfesor, emailProfesor, dniProfesor, esTutor) VALUES
('Laura Gómez',   'laura.gomez@aulapro.com',   '56789012E', 0),
('Miguel Torres', 'miguel.torres@aulapro.com', '67890123F', 0);

-- 2 more modulos for ciclo2 (DAM) and 2 more for ciclo3 (SMR) — each of those only had
-- 1 module before, not enough for a multi-subject weekly grid.
INSERT INTO modulos (nombreModulo, codigoModulo, horasMaximas, idCiclo, idCurso, tipoModulo, cursoAnio, creditosECTS) VALUES
('Acceso a Datos',                 'AD',  160, 2, 4, 'Específico', '2º', 7),
('Desarrollo de Interfaces',       'DI',  160, 2, 4, 'Específico', '2º', 7),
('Redes Locales',                  'RL',  130, 3, 5, 'Específico', '1º', 6),
('Sistemas Operativos Monopuesto', 'SOM', 130, 3, 5, 'Específico', '1º', 6);

-- ── B. Horarios: drop the orphaned rows (idCiclo 2-5, referencing non-existent FKs), ──
-- keep idCiclo=1's 15 valid rows untouched, then insert a valid grid for ciclo 2 and 3.
DELETE FROM horarios WHERE idCiclo <> 1;

-- Ciclo 2 (DAM): módulo 7 (PMDM/profesor3), 9 (AD/profesor4), 10 (DI/profesor2). Aula 2.
INSERT INTO horarios (idCiclo, diaSemana, horaInicio, horaFin, idModulo, idProfesor, idAula) VALUES
(2, 'Lunes',     '08:30:00', '10:30:00', 7,  3, 2),
(2, 'Lunes',     '10:30:00', '12:30:00', 9,  4, 2),
(2, 'Lunes',     '12:30:00', '14:30:00', 10, 2, 2),
(2, 'Martes',    '08:30:00', '10:30:00', 7,  3, 2),
(2, 'Martes',    '10:30:00', '12:30:00', 9,  4, 2),
(2, 'Martes',    '12:30:00', '14:30:00', 10, 2, 2),
(2, 'Miércoles', '08:30:00', '10:30:00', 7,  3, 2),
(2, 'Miércoles', '10:30:00', '12:30:00', 9,  4, 2),
(2, 'Miércoles', '12:30:00', '14:30:00', 10, 2, 2),
(2, 'Jueves',    '08:30:00', '10:30:00', 7,  3, 2),
(2, 'Jueves',    '10:30:00', '12:30:00', 9,  4, 2),
(2, 'Jueves',    '12:30:00', '14:30:00', 10, 2, 2),
(2, 'Viernes',   '08:30:00', '10:30:00', 7,  3, 2),
(2, 'Viernes',   '10:30:00', '12:30:00', 9,  4, 2),
(2, 'Viernes',   '12:30:00', '14:30:00', 10, 2, 2);

-- Ciclo 3 (SMR): módulo 8 (MME/profesor5), 11 (RL/profesor4), 12 (SOM/profesor3). Aula 3.
-- Afternoon/evening shift (SMR runs later) — also keeps profesor4's ciclo2 morning slot
-- and profesor3's ciclo2 morning slot from colliding with their ciclo3 slots here, since
-- uk_horario_profesor is unique on (idProfesor, diaSemana, horaInicio).
INSERT INTO horarios (idCiclo, diaSemana, horaInicio, horaFin, idModulo, idProfesor, idAula) VALUES
(3, 'Lunes',     '15:00:00', '17:00:00', 8,  5, 3),
(3, 'Lunes',     '17:00:00', '19:00:00', 11, 4, 3),
(3, 'Lunes',     '19:00:00', '21:00:00', 12, 3, 3),
(3, 'Martes',    '15:00:00', '17:00:00', 8,  5, 3),
(3, 'Martes',    '17:00:00', '19:00:00', 11, 4, 3),
(3, 'Martes',    '19:00:00', '21:00:00', 12, 3, 3),
(3, 'Miércoles', '15:00:00', '17:00:00', 8,  5, 3),
(3, 'Miércoles', '17:00:00', '19:00:00', 11, 4, 3),
(3, 'Miércoles', '19:00:00', '21:00:00', 12, 3, 3),
(3, 'Jueves',    '15:00:00', '17:00:00', 8,  5, 3),
(3, 'Jueves',    '17:00:00', '19:00:00', 11, 4, 3),
(3, 'Jueves',    '19:00:00', '21:00:00', 12, 3, 3),
(3, 'Viernes',   '15:00:00', '17:00:00', 8,  5, 3),
(3, 'Viernes',   '17:00:00', '19:00:00', 11, 4, 3),
(3, 'Viernes',   '19:00:00', '21:00:00', 12, 3, 3);

-- ── C. Eventos: 5 new active eventos across the next ~21 days, mixed visibility/times ──
-- (idCreador=1 -> Carlos Mendoza, the only row in `directores`).
-- audiencia_json shape must match modelos/eventos.php's obtenerEventosParaUsuario():
-- roles -> {"roles": [...]}, personalizado -> {"usuarios_custom": [{"id":N,"tipo":"rol"}, ...]}.
INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento, idCreador, tipo_visibilidad, audiencia_json, activo) VALUES
('Reunión Consejo Académico', 'Revisión de avances y problemas académicos del cuatrimestre', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', 'Sala de Juntas', 1, 'roles', JSON_OBJECT('roles', JSON_ARRAY('director', 'profesor')), 1),
('Entrega de Retos Finales', 'Última fecha para entregar los retos del ciclo', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '23:59:59', 'Plataforma Virtual', 1, 'publica', NULL, 1),
('Jornada de Tutoría', 'Sesión de tutoría individual con estudiantes', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', 'Oficina de Tutoría', 1, 'roles', JSON_OBJECT('roles', JSON_ARRAY('tutor')), 1),
('Charla de Orientación Laboral', 'Salidas profesionales y mercado de trabajo del sector', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '09:30:00', 'Salón de Actos', 1, 'publica', NULL, 1),
('Reunión de Padres y Tutores', 'Seguimiento individual del alumnado con tutores legales', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '17:00:00', 'Aula 201', 1, 'personalizado', JSON_OBJECT('usuarios_custom', JSON_ARRAY(JSON_OBJECT('id', 1, 'tipo', 'estudiante'), JSON_OBJECT('id', 2, 'tipo', 'estudiante'))), 1);

-- Recordatorios for the 5 new eventos (idEvento auto-assigned above — read back by title
-- since AUTO_INCREMENT value isn't known ahead of time in a plain .sql script).
INSERT INTO recordatorios (idEvento, tipo_recordatorio, minutos_antes, activo)
SELECT idEvento, '24h_antes', 1440, 1 FROM eventos WHERE tituloEvento IN
    ('Reunión Consejo Académico', 'Entrega de Retos Finales', 'Jornada de Tutoría', 'Charla de Orientación Laboral', 'Reunión de Padres y Tutores')
    AND fechaEvento >= CURDATE();
INSERT INTO recordatorios (idEvento, tipo_recordatorio, minutos_antes, activo)
SELECT idEvento, '1h_antes', 60, 1 FROM eventos WHERE tituloEvento IN
    ('Reunión Consejo Académico', 'Entrega de Retos Finales', 'Jornada de Tutoría', 'Charla de Orientación Laboral', 'Reunión de Padres y Tutores')
    AND fechaEvento >= CURDATE();
-- 'en_inicio' only for 3 of the 5, to vary the reminder combination per event.
INSERT INTO recordatorios (idEvento, tipo_recordatorio, minutos_antes, activo)
SELECT idEvento, 'en_inicio', 0, 1 FROM eventos WHERE tituloEvento IN
    ('Reunión Consejo Académico', 'Jornada de Tutoría', 'Reunión de Padres y Tutores')
    AND fechaEvento >= CURDATE();
INSERT INTO recordatorios (idEvento, tipo_recordatorio, minutos_antes, activo)
SELECT idEvento, 'en_inicio', 0, 0 FROM eventos WHERE tituloEvento IN
    ('Entrega de Retos Finales', 'Charla de Orientación Laboral')
    AND fechaEvento >= CURDATE();

-- ── D. Asistencias: 14 more rows (6 existing -> 20 total), across both real ciclos'
-- students (1-4 = ciclo1/DAW, 5-6 = ciclo2/DAM), varied estado and dates, matching the
-- module/professor pairing from the horarios grid above.
INSERT INTO asistencias (idEstudiante, idModulo, idProfesor, fecha, estado, observacion) VALUES
(1, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'ausente',    'Sin justificación'),
(1, 2, 2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'presente',   NULL),
(2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'justificado','Cita médica'),
(2, 2, 2, DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'retraso',    'Autobús con retraso'),
(3, 4, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'presente',   NULL),
(3, 2, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'ausente',    NULL),
(4, 2, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'retraso',    'Tráfico'),
(4, 3, 1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'justificado','Justificante médico'),
(5, 7, 3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'ausente',    'Sin aviso'),
(5, 9, 4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'presente',   NULL),
(5, 10, 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'justificado','Cita médica'),
(6, 7, 3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'presente',   NULL),
(6, 9, 4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'retraso',    'Llegó tarde'),
(6, 10, 2, DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'ausente',    NULL);
