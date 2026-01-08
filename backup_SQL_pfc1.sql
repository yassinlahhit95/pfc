-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-01-2026 a las 16:42:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pfc`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `idAsignatura` int(11) NOT NULL,
  `nombreAsignatura` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`idAsignatura`, `nombreAsignatura`) VALUES
(1, 'Programación'),
(2, 'Bases de Datos'),
(3, 'Sistemas Informáticos'),
(4, 'Despliegue de Aplicaciones Web'),
(5, 'Administración de Sistemas Operativos'),
(6, 'Gestión Documental'),
(7, 'Inglés Técnico'),
(8, 'Formación y Orientación Laboral'),
(9, 'Desarrollo Web en Entorno Cliente'),
(10, 'Desارrollo Web en Entorno Servidor'),
(11, 'Diseño de Interfaces Web'),
(12, 'Seguridad y Alta Disponibilidad'),
(13, 'Servicios de Red e Internet'),
(14, 'Comunicación Empresarial'),
(15, 'Operaciones de Compraventa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `idAula` int(11) NOT NULL,
  `nombreAula` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`idAula`, `nombreAula`) VALUES
(1, 'A1'),
(2, 'A2'),
(3, 'B1'),
(4, 'B2'),
(5, 'C1'),
(6, 'C2'),
(7, 'D1'),
(8, 'D2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `idCurso` int(11) NOT NULL,
  `nombreCurso` varchar(50) NOT NULL,
  `descripcionCurso` varchar(100) NOT NULL,
  `idNivel` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `idAula` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`idCurso`, `nombreCurso`, `descripcionCurso`, `idNivel`, `idProfesor`, `idAula`, `idEstado`) VALUES
(1, '1º DAW', 'Desarrollo de Aplicaciones Web (DAW)', 1, NULL, 1, 1),
(2, '2º DAW', 'Desarrollo de Aplicaciones Web (DAW)', 1, NULL, 1, 1),
(3, '1º ASIR', 'Administración de Sistemas Informáticos en Red (ASIR)', 1, NULL, 2, 2),
(4, '2º ASIR', 'Administración de Sistemas Informáticos en Red (ASIR)', 1, NULL, 3, 2),
(5, '1º GA', 'Gestión Administrativa (GA)', 2, NULL, 4, 3),
(6, '2º GA', 'Gestión Administrativa (GA)', 2, NULL, 5, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `idDirector` int(11) NOT NULL,
  `nombreDirector` varchar(250) NOT NULL,
  `emailDirector` varchar(250) NOT NULL,
  `ciudadDirector` varchar(100) NOT NULL,
  `codigoPostalDirector` int(10) NOT NULL,
  `direccionDirector` varchar(250) NOT NULL,
  `telefonoDirector` int(9) NOT NULL,
  `dniDirector` varchar(20) NOT NULL,
  `fechaAltaDirector` date NOT NULL,
  `idEstado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `idEstado` int(11) NOT NULL,
  `nombreEstado` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`idEstado`, `nombreEstado`) VALUES
(1, 'activo'),
(2, 'inactivo'),
(3, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `idEstudiante` int(11) NOT NULL,
  `nombreEstudiante` varchar(250) NOT NULL,
  `emailEstudiante` varchar(250) NOT NULL,
  `telefonoEstudiante` varchar(20) NOT NULL,
  `fechaNacimientoEstudiante` date NOT NULL,
  `dniEstudiante` varchar(20) NOT NULL,
  `fechaAltaEstudiante` date NOT NULL,
  `direccionEstudiante` varchar(250) NOT NULL,
  `ciudadEstudiante` varchar(100) NOT NULL,
  `codigoPostalEstudiante` int(10) NOT NULL,
  `nivelEstudiante` varchar(10) NOT NULL COMMENT 'nivel de Estudio',
  `observacionesEstudiante` text NOT NULL,
  `idCurso` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `idNivel` int(11) NOT NULL,
  `nombreNivel` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'grado superior'),
(2, 'grado medio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `idProfesor` int(11) NOT NULL,
  `nombreProfesor` varchar(250) NOT NULL,
  `fechaAltaProfesor` date NOT NULL,
  `correoProfesor` varchar(250) NOT NULL,
  `telefonoProfesor` varchar(20) NOT NULL,
  `dniProfesor` varchar(20) NOT NULL,
  `direccionProfesor` varchar(250) NOT NULL,
  `ciudadProfesor` varchar(100) NOT NULL,
  `codigoPostalProfesor` int(10) NOT NULL,
  `idEstado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_asignatura_curso`
--

CREATE TABLE `profesor_asignatura_curso` (
  `idprofesoresAsignaturasCursos` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `idAsignatura` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idAula` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`idAsignatura`);

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`idAula`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`idCurso`),
  ADD KEY `fk_cursos_niveles` (`idNivel`),
  ADD KEY `fk_cursos_profesores` (`idProfesor`),
  ADD KEY `fk_cursos_estados` (`idEstado`),
  ADD KEY `fk_cursos_aulas` (`idAula`);

--
-- Indices de la tabla `directores`
--
ALTER TABLE `directores`
  ADD PRIMARY KEY (`idDirector`),
  ADD KEY `fk_estado` (`idEstado`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`idEstado`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`idEstudiante`),
  ADD KEY `fk_estados` (`idEstado`),
  ADD KEY `fk_curso` (`idCurso`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`idNivel`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`idProfesor`),
  ADD KEY `fk_profesores_estados` (`idEstado`);

--
-- Indices de la tabla `profesor_asignatura_curso`
--
ALTER TABLE `profesor_asignatura_curso`
  ADD PRIMARY KEY (`idprofesoresAsignaturasCursos`),
  ADD KEY `idProfesor` (`idProfesor`),
  ADD KEY `idAsignatura` (`idAsignatura`),
  ADD KEY `idCurso` (`idCurso`),
  ADD KEY `idAula` (`idAula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `idAsignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `idAula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `idCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `idDirector` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `idNivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `idProfesor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesor_asignatura_curso`
--
ALTER TABLE `profesor_asignatura_curso`
  MODIFY `idprofesoresAsignaturasCursos` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_aulas` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_profesores` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `directores`
--
ALTER TABLE `directores`
  ADD CONSTRAINT `fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_curso` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`idCurso`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `fk_profesores_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesor_asignatura_curso`
--
ALTER TABLE `profesor_asignatura_curso`
  ADD CONSTRAINT `profesor_asignatura_curso_ibfk_1` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`),
  ADD CONSTRAINT `profesor_asignatura_curso_ibfk_2` FOREIGN KEY (`idAsignatura`) REFERENCES `asignaturas` (`idAsignatura`),
  ADD CONSTRAINT `profesor_asignatura_curso_ibfk_3` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`idCurso`),
  ADD CONSTRAINT `profesor_asignatura_curso_ibfk_4` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
