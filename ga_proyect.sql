-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2026 a las 07:04:37
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
-- Base de datos: `ga_proyect`
--
CREATE DATABASE IF NOT EXISTS `ga_proyect` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ga_proyect`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desafios`
--

CREATE TABLE `desafios` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion_es` text DEFAULT NULL,
  `descripcion_en` text DEFAULT NULL,
  `descripcion` text NOT NULL,
  `fecha_desafio` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `desafios`
--

INSERT INTO `desafios` (`id`, `titulo`, `descripcion_es`, `descripcion_en`, `descripcion`, `fecha_desafio`) VALUES
(1, 'Desafío Diario', 'realizar el dibujo de dos conejos entrando en una madriguera, situada debajo de un árbol en la pradera.', 'make a drawing of two rabbits entering a borrow, located under a tree in the meadow.', 'realizar el dibujo de dos conejos entrando en una madriguera, situada debajo de un árbol en la pradera.', '0000-00-00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `desafio_id` int(11) DEFAULT NULL,
  `titulo` varchar(100) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `usuario_id`, `desafio_id`, `titulo`, `imagen_url`, `fecha_creacion`) VALUES
(7, 7, 1, 'sona male', '1788364698_WhatsApp Image 2026-09-02 at 12.54.23.jpeg', '2026-09-02 15:58:18'),
(8, 7, 1, 'viperion re-design', '1788364739_WhatsApp Image 2026-09-02 at 12.55.06.jpeg', '2026-09-02 15:58:59'),
(9, 7, 1, 'couple >///<', '1788364779_WhatsApp Image 2026-09-02 at 12.55.07.jpeg', '2026-09-02 15:59:39'),
(10, 7, 1, 'boy clothes', '1788364816_WhatsApp Image 2026-09-02 at 12.55.07 (1).jpeg', '2026-09-02 16:00:16'),
(11, 7, 1, 'deadpool maid', '1788364967_WhatsApp Image 2026-09-02 at 13.02.12.jpeg', '2026-09-02 16:02:47'),
(12, 7, 1, 'ballerina', '1788364987_WhatsApp Image 2026-09-02 at 13.02.12 (1).jpeg', '2026-09-02 16:03:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro_paginas`
--

CREATE TABLE `libro_paginas` (
  `id` int(11) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `imagen_es` varchar(255) DEFAULT NULL,
  `imagen_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libro_paginas`
--

INSERT INTO `libro_paginas` (`id`, `orden`, `imagen_es`, `imagen_en`) VALUES
(1, 1, '../uploads/libro/page1_esp.jpeg', '../uploads/libro/page1_eng.jpeg'),
(2, 2, '../uploads/libro/page2_esp.jpeg', '../uploads/libro/page2_eng.jpeg'),
(11, 3, '../uploads/libro/page3_esp.jpeg', '../uploads/libro/page3_eng.jpeg'),
(12, 4, '../uploads/libro/page4_esp.jpeg', '../uploads/libro/page4_eng.jpeg'),
(13, 5, '../uploads/libro/page5_esp.jpeg', '../uploads/libro/page5_eng.jpeg'),
(14, 6, '../uploads/libro/page6_esp.jpeg', '../uploads/libro/page6_eng.jpeg'),
(15, 7, '../uploads/libro/page7_esp.jpeg', '../uploads/libro/page7_eng.jpeg'),
(16, 8, '../uploads/libro/page8_esp.jpeg', '../uploads/libro/page8_eng.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `pregunta` text NOT NULL,
  `pregunta_es` text DEFAULT NULL,
  `pregunta_en` text DEFAULT NULL,
  `respuesta_correcta` text NOT NULL,
  `puntos_recompensa` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `pregunta`, `pregunta_es`, `pregunta_en`, `respuesta_correcta`, `puntos_recompensa`) VALUES
(1, 'cómo se deben situar las orejas a los lados de la cabeza?', 'cómo se deben situar las orejas a los lados de la cabeza?', 'how should the ears be positioned on the sides of the head?', 'desde la mitad de los ojos hasta el final de la nariz, from the middle of the eyes to the end of the nose ', 10),
(2, 'cuantas cabezas conforman el cuerpo humano?', 'cuantas cabezas conforman el cuerpo humano?', 'How many heads make up the human body?', '7,siete, seven', 10),
(3, 'cómo se denomina a la línea curva que se utiliza cómo guía para poses dinámicas?', 'cómo se denomina a la línea curva que se utiliza cómo guía para poses dinámicas?', 'What is the curved line called that is used as a guide for dynamic poses?', 'línea de acción, line of action', 10),
(4, 'formas en las que se pueden aplicar los colores según la teoría del color:mencionar una de las formas en las que se pueden aplicar los colores según la teoría del color', 'mencionar una de las formas en las que se pueden aplicar los colores según la teoría del color', 'mention one of the ways colors can be applied according to color theory', 'complementaria,análoga,triadica,tetrádica,complementary, analog, triadic, tetradic', 15),
(5, 'qué tanta separación debe haber entre los ojos de un humano?', 'qué tanta separación debe haber entre los ojos de un humano?', 'how much space should there be between a human\'s eyes?', 'un tercer ojo, a third eye', 10),
(6, 'menciona una de las dos perspectivas que se suelen utilizar a la hora de dibujar', 'menciona una de las dos perspectivas que se suelen utilizar a la hora de dibujar', 'mention one of the two perspectives that are usually used when drawing', 'caballera, isométrica, perspectiva caballera, perspectiva isométrica, isometric perspective, isometric, cavalier perspective, cavalier', 10),
(7, 'cuál es la forma más recomendable  a la hora de dibujar animales?', 'cuál es la forma más recomendable  a la hora de dibujar animales?', 'what\'s the recommended way to draw animals?', 'usar formas simples, formas simples, use simple shapes, simple shapes', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_respondidas`
--

CREATE TABLE `tareas_respondidas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `es_correcta` tinyint(1) DEFAULT 0,
  `fecha_respuesta` timestamp NOT NULL DEFAULT current_timestamp(),
  `intentos` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas_respondidas`
--

INSERT INTO `tareas_respondidas` (`id`, `usuario_id`, `tarea_id`, `es_correcta`, `fecha_respuesta`, `intentos`) VALUES
(8, 8, 1, 1, '2026-09-02 16:16:18', 1),
(9, 8, 2, 1, '2026-09-02 16:16:28', 1),
(10, 8, 3, 1, '2026-09-02 16:16:49', 1),
(11, 8, 4, 1, '2026-09-02 16:18:20', 1),
(12, 7, 5, 1, '2026-09-03 19:57:15', 1),
(13, 7, 2, 1, '2026-09-03 20:03:23', 1),
(14, 7, 6, 1, '2026-09-03 20:41:37', 1),
(15, 7, 3, 1, '2026-09-03 21:02:24', 1),
(16, 7, 4, 1, '2026-09-03 21:03:26', 1),
(17, 7, 1, 1, '2026-09-06 01:33:56', 1),
(18, 12, 2, 1, '2026-09-29 15:57:36', 1),
(19, 7, 7, 1, '2026-09-29 18:21:51', 1),
(20, 12, 6, 1, '2026-10-10 19:52:04', 1),
(21, 12, 1, 0, '2026-10-12 02:24:22', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `puntos` int(11) DEFAULT 0,
  `nivel` int(11) DEFAULT 1,
  `pais` varchar(100) DEFAULT NULL,
  `idioma` varchar(50) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` varchar(20) DEFAULT 'usuario',
  `bio` varchar(255) DEFAULT 'Sin biografía aún.',
  `suscrito` tinyint(1) DEFAULT 0,
  `tour_completado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `edad`, `email`, `password`, `puntos`, `nivel`, `pais`, `idioma`, `foto_perfil`, `fecha_registro`, `rol`, `bio`, `suscrito`, `tour_completado`) VALUES
(7, 'yuk', 18, 'bangtan@gmail.com', '$2y$10$5LvtgupgDSjnE0tpJ3h82OYDjJN.wj03tQuMBpUoFNOMUaFZwP58G', 10789, 0, 'Argentina', 'en', '1788199915_6a95c3ebdd85c.jpg', '2026-08-31 18:11:55', 'admin', 'creadora de éste bebito |\r\n @koala.yoon94 en ig publicando mi arte |\r\ncomisiones abiertas |\r\n made in arg |', 0, 1),
(8, 'Vequi Holmes', 49, 'vequiholmes@gmail.com', '$2y$10$sP5JIfvWTU2h/eH9C8diBeINRQ9mXih0PkjygWkUw0ptYj/V4gJaS', 45, 0, 'Argentina', 'es', '1788366474_6a984e8a165d7.jpg', '2026-09-02 16:14:59', 'usuario', 'Sin biografía aún.', 0, 1),
(12, 'blue_ocean', 18, 'blackswan@gmail.com', '$2y$10$Pp4EWEmAcS0iewcUKW.kpO/rTXjEkj.nTVSCt6FGQ0/ytWdWbE8li', 40, 0, 'Argentina', 'es', '1790696604_6abbdc9c60e75.jpg', '2026-09-29 15:28:07', 'usuario', '⭐⭐⭐⭐⭐⭐⭐', 1, 1),
(15, 'spunky_noir', 18, 'spunkynoir@gmail.com', '$2y$10$Vq5oWdjbYWCFKkqXRyeiu.o9QG9B64jaNOIHgsgNmnPCBIpWDW.B6', 0, 0, 'Argentina', 'es', NULL, '2026-10-14 00:01:38', 'usuario', 'Sin biografía aún.', 0, 0),
(16, 'random guy', 18, 'randomguy@gmail.com', '$2y$10$2pXtouoG6b85DRFPiuTXUurGrTi84Xx01HrD/pWOKHfMnHY3JGa8G', 0, 0, 'Argentina', 'es', NULL, '2026-10-21 04:46:42', 'usuario', 'Sin biografía aún.', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos_explicativos`
--

CREATE TABLE `videos_explicativos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `videos_explicativos`
--

INSERT INTO `videos_explicativos` (`id`, `titulo`, `url`, `fecha_creacion`) VALUES
(1, 'flancito', '../uploads/videos/video_6a9dc4e1edfb2.mp4', '2026-09-06 16:54:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `desafios`
--
ALTER TABLE `desafios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha_desafio` (`fecha_desafio`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `desafio_id` (`desafio_id`);

--
-- Indices de la tabla `libro_paginas`
--
ALTER TABLE `libro_paginas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas_respondidas`
--
ALTER TABLE `tareas_respondidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `tarea_id` (`tarea_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `videos_explicativos`
--
ALTER TABLE `videos_explicativos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `desafios`
--
ALTER TABLE `desafios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `libro_paginas`
--
ALTER TABLE `libro_paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tareas_respondidas`
--
ALTER TABLE `tareas_respondidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `videos_explicativos`
--
ALTER TABLE `videos_explicativos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `galeria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `galeria_ibfk_2` FOREIGN KEY (`desafio_id`) REFERENCES `desafios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tareas_respondidas`
--
ALTER TABLE `tareas_respondidas`
  ADD CONSTRAINT `tareas_respondidas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tareas_respondidas_ibfk_2` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
