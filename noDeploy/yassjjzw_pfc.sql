-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 29-07-2026 a las 09:53:40
-- Versión del servidor: 11.4.12-MariaDB-cll-lve-log
-- Versión de PHP: 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `yassjjzw_pfc`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_config`
--

CREATE TABLE `academic_config` (
  `idConfig` int(11) NOT NULL,
  `idCentro` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL DEFAULT 'Configuración académica',
  `anioAcademico` varchar(9) DEFAULT NULL,
  `tipoEducacion` enum('grado_basico','grado_medio','grado_superior','colegio','otro') NOT NULL DEFAULT 'otro',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creadoEn` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizadoEn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academic_config`
--

INSERT INTO `academic_config` (`idConfig`, `idCentro`, `nombre`, `anioAcademico`, `tipoEducacion`, `activo`, `creadoEn`, `actualizadoEn`) VALUES
(1, NULL, 'Configuración heredada (auto-generada)', NULL, 'otro', 1, '2026-07-21 16:23:31', '2026-07-21 16:23:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_periods`
--

CREATE TABLE `academic_periods` (
  `idPeriodo` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `tipo` enum('evaluacion','recuperacion','ordinaria','extraordinaria','final','proyecto','practicas','certificacion','otro') NOT NULL DEFAULT 'evaluacion',
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `peso` decimal(5,2) NOT NULL DEFAULT 100.00,
  `idPeriodoRecuperaDe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academic_periods`
--

INSERT INTO `academic_periods` (`idPeriodo`, `idConfig`, `nombre`, `tipo`, `fechaInicio`, `fechaFin`, `orden`, `visible`, `bloqueado`, `peso`, `idPeriodoRecuperaDe`) VALUES
(1, 1, '1ª Evaluación', 'evaluacion', NULL, NULL, 1, 1, 0, 100.00, NULL),
(2, 1, '2ª Evaluación', 'evaluacion', NULL, NULL, 3, 1, 0, 100.00, NULL),
(3, 1, 'recuperación 1ª Evaluación', 'recuperacion', NULL, NULL, 2, 1, 0, 100.00, 1),
(4, 1, 'recuperación 2ª Evaluación', 'recuperacion', NULL, NULL, 4, 1, 0, 100.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_templates`
--

CREATE TABLE `academic_templates` (
  `idPlantilla` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `configuracionJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`configuracionJson`)),
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `creadoEn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academic_templates`
--

INSERT INTO `academic_templates` (`idPlantilla`, `nombre`, `descripcion`, `configuracionJson`, `editable`, `creadoEn`) VALUES
(1, 'Estándar FP Grado Medio', 'Configuración de partida para ciclos de Grado Medio: 2 evaluaciones + recuperación, examen 75% / reto 25%, aprobado 5.', '{\"config\": {\"activo\": 1, \"nombre\": \"Configuración heredada (auto-generada)\", \"creadoEn\": \"2026-07-21 18:23:31\", \"idCentro\": null, \"idConfig\": 1, \"actualizadoEn\": \"2026-07-21 18:23:31\", \"anioAcademico\": null, \"tipoEducacion\": \"grado_medio\"}, \"periods\": [{\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 1, \"nombre\": \"1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 1, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 2, \"nombre\": \"recuperación 1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 3, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 1}, {\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 3, \"nombre\": \"2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 2, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 4, \"nombre\": \"recuperación 2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 4, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 2}], \"tfg_config\": {\"idConfig\": 1, \"habilitado\": 1, \"notaMinima\": \"5.00\", \"idConfigTFG\": 1, \"pesoEnMedia\": \"1.00\", \"requiereComite\": 0, \"requiereDefensa\": 0, \"permiteRecuperacion\": 1}, \"grading_policy\": {\"idConfig\": 1, \"decimales\": 2, \"escalaMax\": \"10.00\", \"escalaMin\": \"0.00\", \"idPolitica\": 1, \"notaAprobado\": \"5.00\", \"pesoTfgEnMedia\": \"1.00\"}, \"promotion_rule\": {\"idRegla\": 1, \"idConfig\": 1, \"notaMinimaGlobal\": \"5.00\", \"requiereTodosModulos\": 1, \"permiteModulosPendientes\": 0}, \"assessment_types\": [{\"peso\": \"3.00\", \"orden\": 1, \"idTipo\": 1, \"nombre\": \"Examen\", \"origen\": \"examen\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 1, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}, {\"peso\": \"1.00\", \"orden\": 2, \"idTipo\": 2, \"nombre\": \"Reto\", \"origen\": \"reto\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 0, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}], \"challenge_config\": {\"idConfig\": 1, \"pesoDefecto\": \"1.00\", \"idConfigReto\": 1, \"permiteFases\": 0, \"permiteGrupal\": 0, \"evaluacionPares\": 0, \"requiereRubrica\": 0}, \"internship_config\": {\"idConfig\": 1, \"habilitado\": 0, \"idConfigFCT\": 1, \"pesoEnMedia\": \"0.00\", \"metodoEvaluacion\": \"ambos\", \"horasRequeridasDefecto\": 0, \"requiereAprobarParaTitular\": 1}}', 1, '2026-07-21 16:23:31'),
(2, 'Estándar FP Grado Superior', 'Configuración de partida para ciclos de Grado Superior: misma estructura que Grado Medio, totalmente editable tras aplicarla.', '{\"config\": {\"activo\": 1, \"nombre\": \"Configuración heredada (auto-generada)\", \"creadoEn\": \"2026-07-21 18:23:31\", \"idCentro\": null, \"idConfig\": 1, \"actualizadoEn\": \"2026-07-21 18:23:31\", \"anioAcademico\": null, \"tipoEducacion\": \"grado_superior\"}, \"periods\": [{\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 1, \"nombre\": \"1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 1, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 2, \"nombre\": \"recuperación 1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 3, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 1}, {\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 3, \"nombre\": \"2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 2, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 4, \"nombre\": \"recuperación 2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 4, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 2}], \"tfg_config\": {\"idConfig\": 1, \"habilitado\": 1, \"notaMinima\": \"5.00\", \"idConfigTFG\": 1, \"pesoEnMedia\": \"1.00\", \"requiereComite\": 0, \"requiereDefensa\": 0, \"permiteRecuperacion\": 1}, \"grading_policy\": {\"idConfig\": 1, \"decimales\": 2, \"escalaMax\": \"10.00\", \"escalaMin\": \"0.00\", \"idPolitica\": 1, \"notaAprobado\": \"5.00\", \"pesoTfgEnMedia\": \"1.00\"}, \"promotion_rule\": {\"idRegla\": 1, \"idConfig\": 1, \"notaMinimaGlobal\": \"5.00\", \"requiereTodosModulos\": 1, \"permiteModulosPendientes\": 0}, \"assessment_types\": [{\"peso\": \"3.00\", \"orden\": 1, \"idTipo\": 1, \"nombre\": \"Examen\", \"origen\": \"examen\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 1, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}, {\"peso\": \"1.00\", \"orden\": 2, \"idTipo\": 2, \"nombre\": \"Reto\", \"origen\": \"reto\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 0, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}], \"challenge_config\": {\"idConfig\": 1, \"pesoDefecto\": \"1.00\", \"idConfigReto\": 1, \"permiteFases\": 0, \"permiteGrupal\": 0, \"evaluacionPares\": 0, \"requiereRubrica\": 0}, \"internship_config\": {\"idConfig\": 1, \"habilitado\": 0, \"idConfigFCT\": 1, \"pesoEnMedia\": \"0.00\", \"metodoEvaluacion\": \"ambos\", \"horasRequeridasDefecto\": 0, \"requiereAprobarParaTitular\": 1}}', 1, '2026-07-21 16:23:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `account_lockout`
--

CREATE TABLE `account_lockout` (
  `email` varchar(190) NOT NULL,
  `intentos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `window_start` int(10) UNSIGNED NOT NULL,
  `locked_until` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `account_lockout`
--

INSERT INTO `account_lockout` (`email`, `intentos`, `window_start`, `locked_until`) VALUES
('laura@aulapro.com', 4, 1784840639, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `idAnuncio` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaAnuncio` datetime DEFAULT current_timestamp(),
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores','tutores') DEFAULT 'todos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`idAnuncio`, `titulo`, `mensaje`, `fechaAnuncio`, `fechaExpiracion`, `dirigidoA`) VALUES
(1, 'Bienvenida al año Académico 2026/2027', 'Les damos la mís cordial bienvenida a todos los estudiantes y profesores a este nuevo año académico. Las clases comienzan el 15 de Septiembre a las 8:30.', '2026-07-27 17:36:17', '2026-10-31', 'todos'),
(2, 'Entrega de Proyectos TFG', 'Se recuerda a los estudiantes de 2┬║ año que el plazo m├íximo para la subida del TFG y su documentación al Aula Virtual es el 15 de Junio.', '2026-07-27 17:36:17', '2027-06-15', 'estudiantes'),
(3, 'Reunión Extraordinaria de Claustro', 'Estimados docentes, se convoca una reunión extraordinaria de claustro para tratar las nuevas normativas de FP Dual el lunes 2 de Agosto a las 16:30.', '2026-07-27 17:36:17', '2026-08-03', 'profesores');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('estudiante','profesor','director','tutor','secretaria') NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` char(64) NOT NULL,
  `device_info` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `user_type`, `user_id`, `token`, `device_info`, `created_at`, `expires_at`, `last_used_at`) VALUES
(1, 'director', 1, '99c0bc6f50e5dca133a3f2df5f06ea2d250bf6fa9b299e3d6564f29adbfe3ecf', 'Android', '2026-07-23 18:22:43', '2026-08-22 18:22:43', '2026-07-23 18:22:44'),
(4, 'director', 1, 'dc86f9a8d7466b44afbfbd53d8c3fbf0a4e13c678174f5f9c690d9e0e9a4ff68', '', '2026-07-23 18:28:47', '2026-08-22 18:28:47', '2026-07-23 18:28:47'),
(5, 'tutor', 1, 'aa695151610220e0b5007a378c70bf384f356319c31ff2db5e07f0a41630fd2b', '', '2026-07-23 18:28:47', '2026-08-22 18:28:47', '2026-07-23 18:28:47'),
(8, 'director', 1, '1ee7f54e45391d4e4d55a24b407186163b0c5992c94104e6eaa248902a4397cc', '', '2026-07-23 18:40:17', '2026-08-22 18:40:17', '2026-07-23 18:40:17'),
(9, 'profesor', 1, '3cac4f6184ac1cba8625e6db804537b72458220f6a530bc420b2cc565da72952', '', '2026-07-23 18:40:18', '2026-08-22 18:40:18', '2026-07-23 18:40:18'),
(10, 'director', 1, '898c18e2fbe260264cb849b4494346a0caf42a09da936cc766ae03bf48e38740', 'Android', '2026-07-23 18:47:32', '2026-08-22 18:47:32', '2026-07-23 18:51:01'),
(15, 'tutor', 1, 'd1c0f552e4dd21f3aa0fd127e694f96dfa549e026b5b1bbfe7b670704d1efa05', '', '2026-07-23 19:01:36', '2026-08-22 19:01:36', '2026-07-23 19:01:36'),
(16, 'profesor', 1, '70085cf3d8a136404f6340782aa09ea9afa951c6774304ac97de7c295d181e30', '', '2026-07-23 19:01:36', '2026-08-22 19:01:36', '2026-07-23 19:01:36'),
(17, 'tutor', 1, '3a8517515f692950e33968dde3a6e01b7b485cabbedfda37883b9e5d3dbcce38', '', '2026-07-23 19:02:14', '2026-08-22 19:02:14', '2026-07-23 19:02:15'),
(18, 'estudiante', 3, '9714c7dfc31587e28c5cdbad742cb1c027766ec58b402026923cf0526ce29f6b', '', '2026-07-23 19:02:28', '2026-08-22 19:02:28', '2026-07-23 19:02:28'),
(19, 'profesor', 1, '913d092defb4c92e11781ce80a9eacada0daa9f1804f681d57aca6d599cb1119', '', '2026-07-23 19:02:28', '2026-08-22 19:02:28', '2026-07-23 19:02:28'),
(20, 'director', 1, 'da14ae3b693c83e522ab2b9f911003a6de41e29aa0117b202fbec1f5aab91936', '', '2026-07-23 19:02:29', '2026-08-22 19:02:29', '2026-07-23 19:02:29'),
(21, 'profesor', 1, 'f609270be2c3487fcea8867ec1b8f5c21ab548bbb2cc31224a989998c9d7486a', '', '2026-07-23 19:04:22', '2026-08-22 19:04:22', '2026-07-23 19:04:22'),
(22, 'director', 1, 'a0293feccb6e81f27f90c1afea03dbc5ef376c50daf2f7b92f6511898226bd70', '', '2026-07-23 19:08:49', '2026-08-22 19:08:49', '2026-07-23 19:08:49'),
(23, 'director', 1, 'd66db94ef0c5dc418147469b89b52e59f9f72d9de8b338e0018cb56196b2c0c8', '', '2026-07-23 19:08:58', '2026-08-22 19:08:58', '2026-07-23 19:08:58'),
(30, 'estudiante', 1, '8a62e25dd6c84fc41a27b90b1b1718e91d82b2380e4f28a4ae7b505940645767', '', '2026-07-23 19:42:23', '2026-08-22 19:42:23', '2026-07-23 19:42:23'),
(31, 'estudiante', 1, '2e19cb79a10cfdfe401b0d195d63a66408de3f68d8049e4b7596d2b6f5722c10', '', '2026-07-23 19:42:24', '2026-08-22 19:42:24', NULL),
(32, 'tutor', 1, '1d579aa1d89579fd3b169e2232d04c3b6f0764dbda3cbbb193cf99e924b4c687', '', '2026-07-23 19:42:34', '2026-08-22 19:42:34', '2026-07-23 19:42:34'),
(35, 'tutor', 1, 'e58c27edc5025289cdebbc70f001a2808bbdfbc57f461c15eddf3a44a9c69ea5', 'Android', '2026-07-23 21:51:16', '2026-08-22 21:51:16', '2026-07-23 21:54:56'),
(36, 'estudiante', 2, '9c9da5c9a13190f93e7a8fe9d105dc4654fcc19a7adaae88c3981e3b8f45c616', '', '2026-07-23 22:05:02', '2026-08-22 22:05:02', '2026-07-23 22:05:02'),
(37, 'estudiante', 2, '5d50c6a368f7545733e1d297f6d938273cec6c1a94c94f56818ffdf7f65dd363', '', '2026-07-23 22:05:20', '2026-08-22 22:05:20', '2026-07-23 22:05:20'),
(38, 'estudiante', 2, '8967cb09781fa7c081c592cae5f1e1521cfacac2cc7338a6626dcb45103f5187', '', '2026-07-23 22:05:50', '2026-08-22 22:05:50', '2026-07-23 22:05:50'),
(39, 'estudiante', 2, '4fdffd02e34612eec1c645813d0ae08dc85ff7ff339044c2db19b5ad3079d6f0', '', '2026-07-23 22:06:14', '2026-08-22 22:06:14', '2026-07-23 22:06:15'),
(40, 'tutor', 2, '339443d9008100a8599d3159339ea32de030dab0a83f984ed2bb0e54f3fdaa7a', '', '2026-07-23 22:06:33', '2026-08-22 22:06:33', '2026-07-23 22:06:33'),
(41, 'profesor', 2, 'f71d43bdfffb3e5a674a2d8855679fc4abaadbe5cbd586ab6b1a50d1f2b61052', '', '2026-07-23 22:06:33', '2026-08-22 22:06:33', '2026-07-23 22:06:33'),
(42, 'profesor', 1, '0dd02b69b57f5edd5ece99aca6fcd02fb5f786c605d59883221cda14c6c32011', 'Android', '2026-07-23 22:20:52', '2026-08-22 22:20:52', '2026-07-23 23:43:26'),
(43, 'secretaria', 1, '7cc5f823db354da192ffd759dbbec6ceaa1a3b98f0eab43609418864e0629b3b', '', '2026-07-23 23:03:59', '2026-08-22 23:03:59', '2026-07-23 23:04:40'),
(44, 'director', 1, '373c27b3f1c28e5bde7f061c5791cb9eeae7320e86b0ce0b34d6897a1ca00fef', '', '2026-07-23 23:04:24', '2026-08-22 23:04:24', '2026-07-23 23:06:28'),
(45, 'profesor', 2, 'c7face573e51e73706cb85a691089332b2fe7be9b40b8474a30641ffe4d95eb4', '', '2026-07-23 23:04:24', '2026-08-22 23:04:24', '2026-07-23 23:04:59'),
(46, 'estudiante', 2, 'd9d9e2ebf85ee7ae31241528f18be2495cc64da33c74489eb0902f6ab2f4bee5', '', '2026-07-23 23:04:24', '2026-08-22 23:04:24', '2026-07-23 23:06:28'),
(47, 'tutor', 1, '76ae15b721b71e9b2028733897bd71a15e1915860946ec647c195a4fad643dfd', '', '2026-07-23 23:04:24', '2026-08-22 23:04:24', '2026-07-23 23:04:42'),
(48, 'director', 1, '911675bec53ba63c8cbad1f7e9eba5f58aaa48359eaffbe76566f0e38c1e66f6', '', '2026-07-24 01:01:40', '2026-08-23 01:01:40', '2026-07-24 01:01:49'),
(49, 'director', 1, '28a50fe39e1339bedabfbf5a90db2322647618582516a1d4fb766d0480eee3eb', 'Android', '2026-07-24 01:08:19', '2026-08-23 01:08:19', '2026-07-24 01:09:50'),
(50, 'estudiante', 2, '34ebbfb5071a035a688c1aefe35ebe00c7813bf88429a20ee62fed79df6e2a1d', 'Android', '2026-07-24 02:45:40', '2026-08-23 02:45:40', '2026-07-24 03:04:44'),
(51, 'director', 1, '7a29b0c91c24d426687d50ad53171691528b3edbbe22f7ca0879508d2d7f9350', 'claude-audit', '2026-07-24 19:27:18', '2026-08-23 19:27:18', '2026-07-24 19:31:38'),
(52, 'estudiante', 1, 'd9fae6d8e172f463a8c3eee6480aaa2fa5663c06b48f956794a5b24787b0fdcd', '', '2026-07-24 19:47:29', '2026-08-23 19:47:29', '2026-07-24 19:47:31'),
(53, 'estudiante', 1, '110d1ed40b131f3cc746af0cef073324c2664bdee9a6f9f74ad24b1a15447ef4', '', '2026-07-24 19:47:46', '2026-08-23 19:47:46', '2026-07-24 19:47:47'),
(54, 'director', 1, 'd66be5fd028fce95051bf0b7a12675a03e158a16b6384e0b9d0d2e31ed4d77f3', '', '2026-07-24 20:40:12', '2026-08-23 20:40:12', '2026-07-24 20:40:12'),
(55, 'director', 1, '1cf55157ac412921e4bf713284ae5a167254e5bd0a51c9c5727eda8c3a2d2a92', '', '2026-07-24 21:00:47', '2026-08-23 21:00:47', '2026-07-24 21:01:03'),
(56, 'profesor', 1, '2b2dcbf16574deb71c653561b20f72bc85a95114ea8b8582c3589300b18790bc', '', '2026-07-24 21:01:20', '2026-08-23 21:01:20', '2026-07-24 21:01:21'),
(57, 'director', 1, '6d3d20ef2b6b6f94366bb0e327d15831b5fc04ff681a42f7e448144d7c1e083d', '', '2026-07-24 21:18:18', '2026-08-23 21:18:18', '2026-07-24 21:18:18'),
(62, 'director', 1, '3b3095912c997e933e2226f6f786e80272ea38080a6b7c7e7d372fe4b1a06018', 'Android', '2026-07-29 04:33:38', '2026-08-28 04:33:38', '2026-07-29 04:36:44'),
(64, 'director', 1, '5611dd47a84b57b7f9f3510b1fbe664f44a300659b8b6cfe6bf137293e090acf', 'Android', '2026-07-29 05:04:06', '2026-08-28 05:04:06', '2026-07-29 05:10:15'),
(65, 'director', 1, 'ae4c3d7b608807db3a6a6cade8c3fb357adac53fdecae48d82ef53cf11801fb7', 'Android', '2026-07-29 05:10:42', '2026-08-28 05:10:42', '2026-07-29 05:35:07'),
(66, 'director', 1, '3ec1bb6eb00b71201fb6a7c495b178f3e3c2dcce89acfdfc6413c011f84733b0', 'Android', '2026-07-29 05:36:20', '2026-08-28 05:36:20', '2026-07-29 06:24:37'),
(67, 'director', 1, '1ed918fd6a5eecbc5612dbae52d543fbe031b0f10e15a1a37ca875b4e6027f4b', 'Android', '2026-07-29 15:12:58', '2026-08-28 15:12:58', '2026-07-29 15:26:27'),
(68, 'director', 1, '2cc7ee7756e3b0bc7623830ba49a49e2fc7ccebed9031fc1da22270e43069bc5', 'Android', '2026-07-29 15:28:38', '2026-08-28 15:28:38', '2026-07-29 15:50:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `idAsistencia` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `rolRegistrador` enum('profesor','secretaria','director') DEFAULT 'profesor',
  `idRegistrador` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','ausente','retraso','justificado') NOT NULL DEFAULT 'presente',
  `observacion` varchar(255) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hora` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`idAsistencia`, `idEstudiante`, `idModulo`, `idProfesor`, `rolRegistrador`, `idRegistrador`, `fecha`, `estado`, `observacion`, `fechaRegistro`, `hora`) VALUES
(1, 1, 3, 1, 'profesor', NULL, '2026-07-26', 'presente', 'Llegó a la hora correcta', '2026-07-27 15:36:17', NULL),
(2, 2, 3, 1, 'profesor', NULL, '2026-07-26', 'retraso', 'Llegó 15 minutos tarde por tr├ífico', '2026-07-27 15:36:17', NULL),
(3, 3, 1, 2, 'profesor', NULL, '2026-07-26', 'presente', NULL, '2026-07-27 15:36:17', NULL),
(4, 4, 1, 2, 'profesor', NULL, '2026-07-26', 'ausente', 'No comunicó la falta', '2026-07-27 15:36:17', NULL),
(5, 1, 4, 1, 'profesor', NULL, '2026-07-27', 'presente', NULL, '2026-07-27 15:36:17', NULL),
(6, 2, 4, 1, 'profesor', NULL, '2026-07-27', 'justificado', 'Tiene justificante de cita médica', '2026-07-27 15:36:17', NULL),
(7, 1, 1, 1, 'profesor', NULL, '2026-07-25', 'ausente', 'Sin justificación', '2026-07-28 13:24:46', NULL),
(8, 1, 2, 2, 'profesor', NULL, '2026-07-22', 'presente', NULL, '2026-07-28 13:24:46', NULL),
(9, 2, 1, 1, 'profesor', NULL, '2026-07-24', 'justificado', 'Cita médica', '2026-07-28 13:24:46', NULL),
(10, 2, 2, 2, 'profesor', NULL, '2026-07-20', 'retraso', 'Autobús con retraso', '2026-07-28 13:24:46', NULL),
(11, 3, 4, 1, 'profesor', NULL, '2026-07-23', 'presente', NULL, '2026-07-28 13:24:46', NULL),
(12, 3, 2, 2, 'profesor', NULL, '2026-07-26', 'ausente', NULL, '2026-07-28 13:24:46', NULL),
(13, 4, 2, 2, 'profesor', NULL, '2026-07-25', 'retraso', 'Tr├ífico', '2026-07-28 13:24:46', NULL),
(14, 4, 3, 1, 'profesor', NULL, '2026-07-22', 'justificado', 'Justificante médico', '2026-07-28 13:24:46', NULL),
(15, 5, 7, 3, 'profesor', NULL, '2026-07-27', 'ausente', 'Sin aviso', '2026-07-28 13:24:46', NULL),
(16, 5, 9, 4, 'profesor', NULL, '2026-07-24', 'presente', NULL, '2026-07-28 13:24:46', NULL),
(17, 5, 10, 2, 'profesor', NULL, '2026-07-21', 'justificado', 'Cita médica', '2026-07-28 13:24:46', NULL),
(18, 6, 7, 3, 'profesor', NULL, '2026-07-26', 'presente', NULL, '2026-07-28 13:24:46', NULL),
(19, 6, 9, 4, 'profesor', NULL, '2026-07-23', 'retraso', 'Llegó tarde', '2026-07-28 13:24:46', NULL),
(20, 6, 10, 2, 'profesor', NULL, '2026-07-19', 'ausente', NULL, '2026-07-28 13:24:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assessment_types`
--

CREATE TABLE `assessment_types` (
  `idTipo` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `notaMaxima` decimal(4,2) NOT NULL DEFAULT 10.00,
  `peso` decimal(6,2) NOT NULL DEFAULT 1.00,
  `aprobadoMinimo` decimal(4,2) DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 0,
  `recuperable` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `editableProfesor` tinyint(1) NOT NULL DEFAULT 1,
  `editableDirector` tinyint(1) NOT NULL DEFAULT 1,
  `incluirEnMedia` tinyint(1) NOT NULL DEFAULT 1,
  `origen` enum('examen','reto','ra_ce','fct','tfg','otro') NOT NULL DEFAULT 'otro',
  `orden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `assessment_types`
--

INSERT INTO `assessment_types` (`idTipo`, `idConfig`, `nombre`, `notaMaxima`, `peso`, `aprobadoMinimo`, `obligatorio`, `recuperable`, `visible`, `editableProfesor`, `editableDirector`, `incluirEnMedia`, `origen`, `orden`) VALUES
(1, 1, 'Examen', 10.00, 3.00, NULL, 1, 1, 1, 1, 1, 1, 'examen', 1),
(2, 1, 'Reto', 10.00, 1.00, NULL, 0, 1, 1, 1, 1, 1, 'reto', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `idAula` int(11) NOT NULL,
  `planta` tinyint(4) NOT NULL,
  `numero` int(11) NOT NULL,
  `codigoAula` varchar(10) GENERATED ALWAYS AS (concat(`planta`,lpad(`numero`,2,_utf8mb4'0'))) STORED,
  `nombreAula` varchar(60) DEFAULT NULL,
  `tipoAula` enum('teoria','laboratorio','taller','otro') NOT NULL DEFAULT 'teoria',
  `capacidad` int(11) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`, `activa`) VALUES
(1, 1, 1, 'Laboratorio Inform├ítica I', 'laboratorio', 25, 1),
(2, 1, 2, 'Laboratorio Inform├ítica II', 'laboratorio', 25, 1),
(3, 2, 1, 'Aula de Teoría 201', 'teoria', 30, 1),
(4, 2, 2, 'Taller de Hardware', 'taller', 20, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_almacenamiento_ciclo`
--

CREATE TABLE `aula_almacenamiento_ciclo` (
  `idCiclo` int(11) NOT NULL,
  `limiteBytes` bigint(20) NOT NULL DEFAULT 5368709120
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_analytics`
--

CREATE TABLE `aula_analytics` (
  `idAnalytics` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('estudiante','profesor') NOT NULL,
  `accion` varchar(50) NOT NULL,
  `idModulo` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `userAgent` text DEFAULT NULL,
  `metadatos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadatos`)),
  `fechaCreacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_archivos`
--

CREATE TABLE `aula_archivos` (
  `idArchivo` int(11) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `tamanio` int(11) DEFAULT 0,
  `descripcion` varchar(500) DEFAULT NULL,
  `idCarpeta` int(11) DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `fijado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_archivos`
--

INSERT INTO `aula_archivos` (`idArchivo`, `nombreArchivo`, `nombreOriginal`, `extension`, `tamanio`, `descripcion`, `idCarpeta`, `idModulo`, `idProfesor`, `version`, `fijado`, `eliminado`, `fechaEliminacion`, `fechaSubida`) VALUES
(1, 'demo_apuntes_prog.txt', 'Apuntes - Tema 1 Introduccion.txt', 'txt', 282, 'Apuntes de la primera unidad', 1, 1, 1, 1, 0, 0, NULL, '2026-07-23 18:35:03'),
(2, 'demo_guia_ejercicios.txt', 'Guia de Ejercicios - Bloque 1.txt', 'txt', 299, 'Ejercicios practicos para entregar', 1, 1, 1, 1, 0, 0, NULL, '2026-07-23 18:35:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_archivo_accesos`
--

CREATE TABLE `aula_archivo_accesos` (
  `idAcceso` int(11) NOT NULL,
  `idArchivo` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `tipo` enum('vista','descarga') NOT NULL DEFAULT 'vista',
  `fechaAcceso` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_archivo_accesos`
--

INSERT INTO `aula_archivo_accesos` (`idAcceso`, `idArchivo`, `idEstudiante`, `tipo`, `fechaAcceso`) VALUES
(1, 1, 1, 'descarga', '2026-07-23 18:35:13'),
(2, 1, 1, 'descarga', '2026-07-23 18:56:08'),
(3, 1, 1, 'descarga', '2026-07-23 19:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_archivo_versiones`
--

CREATE TABLE `aula_archivo_versiones` (
  `idVersion` int(11) NOT NULL,
  `idArchivo` int(11) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `tamanio` int(11) DEFAULT 0,
  `version` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_asistencia_sesion`
--

CREATE TABLE `aula_asistencia_sesion` (
  `idAsistencia` int(11) NOT NULL,
  `idSesion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `horaUnion` time DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_carpetas`
--

CREATE TABLE `aula_carpetas` (
  `idCarpeta` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `idPadre` int(11) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) NOT NULL DEFAULT 'fa-folder',
  `fijado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_carpetas`
--

INSERT INTO `aula_carpetas` (`idCarpeta`, `nombre`, `idModulo`, `idProfesor`, `idPadre`, `color`, `icono`, `fijado`, `eliminado`, `fechaEliminacion`, `fechaCreacion`) VALUES
(1, 'Material del curso', 1, 1, NULL, '#4F46E5', 'folder', 0, 0, NULL, '2026-07-23 18:35:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_comentarios`
--

CREATE TABLE `aula_comentarios` (
  `idComentario` int(11) NOT NULL,
  `idEntrega` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('profesor','estudiante') NOT NULL,
  `mensaje` text NOT NULL,
  `archivoCorreccion` varchar(255) DEFAULT NULL,
  `fechaComentario` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_entregas`
--

CREATE TABLE `aula_entregas` (
  `idEntrega` int(11) NOT NULL,
  `idTarea` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `fechaEntrega` datetime NOT NULL DEFAULT current_timestamp(),
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` enum('enviada','corregida') NOT NULL DEFAULT 'enviada',
  `comentarioCalificacion` text DEFAULT NULL,
  `archivoCorreccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_entregas`
--

INSERT INTO `aula_entregas` (`idEntrega`, `idTarea`, `idEstudiante`, `archivoEntrega`, `respuesta`, `version`, `fechaEntrega`, `nota`, `estado`, `comentarioCalificacion`, `archivoCorreccion`) VALUES
(1, 1, 1, 'practica_1_ana_silva.zip', 'Profesor, adjunto la pr├íctica resuelta. He añadido como extra una vista HTML bísica para probar las validaciones.', 1, '2026-07-25 17:36:17', 8.80, 'corregida', 'Excelente código, muy limpio y estructurado. Los extras est├ín muy bien implementados.', NULL),
(2, 1, 2, 'practica_1_david_ortiz.zip', 'Hola Juan, aquí tiene mi entrega de PHP. Un saludo.', 1, '2026-07-25 17:36:17', 7.20, 'corregida', 'Buen trabajo en general. Ten cuidado con los nombres de variables y la indentación.', NULL),
(3, 2, 1, 'api_rest_ana_silva.zip', 'He diseñado los endpoints según los Estándares REST. Se incluye archivo OpenAPI (Swagger) de documentación.', 1, '2026-07-26 17:36:17', NULL, 'enviada', NULL, NULL),
(4, 3, 1, 'dom_ana_silva.zip', 'Adjunto código JS listo. He implementado delegación de eventos en la tabla para optimizar rendimiento.', 1, '2026-07-27 05:36:17', 9.60, 'corregida', 'Fantístico uso de delegación de eventos y modularización del script JS. ┬íEnhorabuena!', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_favoritos`
--

CREATE TABLE `aula_favoritos` (
  `idFavorito` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idArchivo` int(11) NOT NULL,
  `fechaMarcado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_kanban_estado`
--

CREATE TABLE `aula_kanban_estado` (
  `idEstado` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idTarea` int(11) NOT NULL,
  `estado` varchar(20) DEFAULT 'todo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_notificaciones`
--

CREATE TABLE `aula_notificaciones` (
  `idNotificacion` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('profesor','estudiante','admin') NOT NULL,
  `tipo` enum('archivo_subido','entrega_enviada','correccion','comentario') NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `idReferencia` int(11) DEFAULT NULL,
  `tipoReferencia` varchar(50) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_notificaciones`
--

INSERT INTO `aula_notificaciones` (`idNotificacion`, `idUsuario`, `tipoUsuario`, `tipo`, `titulo`, `mensaje`, `leida`, `idReferencia`, `tipoReferencia`, `fechaCreacion`) VALUES
(1, 1, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13'),
(2, 2, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13'),
(3, 4, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13'),
(4, 5, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13'),
(5, 6, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13'),
(6, 7, 'estudiante', 'archivo_subido', 'Nuevo archivo en Programación', '1 ha subido: Prueba de subida.txt', 0, 3, 'archivo', '2026-07-24 02:37:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_retos`
--

CREATE TABLE `aula_retos` (
  `idReto` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `archivoAdjunto` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_retos_entregas`
--

CREATE TABLE `aula_retos_entregas` (
  `idEntrega` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `respuesta` text DEFAULT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fechaEntrega` datetime DEFAULT current_timestamp(),
  `fechaCorreccion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_sesiones_vivas`
--

CREATE TABLE `aula_sesiones_vivas` (
  `idSesion` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fechaSesion` date NOT NULL,
  `horaSesion` time NOT NULL,
  `enlaceReunion` varchar(500) DEFAULT NULL,
  `plataforma` varchar(100) DEFAULT NULL,
  `estado` enum('programada','en_vivo','finalizada') NOT NULL DEFAULT 'programada',
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_sesiones_vivas`
--

INSERT INTO `aula_sesiones_vivas` (`idSesion`, `idModulo`, `idProfesor`, `titulo`, `descripcion`, `fechaSesion`, `horaSesion`, `enlaceReunion`, `plataforma`, `estado`, `fechaCreacion`) VALUES
(1, 3, 1, 'Resolución de Dudas: API REST', 'Revisión grupal y solución de dudas sobre cómo diseñar e integrar los verbos y códigos de respuesta en endpoints.', '2026-07-29', '11:00:00', 'https://meet.google.com/xyz-pdq-abc', 'Google Meet', 'programada', '2026-07-27 17:36:17'),
(2, 4, 1, 'Taller JavaScript: Programación Asíncrona', 'Explicación detallada y pr├íctica sobre el flujo con Event Loop, Promises, Fetch API y Async/Await.', '2026-07-30', '10:00:00', 'https://meet.google.com/uvw-xyz-rst', 'Google Meet', 'programada', '2026-07-27 17:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_tareas`
--

CREATE TABLE `aula_tareas` (
  `idTarea` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `archivoAdjunto` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aula_tareas`
--

INSERT INTO `aula_tareas` (`idTarea`, `titulo`, `descripcion`, `idModulo`, `idProfesor`, `archivoAdjunto`, `publicado`, `fechaCreacion`) VALUES
(1, 'Estructuras de Control en PHP', 'Desarrollar una biblioteca bísica de validación de datos utilizando sentencias condicionales, bucles anidados y arrays asociativos.', 3, 1, NULL, 1, '2026-07-27 17:36:17'),
(2, 'Diseño e Implementación de API REST', 'Crear una API RESTful para la gestión de productos con soporte para operaciones CRUD y respuestas estructuradas en formato JSON.', 3, 1, NULL, 1, '2026-07-27 17:36:17'),
(3, 'Manipulación Din├ímica del DOM', 'Desarrollar una aplicación interactiva simple en JavaScript que agregue, elimine y filtre elementos de una tabla usando eventos y selectores nativos.', 4, 1, NULL, 1, '2026-07-27 17:36:17'),
(4, 'Maquetación Avanzada con CSS Grid', 'Crear un dashboard de administración responsive utilizando exclusivamente CSS Grid y Flexbox para organizar la cuadrícula.', 5, 3, NULL, 1, '2026-07-27 17:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula_versiones_entrega`
--

CREATE TABLE `aula_versiones_entrega` (
  `idVersion` int(11) NOT NULL,
  `idTarea` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `version` int(11) NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog_posts`
--

CREATE TABLE `blog_posts` (
  `idPost` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `resumen` varchar(500) NOT NULL DEFAULT '',
  `contenido` mediumtext DEFAULT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT '',
  `categoria` varchar(80) NOT NULL DEFAULT '',
  `autor` varchar(120) NOT NULL DEFAULT '',
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaPublicacion` datetime DEFAULT NULL,
  `creadoEn` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizadoEn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blog_posts`
--

INSERT INTO `blog_posts` (`idPost`, `titulo`, `slug`, `resumen`, `contenido`, `imagen`, `categoria`, `autor`, `publicado`, `destacado`, `fechaPublicacion`, `creadoEn`, `actualizadoEn`) VALUES
(1, 'Apertura del Centro Formativo', 'apertura', 'Comienza un nuevo año con gran ilusión.', 'El día 15 damos el pistoletazo de salida...', '', '', '', 1, 1, '2026-09-01 00:00:00', '2026-07-23 16:05:10', '2026-07-23 16:05:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletines_log`
--

CREATE TABLE `boletines_log` (
  `serial` varchar(40) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `nombreEstudiante` varchar(255) NOT NULL,
  `nombreCiclo` varchar(255) NOT NULL,
  `cursoEscolar` varchar(20) NOT NULL,
  `fechaGeneracion` datetime DEFAULT current_timestamp(),
  `scan_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_scan_at` datetime DEFAULT NULL,
  `last_scan_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_ce`
--

CREATE TABLE `calificaciones_ce` (
  `idCalificacionCE` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idCE` int(11) NOT NULL,
  `nota` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `calificaciones_ce`
--

INSERT INTO `calificaciones_ce` (`idCalificacionCE`, `idEstudiante`, `idCE`, `nota`) VALUES
(1, 1, 1, 9.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_modulos`
--

CREATE TABLE `calificaciones_modulos` (
  `idCalificacion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `nota_1ev` decimal(4,2) DEFAULT NULL,
  `nota_1final` decimal(4,2) DEFAULT NULL,
  `nota_2ev` decimal(4,2) DEFAULT NULL,
  `nota_2final` decimal(4,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado_1ev` varchar(2) DEFAULT NULL,
  `estado_1final` varchar(2) DEFAULT NULL,
  `estado_2ev` varchar(2) DEFAULT NULL,
  `estado_2final` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones_modulos`
--

INSERT INTO `calificaciones_modulos` (`idCalificacion`, `idEstudiante`, `idModulo`, `nota_1ev`, `nota_1final`, `nota_2ev`, `nota_2final`, `observaciones`, `estado_1ev`, `estado_1final`, `estado_2ev`, `estado_2final`) VALUES
(1, 1, 1, 8.50, NULL, NULL, NULL, NULL, 'CO', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_periodo`
--

CREATE TABLE `calificaciones_periodo` (
  `idCalificacion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idPeriodo` int(11) NOT NULL,
  `idTipo` int(11) NOT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `actualizadoEn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_retos`
--

CREATE TABLE `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_tfg`
--

CREATE TABLE `calificaciones_tfg` (
  `idCalificacion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `convocatoria` enum('ordinaria','extraordinaria') NOT NULL DEFAULT 'ordinaria',
  `nota` decimal(4,2) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_gasto`
--

CREATE TABLE `categorias_gasto` (
  `idCategoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `presupuestoAnual` decimal(10,2) DEFAULT 0.00,
  `color` varchar(20) DEFAULT '#6c757d',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_gasto`
--

INSERT INTO `categorias_gasto` (`idCategoria`, `nombre`, `presupuestoAnual`, `color`, `activo`) VALUES
(1, 'Licencias de Software', 5000.00, '#0ea5e9', 1),
(2, 'Material e Instrumentos de Laboratorio', 10000.00, '#10b981', 1),
(3, 'Material de Oficina e Imprenta', 2000.00, '#f59e0b', 1),
(4, 'Infraestructura, Servidores y Cableado', 8000.00, '#ef4444', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `challenge_config`
--

CREATE TABLE `challenge_config` (
  `idConfigReto` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `pesoDefecto` decimal(6,2) NOT NULL DEFAULT 1.00,
  `permiteGrupal` tinyint(1) NOT NULL DEFAULT 0,
  `permiteFases` tinyint(1) NOT NULL DEFAULT 0,
  `requiereRubrica` tinyint(1) NOT NULL DEFAULT 0,
  `evaluacionPares` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `challenge_config`
--

INSERT INTO `challenge_config` (`idConfigReto`, `idConfig`, `pesoDefecto`, `permiteGrupal`, `permiteFases`, `requiereRubrica`, `evaluacionPares`) VALUES
(1, 1, 1.00, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_conversaciones`
--

CREATE TABLE `chat_conversaciones` (
  `id` int(11) NOT NULL,
  `user_a_rol` varchar(20) NOT NULL,
  `user_a_id` int(11) NOT NULL,
  `user_b_rol` varchar(20) NOT NULL,
  `user_b_id` int(11) NOT NULL,
  `last_message_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_conversaciones`
--

INSERT INTO `chat_conversaciones` (`id`, `user_a_rol`, `user_a_id`, `user_b_rol`, `user_b_id`, `last_message_at`) VALUES
(1, 'profesor', 1, 'estudiante', 1, '2026-07-27 17:26:17'),
(2, 'profesor', 1, 'estudiante', 2, '2026-07-27 16:36:17'),
(3, 'admin', 1, 'profesor', 5, '2026-07-29 04:34:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

CREATE TABLE `chat_mensajes` (
  `id` int(11) NOT NULL,
  `conversacion_id` int(11) NOT NULL,
  `emisor_rol` varchar(20) NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_mensajes`
--

INSERT INTO `chat_mensajes` (`id`, `conversacion_id`, `emisor_rol`, `emisor_id`, `contenido`, `leido`, `fecha`) VALUES
(1, 1, 'estudiante', 1, 'Hola profesor, ┬┐el lunes es festivo o hay entrega normal de la pr├íctica 2?', 1, '2026-07-27 17:06:17'),
(2, 1, 'profesor', 1, 'Hola Ana. Es día lectivo normal, por lo tanto la entrega se mantiene para las 23:59 de ese día.', 1, '2026-07-27 17:11:17'),
(3, 1, 'estudiante', 1, 'Perfecto, ya la tengo casi lista. Muchas gracias por la aclaración.', 0, '2026-07-27 17:26:17'),
(4, 2, 'estudiante', 2, 'Hola Juan, tengo un fallo al validar el token en la pr├íctica de REST. ┬┐Me podría guiar un poco?', 1, '2026-07-27 16:36:17'),
(5, 2, 'profesor', 1, 'Hola David. Revisa la cabecera \"Authorization\" en tu middleware. Asegúrate de separar el prefijo \"Bearer \" del token propiamente dicho.', 0, '2026-07-27 16:51:17'),
(6, 3, 'admin', 1, 'u', 0, '2026-07-29 04:34:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

CREATE TABLE `ciclos` (
  `idCiclo` int(11) NOT NULL,
  `nombreCiclo` varchar(100) NOT NULL,
  `abreviaturaCiclo` varchar(10) NOT NULL,
  `precioCiclo` decimal(10,2) DEFAULT NULL,
  `idNivel` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fechaArchivado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`, `activo`, `fechaArchivado`) VALUES
(1, 'Desarrollo de Aplicaciones Web', 'DAW', 1200.00, 1, 1, NULL),
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'DAM', 1200.00, 1, 1, NULL),
(3, 'Sistemas Microinform├íticos y Redes', 'SMR', 900.00, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclo_profesor`
--

CREATE TABLE `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ciclo_profesor`
--

INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cola_emails`
--

CREATE TABLE `cola_emails` (
  `id` int(11) NOT NULL,
  `destinatario_email` varchar(150) NOT NULL,
  `destinatario_nombre` varchar(150) DEFAULT NULL,
  `asunto` varchar(255) NOT NULL,
  `html_content` longtext NOT NULL,
  `estado` enum('pendiente','enviado','fallido') NOT NULL DEFAULT 'pendiente',
  `intentos` tinyint(4) NOT NULL DEFAULT 0,
  `ultimo_error` text DEFAULT NULL,
  `enviado_at` datetime DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_centro`
--

CREATE TABLE `configuracion_centro` (
  `idConfig` int(11) NOT NULL DEFAULT 1,
  `nombreCentro` varchar(200) DEFAULT 'Centro de Formación Profesional',
  `codigoCentro` varchar(50) DEFAULT '',
  `nifCifCentro` varchar(20) NOT NULL DEFAULT '',
  `direccionCentro` varchar(200) DEFAULT '',
  `ciudadCentro` varchar(100) DEFAULT '',
  `cpCentro` varchar(10) DEFAULT '',
  `telefonoCentro` varchar(20) DEFAULT '',
  `emailCentro` varchar(100) DEFAULT '',
  `cursoEscolar` varchar(20) DEFAULT '2024-2025',
  `logoCentro` varchar(255) DEFAULT '',
  `logoGobierno1` varchar(255) DEFAULT '',
  `logoGobierno2` varchar(255) DEFAULT '',
  `textoLegal` text DEFAULT NULL,
  `nombreDirectorFirmante` varchar(150) DEFAULT '',
  `feature_prematricula` tinyint(1) NOT NULL DEFAULT 1,
  `feature_chat` tinyint(1) NOT NULL DEFAULT 1,
  `feature_inventario` tinyint(1) NOT NULL DEFAULT 1,
  `feature_subida_tfg` tinyint(1) NOT NULL DEFAULT 1,
  `instance_status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  `suspension_message` text DEFAULT NULL,
  `saas_lock_features` tinyint(1) NOT NULL DEFAULT 0,
  `saas_message` text DEFAULT NULL,
  `saas_message_type` varchar(20) NOT NULL DEFAULT 'info',
  `saas_last_sync` datetime DEFAULT NULL,
  `license_token` text DEFAULT NULL,
  `license_token_exp` datetime DEFAULT NULL,
  `feature_horario` tinyint(1) DEFAULT 1,
  `feature_anuncios` tinyint(1) DEFAULT 1,
  `feature_eventos` tinyint(1) DEFAULT 1,
  `feature_retos` tinyint(1) DEFAULT 1,
  `feature_mensajes` tinyint(1) DEFAULT 1,
  `feature_pagos` tinyint(1) DEFAULT 1,
  `feature_gastos` tinyint(1) DEFAULT 1,
  `feature_informes` tinyint(1) DEFAULT 1,
  `feature_geoblock_admin` tinyint(1) NOT NULL DEFAULT 1,
  `feature_ra_ce` tinyint(1) DEFAULT 0,
  `feature_fp_dual` tinyint(1) DEFAULT 0,
  `feature_landing` tinyint(1) NOT NULL DEFAULT 1,
  `prematricula_filtrar_niveles` tinyint(1) NOT NULL DEFAULT 0,
  `feature_academico_config` tinyint(1) NOT NULL DEFAULT 0,
  `feature_fct` tinyint(1) NOT NULL DEFAULT 1,
  `feature_modulos` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `configuracion_centro`
--

INSERT INTO `configuracion_centro` (`idConfig`, `nombreCentro`, `codigoCentro`, `nifCifCentro`, `direccionCentro`, `ciudadCentro`, `cpCentro`, `telefonoCentro`, `emailCentro`, `cursoEscolar`, `logoCentro`, `logoGobierno1`, `logoGobierno2`, `textoLegal`, `nombreDirectorFirmante`, `feature_prematricula`, `feature_chat`, `feature_inventario`, `feature_subida_tfg`, `instance_status`, `suspension_message`, `saas_lock_features`, `saas_message`, `saas_message_type`, `saas_last_sync`, `license_token`, `license_token_exp`, `feature_horario`, `feature_anuncios`, `feature_eventos`, `feature_retos`, `feature_mensajes`, `feature_pagos`, `feature_gastos`, `feature_informes`, `feature_geoblock_admin`, `feature_ra_ce`, `feature_fp_dual`, `feature_landing`, `prematricula_filtrar_niveles`, `feature_academico_config`, `feature_fct`, `feature_modulos`) VALUES
(1, 'AulaPro Formación Profesional', 'CENTRO001', 'B12345678', 'Av. de la Innovación 42', 'Madrid', '28042', '912345678', 'info@aulapro.com', '2026-2027', '', '', '', 'Aviso legal: Este es un entorno de demostración de AulaPro.', 'Carlos Mendoza', 1, 1, 1, 1, 'active', NULL, 0, NULL, 'info', NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consentimientos`
--

CREATE TABLE `consentimientos` (
  `idConsentimiento` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criterios_evaluacion`
--

CREATE TABLE `criterios_evaluacion` (
  `idCE` int(11) NOT NULL,
  `idRA` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `criterios_evaluacion`
--

INSERT INTO `criterios_evaluacion` (`idCE`, `idRA`, `codigo`, `descripcion`) VALUES
(1, 1, 'CE1.a', 'Declara variables y estructuras de control.'),
(2, 2, 'CE2.a', 'Instancia clases y usa herencia.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos_academicos`
--

CREATE TABLE `cursos_academicos` (
  `idCurso` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cursos_academicos`
--

INSERT INTO `cursos_academicos` (`idCurso`, `idCiclo`, `nombre`, `orden`) VALUES
(1, 1, '1┬║ DAW', 1),
(2, 1, '2┬║ DAW', 2),
(3, 2, '1┬║ DAM', 1),
(4, 2, '2┬║ DAM', 2),
(5, 3, '1┬║ SMR', 1),
(6, 3, '2┬║ SMR', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `idDirector` int(11) NOT NULL,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoDirector` text DEFAULT NULL,
  `dniDirector` varchar(255) NOT NULL,
  `fechaNacimientoDirector` varchar(255) DEFAULT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  `direccionDirector` text DEFAULT NULL,
  `ciudadDirector` varchar(100) DEFAULT NULL,
  `codigoPostalDirector` varchar(10) DEFAULT NULL,
  `observacionesDirector` text DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` text DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `directores`
--

INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `password`, `telefonoDirector`, `dniDirector`, `fechaNacimientoDirector`, `fechaAltaDirector`, `direccionDirector`, `ciudadDirector`, `codigoPostalDirector`, `observacionesDirector`, `fcm_token`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Carlos Mendoza', 'admin@aulapro.com', '$2y$12$8BcEEoY4aFlVujZhRQnHgOcJO7fL.XGQsFtdngE5w9ER2//EphGbm', '600111222', '12345678A', '1980-05-15', '2024-09-01', 'Calle Mayor 1', 'Madrid', '28001', 'Director General de AulaPro', 'c4Xm0un3T7OF3Ys5Grz_TX:APA91bEl195en19HYRJjyopzQElzWDWhjcuAfAmJ431c1RVzQpUvIGHtnZxgAq_ZhSuOFK5hJsooJD_M8ld0pnIiWeZrSxFogXIZZaeqG7pZp31jA6BqEO0', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `idDispositivo` int(11) NOT NULL,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `estadoDispositivo` enum('disponible','prestado') DEFAULT 'disponible',
  `foto` varchar(255) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dispositivos`
--

INSERT INTO `dispositivos` (`idDispositivo`, `nombreDispositivo`, `numeroSerie`, `estadoDispositivo`, `foto`, `cantidad`, `deleted_at`) VALUES
(1, 'Port├ítil Dell Latitude', 'DL-2025-001', 'disponible', NULL, 1, NULL),
(2, 'Port├ítil Lenovo ThinkPad', 'LN-2025-002', 'prestado', NULL, 1, NULL),
(3, '556uj', 'bbhh', 'disponible', 'dev_6a6a007c78138.jpg', 10, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `idEstudiante` int(11) NOT NULL,
  `nombreEstudiante` varchar(100) NOT NULL,
  `emailEstudiante` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoEstudiante` text DEFAULT NULL,
  `dniEstudiante` varchar(255) NOT NULL,
  `fechaNacimientoEstudiante` varchar(255) DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` text DEFAULT NULL,
  `ciudadEstudiante` varchar(80) DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) DEFAULT NULL,
  `observacionesEstudiante` text DEFAULT NULL,
  `idCiclo` int(11) DEFAULT NULL,
  `curso` enum('Grado Medio','Grado Superior') DEFAULT NULL,
  `anioEstudio` enum('1┬║','2┬║') DEFAULT NULL,
  `idCurso` int(11) DEFAULT NULL,
  `archivoTFG` varchar(255) DEFAULT NULL,
  `tituloTFG` varchar(255) DEFAULT NULL,
  `fechaSubidaTFG` datetime DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` text DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL,
  `idGrupo` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `telefonoEstudiante`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `direccionEstudiante`, `ciudadEstudiante`, `codigoPostalEstudiante`, `observacionesEstudiante`, `idCiclo`, `curso`, `anioEstudio`, `idCurso`, `archivoTFG`, `tituloTFG`, `fechaSubidaTFG`, `fcm_token`, `eliminado`, `fecha_eliminacion`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`, `idGrupo`, `deleted_at`) VALUES
(1, 'Ana Silva', 'ana.silva@aulapro.com', '$2y$12$KvgcgImetxRJJLTc8LPaauhIUxjvmQlfLPbROwcC0rAfxKq6DkqUy', '600666777', '56789012E', '2005-04-10', '2024-09-01', 'Calle Verde 5', 'Madrid', '28005', 'Delegada de clase. Excelente rendimiento académico.', 1, 'Grado Superior', '2┬║', 2, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 2, NULL),
(2, 'David Ortiz', 'david.ortiz@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600777888', '67890123F', '2005-09-18', '2024-09-01', 'Calle Azul 6', 'Madrid', '28006', 'Participativo y muy interesado en diseño Frontend.', 1, 'Grado Superior', '2┬║', 2, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 2, NULL),
(3, 'Elena Pastor', 'elena.pastor@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600888999', '78901234G', '2006-01-22', '2025-09-01', 'Calle Roja 7', 'Madrid', '28007', 'Interés en frameworks modernos y diseño UI/UX.', 1, 'Grado Superior', '1┬║', 1, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, NULL),
(4, 'Javier Ruiz', 'javier.ruiz@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600999000', '89012345H', '2006-05-30', '2025-09-01', 'Calle Amarilla 8', 'Madrid', '28008', 'Tiene conocimientos previos de programación autodidacta.', 1, 'Grado Superior', '1┬║', 1, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, NULL),
(5, 'Lucía Mendez', 'lucia.mendez@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600000111', '90123456I', '2005-11-05', '2024-09-01', 'Calle Naranja 9', 'Madrid', '28009', 'Estudiante de 2┬║ DAM. Interesada en desarrollo de videojuegos.', 2, 'Grado Superior', '2┬║', 4, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 3, NULL),
(6, 'Sergio Abad', 'sergio.abad@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600111000', '01234567J', '2005-02-14', '2024-09-01', 'Calle Violeta 10', 'Madrid', '28010', 'Interés en administración de servidores y redes.', 2, 'Grado Superior', '2┬║', 4, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante_tutor`
--

CREATE TABLE `estudiante_tutor` (
  `idEstudiante` int(11) NOT NULL,
  `idTutor` int(11) NOT NULL,
  `parentesco` varchar(50) DEFAULT 'Tutor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiante_tutor`
--

INSERT INTO `estudiante_tutor` (`idEstudiante`, `idTutor`, `parentesco`) VALUES
(1, 1, 'Padre'),
(2, 2, 'Madre'),
(3, 3, 'Madre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `idEvento` int(11) NOT NULL,
  `tituloEvento` varchar(150) NOT NULL,
  `descripcionEvento` text DEFAULT NULL,
  `fechaEvento` date NOT NULL,
  `horaEvento` time DEFAULT NULL,
  `ubicacionEvento` varchar(150) DEFAULT NULL,
  `idCreador` int(11) NOT NULL DEFAULT 1,
  `tipo_visibilidad` enum('publica','roles','personalizado','privada') DEFAULT 'publica',
  `audiencia_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`audiencia_json`)),
  `activo` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`idEvento`, `tituloEvento`, `descripcionEvento`, `fechaEvento`, `horaEvento`, `ubicacionEvento`, `idCreador`, `tipo_visibilidad`, `audiencia_json`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Exposición de Proyectos de Fin de Grado', 'Defensa de los Proyectos TFG/Proyectos Integradores ante el comité evaluador.', '2026-06-20', '09:00:00', 'Salón de Actos - Edificio Central', 1, 'publica', NULL, 1, '2026-07-28 12:11:21', '2026-07-28 12:11:21'),
(2, 'Jornada Informativa: Inicio de FCT', 'Charla obligatoria sobre el proceso, documentación y pautas a seguir durante el periodo de pr├ícticas FCT.', '2026-02-15', '12:30:00', 'Laboratorio Inform├ítica I', 1, 'publica', NULL, 1, '2026-07-28 12:11:21', '2026-07-28 12:11:21'),
(3, 'Conferencia: Salidas Laborales en el ├ümbito Web', 'Charla tecnológica a cargo de directores de desarrollo y talento de Tech Solutions.', '2026-07-30', '16:00:00', 'Salón de Actos', 1, 'publica', NULL, 1, '2026-07-28 12:11:21', '2026-07-28 12:11:21'),
(4, 'Prueba Evento', NULL, '2026-08-15', '10:00:00', NULL, 1, 'publica', '{}', 0, '2026-07-28 12:11:28', '2026-07-28 12:13:30'),
(5, 'Prueba Actualizado', NULL, '2026-08-15', '10:00:00', NULL, 1, 'publica', '{}', 0, '2026-07-28 12:11:48', '2026-07-28 12:11:48'),
(6, 'Prueba Actualizado', NULL, '2026-08-15', '10:00:00', NULL, 1, 'publica', '{}', 0, '2026-07-28 12:12:55', '2026-07-28 12:12:55'),
(7, 'Solo profesores', NULL, '2026-09-01', NULL, NULL, 1, 'roles', '{\"roles\": [\"profesor\"]}', 0, '2026-07-28 12:13:06', '2026-07-28 12:13:06'),
(8, 'Solo usuario 1', NULL, '2026-09-02', NULL, NULL, 1, 'personalizado', '{\"usuarios_custom\": [1, 2, 3]}', 0, '2026-07-28 12:13:06', '2026-07-28 12:13:06'),
(12, 'Updated Title', 'Evento de prueba Task16', '2026-07-28', '10:00:00', 'Aula QA', 1, 'roles', '{\"roles\": [\"profesor\"]}', 0, '2026-07-28 12:59:05', '2026-07-28 13:08:01'),
(17, 'QA Cron Test', '', '2026-07-28', '10:00:00', 'Aula QA', 1, 'publica', NULL, 0, '2026-07-28 13:08:35', '2026-07-28 13:21:37'),
(18, 'QA Secretaria Test Edited', '', '2026-08-01', NULL, '', 1, 'publica', NULL, 0, '2026-07-28 13:13:06', '2026-07-28 13:13:20'),
(19, 'Reunión Consejo Académico', 'Revisión de avances y problemas académicos del cuatrimestre', '2026-07-31', '10:00:00', 'Sala de Juntas', 1, 'roles', '{\"roles\": [\"director\", \"profesor\"]}', 1, '2026-07-28 13:24:46', '2026-07-28 13:24:46'),
(20, 'Entrega de Retos Finales', '├Ültima fecha para entregar los retos del ciclo', '2026-08-04', '23:59:59', 'Plataforma Virtual', 1, 'publica', NULL, 1, '2026-07-28 13:24:46', '2026-07-28 13:24:46'),
(21, 'Jornada de Tutoría', 'Sesión de tutoría individual con estudiantes', '2026-08-02', '14:00:00', 'Oficina de Tutoría', 1, 'roles', '{\"roles\": [\"tutor\"]}', 1, '2026-07-28 13:24:46', '2026-07-28 13:24:46'),
(22, 'Charla de Orientación Laboral', 'Salidas profesionales y mercado de trabajo del sector', '2026-08-11', '09:30:00', 'Salón de Actos', 1, 'publica', NULL, 1, '2026-07-28 13:24:46', '2026-07-28 13:24:46'),
(23, 'Reunión de Padres y Tutores', 'Seguimiento individual del alumnado con tutores legales', '2026-08-18', '17:00:00', 'Aula 201', 1, 'personalizado', '{\"usuarios_custom\": [{\"id\": 1, \"tipo\": \"estudiante\"}, {\"id\": 2, \"tipo\": \"estudiante\"}]}', 1, '2026-07-28 13:24:46', '2026-07-28 13:24:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fct`
--

CREATE TABLE `fct` (
  `idFCT` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `empresa` varchar(200) NOT NULL,
  `idEmpresa` int(11) DEFAULT NULL,
  `tutorEmpresa` text DEFAULT NULL,
  `emailTutorEmpresa` text DEFAULT NULL,
  `telefonoEmpresa` text DEFAULT NULL,
  `ciudadEmpresa` varchar(100) DEFAULT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `horasTotales` int(11) DEFAULT NULL,
  `horasRealizadas` int(11) DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `apto` tinyint(1) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `idProfesorTutor` int(11) DEFAULT NULL,
  `fase` int(11) NOT NULL DEFAULT 1,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fct`
--

INSERT INTO `fct` (`idFCT`, `idEstudiante`, `idCiclo`, `empresa`, `idEmpresa`, `tutorEmpresa`, `emailTutorEmpresa`, `telefonoEmpresa`, `ciudadEmpresa`, `fechaInicio`, `fechaFin`, `horasTotales`, `horasRealizadas`, `nota`, `apto`, `observaciones`, `idProfesorTutor`, `fase`, `creado_at`) VALUES
(1, 1, 1, 'Tech Solutions S.L.', 1, 'Ramón Gómez', 'ramon.gomez@techsolutions.com', '655987654', 'Madrid', '2026-03-01', '2026-06-30', 400, 400, 9.20, 1, 'Excelente desempeño en el stack de desarrollo backend con PHP.', 1, 1, '2026-07-27 17:36:17'),
(2, 2, 1, 'Global Web Developers', 2, 'Sofía Martínez', 'sofia.martinez@globalweb.com', '655654321', 'Madrid', '2026-03-01', '2026-06-30', 400, 260, NULL, NULL, 'Buen ritmo en maquetación. En progreso continuo.', 1, 1, '2026-07-27 17:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fct_diarios`
--

CREATE TABLE `fct_diarios` (
  `idDiario` int(11) NOT NULL,
  `idFCT` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horas` decimal(4,2) NOT NULL,
  `actividades` text NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `observacionesTutor` text DEFAULT NULL,
  `tokenAprobacion` varchar(64) DEFAULT NULL,
  `creadoEn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fct_diarios`
--

INSERT INTO `fct_diarios` (`idDiario`, `idFCT`, `fecha`, `horas`, `actividades`, `estado`, `observacionesTutor`, `tokenAprobacion`, `creadoEn`) VALUES
(1, 1, '2026-07-24', 8.00, 'Configuración del entorno de desarrollo local con Docker. Clonado del repositorio y primer contacto con el esquema de base de datos.', 'aprobado', 'Buen comienzo. Entorno configurado correctamente.', NULL, '2026-07-27 15:36:17'),
(2, 1, '2026-07-25', 8.00, 'Desarrollo de los endpoints de la API de autenticación y validación de tokens JWT.', 'aprobado', 'Código limpio y siguiendo las directrices de seguridad.', NULL, '2026-07-27 15:36:17'),
(3, 1, '2026-07-26', 8.00, 'Creación de pruebas unitarias para los controladores de usuarios y resolución de bugs menores en el middleware.', 'pendiente', NULL, NULL, '2026-07-27 15:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fp_empresas`
--

CREATE TABLE `fp_empresas` (
  `idEmpresa` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cif` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `persona_contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `fp_empresas`
--

INSERT INTO `fp_empresas` (`idEmpresa`, `nombre`, `cif`, `direccion`, `persona_contacto`, `telefono`, `email`, `activo`) VALUES
(1, 'Tech Solutions S.L.', 'B12345678', 'Parque Tecnológico, Edificio A', 'Marta García', '600123456', 'marta.garcia@techsolutions.com', 1),
(2, 'Global Web Developers', 'B87654321', 'Avenida de la Inform├ítica 10', 'Luis Naranjo', '600987654', 'luis.naranjo@globalweb.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `idGasto` int(11) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `idCiclo` int(11) DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `tipoJustificante` varchar(80) DEFAULT NULL,
  `numeroReferencia` varchar(100) DEFAULT NULL,
  `archivoJustificante` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  `creadoPorId` int(11) DEFAULT NULL,
  `creadoPorRol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idGasto`, `idCategoria`, `idCiclo`, `concepto`, `importe`, `fecha`, `tipoJustificante`, `numeroReferencia`, `archivoJustificante`, `observaciones`, `creado_at`, `creadoPorId`, `creadoPorRol`) VALUES
(1, 1, 1, 'Licencias JetBrains IDE (25 licencias académicas)', 450.00, '2026-07-12', 'Factura', 'JET-2026-001', NULL, 'Uso exclusivo para DAW/DAM', '2026-07-27 17:36:17', NULL, NULL),
(2, 2, 3, 'Componentes de red (Switches administrables y Cat 6)', 1200.00, '2026-07-15', 'Factura', 'NET-2026-104', NULL, 'Para laboratorio del ciclo SMR', '2026-07-27 17:36:17', NULL, NULL),
(3, 3, NULL, 'Lote de folios, bolígrafos, tóners y carpetas de secretaría', 185.50, '2026-07-17', 'Ticket', 'OFF-9923', NULL, 'Material de oficina general', '2026-07-27 17:36:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_categorias`
--

CREATE TABLE `gastos_categorias` (
  `idCategoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(20) DEFAULT '#808080',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos_categorias`
--

INSERT INTO `gastos_categorias` (`idCategoria`, `nombre`, `color`, `activo`) VALUES
(1, 'Material de Oficina', '#4CAF50', 1),
(2, 'Mantenimiento', '#FF9800', 1),
(3, 'Eventos', '#9C27B0', 1),
(4, 'Equipamiento', '#2196F3', 1),
(5, 'Otros', '#607D8B', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grading_policies`
--

CREATE TABLE `grading_policies` (
  `idPolitica` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `escalaMin` decimal(4,2) NOT NULL DEFAULT 0.00,
  `escalaMax` decimal(4,2) NOT NULL DEFAULT 10.00,
  `notaAprobado` decimal(4,2) NOT NULL DEFAULT 5.00,
  `decimales` tinyint(4) NOT NULL DEFAULT 0,
  `pesoTfgEnMedia` decimal(6,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grading_policies`
--

INSERT INTO `grading_policies` (`idPolitica`, `idConfig`, `escalaMin`, `escalaMax`, `notaAprobado`, `decimales`, `pesoTfgEnMedia`) VALUES
(1, 1, 0.00, 10.00, 5.00, 0, 1.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `idGrupo` int(11) NOT NULL,
  `nombreGrupo` varchar(100) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `anioEstudio` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idGrupo`, `nombreGrupo`, `idCiclo`, `anioEstudio`) VALUES
(1, 'DAW-A', 1, '1┬║'),
(2, 'DAW-B', 1, '2┬║'),
(3, 'DAM-A', 2, '2┬║');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_secretarias`
--

CREATE TABLE `historial_secretarias` (
  `idHistorial` int(11) NOT NULL,
  `idSecretaria` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `entidad` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `historial_secretarias`
--

INSERT INTO `historial_secretarias` (`idHistorial`, `idSecretaria`, `accion`, `entidad`, `detalles`, `fecha`) VALUES
(1, 1, 'rechazar_comprobante', 'pagos', 'ID: 2 Foto borrosa, vuelve a subir', '2026-07-26 17:38:51'),
(2, 1, 'insertar', 'eventos', 'ID: 18 QA Secretaria Test', '2026-07-28 15:13:06'),
(3, 1, 'actualizar', 'eventos', 'ID: 18 QA Secretaria Test Edited', '2026-07-28 15:13:20'),
(4, 1, 'borrar', 'eventos', 'ID: 18 QA Secretaria Test Edited', '2026-07-28 15:13:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `idHorario` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `diaSemana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') DEFAULT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `idModulo` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `idAula` int(11) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`idHorario`, `idCiclo`, `diaSemana`, `horaInicio`, `horaFin`, `idModulo`, `idProfesor`, `idAula`, `fechaCreacion`) VALUES
(79, 1, 'Lunes', '08:30:00', '10:30:00', 1, 1, 1, '2026-07-24 01:57:30'),
(80, 1, 'Lunes', '10:30:00', '12:30:00', 2, 2, 1, '2026-07-24 01:57:30'),
(81, 1, 'Lunes', '12:30:00', '14:30:00', 3, 1, 1, '2026-07-24 01:57:30'),
(82, 1, 'Martes', '08:30:00', '10:30:00', 1, 1, 1, '2026-07-24 01:57:30'),
(83, 1, 'Martes', '10:30:00', '12:30:00', 2, 2, 1, '2026-07-24 01:57:30'),
(84, 1, 'Martes', '12:30:00', '14:30:00', 3, 1, 1, '2026-07-24 01:57:30'),
(85, 1, 'Miércoles', '08:30:00', '10:30:00', 1, 1, 1, '2026-07-24 01:57:30'),
(86, 1, 'Miércoles', '10:30:00', '12:30:00', 2, 2, 1, '2026-07-24 01:57:30'),
(87, 1, 'Miércoles', '12:30:00', '14:30:00', 3, 1, 1, '2026-07-24 01:57:30'),
(88, 1, 'Jueves', '08:30:00', '10:30:00', 1, 1, 1, '2026-07-24 01:57:30'),
(89, 1, 'Jueves', '10:30:00', '12:30:00', 2, 2, 1, '2026-07-24 01:57:30'),
(90, 1, 'Jueves', '12:30:00', '14:30:00', 3, 1, 1, '2026-07-24 01:57:30'),
(91, 1, 'Viernes', '08:30:00', '10:30:00', 1, 1, 1, '2026-07-24 01:57:30'),
(92, 1, 'Viernes', '10:30:00', '12:30:00', 2, 2, 1, '2026-07-24 01:57:30'),
(93, 1, 'Viernes', '12:30:00', '14:30:00', 3, 1, 1, '2026-07-24 01:57:30'),
(154, 2, 'Lunes', '08:30:00', '10:30:00', 7, 3, 2, '2026-07-28 15:22:35'),
(155, 2, 'Lunes', '10:30:00', '12:30:00', 9, 4, 2, '2026-07-28 15:22:35'),
(156, 2, 'Lunes', '12:30:00', '14:30:00', 10, 2, 2, '2026-07-28 15:22:35'),
(157, 2, 'Martes', '08:30:00', '10:30:00', 7, 3, 2, '2026-07-28 15:22:35'),
(158, 2, 'Martes', '10:30:00', '12:30:00', 9, 4, 2, '2026-07-28 15:22:35'),
(159, 2, 'Martes', '12:30:00', '14:30:00', 10, 2, 2, '2026-07-28 15:22:35'),
(160, 2, 'Miércoles', '08:30:00', '10:30:00', 7, 3, 2, '2026-07-28 15:22:35'),
(161, 2, 'Miércoles', '10:30:00', '12:30:00', 9, 4, 2, '2026-07-28 15:22:35'),
(162, 2, 'Miércoles', '12:30:00', '14:30:00', 10, 2, 2, '2026-07-28 15:22:35'),
(163, 2, 'Jueves', '08:30:00', '10:30:00', 7, 3, 2, '2026-07-28 15:22:35'),
(164, 2, 'Jueves', '10:30:00', '12:30:00', 9, 4, 2, '2026-07-28 15:22:35'),
(165, 2, 'Jueves', '12:30:00', '14:30:00', 10, 2, 2, '2026-07-28 15:22:35'),
(166, 2, 'Viernes', '08:30:00', '10:30:00', 7, 3, 2, '2026-07-28 15:22:35'),
(167, 2, 'Viernes', '10:30:00', '12:30:00', 9, 4, 2, '2026-07-28 15:22:35'),
(168, 2, 'Viernes', '12:30:00', '14:30:00', 10, 2, 2, '2026-07-28 15:22:35'),
(184, 3, 'Lunes', '15:00:00', '17:00:00', 8, 5, 3, '2026-07-28 15:24:46'),
(185, 3, 'Lunes', '17:00:00', '19:00:00', 11, 4, 3, '2026-07-28 15:24:46'),
(186, 3, 'Lunes', '19:00:00', '21:00:00', 12, 3, 3, '2026-07-28 15:24:46'),
(187, 3, 'Martes', '15:00:00', '17:00:00', 8, 5, 3, '2026-07-28 15:24:46'),
(188, 3, 'Martes', '17:00:00', '19:00:00', 11, 4, 3, '2026-07-28 15:24:46'),
(189, 3, 'Martes', '19:00:00', '21:00:00', 12, 3, 3, '2026-07-28 15:24:46'),
(190, 3, 'Miércoles', '15:00:00', '17:00:00', 8, 5, 3, '2026-07-28 15:24:46'),
(191, 3, 'Miércoles', '17:00:00', '19:00:00', 11, 4, 3, '2026-07-28 15:24:46'),
(192, 3, 'Miércoles', '19:00:00', '21:00:00', 12, 3, 3, '2026-07-28 15:24:46'),
(193, 3, 'Jueves', '15:00:00', '17:00:00', 8, 5, 3, '2026-07-28 15:24:46'),
(194, 3, 'Jueves', '17:00:00', '19:00:00', 11, 4, 3, '2026-07-28 15:24:46'),
(195, 3, 'Jueves', '19:00:00', '21:00:00', 12, 3, 3, '2026-07-28 15:24:46'),
(196, 3, 'Viernes', '15:00:00', '17:00:00', 8, 5, 3, '2026-07-28 15:24:46'),
(197, 3, 'Viernes', '17:00:00', '19:00:00', 11, 4, 3, '2026-07-28 15:24:46'),
(198, 3, 'Viernes', '19:00:00', '21:00:00', 12, 3, 3, '2026-07-28 15:24:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_franjas`
--

CREATE TABLE `horario_franjas` (
  `idFranja` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `esReceso` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `horario_franjas`
--

INSERT INTO `horario_franjas` (`idFranja`, `idCiclo`, `horaInicio`, `horaFin`, `esReceso`) VALUES
(1, 1, '08:00:00', '09:00:00', 0),
(2, 1, '09:00:00', '10:00:00', 0),
(3, 1, '10:00:00', '11:00:00', 0),
(4, 1, '11:00:00', '11:30:00', 1),
(5, 1, '11:30:00', '12:30:00', 0),
(6, 1, '12:30:00', '13:30:00', 0),
(7, 1, '13:30:00', '14:30:00', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `internship_config`
--

CREATE TABLE `internship_config` (
  `idConfigFCT` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT 0,
  `horasRequeridasDefecto` int(11) NOT NULL DEFAULT 0,
  `metodoEvaluacion` enum('nota','apto_no_apto','ambos') NOT NULL DEFAULT 'ambos',
  `pesoEnMedia` decimal(6,2) NOT NULL DEFAULT 0.00,
  `requiereAprobarParaTitular` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `internship_config`
--

INSERT INTO `internship_config` (`idConfigFCT`, `idConfig`, `habilitado`, `horasRequeridasDefecto`, `metodoEvaluacion`, `pesoEnMedia`, `requiereAprobarParaTitular`) VALUES
(1, 1, 0, 0, 'ambos', 0.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `idInventario` int(11) NOT NULL,
  `nombreArticulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justificaciones_falta`
--

CREATE TABLE `justificaciones_falta` (
  `idJustificacion` int(11) NOT NULL,
  `idAsistencia` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `estadoOriginal` enum('ausente','retraso') NOT NULL DEFAULT 'ausente',
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `rolSolicitante` enum('estudiante','tutor','profesor','secretaria','director') NOT NULL DEFAULT 'estudiante',
  `idSolicitante` int(11) DEFAULT NULL,
  `idResuelvePor` int(11) DEFAULT NULL,
  `rolResuelve` enum('profesor','secretaria','director') DEFAULT NULL,
  `motivoRechazo` varchar(500) DEFAULT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fechaResolucion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `justificaciones_falta`
--

INSERT INTO `justificaciones_falta` (`idJustificacion`, `idAsistencia`, `idEstudiante`, `motivo`, `archivo`, `estadoOriginal`, `estado`, `rolSolicitante`, `idSolicitante`, `idResuelvePor`, `rolResuelve`, `motivoRechazo`, `fechaSolicitud`, `fechaResolucion`) VALUES
(1, 3, 3, 'Cita médica adjunta.', NULL, 'ausente', 'aprobada', 'estudiante', NULL, 3, NULL, NULL, '2026-10-03 13:00:00', '2026-10-04 07:00:00'),
(2, 1, 1, 'Problema de transporte.', NULL, 'ausente', 'aprobada', 'estudiante', NULL, 1, NULL, '', '2026-10-02 08:00:00', '2026-07-23 17:02:28'),
(3, 2, 2, 'Cita medica, adjunto justificante', 'demo_justificante_elena.pdf', 'ausente', 'pendiente', 'estudiante', NULL, NULL, NULL, NULL, '2026-07-23 20:06:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `landing_ciclos`
--

CREATE TABLE `landing_ciclos` (
  `idLandingCiclo` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `etiqueta` varchar(60) NOT NULL DEFAULT '',
  `resumen` varchar(300) NOT NULL DEFAULT '',
  `descripcion` mediumtext DEFAULT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT '',
  `precio` varchar(60) NOT NULL DEFAULT '',
  `duracion` varchar(60) NOT NULL DEFAULT '',
  `modalidad` varchar(60) NOT NULL DEFAULT '',
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `creadoEn` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizadoEn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `landing_ciclos`
--

INSERT INTO `landing_ciclos` (`idLandingCiclo`, `titulo`, `slug`, `etiqueta`, `resumen`, `descripcion`, `imagen`, `precio`, `duracion`, `modalidad`, `publicado`, `destacado`, `orden`, `creadoEn`, `actualizadoEn`) VALUES
(1, 'Desarrollo de Aplicaciones Web', 'desarrollo-de-aplicaciones-web', 'Grado Superior', 'Aprende a diseñar, crear y mantener aplicaciones web modernas y seguras.', '<p>En este ciclo aprenderís a dominar tanto el frontend (HTML, CSS, JS, React) como el backend (PHP, Node, SQL) para convertirte en un Full-Stack Developer muy demandado por el mercado.</p>', '', '1200Ôé¼ / año', '2 años (2000 horas)', 'Presencial / Online', 1, 1, 1, '2026-07-27 15:36:17', '2026-07-27 15:36:17'),
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'desarrollo-de-aplicaciones-multiplataforma', 'Grado Superior', 'Conviértete en desarrollador de apps móviles, de escritorio y videojuegos.', '<p>Domina lenguajes como Java, C# y Kotlin para crear software robusto multiplataforma. Incluye programación de interfaces gr├íficas avanzadas y acceso a datos.</p>', '', '1200Ôé¼ / año', '2 años (2000 horas)', 'Presencial', 1, 1, 2, '2026-07-27 15:36:17', '2026-07-27 15:36:17'),
(3, 'Sistemas Microinform├íticos y Redes', 'sistemas-microinformaticos-y-redes', 'Grado Medio', 'Montaje de hardware, instalación de redes y soporte técnico.', '<p>Fórmate como técnico de sistemas. Aprenderís a montar servidores, administrar redes locales, configurar routers y resolver incidencias de hardware en empresas.</p>', '', '900Ôé¼ / año', '2 años (2000 horas)', 'Presencial', 1, 0, 3, '2026-07-27 15:36:17', '2026-07-27 15:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `landing_config`
--

CREATE TABLE `landing_config` (
  `idLanding` int(11) NOT NULL DEFAULT 1,
  `plantilla` varchar(30) DEFAULT NULL,
  `ajustes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ajustes`)),
  `plantilla_pub` varchar(30) DEFAULT NULL,
  `ajustes_pub` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ajustes_pub`)),
  `publicadoEn` datetime DEFAULT NULL,
  `actualizadoEn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `landing_config`
--

INSERT INTO `landing_config` (`idLanding`, `plantilla`, `ajustes`, `plantilla_pub`, `ajustes_pub`, `publicadoEn`, `actualizadoEn`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:23:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `landing_secciones`
--

CREATE TABLE `landing_secciones` (
  `idSeccion` int(11) NOT NULL,
  `version` enum('draft','live') NOT NULL DEFAULT 'draft',
  `tipo` varchar(40) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`contenido`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_intentos`
--

CREATE TABLE `login_intentos` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `intentos` tinyint(4) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_intento` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_acciones`
--

CREATE TABLE `log_acciones` (
  `idLog` int(11) NOT NULL,
  `idAdmin` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(100) NOT NULL,
  `idRegistro` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `log_acciones`
--

INSERT INTO `log_acciones` (`idLog`, `idAdmin`, `accion`, `tabla`, `idRegistro`, `descripcion`, `ip`, `fecha`) VALUES
(1, 1, 'add_franja', 'horario', 1, '19:00-19:30', '172.20.10.2', '2026-07-24 02:18:17'),
(2, 1, 'remove_franja', 'horario', 1, '19:00', '172.20.10.2', '2026-07-24 02:18:17'),
(3, NULL, 'insertar', 'anuncios', NULL, 'Prueba API movil', '127.0.0.1', '2026-07-24 21:00:47'),
(6, 1, 'insertar', 'eventos', 12, 'QA Test Evento Fase1', '127.0.0.1', '2026-07-28 14:59:06'),
(7, 1, 'actualizar', 'eventos', 12, 'Updated Title', '127.0.0.1', '2026-07-28 14:59:23'),
(8, 1, 'borrar', 'eventos', 12, 'Updated Title', '127.0.0.1', '2026-07-28 15:08:01'),
(9, 1, 'insertar', 'eventos', 17, 'QA Cron Test', '127.0.0.1', '2026-07-28 15:08:35'),
(10, 1, 'borrar', 'eventos', 17, 'QA Cron Test', '127.0.0.1', '2026-07-28 15:21:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `idModulo` int(11) NOT NULL,
  `nombreModulo` varchar(120) NOT NULL,
  `codigoModulo` varchar(20) DEFAULT NULL,
  `horasMaximas` int(11) DEFAULT NULL,
  `idCiclo` int(11) NOT NULL,
  `idCurso` int(11) DEFAULT NULL,
  `tipoModulo` enum('Específico','Transversal','Proyecto','Empresa') DEFAULT 'Específico',
  `cursoAnio` enum('1┬║','2┬║') DEFAULT '1┬║',
  `creditosECTS` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `codigoModulo`, `horasMaximas`, `idCiclo`, `idCurso`, `tipoModulo`, `cursoAnio`, `creditosECTS`) VALUES
(1, 'Programación', 'PRG', 240, 1, 1, 'Específico', '1┬║', 10),
(2, 'Bases de Datos', 'BD', 180, 1, 1, 'Específico', '1┬║', 8),
(3, 'Desarrollo Web en Entorno Servidor', 'DWES', 180, 1, 2, 'Específico', '2┬║', 9),
(4, 'Desarrollo Web en Entorno Cliente', 'DWEC', 140, 1, 2, 'Específico', '2┬║', 7),
(5, 'Diseño de Interfaces Web', 'DIW', 120, 1, 2, 'Específico', '2┬║', 6),
(6, 'Entornos de Desarrollo', 'ED', 90, 1, 1, 'Específico', '1┬║', 4),
(7, 'Programación Multimedia y Dispositivos Móviles', 'PMDM', 120, 2, 4, 'Específico', '2┬║', 6),
(8, 'Montaje y Mantenimiento de Equipos', 'MME', 150, 3, 5, 'Específico', '1┬║', 8),
(9, 'Acceso a Datos', 'AD', 160, 2, 4, 'Específico', '2┬║', 7),
(10, 'Desarrollo de Interfaces', 'DI', 160, 2, 4, 'Específico', '2┬║', 7),
(11, 'Redes Locales', 'RL', 130, 3, 5, 'Específico', '1┬║', 6),
(12, 'Sistemas Operativos Monopuesto', 'SOM', 130, 3, 5, 'Específico', '1┬║', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_profesor`
--

CREATE TABLE `modulo_profesor` (
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulo_profesor`
--

INSERT INTO `modulo_profesor` (`idModulo`, `idProfesor`) VALUES
(1, 2),
(2, 2),
(3, 1),
(4, 1),
(5, 3),
(6, 2),
(7, 3),
(8, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_reto`
--

CREATE TABLE `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulo_reto`
--

INSERT INTO `modulo_reto` (`idModulo`, `idReto`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `idNivel` int(11) NOT NULL,
  `nombreNivel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'Grado Superior'),
(2, 'Grado Medio'),
(3, 'Grado Bísico'),
(4, 'Colegio (Primaria/ESO/Bachillerato)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_recordatorios`
--

CREATE TABLE `notificaciones_recordatorios` (
  `idNotificacionRecordatorio` int(11) NOT NULL,
  `idEvento` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('director','profesor','secretaria','estudiante','tutor') NOT NULL,
  `idRecordatorio` int(11) DEFAULT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_enviada` datetime DEFAULT NULL,
  `leido` tinyint(4) DEFAULT 0,
  `estado` enum('pendiente','enviado','fallido') DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `notificaciones_recordatorios`
--

INSERT INTO `notificaciones_recordatorios` (`idNotificacionRecordatorio`, `idEvento`, `idUsuario`, `tipoUsuario`, `idRecordatorio`, `fecha_programada`, `fecha_enviada`, `leido`, `estado`, `created_at`) VALUES
(6, 17, 1, 'director', 37, '2026-07-28 15:08:53', NULL, 1, 'pendiente', '2026-07-28 13:08:53'),
(7, 17, 1, 'profesor', 37, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(8, 17, 2, 'profesor', 37, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(9, 17, 3, 'profesor', 37, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(10, 17, 1, 'secretaria', 37, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(11, 17, 1, 'director', 38, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(12, 17, 1, 'profesor', 38, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(13, 17, 2, 'profesor', 38, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(14, 17, 3, 'profesor', 38, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(15, 17, 1, 'secretaria', 38, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(16, 17, 1, 'director', 39, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(17, 17, 1, 'profesor', 39, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(18, 17, 2, 'profesor', 39, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(19, 17, 3, 'profesor', 39, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53'),
(20, 17, 1, 'secretaria', 39, '2026-07-28 15:08:53', NULL, 0, 'pendiente', '2026-07-28 13:08:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `rolRegistrador` enum('secretaria','director') DEFAULT NULL,
  `idRegistrador` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date DEFAULT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico') DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `prorrogaHasta` date DEFAULT NULL,
  `estadoComprobante` enum('ninguno','verificando','aprobado','rechazado') DEFAULT 'ninguno',
  `motivoRechazoComprobante` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`idPago`, `idEstudiante`, `rolRegistrador`, `idRegistrador`, `monto`, `fechaPago`, `fechaProximoPago`, `tipoPago`, `comprobante`, `prorrogaHasta`, `estadoComprobante`, `motivoRechazoComprobante`) VALUES
(1, 1, NULL, NULL, 2500.00, '2026-08-15', NULL, 'unico', NULL, NULL, 'aprobado', NULL),
(2, 2, NULL, NULL, 2500.00, '2026-08-20', NULL, 'unico', NULL, NULL, 'aprobado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `email` varchar(150) NOT NULL,
  `tipo_usuario` varchar(20) NOT NULL,
  `expires_at` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `idPrestamo` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') DEFAULT 'en curso',
  `idDispositivo` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`idPrestamo`, `idEstudiante`, `numeroSerie`, `fechaPrestamo`, `fechaDevolucion`, `estadoPrestamo`, `idDispositivo`, `deleted_at`) VALUES
(1, 1, 'LN-2025-002', '2026-09-15', NULL, 'en curso', 2, NULL),
(2, 3, 'DL-2025-001', '2026-07-23', '2026-07-23', 'devuelto', 1, NULL),
(3, 1, '', '2026-07-29', NULL, '', 3, NULL),
(4, 1, '', '2026-07-29', NULL, '', 3, NULL),
(5, 1, '', '2026-07-29', NULL, '', 3, NULL),
(6, 2, '', '2026-07-29', NULL, '', 3, NULL),
(7, 5, '', '2026-07-29', NULL, '', 3, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pre_matriculas`
--

CREATE TABLE `pre_matriculas` (
  `idPreMatricula` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `idCiclo` int(11) NOT NULL,
  `curso` varchar(20) DEFAULT '1┬║',
  `nombreTutor` varchar(150) DEFAULT NULL,
  `dniTutor` varchar(20) DEFAULT NULL,
  `emailTutor` varchar(150) DEFAULT NULL,
  `telefonoTutor` varchar(20) DEFAULT NULL,
  `parentescoTutor` varchar(50) DEFAULT NULL,
  `estado` enum('pendiente','revisando','aceptada','rechazada','subsanacion') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `fechaSolicitud` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pre_matriculas`
--

INSERT INTO `pre_matriculas` (`idPreMatricula`, `dni`, `nombre`, `apellidos`, `email`, `telefono`, `idCiclo`, `curso`, `nombreTutor`, `dniTutor`, `emailTutor`, `telefonoTutor`, `parentescoTutor`, `estado`, `observaciones`, `fechaSolicitud`) VALUES
(1, '77777771Z', 'Nuevo Alumno 1', 'Martín Silva', 'nuevo1@aulapro.com', '677111222', 1, 'Primero', NULL, NULL, NULL, NULL, NULL, 'pendiente', '', '2026-08-01 10:00:00'),
(2, '77777772Z', 'Nuevo Alumno 2', 'García López', 'nuevo2@aulapro.com', '677222333', 2, 'Segundo', NULL, NULL, NULL, NULL, NULL, 'aceptada', NULL, '2026-08-02 11:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pre_matricula_archivos`
--

CREATE TABLE `pre_matricula_archivos` (
  `idArchivo` int(11) NOT NULL,
  `idPreMatricula` int(11) NOT NULL,
  `tipoDocumento` varchar(80) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `idProfesor` int(11) NOT NULL,
  `nombreProfesor` varchar(100) NOT NULL,
  `emailProfesor` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoProfesor` varchar(15) DEFAULT NULL,
  `dniProfesor` varchar(12) DEFAULT NULL,
  `fechaNacimientoProfesor` date DEFAULT NULL,
  `fechaAltaProfesor` date DEFAULT NULL,
  `direccionProfesor` varchar(200) DEFAULT NULL,
  `ciudadProfesor` varchar(80) DEFAULT NULL,
  `codigoPostalProfesor` varchar(10) DEFAULT NULL,
  `observacionesProfesor` text DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `esTutor` tinyint(1) DEFAULT 0,
  `idCicloTutor` int(11) DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` text DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, `dniProfesor`, `fechaNacimientoProfesor`, `fechaAltaProfesor`, `direccionProfesor`, `ciudadProfesor`, `codigoPostalProfesor`, `observacionesProfesor`, `fcm_token`, `esTutor`, `idCicloTutor`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Juan Pérez', 'juan.perez@aulapro.com', '$2y$12$NoDoFaNeZT43YYAR1XnAGOEhZHc9NGdJXxGc.JOceS21paDUZnQRq', '600333444', '23456789B', '1985-10-20', '2024-09-01', 'Calle Secundaria 2', 'Madrid', '28002', 'Profesor especialista en Backend. Tutor de 2┬║ DAW.', NULL, 1, 1, 0, NULL, NULL),
(2, 'María Rodríguez', 'maria.rodriguez@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600444555', '34567890C', '1988-03-12', '2024-09-01', 'Avenida Principal 3', 'Madrid', '28003', 'Profesora de programación e iniciación al desarrollo.', NULL, 0, NULL, 0, NULL, NULL),
(3, 'Pedro Martínez', 'pedro.martinez@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '600555666', '45678901D', '1982-07-25', '2024-09-01', 'Paseo del Prado 4', 'Madrid', '28004', 'Profesor de multiplataforma y hardware. Tutor de 2┬║ DAM.', NULL, 1, 2, 0, NULL, NULL),
(4, 'Laura Gómez', 'laura.gomez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', NULL, '56789012E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL),
(5, 'Miguel Torres', 'miguel.torres@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', NULL, '67890123F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promotion_rules`
--

CREATE TABLE `promotion_rules` (
  `idRegla` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `requiereTodosModulos` tinyint(1) NOT NULL DEFAULT 1,
  `notaMinimaGlobal` decimal(4,2) NOT NULL DEFAULT 5.00,
  `permiteModulosPendientes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promotion_rules`
--

INSERT INTO `promotion_rules` (`idRegla`, `idConfig`, `requiereTodosModulos`, `notaMinimaGlobal`, `permiteModulosPendientes`) VALUES
(1, 1, 1, 5.00, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope` varchar(64) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `window_start` int(10) UNSIGNED NOT NULL,
  `blocked_until` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `scope`, `ip`, `hits`, `window_start`, `blocked_until`) VALUES
(1, 'api_login', '172.20.10.3', 1, 1784853940, NULL),
(2, 'apiv1_99c0bc6f', '172.20.10.3', 3, 1784823764, NULL),
(3, 'api_login', '172.20.10.2', 1, 1784847700, NULL),
(4, 'apiv1_e1ad8f35', '172.20.10.2', 6, 1784824108, NULL),
(5, 'apiv1_7b039a22', '172.20.10.2', 3, 1784824126, NULL),
(6, 'chat_send_estudiante_1', '172.20.10.2', 1, 1784824126, NULL),
(7, 'apiv1_dc86f9a8', '172.20.10.2', 1, 1784824127, NULL),
(8, 'apiv1_aa695151', '172.20.10.2', 1, 1784824127, NULL),
(9, 'apiv1_d88f0c49', '172.20.10.2', 5, 1784824513, NULL),
(10, 'apiv1_d062516a', '172.20.10.2', 1, 1784824526, NULL),
(11, 'apiv1_1ee7f54e', '172.20.10.2', 1, 1784824817, NULL),
(12, 'apiv1_3cac4f61', '172.20.10.2', 1, 1784824818, NULL),
(13, 'apiv1_898c18e2', '172.20.10.3', 3, 1784825438, NULL),
(14, 'chat_send_admin_1', '172.20.10.3', 1, 1784825263, NULL),
(15, 'apiv1_6255e3e5', '172.20.10.2', 5, 1784825554, NULL),
(16, 'apiv1_04eb4a84', '172.20.10.2', 1, 1784825768, NULL),
(17, 'apiv1_30747044', '172.20.10.2', 1, 1784826096, NULL),
(18, 'apiv1_d1c0f552', '172.20.10.2', 1, 1784826096, NULL),
(19, 'apiv1_70085cf3', '172.20.10.2', 1, 1784826096, NULL),
(20, 'apiv1_3a851751', '172.20.10.2', 2, 1784826135, NULL),
(21, 'apiv1_9714c7df', '172.20.10.2', 1, 1784826148, NULL),
(22, 'apiv1_913d092d', '172.20.10.2', 2, 1784826148, NULL),
(23, 'apiv1_da14ae3b', '172.20.10.2', 3, 1784826149, NULL),
(24, 'apiv1_f609270b', '172.20.10.2', 1, 1784826262, NULL),
(25, 'apiv1_a0293fec', '172.20.10.2', 3, 1784826529, NULL),
(26, 'apiv1_d66db94e', '172.20.10.2', 2, 1784826538, NULL),
(27, 'apiv1_c1e12e34', '172.20.10.3', 21, 1784827760, NULL),
(28, 'apiv1_73676419', '172.20.10.3', 1, 1784828397, NULL),
(29, 'chat_send_estudiante_1', '172.20.10.3', 2, 1784827918, NULL),
(30, 'apiv1_b06c58f5', '172.20.10.2', 5, 1784828490, NULL),
(31, 'apiv1_69bf054b', '172.20.10.2', 2, 1784828527, NULL),
(32, 'apiv1_8a62e25d', '172.20.10.2', 1, 1784828543, NULL),
(33, 'apiv1_1d579aa1', '172.20.10.2', 1, 1784828554, NULL),
(34, 'apiv1_168c54b9', '172.20.10.3', 20, 1784836110, NULL),
(35, 'apiv1_ff523a84', '172.20.10.3', 17, 1784836201, NULL),
(36, 'apiv1_e58c27ed', '172.20.10.3', 4, 1784836472, NULL),
(37, 'apiv1_9c9da5c9', '172.20.10.2', 1, 1784837102, NULL),
(38, 'apiv1_5d50c6a3', '172.20.10.2', 1, 1784837120, NULL),
(39, 'apiv1_8967cb09', '172.20.10.2', 2, 1784837150, NULL),
(40, 'apiv1_4fdffd02', '172.20.10.2', 1, 1784837175, NULL),
(41, 'apiv1_339443d9', '172.20.10.2', 1, 1784837193, NULL),
(42, 'apiv1_f71d43bd', '172.20.10.2', 1, 1784837193, NULL),
(43, 'apiv1_0dd02b69', '172.20.10.3', 3, 1784842991, NULL),
(44, 'apiv1_7cc5f823', '172.20.10.2', 2, 1784840680, NULL),
(45, 'apiv1_373c27b3', '172.20.10.2', 2, 1784840788, NULL),
(46, 'apiv1_c7face57', '172.20.10.2', 5, 1784840681, NULL),
(47, 'apiv1_d9d9e2eb', '172.20.10.2', 3, 1784840788, NULL),
(48, 'apiv1_76ae15b7', '172.20.10.2', 4, 1784840682, NULL),
(49, 'chat_send_estudiante_2', '172.20.10.2', 1, 1784840699, NULL),
(50, 'apiv1_911675be', '172.20.10.2', 3, 1784847701, NULL),
(51, 'apiv1_28a50fe3', '172.20.10.3', 4, 1784848165, NULL),
(52, 'apiv1_34ebbfb5', '172.20.10.3', 1, 1784855084, NULL),
(53, 'chat_send_estudiante_2', '172.20.10.3', 1, 1784853950, NULL),
(54, 'api_login', '127.0.0.1', 1, 1784920698, NULL),
(55, 'apiv1_7a29b0c9', '127.0.0.1', 1, 1784914298, NULL),
(56, 'apiv1_d9fae6d8', '127.0.0.1', 3, 1784915251, NULL),
(57, 'apiv1_110d1ed4', '127.0.0.1', 1, 1784915267, NULL),
(58, 'apiv1_d66be5fd', '127.0.0.1', 2, 1784918412, NULL),
(59, 'apiv1_1cf55157', '127.0.0.1', 2, 1784919647, NULL),
(60, 'apiv1_2b2dcbf1', '127.0.0.1', 1, 1784919681, NULL),
(61, 'apiv1_6d3d20ef', '127.0.0.1', 3, 1784920698, NULL),
(62, 'apiv1_d223dc88', '127.0.0.1', 4, 1785080278, NULL),
(63, 'apiv1_2a9b6632', '127.0.0.1', 2, 1785080105, NULL),
(64, 'apiv1_a582c74b', '127.0.0.1', 2, 1785080279, NULL),
(65, 'apiv1_f85bae39', '127.0.0.1', 2, 1785080329, NULL),
(66, 'api_login', '185.13.202.184', 1, 1785296180, NULL),
(67, 'apiv1_3b309591', '185.13.202.184', 1, 1785292604, NULL),
(68, 'chat_send_admin_1', '185.13.202.184', 1, 1785292485, NULL),
(69, 'apiv1_e5c6be66', '185.13.202.184', 13, 1785294165, NULL),
(70, 'apiv1_5611dd47', '185.13.202.184', 12, 1785294582, NULL),
(71, 'apiv1_ae4c3d7b', '185.13.202.184', 1, 1785296107, NULL),
(72, 'apiv1_3ec1bb6e', '185.13.202.184', 2, 1785296714, NULL),
(73, 'apiv1_3ec1bb6e', '46.37.82.215', 3, 1785299046, NULL),
(74, 'api_login', '171.33.234.173', 1, 1785331717, NULL),
(75, 'apiv1_1ed918fd', '171.33.234.173', 7, 1785331546, NULL),
(76, 'apiv1_2cc7ee77', '171.33.234.173', 2, 1785333050, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamaciones`
--

CREATE TABLE `reclamaciones` (
  `idReclamacion` int(11) NOT NULL,
  `idEstudiante` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `emisor_rol` enum('estudiante','profesor','admin') NOT NULL,
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoReclamacion` enum('pendiente','atendido') DEFAULT 'pendiente',
  `leido` tinyint(1) DEFAULT 0,
  `respuesta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reclamaciones`
--

INSERT INTO `reclamaciones` (`idReclamacion`, `idEstudiante`, `idProfesor`, `id_parent`, `emisor_rol`, `asunto`, `descripcion`, `fecha`, `estadoReclamacion`, `leido`, `respuesta`) VALUES
(1, 1, 1, NULL, 'estudiante', 'Revisión nota Tarea 1', 'Creo que el ejercicio 3 est├í correcto.', '2026-10-15 10:00:00', 'atendido', 1, NULL),
(2, 1, 1, 1, 'estudiante', 'Revisión nota Tarea 1', 'Gracias por revisarlo', '2026-07-23 18:28:46', 'atendido', 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios`
--

CREATE TABLE `recordatorios` (
  `idRecordatorio` int(11) NOT NULL,
  `idEvento` int(11) NOT NULL,
  `tipo_recordatorio` enum('24h_antes','1h_antes','en_inicio') NOT NULL,
  `minutos_antes` int(11) NOT NULL,
  `activo` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `recordatorios`
--

INSERT INTO `recordatorios` (`idRecordatorio`, `idEvento`, `tipo_recordatorio`, `minutos_antes`, `activo`, `created_at`) VALUES
(1, 5, '24h_antes', 1440, 1, '2026-07-28 12:11:48'),
(2, 5, '1h_antes', 60, 0, '2026-07-28 12:11:48'),
(3, 5, 'en_inicio', 0, 0, '2026-07-28 12:11:48'),
(4, 6, '24h_antes', 1440, 1, '2026-07-28 12:12:55'),
(5, 6, '1h_antes', 60, 0, '2026-07-28 12:12:55'),
(6, 6, 'en_inicio', 0, 0, '2026-07-28 12:12:55'),
(7, 7, '24h_antes', 1440, 0, '2026-07-28 12:13:06'),
(8, 7, '1h_antes', 60, 0, '2026-07-28 12:13:06'),
(9, 7, 'en_inicio', 0, 0, '2026-07-28 12:13:06'),
(10, 8, '24h_antes', 1440, 0, '2026-07-28 12:13:06'),
(11, 8, '1h_antes', 60, 0, '2026-07-28 12:13:06'),
(12, 8, 'en_inicio', 0, 0, '2026-07-28 12:13:06'),
(16, 10, '24h_antes', 1440, 0, '2026-07-28 12:55:17'),
(17, 10, '1h_antes', 60, 0, '2026-07-28 12:55:17'),
(18, 10, 'en_inicio', 0, 0, '2026-07-28 12:55:17'),
(19, 11, '24h_antes', 1440, 0, '2026-07-28 12:55:17'),
(20, 11, '1h_antes', 60, 0, '2026-07-28 12:55:17'),
(21, 11, 'en_inicio', 0, 0, '2026-07-28 12:55:17'),
(22, 12, '24h_antes', 1440, 1, '2026-07-28 12:59:05'),
(23, 12, '1h_antes', 60, 0, '2026-07-28 12:59:05'),
(24, 12, 'en_inicio', 0, 0, '2026-07-28 12:59:05'),
(37, 17, '24h_antes', 1440, 1, '2026-07-28 13:08:35'),
(38, 17, '1h_antes', 60, 1, '2026-07-28 13:08:35'),
(39, 17, 'en_inicio', 0, 1, '2026-07-28 13:08:35'),
(40, 18, '24h_antes', 1440, 0, '2026-07-28 13:13:06'),
(41, 18, '1h_antes', 60, 0, '2026-07-28 13:13:06'),
(42, 18, 'en_inicio', 0, 0, '2026-07-28 13:13:06'),
(43, 19, '24h_antes', 1440, 1, '2026-07-28 13:24:46'),
(44, 20, '24h_antes', 1440, 1, '2026-07-28 13:24:46'),
(45, 21, '24h_antes', 1440, 1, '2026-07-28 13:24:46'),
(46, 22, '24h_antes', 1440, 1, '2026-07-28 13:24:46'),
(47, 23, '24h_antes', 1440, 1, '2026-07-28 13:24:46'),
(50, 19, '1h_antes', 60, 1, '2026-07-28 13:24:46'),
(51, 20, '1h_antes', 60, 1, '2026-07-28 13:24:46'),
(52, 21, '1h_antes', 60, 1, '2026-07-28 13:24:46'),
(53, 22, '1h_antes', 60, 1, '2026-07-28 13:24:46'),
(54, 23, '1h_antes', 60, 1, '2026-07-28 13:24:46'),
(57, 19, 'en_inicio', 0, 1, '2026-07-28 13:24:46'),
(58, 21, 'en_inicio', 0, 1, '2026-07-28 13:24:46'),
(59, 23, 'en_inicio', 0, 1, '2026-07-28 13:24:46'),
(60, 20, 'en_inicio', 0, 0, '2026-07-28 13:24:46'),
(61, 22, 'en_inicio', 0, 0, '2026-07-28 13:24:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados_aprendizaje`
--

CREATE TABLE `resultados_aprendizaje` (
  `idRA` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `porcentaje` int(11) DEFAULT 0,
  `idTipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `resultados_aprendizaje`
--

INSERT INTO `resultados_aprendizaje` (`idRA`, `idModulo`, `codigo`, `descripcion`, `porcentaje`, `idTipo`) VALUES
(1, 1, 'RA1', 'Programa aplicaciones bísicas.', 50, NULL),
(2, 1, 'RA2', 'Usa POO en Java.', 50, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retos`
--

CREATE TABLE `retos` (
  `idReto` int(11) NOT NULL,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `retos`
--

INSERT INTO `retos` (`idReto`, `nombreReto`, `fechaInicio`, `fechaFin`, `horasReto`) VALUES
(1, 'Crear E-Commerce desde cero', '2026-01-01', '2026-12-31', 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reto_archivos`
--

CREATE TABLE `reto_archivos` (
  `idArchivo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `tipoArchivo` varchar(50) DEFAULT NULL,
  `fechaSubida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rgpd_eliminaciones`
--

CREATE TABLE `rgpd_eliminaciones` (
  `id` int(11) NOT NULL,
  `idAdmin` int(11) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `idRegistro` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `datos_backup` longtext NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rgpd_solicitudes`
--

CREATE TABLE `rgpd_solicitudes` (
  `idSolicitud` int(11) NOT NULL,
  `rolSesion` varchar(20) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nombreUsuario` varchar(255) NOT NULL DEFAULT '',
  `emailUsuario` varchar(255) NOT NULL DEFAULT '',
  `motivo` text NOT NULL,
  `estado` enum('pendiente','resuelta') NOT NULL DEFAULT 'pendiente',
  `resueltaPorAdmin` int(11) DEFAULT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fechaResolucion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secretarias`
--

CREATE TABLE `secretarias` (
  `idSecretaria` int(11) NOT NULL,
  `nombreSecretaria` varchar(150) NOT NULL,
  `emailSecretaria` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activoSecretaria` tinyint(1) NOT NULL DEFAULT 1,
  `token_fcm` text DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
  `fechaAltaSecretaria` datetime DEFAULT current_timestamp(),
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` text DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `secretarias`
--

INSERT INTO `secretarias` (`idSecretaria`, `nombreSecretaria`, `emailSecretaria`, `password`, `activoSecretaria`, `token_fcm`, `must_change_password`, `pwd_changed_at`, `fechaAltaSecretaria`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Laura Gómez', 'laura.gomez@aulapro.com', '$2y$12$4H2Qo1/AFoW4f1oMOVBgauHxMMKa2dWes6FoLoKsWlSPSIwO.jNIC', 1, NULL, 0, NULL, '2026-07-27 17:36:16', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tfg_config`
--

CREATE TABLE `tfg_config` (
  `idConfigTFG` int(11) NOT NULL,
  `idConfig` int(11) NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT 1,
  `requiereComite` tinyint(1) NOT NULL DEFAULT 0,
  `requiereDefensa` tinyint(1) NOT NULL DEFAULT 0,
  `notaMinima` decimal(4,2) NOT NULL DEFAULT 5.00,
  `pesoEnMedia` decimal(6,2) NOT NULL DEFAULT 1.00,
  `permiteRecuperacion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tfg_config`
--

INSERT INTO `tfg_config` (`idConfigTFG`, `idConfig`, `habilitado`, `requiereComite`, `requiereDefensa`, `notaMinima`, `pesoEnMedia`, `permiteRecuperacion`) VALUES
(1, 1, 1, 0, 0, 5.00, 1.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tours_completados`
--

CREATE TABLE `tours_completados` (
  `idTourCompletado` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('admin','profesor','secretaria','estudiante','tutor') NOT NULL,
  `tour_key` varchar(50) NOT NULL,
  `completado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tours_completados`
--

INSERT INTO `tours_completados` (`idTourCompletado`, `idUsuario`, `tipoUsuario`, `tour_key`, `completado_en`) VALUES
(1, 1, 'admin', 'primeros_pasos_v1', '2026-07-28 14:50:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

CREATE TABLE `tutores` (
  `idTutor` int(11) NOT NULL,
  `nombreTutor` varchar(150) NOT NULL,
  `emailTutor` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefonoTutor` varchar(20) DEFAULT NULL,
  `dniTutor` varchar(20) DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
  `idEstudiante` int(11) DEFAULT NULL,
  `fechaAltaTutor` datetime DEFAULT current_timestamp(),
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` text DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tutores`
--

INSERT INTO `tutores` (`idTutor`, `nombreTutor`, `emailTutor`, `password`, `telefonoTutor`, `dniTutor`, `fcm_token`, `must_change_password`, `pwd_changed_at`, `idEstudiante`, `fechaAltaTutor`, `mfa_enabled`, `mfa_secret`, `mfa_backup_codes`) VALUES
(1, 'Pedro Silva', 'pedro.silva@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655111222', 'A1234567B', NULL, 0, NULL, 1, '2026-07-27 17:36:17', 0, NULL, NULL),
(2, 'Marta Ortiz', 'marta.ortiz@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655222333', 'B2345678C', NULL, 0, NULL, 2, '2026-07-27 17:36:17', 0, NULL, NULL),
(3, 'Carmen Pastor', 'carmen.pastor@aulapro.com', '$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu', '655333444', 'C3456789D', NULL, 0, NULL, 3, '2026-07-27 17:36:17', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verificaciones_log`
--

CREATE TABLE `verificaciones_log` (
  `id` int(11) NOT NULL,
  `serial_buscado` varchar(40) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `resultado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academic_config`
--
ALTER TABLE `academic_config`
  ADD PRIMARY KEY (`idConfig`),
  ADD KEY `idx_ac_centro_activo` (`idCentro`,`activo`);

--
-- Indices de la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  ADD PRIMARY KEY (`idPeriodo`),
  ADD KEY `idPeriodoRecuperaDe` (`idPeriodoRecuperaDe`),
  ADD KEY `idx_periodo_config_orden` (`idConfig`,`orden`);

--
-- Indices de la tabla `academic_templates`
--
ALTER TABLE `academic_templates`
  ADD PRIMARY KEY (`idPlantilla`);

--
-- Indices de la tabla `account_lockout`
--
ALTER TABLE `account_lockout`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`idAnuncio`),
  ADD KEY `idx_anuncio_fecha` (`fechaAnuncio`);

--
-- Indices de la tabla `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_token` (`token`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`idAsistencia`),
  ADD UNIQUE KEY `idx_asistencia_unica` (`idEstudiante`,`idModulo`,`fecha`),
  ADD KEY `idModulo` (`idModulo`),
  ADD KEY `idProfesor` (`idProfesor`);

--
-- Indices de la tabla `assessment_types`
--
ALTER TABLE `assessment_types`
  ADD PRIMARY KEY (`idTipo`),
  ADD KEY `idx_tipo_config` (`idConfig`);

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`idAula`),
  ADD UNIQUE KEY `uk_aula_planta_numero` (`planta`,`numero`),
  ADD UNIQUE KEY `uk_aula_codigo` (`codigoAula`);

--
-- Indices de la tabla `aula_almacenamiento_ciclo`
--
ALTER TABLE `aula_almacenamiento_ciclo`
  ADD PRIMARY KEY (`idCiclo`);

--
-- Indices de la tabla `aula_analytics`
--
ALTER TABLE `aula_analytics`
  ADD PRIMARY KEY (`idAnalytics`),
  ADD KEY `idx_analytics_usr` (`idUsuario`),
  ADD KEY `idx_analytics_mod` (`idModulo`),
  ADD KEY `idx_analytics_fecha` (`fechaCreacion`);

--
-- Indices de la tabla `aula_archivos`
--
ALTER TABLE `aula_archivos`
  ADD PRIMARY KEY (`idArchivo`),
  ADD KEY `idx_aula_arch_mod` (`idModulo`),
  ADD KEY `idx_aula_arch_carp` (`idCarpeta`),
  ADD KEY `idx_aula_arch_elim` (`eliminado`),
  ADD KEY `fk_aulaarch_prof` (`idProfesor`);

--
-- Indices de la tabla `aula_archivo_accesos`
--
ALTER TABLE `aula_archivo_accesos`
  ADD PRIMARY KEY (`idAcceso`),
  ADD KEY `fk_aulaacc_arch` (`idArchivo`),
  ADD KEY `fk_aulaacc_est` (`idEstudiante`);

--
-- Indices de la tabla `aula_archivo_versiones`
--
ALTER TABLE `aula_archivo_versiones`
  ADD PRIMARY KEY (`idVersion`),
  ADD KEY `fk_aulaver_arch` (`idArchivo`),
  ADD KEY `fk_aulaver_prof` (`idProfesor`);

--
-- Indices de la tabla `aula_asistencia_sesion`
--
ALTER TABLE `aula_asistencia_sesion`
  ADD PRIMARY KEY (`idAsistencia`),
  ADD UNIQUE KEY `uk_sesion_estudiante` (`idSesion`,`idEstudiante`),
  ADD KEY `fk_aulasis_est` (`idEstudiante`);

--
-- Indices de la tabla `aula_carpetas`
--
ALTER TABLE `aula_carpetas`
  ADD PRIMARY KEY (`idCarpeta`),
  ADD KEY `idx_aula_carp_mod` (`idModulo`),
  ADD KEY `idx_aula_carp_padre` (`idPadre`),
  ADD KEY `fk_aulacarp_prof` (`idProfesor`);

--
-- Indices de la tabla `aula_comentarios`
--
ALTER TABLE `aula_comentarios`
  ADD PRIMARY KEY (`idComentario`),
  ADD KEY `fk_aulacomen_entr` (`idEntrega`);

--
-- Indices de la tabla `aula_entregas`
--
ALTER TABLE `aula_entregas`
  ADD PRIMARY KEY (`idEntrega`),
  ADD UNIQUE KEY `uk_aula_entrega` (`idTarea`,`idEstudiante`),
  ADD KEY `idx_aula_entr_est` (`idEstudiante`);

--
-- Indices de la tabla `aula_favoritos`
--
ALTER TABLE `aula_favoritos`
  ADD PRIMARY KEY (`idFavorito`),
  ADD UNIQUE KEY `uk_aulafav` (`idEstudiante`,`idArchivo`),
  ADD KEY `fk_aulafav_arch` (`idArchivo`);

--
-- Indices de la tabla `aula_kanban_estado`
--
ALTER TABLE `aula_kanban_estado`
  ADD PRIMARY KEY (`idEstado`),
  ADD UNIQUE KEY `uk_est_tarea` (`idEstudiante`,`idTarea`),
  ADD KEY `fk_kanban_tarea` (`idTarea`);

--
-- Indices de la tabla `aula_notificaciones`
--
ALTER TABLE `aula_notificaciones`
  ADD PRIMARY KEY (`idNotificacion`),
  ADD KEY `idx_aula_notif_usr` (`idUsuario`,`tipoUsuario`,`leida`);

--
-- Indices de la tabla `aula_retos`
--
ALTER TABLE `aula_retos`
  ADD PRIMARY KEY (`idReto`),
  ADD KEY `idModulo` (`idModulo`),
  ADD KEY `idProfesor` (`idProfesor`);

--
-- Indices de la tabla `aula_retos_entregas`
--
ALTER TABLE `aula_retos_entregas`
  ADD PRIMARY KEY (`idEntrega`),
  ADD KEY `idReto` (`idReto`),
  ADD KEY `idEstudiante` (`idEstudiante`);

--
-- Indices de la tabla `aula_sesiones_vivas`
--
ALTER TABLE `aula_sesiones_vivas`
  ADD PRIMARY KEY (`idSesion`),
  ADD KEY `idx_sesion_mod` (`idModulo`),
  ADD KEY `idx_sesion_prof` (`idProfesor`);

--
-- Indices de la tabla `aula_tareas`
--
ALTER TABLE `aula_tareas`
  ADD PRIMARY KEY (`idTarea`),
  ADD KEY `idx_aula_tarea_mod` (`idModulo`),
  ADD KEY `fk_aulatar_prof` (`idProfesor`);

--
-- Indices de la tabla `aula_versiones_entrega`
--
ALTER TABLE `aula_versiones_entrega`
  ADD PRIMARY KEY (`idVersion`),
  ADD KEY `fk_aulavers_tar` (`idTarea`),
  ADD KEY `fk_aulavers_est` (`idEstudiante`);

--
-- Indices de la tabla `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`idPost`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_publicado` (`publicado`,`fechaPublicacion`);

--
-- Indices de la tabla `boletines_log`
--
ALTER TABLE `boletines_log`
  ADD PRIMARY KEY (`serial`);

--
-- Indices de la tabla `calificaciones_ce`
--
ALTER TABLE `calificaciones_ce`
  ADD PRIMARY KEY (`idCalificacionCE`),
  ADD UNIQUE KEY `idx_estudiante_ce` (`idEstudiante`,`idCE`),
  ADD KEY `idCE` (`idCE`);

--
-- Indices de la tabla `calificaciones_modulos`
--
ALTER TABLE `calificaciones_modulos`
  ADD PRIMARY KEY (`idCalificacion`),
  ADD UNIQUE KEY `uk_est_mod` (`idEstudiante`,`idModulo`),
  ADD KEY `idx_cm_mod` (`idModulo`);

--
-- Indices de la tabla `calificaciones_periodo`
--
ALTER TABLE `calificaciones_periodo`
  ADD PRIMARY KEY (`idCalificacion`),
  ADD UNIQUE KEY `uk_cp_est_mod_periodo_tipo` (`idEstudiante`,`idModulo`,`idPeriodo`,`idTipo`),
  ADD KEY `idModulo` (`idModulo`),
  ADD KEY `idTipo` (`idTipo`),
  ADD KEY `idPeriodo` (`idPeriodo`);

--
-- Indices de la tabla `calificaciones_retos`
--
ALTER TABLE `calificaciones_retos`
  ADD PRIMARY KEY (`idCalificacion`),
  ADD KEY `idx_cal_reto_est` (`idEstudiante`),
  ADD KEY `idx_cal_reto_reto` (`idReto`);

--
-- Indices de la tabla `calificaciones_tfg`
--
ALTER TABLE `calificaciones_tfg`
  ADD PRIMARY KEY (`idCalificacion`),
  ADD UNIQUE KEY `uk_est_tfg` (`idEstudiante`,`convocatoria`);

--
-- Indices de la tabla `categorias_gasto`
--
ALTER TABLE `categorias_gasto`
  ADD PRIMARY KEY (`idCategoria`);

--
-- Indices de la tabla `challenge_config`
--
ALTER TABLE `challenge_config`
  ADD PRIMARY KEY (`idConfigReto`),
  ADD UNIQUE KEY `uk_cc_config` (`idConfig`);

--
-- Indices de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_conv_pair` (`user_a_rol`,`user_a_id`,`user_b_rol`,`user_b_id`),
  ADD KEY `idx_conv_b` (`user_b_rol`,`user_b_id`),
  ADD KEY `idx_conv_last` (`last_message_at`);

--
-- Indices de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg_conv` (`conversacion_id`),
  ADD KEY `idx_msg_fecha` (`fecha`),
  ADD KEY `idx_msg_leido` (`leido`),
  ADD KEY `idx_msg_conv_leido` (`conversacion_id`,`leido`);

--
-- Indices de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  ADD PRIMARY KEY (`idCiclo`),
  ADD KEY `idx_ciclo_nivel` (`idNivel`);

--
-- Indices de la tabla `ciclo_profesor`
--
ALTER TABLE `ciclo_profesor`
  ADD PRIMARY KEY (`idCiclo`,`idProfesor`),
  ADD KEY `fk_rel_prof` (`idProfesor`);

--
-- Indices de la tabla `cola_emails`
--
ALTER TABLE `cola_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cola_estado` (`estado`,`intentos`),
  ADD KEY `idx_cola_creado` (`creado_at`);

--
-- Indices de la tabla `configuracion_centro`
--
ALTER TABLE `configuracion_centro`
  ADD PRIMARY KEY (`idConfig`);

--
-- Indices de la tabla `consentimientos`
--
ALTER TABLE `consentimientos`
  ADD PRIMARY KEY (`idConsentimiento`),
  ADD KEY `idx_consentimiento_estudiante` (`idEstudiante`);

--
-- Indices de la tabla `criterios_evaluacion`
--
ALTER TABLE `criterios_evaluacion`
  ADD PRIMARY KEY (`idCE`),
  ADD KEY `idRA` (`idRA`);

--
-- Indices de la tabla `cursos_academicos`
--
ALTER TABLE `cursos_academicos`
  ADD PRIMARY KEY (`idCurso`),
  ADD UNIQUE KEY `uk_curso_ciclo_orden` (`idCiclo`,`orden`);

--
-- Indices de la tabla `directores`
--
ALTER TABLE `directores`
  ADD PRIMARY KEY (`idDirector`),
  ADD UNIQUE KEY `uk_email_dir` (`emailDirector`),
  ADD UNIQUE KEY `uk_dni_dir` (`dniDirector`);

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`idDispositivo`),
  ADD UNIQUE KEY `uk_serie` (`numeroSerie`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`idEstudiante`),
  ADD UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  ADD UNIQUE KEY `uk_dni_est` (`dniEstudiante`),
  ADD KEY `idx_est_ciclo` (`idCiclo`),
  ADD KEY `idx_est_curso` (`idCurso`),
  ADD KEY `fk_estudiantes_grupo` (`idGrupo`);

--
-- Indices de la tabla `estudiante_tutor`
--
ALTER TABLE `estudiante_tutor`
  ADD PRIMARY KEY (`idEstudiante`,`idTutor`),
  ADD KEY `idx_tutor` (`idTutor`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`idEvento`),
  ADD KEY `idx_evento_fecha` (`fechaEvento`);

--
-- Indices de la tabla `fct`
--
ALTER TABLE `fct`
  ADD PRIMARY KEY (`idFCT`),
  ADD UNIQUE KEY `uq_fct_est_ciclo_fase` (`idEstudiante`,`idCiclo`,`fase`),
  ADD KEY `idx_fct_ciclo` (`idCiclo`),
  ADD KEY `idx_fct_profesor` (`idProfesorTutor`),
  ADD KEY `idx_fct_empresa` (`idEmpresa`);

--
-- Indices de la tabla `fct_diarios`
--
ALTER TABLE `fct_diarios`
  ADD PRIMARY KEY (`idDiario`),
  ADD KEY `idx_fct_diarios_fct` (`idFCT`);

--
-- Indices de la tabla `fp_empresas`
--
ALTER TABLE `fp_empresas`
  ADD PRIMARY KEY (`idEmpresa`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idGasto`),
  ADD KEY `idx_gasto_categoria` (`idCategoria`),
  ADD KEY `idx_gasto_ciclo` (`idCiclo`),
  ADD KEY `idx_gasto_fecha` (`fecha`);

--
-- Indices de la tabla `gastos_categorias`
--
ALTER TABLE `gastos_categorias`
  ADD PRIMARY KEY (`idCategoria`);

--
-- Indices de la tabla `grading_policies`
--
ALTER TABLE `grading_policies`
  ADD PRIMARY KEY (`idPolitica`),
  ADD UNIQUE KEY `uk_gp_config` (`idConfig`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`idGrupo`),
  ADD KEY `idx_grupos_ciclo` (`idCiclo`);

--
-- Indices de la tabla `historial_secretarias`
--
ALTER TABLE `historial_secretarias`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `idSecretaria` (`idSecretaria`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`idHorario`),
  ADD UNIQUE KEY `uk_horario_celda` (`idCiclo`,`diaSemana`,`horaInicio`),
  ADD UNIQUE KEY `uk_horario_aula` (`idAula`,`diaSemana`,`horaInicio`),
  ADD UNIQUE KEY `uk_horario_profesor` (`idProfesor`,`diaSemana`,`horaInicio`),
  ADD KEY `indice_horario_modulo` (`idModulo`);

--
-- Indices de la tabla `horario_franjas`
--
ALTER TABLE `horario_franjas`
  ADD PRIMARY KEY (`idFranja`),
  ADD UNIQUE KEY `uq_ciclo_inicio` (`idCiclo`,`horaInicio`);

--
-- Indices de la tabla `internship_config`
--
ALTER TABLE `internship_config`
  ADD PRIMARY KEY (`idConfigFCT`),
  ADD UNIQUE KEY `uk_ic_config` (`idConfig`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`idInventario`);

--
-- Indices de la tabla `justificaciones_falta`
--
ALTER TABLE `justificaciones_falta`
  ADD PRIMARY KEY (`idJustificacion`),
  ADD KEY `idx_asistencia` (`idAsistencia`),
  ADD KEY `idx_estudiante` (`idEstudiante`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `landing_ciclos`
--
ALTER TABLE `landing_ciclos`
  ADD PRIMARY KEY (`idLandingCiclo`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_publicado` (`publicado`,`orden`);

--
-- Indices de la tabla `landing_config`
--
ALTER TABLE `landing_config`
  ADD PRIMARY KEY (`idLanding`);

--
-- Indices de la tabla `landing_secciones`
--
ALTER TABLE `landing_secciones`
  ADD PRIMARY KEY (`idSeccion`),
  ADD KEY `idx_landing_version_orden` (`version`,`orden`);

--
-- Indices de la tabla `login_intentos`
--
ALTER TABLE `login_intentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ip` (`ip`);

--
-- Indices de la tabla `log_acciones`
--
ALTER TABLE `log_acciones`
  ADD PRIMARY KEY (`idLog`),
  ADD KEY `idx_log_admin` (`idAdmin`),
  ADD KEY `idx_log_fecha` (`fecha`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`idModulo`),
  ADD KEY `idx_modulo_ciclo` (`idCiclo`),
  ADD KEY `idx_modulo_curso` (`idCurso`);

--
-- Indices de la tabla `modulo_profesor`
--
ALTER TABLE `modulo_profesor`
  ADD PRIMARY KEY (`idModulo`,`idProfesor`),
  ADD UNIQUE KEY `idx_unico_modulo` (`idModulo`),
  ADD KEY `fk_relm_prof` (`idProfesor`);

--
-- Indices de la tabla `modulo_reto`
--
ALTER TABLE `modulo_reto`
  ADD PRIMARY KEY (`idModulo`,`idReto`),
  ADD KEY `fk_mr_reto` (`idReto`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`idNivel`);

--
-- Indices de la tabla `notificaciones_recordatorios`
--
ALTER TABLE `notificaciones_recordatorios`
  ADD PRIMARY KEY (`idNotificacionRecordatorio`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idPago`),
  ADD KEY `idx_pago_est` (`idEstudiante`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_token` (`token`),
  ADD KEY `idx_pr_email` (`email`),
  ADD KEY `idx_pr_expires` (`expires_at`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`idPrestamo`),
  ADD KEY `idx_pres_est` (`idEstudiante`),
  ADD KEY `idx_pres_serie` (`numeroSerie`);

--
-- Indices de la tabla `pre_matriculas`
--
ALTER TABLE `pre_matriculas`
  ADD PRIMARY KEY (`idPreMatricula`),
  ADD KEY `idx_pm_ciclo` (`idCiclo`),
  ADD KEY `idx_pm_estado` (`estado`),
  ADD KEY `idx_pm_dni` (`dni`);

--
-- Indices de la tabla `pre_matricula_archivos`
--
ALTER TABLE `pre_matricula_archivos`
  ADD PRIMARY KEY (`idArchivo`),
  ADD KEY `idx_pma_prematricula` (`idPreMatricula`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`idProfesor`),
  ADD UNIQUE KEY `uk_email_prof` (`emailProfesor`),
  ADD KEY `idx_prof_dni` (`dniProfesor`);

--
-- Indices de la tabla `promotion_rules`
--
ALTER TABLE `promotion_rules`
  ADD PRIMARY KEY (`idRegla`),
  ADD UNIQUE KEY `uk_pr_config` (`idConfig`);

--
-- Indices de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_scope_ip` (`scope`,`ip`);

--
-- Indices de la tabla `reclamaciones`
--
ALTER TABLE `reclamaciones`
  ADD PRIMARY KEY (`idReclamacion`),
  ADD KEY `idx_rec_est` (`idEstudiante`),
  ADD KEY `idx_rec_prof` (`idProfesor`),
  ADD KEY `fk_reclamaciones_parent` (`id_parent`);

--
-- Indices de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  ADD PRIMARY KEY (`idRecordatorio`),
  ADD UNIQUE KEY `uk_evento_tipo` (`idEvento`,`tipo_recordatorio`);

--
-- Indices de la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  ADD PRIMARY KEY (`idRA`),
  ADD KEY `idModulo` (`idModulo`),
  ADD KEY `idx_ra_tipo` (`idTipo`);

--
-- Indices de la tabla `retos`
--
ALTER TABLE `retos`
  ADD PRIMARY KEY (`idReto`);

--
-- Indices de la tabla `reto_archivos`
--
ALTER TABLE `reto_archivos`
  ADD PRIMARY KEY (`idArchivo`),
  ADD KEY `idReto` (`idReto`);

--
-- Indices de la tabla `rgpd_eliminaciones`
--
ALTER TABLE `rgpd_eliminaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rgpd_solicitudes`
--
ALTER TABLE `rgpd_solicitudes`
  ADD PRIMARY KEY (`idSolicitud`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `secretarias`
--
ALTER TABLE `secretarias`
  ADD PRIMARY KEY (`idSecretaria`),
  ADD UNIQUE KEY `uq_email_sec` (`emailSecretaria`);

--
-- Indices de la tabla `tfg_config`
--
ALTER TABLE `tfg_config`
  ADD PRIMARY KEY (`idConfigTFG`),
  ADD UNIQUE KEY `uk_tc_config` (`idConfig`);

--
-- Indices de la tabla `tours_completados`
--
ALTER TABLE `tours_completados`
  ADD PRIMARY KEY (`idTourCompletado`),
  ADD UNIQUE KEY `uniq_usuario_tour` (`idUsuario`,`tipoUsuario`,`tour_key`);

--
-- Indices de la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD PRIMARY KEY (`idTutor`);

--
-- Indices de la tabla `verificaciones_log`
--
ALTER TABLE `verificaciones_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_verif_ip_fecha` (`ip`,`created_at`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `academic_config`
--
ALTER TABLE `academic_config`
  MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  MODIFY `idPeriodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `academic_templates`
--
ALTER TABLE `academic_templates`
  MODIFY `idPlantilla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `idAnuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `assessment_types`
--
ALTER TABLE `assessment_types`
  MODIFY `idTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `idAula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aula_analytics`
--
ALTER TABLE `aula_analytics`
  MODIFY `idAnalytics` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_archivos`
--
ALTER TABLE `aula_archivos`
  MODIFY `idArchivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `aula_archivo_accesos`
--
ALTER TABLE `aula_archivo_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `aula_archivo_versiones`
--
ALTER TABLE `aula_archivo_versiones`
  MODIFY `idVersion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_asistencia_sesion`
--
ALTER TABLE `aula_asistencia_sesion`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_carpetas`
--
ALTER TABLE `aula_carpetas`
  MODIFY `idCarpeta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `aula_comentarios`
--
ALTER TABLE `aula_comentarios`
  MODIFY `idComentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_entregas`
--
ALTER TABLE `aula_entregas`
  MODIFY `idEntrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aula_favoritos`
--
ALTER TABLE `aula_favoritos`
  MODIFY `idFavorito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_kanban_estado`
--
ALTER TABLE `aula_kanban_estado`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_notificaciones`
--
ALTER TABLE `aula_notificaciones`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `aula_retos`
--
ALTER TABLE `aula_retos`
  MODIFY `idReto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_retos_entregas`
--
ALTER TABLE `aula_retos_entregas`
  MODIFY `idEntrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aula_sesiones_vivas`
--
ALTER TABLE `aula_sesiones_vivas`
  MODIFY `idSesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `aula_tareas`
--
ALTER TABLE `aula_tareas`
  MODIFY `idTarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aula_versiones_entrega`
--
ALTER TABLE `aula_versiones_entrega`
  MODIFY `idVersion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `idPost` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `calificaciones_ce`
--
ALTER TABLE `calificaciones_ce`
  MODIFY `idCalificacionCE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `calificaciones_modulos`
--
ALTER TABLE `calificaciones_modulos`
  MODIFY `idCalificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `calificaciones_periodo`
--
ALTER TABLE `calificaciones_periodo`
  MODIFY `idCalificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calificaciones_retos`
--
ALTER TABLE `calificaciones_retos`
  MODIFY `idCalificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calificaciones_tfg`
--
ALTER TABLE `calificaciones_tfg`
  MODIFY `idCalificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_gasto`
--
ALTER TABLE `categorias_gasto`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `challenge_config`
--
ALTER TABLE `challenge_config`
  MODIFY `idConfigReto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  MODIFY `idCiclo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cola_emails`
--
ALTER TABLE `cola_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consentimientos`
--
ALTER TABLE `consentimientos`
  MODIFY `idConsentimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `criterios_evaluacion`
--
ALTER TABLE `criterios_evaluacion`
  MODIFY `idCE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cursos_academicos`
--
ALTER TABLE `cursos_academicos`
  MODIFY `idCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `idDirector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `idDispositivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `idEvento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `fct`
--
ALTER TABLE `fct`
  MODIFY `idFCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `fct_diarios`
--
ALTER TABLE `fct_diarios`
  MODIFY `idDiario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `fp_empresas`
--
ALTER TABLE `fp_empresas`
  MODIFY `idEmpresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `gastos_categorias`
--
ALTER TABLE `gastos_categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `grading_policies`
--
ALTER TABLE `grading_policies`
  MODIFY `idPolitica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_secretarias`
--
ALTER TABLE `historial_secretarias`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `idHorario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT de la tabla `horario_franjas`
--
ALTER TABLE `horario_franjas`
  MODIFY `idFranja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `internship_config`
--
ALTER TABLE `internship_config`
  MODIFY `idConfigFCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `idInventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `justificaciones_falta`
--
ALTER TABLE `justificaciones_falta`
  MODIFY `idJustificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `landing_ciclos`
--
ALTER TABLE `landing_ciclos`
  MODIFY `idLandingCiclo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `landing_secciones`
--
ALTER TABLE `landing_secciones`
  MODIFY `idSeccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `login_intentos`
--
ALTER TABLE `login_intentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_acciones`
--
ALTER TABLE `log_acciones`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `idModulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `idNivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notificaciones_recordatorios`
--
ALTER TABLE `notificaciones_recordatorios`
  MODIFY `idNotificacionRecordatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `idPrestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pre_matriculas`
--
ALTER TABLE `pre_matriculas`
  MODIFY `idPreMatricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pre_matricula_archivos`
--
ALTER TABLE `pre_matricula_archivos`
  MODIFY `idArchivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `idProfesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `promotion_rules`
--
ALTER TABLE `promotion_rules`
  MODIFY `idRegla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `reclamaciones`
--
ALTER TABLE `reclamaciones`
  MODIFY `idReclamacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  MODIFY `idRecordatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  MODIFY `idRA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `retos`
--
ALTER TABLE `retos`
  MODIFY `idReto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reto_archivos`
--
ALTER TABLE `reto_archivos`
  MODIFY `idArchivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rgpd_eliminaciones`
--
ALTER TABLE `rgpd_eliminaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rgpd_solicitudes`
--
ALTER TABLE `rgpd_solicitudes`
  MODIFY `idSolicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `secretarias`
--
ALTER TABLE `secretarias`
  MODIFY `idSecretaria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tfg_config`
--
ALTER TABLE `tfg_config`
  MODIFY `idConfigTFG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tours_completados`
--
ALTER TABLE `tours_completados`
  MODIFY `idTourCompletado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tutores`
--
ALTER TABLE `tutores`
  MODIFY `idTutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `verificaciones_log`
--
ALTER TABLE `verificaciones_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  ADD CONSTRAINT `academic_periods_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_periods_ibfk_2` FOREIGN KEY (`idPeriodoRecuperaDe`) REFERENCES `academic_periods` (`idPeriodo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `assessment_types`
--
ALTER TABLE `assessment_types`
  ADD CONSTRAINT `assessment_types_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_almacenamiento_ciclo`
--
ALTER TABLE `aula_almacenamiento_ciclo`
  ADD CONSTRAINT `fk_aulaalm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_archivos`
--
ALTER TABLE `aula_archivos`
  ADD CONSTRAINT `fk_aulaarch_carp` FOREIGN KEY (`idCarpeta`) REFERENCES `aula_carpetas` (`idCarpeta`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_aulaarch_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulaarch_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_archivo_accesos`
--
ALTER TABLE `aula_archivo_accesos`
  ADD CONSTRAINT `fk_aulaacc_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulaacc_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_archivo_versiones`
--
ALTER TABLE `aula_archivo_versiones`
  ADD CONSTRAINT `fk_aulaver_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulaver_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_asistencia_sesion`
--
ALTER TABLE `aula_asistencia_sesion`
  ADD CONSTRAINT `fk_aulasis_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulasis_sesion` FOREIGN KEY (`idSesion`) REFERENCES `aula_sesiones_vivas` (`idSesion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_carpetas`
--
ALTER TABLE `aula_carpetas`
  ADD CONSTRAINT `fk_aulacarp_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulacarp_padre` FOREIGN KEY (`idPadre`) REFERENCES `aula_carpetas` (`idCarpeta`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulacarp_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_comentarios`
--
ALTER TABLE `aula_comentarios`
  ADD CONSTRAINT `fk_aulacomen_entr` FOREIGN KEY (`idEntrega`) REFERENCES `aula_entregas` (`idEntrega`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_entregas`
--
ALTER TABLE `aula_entregas`
  ADD CONSTRAINT `fk_aulaentr_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulaentr_tar` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_favoritos`
--
ALTER TABLE `aula_favoritos`
  ADD CONSTRAINT `fk_aulafav_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulafav_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_kanban_estado`
--
ALTER TABLE `aula_kanban_estado`
  ADD CONSTRAINT `fk_kanban_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kanban_tarea` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_retos`
--
ALTER TABLE `aula_retos`
  ADD CONSTRAINT `aula_retos_ibfk_1` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `aula_retos_ibfk_2` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_retos_entregas`
--
ALTER TABLE `aula_retos_entregas`
  ADD CONSTRAINT `aula_retos_entregas_ibfk_1` FOREIGN KEY (`idReto`) REFERENCES `aula_retos` (`idReto`) ON DELETE CASCADE,
  ADD CONSTRAINT `aula_retos_entregas_ibfk_2` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_sesiones_vivas`
--
ALTER TABLE `aula_sesiones_vivas`
  ADD CONSTRAINT `fk_aulasesion_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulasesion_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_tareas`
--
ALTER TABLE `aula_tareas`
  ADD CONSTRAINT `fk_aulatar_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulatar_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aula_versiones_entrega`
--
ALTER TABLE `aula_versiones_entrega`
  ADD CONSTRAINT `fk_aulavers_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aulavers_tar` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_ce`
--
ALTER TABLE `calificaciones_ce`
  ADD CONSTRAINT `calificaciones_ce_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_ce_ibfk_2` FOREIGN KEY (`idCE`) REFERENCES `criterios_evaluacion` (`idCE`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_modulos`
--
ALTER TABLE `calificaciones_modulos`
  ADD CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_periodo`
--
ALTER TABLE `calificaciones_periodo`
  ADD CONSTRAINT `calificaciones_periodo_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_periodo_ibfk_2` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_periodo_ibfk_4` FOREIGN KEY (`idTipo`) REFERENCES `assessment_types` (`idTipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_periodo_ibfk_5` FOREIGN KEY (`idPeriodo`) REFERENCES `academic_periods` (`idPeriodo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_retos`
--
ALTER TABLE `calificaciones_retos`
  ADD CONSTRAINT `fk_cr_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_tfg`
--
ALTER TABLE `calificaciones_tfg`
  ADD CONSTRAINT `fk_ctfg_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `challenge_config`
--
ALTER TABLE `challenge_config`
  ADD CONSTRAINT `challenge_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;

--
-- Filtros para la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ciclos`
--
ALTER TABLE `ciclos`
  ADD CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`);

--
-- Filtros para la tabla `ciclo_profesor`
--
ALTER TABLE `ciclo_profesor`
  ADD CONSTRAINT `fk_rel_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rel_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consentimientos`
--
ALTER TABLE `consentimientos`
  ADD CONSTRAINT `fk_consentimiento_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `criterios_evaluacion`
--
ALTER TABLE `criterios_evaluacion`
  ADD CONSTRAINT `criterios_evaluacion_ibfk_1` FOREIGN KEY (`idRA`) REFERENCES `resultados_aprendizaje` (`idRA`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cursos_academicos`
--
ALTER TABLE `cursos_academicos`
  ADD CONSTRAINT `cursos_academicos_ibfk_1` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_estudiantes_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `estudiante_tutor`
--
ALTER TABLE `estudiante_tutor`
  ADD CONSTRAINT `fk_et_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_et_tut` FOREIGN KEY (`idTutor`) REFERENCES `tutores` (`idTutor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fct`
--
ALTER TABLE `fct`
  ADD CONSTRAINT `fk_fct_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fct_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fct_prof` FOREIGN KEY (`idProfesorTutor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL;

--
-- Filtros para la tabla `fct_diarios`
--
ALTER TABLE `fct_diarios`
  ADD CONSTRAINT `fk_fct_diarios_fct` FOREIGN KEY (`idFCT`) REFERENCES `fct` (`idFCT`) ON DELETE CASCADE;

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_gasto_cat` FOREIGN KEY (`idCategoria`) REFERENCES `categorias_gasto` (`idCategoria`),
  ADD CONSTRAINT `fk_gasto_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `grading_policies`
--
ALTER TABLE `grading_policies`
  ADD CONSTRAINT `grading_policies_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupos_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_secretarias`
--
ALTER TABLE `historial_secretarias`
  ADD CONSTRAINT `historial_secretarias_ibfk_1` FOREIGN KEY (`idSecretaria`) REFERENCES `secretarias` (`idSecretaria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `fk_horario_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_horario_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_horario_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_horario_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL;

--
-- Filtros para la tabla `horario_franjas`
--
ALTER TABLE `horario_franjas`
  ADD CONSTRAINT `horario_franjas_ibfk_1` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `internship_config`
--
ALTER TABLE `internship_config`
  ADD CONSTRAINT `internship_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;

--
-- Filtros para la tabla `log_acciones`
--
ALTER TABLE `log_acciones`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`idAdmin`) REFERENCES `directores` (`idDirector`) ON DELETE SET NULL;

--
-- Filtros para la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `modulo_profesor`
--
ALTER TABLE `modulo_profesor`
  ADD CONSTRAINT `fk_relm_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_relm_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `modulo_reto`
--
ALTER TABLE `modulo_reto`
  ADD CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_pres_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pre_matriculas`
--
ALTER TABLE `pre_matriculas`
  ADD CONSTRAINT `fk_pm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pre_matricula_archivos`
--
ALTER TABLE `pre_matricula_archivos`
  ADD CONSTRAINT `fk_pma_pm` FOREIGN KEY (`idPreMatricula`) REFERENCES `pre_matriculas` (`idPreMatricula`) ON DELETE CASCADE;

--
-- Filtros para la tabla `promotion_rules`
--
ALTER TABLE `promotion_rules`
  ADD CONSTRAINT `promotion_rules_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reclamaciones`
--
ALTER TABLE `reclamaciones`
  ADD CONSTRAINT `fk_rec_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rec_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reclamaciones_parent` FOREIGN KEY (`id_parent`) REFERENCES `reclamaciones` (`idReclamacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  ADD CONSTRAINT `resultados_aprendizaje_ibfk_1` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tfg_config`
--
ALTER TABLE `tfg_config`
  ADD CONSTRAINT `tfg_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
