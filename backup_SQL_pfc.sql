-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-01-2026 a las 00:02:19
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
(1, '1º DAW', 'Desarrollo de Aplicaciones Web (DAW)', 1, 1, 1, 1),
(2, '2º DAW', 'Desarrollo de Aplicaciones Web (DAW)', 1, 2, 1, 1),
(3, '1º ASIR', 'Administración de Sistemas Informáticos en Red (ASIR)', 1, 3, 2, 2),
(4, '2º ASIR', 'Administración de Sistemas Informáticos en Red (ASIR)', 1, 4, 3, 2),
(5, '1º GA', 'Gestión Administrativa (GA)', 2, 5, 4, 3),
(6, '2º GA', 'Gestión Administrativa (GA)', 2, 6, 5, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `idDirector` int(11) NOT NULL,
  `nombreDirector` varchar(250) NOT NULL,
  `emailDirector` varchar(250) NOT NULL,
  `telefonoDirector` int(9) NOT NULL,
  `fechaAltaDirector` date NOT NULL,
  `idEstado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `directores`
--

INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `telefonoDirector`, `fechaAltaDirector`, `idEstado`) VALUES
(1, 'Pedro Sánchez', 'pedro.sanchez@gob.es', 688111111, '2019-01-15', 1),
(2, 'Mariano Rajoy', 'Mariano Rajoy@gob.es', 1122334, '2011-12-21', 3),
(3, 'José Luis Rodríguez Zapatero', 'jl.zapatero@gob.es', 900000000, '2004-04-17', 2);

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
  `telefonoEstudiante` text NOT NULL,
  `fechaAltaEstudiante` date NOT NULL,
  `direccionEstudiante` varchar(250) NOT NULL,
  `fechaNacimientoEstudiante` date NOT NULL,
  `nivelEstudiante` varchar(10) NOT NULL COMMENT 'nivel de Estudio',
  `idCurso` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `telefonoEstudiante`, `fechaAltaEstudiante`, `direccionEstudiante`, `fechaNacimientoEstudiante`, `nivelEstudiante`, `idCurso`, `idEstado`) VALUES
(1, 'Shakira Mebarak', 'shakira@example.com', '+34 612 345 678', '2023-09-12', 'Calle Famosa 12, Barcelona', '1977-02-02', 'avanzado', 2, 1),
(2, 'Enrique Iglesias', 'enrique@example.com', '+34 698 234 567', '2022-05-20', 'Avenida Estrella 45, Madrid', '1975-05-08', 'avanzado', 5, 1),
(3, 'Penélope Cruz', 'penelope.cruz@example.com', '+34 622 987 654', '2024-01-10', 'Calle del Cine 3, Madrid', '1974-04-28', 'medio', 1, 2),
(4, 'Antonio Banderas', 'antonio.banderas@example.com', '+34 611 223 334', '2021-12-05', 'Plaza de la Actuación 7, Málaga', '1960-08-10', 'medio', 6, 2),
(5, 'Ricky Martin', 'ricky.martin@example.com', '+34 699 556 778', '2023-07-22', 'Calle del Ritmo 19, Madrid', '1971-12-24', 'avanzado', 3, 2),
(6, 'Salma Hayek', 'salma.hayek@example.com', '+34 677 889 990', '2022-11-11', 'Avenida del Estudio 8, Veracruz', '1966-09-02', 'medio', 4, 2),
(7, 'Javier Bardem', 'javier.bardem@example.com', '+34 633 112 233', '2023-04-18', 'Calle Estrella 27, Las Palmas', '1969-03-01', 'básico', 3, 1),
(8, 'Rosalía Vila', 'rosalia@example.com', '+34 688 445 556', '2024-03-15', 'Plaza Flamenco 6, Barcelona', '1993-09-25', 'medio', 1, 1),
(9, 'Pablo Alborán', 'pablo.alboran@example.com', '+34 622 334 445', '2022-08-30', 'Calle del Arte 14, Málaga', '1989-05-31', 'medio', 4, 2),
(10, 'David Bisbal', 'david.bisbal@example.com', '+34 699 778 889', '2023-10-01', 'Avenida Musical 21, Almería', '1979-06-05', 'básico', 2, 1);

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
(1, 'grado superio'),
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
  `telefonoProfesor` varchar(9) NOT NULL,
  `direccionProfesor` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `fechaAltaProfesor`, `correoProfesor`, `telefonoProfesor`, `direccionProfesor`) VALUES
(1, 'Pau Casals', '2020-09-01', 'pau.casals@educacion.es', '612345678', 'El Vendrell, España'),
(2, 'Margarita Salas', '2020-09-01', 'margarita.salas@educacion.es', '623456789', 'Asturias, España'),
(3, 'Eduardo Chillida', '2020-09-01', 'eduardo.chillida@educacion.es', '634567890', 'Hernani, País Vasco'),
(4, 'Juan Ignacio Cirac', '2020-09-01', 'juan.cirac@educacion.es', '645678901', 'Manresa, España'),
(5, 'Cristóbal Balenciaga', '2020-09-01', 'balenciaga@educacion.es', '656789012', 'Getaria, País Vasco'),
(6, 'Ramón y Cajal', '2020-09-01', 'ramon.cajal@educacion.es', '667890123', 'Navarra, España');

--
-- Índices para tablas volcadas
--

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
  ADD KEY `fk_cursos_aulas` (`idAula`),
  ADD KEY `fk_cursos_estados` (`idEstado`);

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
  ADD KEY `fk_curso` (`idCurso`),
  ADD KEY `fk_estados` (`idEstado`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`idNivel`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`idProfesor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

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
  MODIFY `idDirector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `idNivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `idProfesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_aulas` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_profesores` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `directores`
--
ALTER TABLE `directores`
  ADD CONSTRAINT `fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_curso` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`idCurso`),
  ADD CONSTRAINT `fk_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
