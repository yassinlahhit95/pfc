-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-01-2026 a las 09:16:28
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`idAsignatura`, `nombreAsignatura`) VALUES
(1, 'Programación'),
(2, 'Bases de Datos'),
(3, 'Sistemas Informáticos'),
(4, 'Redes Local'),
(5, 'Seguridad'),
(6, 'Servicios Red'),
(7, 'Sistemas Operativos'),
(8, 'FOL'),
(9, 'Empresa'),
(10, 'Interfaces');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `idAula` int(11) NOT NULL,
  `nombreAula` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`idAula`, `nombreAula`) VALUES
(1, 'A1'),
(2, 'A2'),
(3, 'B1'),
(4, 'B2'),
(5, 'C1'),
(6, 'C2');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`idCurso`, `nombreCurso`, `descripcionCurso`, `idNivel`, `idProfesor`, `idAula`, `idEstado`) VALUES
(1, '1º DAW', 'Web Superior', 1, 1, 1, 1),
(2, '2º DAW', 'Web Superior', 1, 2, 2, 1),
(3, '1º ASIR', 'Sistemas Superior', 1, 3, 3, 1),
(4, '2º ASIR', 'Sistemas Superior', 1, 4, 4, 1),
(5, '1º SMR', 'Sistemas Medio', 2, 5, 5, 1),
(6, '2º SMR', 'Sistemas Medio', 2, 6, 6, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `directores`
--

INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `ciudadDirector`, `codigoPostalDirector`, `direccionDirector`, `telefonoDirector`, `dniDirector`, `fechaAltaDirector`, `idEstado`) VALUES
(1, 'Pedro Almodóvar', 'pedro@pfc.es', 'Madrid', 28001, 'Calle del Cine 1', 600123456, '99999999Z', '2024-01-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `idEstado` int(11) NOT NULL,
  `nombreEstado` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `nivelEstudiante` varchar(10) NOT NULL,
  `observacionesEstudiante` text NOT NULL,
  `idCurso` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `telefonoEstudiante`, `fechaNacimientoEstudiante`, `dniEstudiante`, `fechaAltaEstudiante`, `direccionEstudiante`, `ciudadEstudiante`, `codigoPostalEstudiante`, `nivelEstudiante`, `observacionesEstudiante`, `idCurso`, `idEstado`) VALUES
(1, 'Rosalía Vila', 'rosalia@music.es', '611001', '1992-09-25', '11111111R', '2023-09-01', 'Calle Motomami', 'Barcelona', 8001, 'GS', '', 1, 1),
(2, 'Enrique Iglesias', 'enrique@music.es', '611002', '1975-05-08', '22222222E', '2023-09-01', 'Av. Miami', 'Madrid', 28001, 'GS', '', 1, 1),
(3, 'David Bisbal', 'david@music.es', '611003', '1979-06-05', '44444444D', '2023-09-01', 'Calle Buleria', 'Almeria', 4001, 'GM', '', 5, 1),
(4, 'Aitana Ocaña', 'aitana@pop.es', '611004', '1999-06-27', '55555555T', '2023-09-01', 'Calle Mariposas', 'Barcelona', 8002, 'GS', '', 2, 1),
(5, 'Lola Indigo', 'lola@dance.es', '611005', '1992-04-01', '66666666L', '2023-09-01', 'Calle Baile', 'Madrid', 28010, 'GS', '', 2, 1),
(6, 'C. Tangana', 'pucho@madrid.es', '611006', '1990-07-16', '77777777C', '2023-09-01', 'Calle Yate', 'Madrid', 28002, 'GS', '', 3, 1),
(7, 'Ursula Corbero', 'tokio@lcdp.es', '611007', '1989-08-11', '88888888U', '2023-09-01', 'Calle Atraco', 'Barcelona', 8005, 'GS', '', 3, 1),
(8, 'Ester Exposito', 'ester@elite.es', '611008', '2000-01-26', '00000000E', '2023-09-01', 'Calle Elite', 'Madrid', 28020, 'GS', '', 4, 1),
(9, 'Ibai Llanos', 'ibai@twitch.es', '611009', '1995-03-26', '12121212I', '2023-09-01', 'Calle Stream', 'Bilbao', 48001, 'GS', 'Vizcaya', 3, 1),
(10, 'Lamine Yamal', 'lamine@barca.es', '611010', '2007-07-13', '20202020L', '2023-09-01', 'Calle Rocafonda', 'Mataro', 8301, 'GM', '', 6, 1),
(11, 'Kepa Arrizabalaga', 'kepa@vizcaya.es', '699001', '1994-10-03', '48000001K', '2023-09-01', 'Calle Ondarroa', 'Bilbao', 48001, 'GS', 'Vizcaya', 1, 1),
(12, 'Amaia Romero', 'amaia@music.es', '699002', '1999-01-03', '48000002A', '2023-09-01', 'Av. Guggenheim', 'Bilbao', 48002, 'GS', 'Vizcaya', 2, 1),
(13, 'Inaki Williams', 'inaki@athletic.es', '699003', '1994-06-15', '48000003I', '2023-09-01', 'San Mames 9', 'Bilbao', 48003, 'GS', 'Vizcaya', 3, 1),
(14, 'Anne Igartiburu', 'anne@tv.es', '699004', '1969-02-18', '48000004N', '2023-09-01', 'Calle Elorrio', 'Bilbao', 48004, 'GS', 'Vizcaya', 4, 1),
(15, 'Alex de la Iglesia', 'alex@cine.es', '699005', '1965-12-04', '48000005D', '2023-09-01', 'Calle Director', 'Bilbao', 48005, 'GS', 'Vizcaya', 4, 1),
(16, 'Fito Cabrales', 'fito@rock.es', '699006', '1966-10-06', '48000006F', '2023-09-01', 'Calle Fitipaldi', 'Bilbao', 48008, 'GM', 'Vizcaya', 5, 1),
(17, 'Unai Simon', 'unai@portero.es', '699007', '1997-06-11', '48000007U', '2023-09-01', 'Calle Murgia', 'Bilbao', 48010, 'GM', 'Vizcaya', 6, 1),
(18, 'Nerea Garmendia', 'nerea@actriz.es', '699008', '1979-10-29', '48000008G', '2023-09-01', 'Av. Euskadi', 'Bilbao', 48011, 'GS', 'Vizcaya', 2, 1),
(19, 'Dani Garcia', 'dani@cocina.es', '699009', '1975-12-30', '48000009D', '2023-09-01', 'Calle Chef', 'Bilbao', 48012, 'GS', 'Vizcaya', 1, 1),
(20, 'Aritz Aduriz', 'aritz@gol.es', '699010', '1981-02-11', '48000010A', '2023-09-01', 'Calle Leyenda', 'Bilbao', 48001, 'GM', 'Vizcaya', 6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `idNivel` int(11) NOT NULL,
  `nombreNivel` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'Grado Superior'),
(2, 'Grado Medio');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `fechaAltaProfesor`, `correoProfesor`, `telefonoProfesor`, `dniProfesor`, `direccionProfesor`, `ciudadProfesor`, `codigoPostalProfesor`, `idEstado`) VALUES
(1, 'Antonio Banderas', '2023-01-01', 'antonio@edu.es', '600001', '12345678A', 'Calle Cine 1', 'Málaga', 29001, 1),
(2, 'Penélope Cruz', '2023-01-01', 'penelope@edu.es', '600002', '22345678B', 'Av. Alcobendas', 'Madrid', 28001, 1),
(3, 'Javier Bardem', '2023-01-01', 'bardem@edu.es', '600003', '32345678C', 'Calle Oscar', 'Madrid', 28002, 1),
(4, 'Alejandro Sanz', '2023-01-01', 'alejandro@edu.es', '600004', '42345678D', 'Calle Corazon', 'Madrid', 28005, 1),
(5, 'Paz Vega', '2023-01-01', 'paz@edu.es', '600005', '52345678E', 'Calle Sevilla', 'Sevilla', 41001, 1),
(6, 'Luis Tosar', '2023-01-01', 'luis@edu.es', '600006', '62345678F', 'Av. Galicia', 'Lugo', 27001, 1),
(7, 'Najwa Nimri', '2023-01-01', 'najwa@edu.es', '600007', '72345678G', 'Calle Vis a Vis', 'Pamplona', 31001, 1),
(8, 'Mario Casas', '2023-01-01', 'mario@edu.es', '600008', '82345678H', 'Calle 3MSC', 'A Coruña', 15001, 1),
(9, 'Blanca Suárez', '2023-01-01', 'blanca@edu.es', '600009', '92345678I', 'Calle Internado', 'Madrid', 28001, 1),
(10, 'Rossy de Palma', '2023-01-01', 'rossy@edu.es', '600010', '02345678J', 'Calle Palma', 'Palma', 7001, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `profesor_asignatura_curso`
--

INSERT INTO `profesor_asignatura_curso` (`idprofesoresAsignaturasCursos`, `idProfesor`, `idAsignatura`, `idCurso`, `idAula`) VALUES
(1, 1, 1, 1, 1),
(2, 2, 2, 2, 2),
(3, 3, 3, 3, 3),
(4, 4, 4, 5, 5),
(5, 5, 5, 6, 6);

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
  ADD PRIMARY KEY (`idCurso`);

--
-- Indices de la tabla `directores`
--
ALTER TABLE `directores`
  ADD PRIMARY KEY (`idDirector`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`idEstado`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`idEstudiante`);

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
-- Indices de la tabla `profesor_asignatura_curso`
--
ALTER TABLE `profesor_asignatura_curso`
  ADD PRIMARY KEY (`idprofesoresAsignaturasCursos`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `idAsignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `idAula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `idCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `idDirector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `idNivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `idProfesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `profesor_asignatura_curso`
--
ALTER TABLE `profesor_asignatura_curso`
  MODIFY `idprofesoresAsignaturasCursos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
