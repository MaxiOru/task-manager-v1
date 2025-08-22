-- --------------------------------------------------------
-- Dump de la base de datos: task_manager_v1
-- Generado para XAMPP/MySQL
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `task_manager_v1`
--

CREATE DATABASE IF NOT EXISTS `task_manager_v1` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `task_manager_v1`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('jefe','normal') NOT NULL DEFAULT 'normal',
  `approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
-- Contraseña para ambos usuarios: "password"
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `approved`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jefe', 1),
(2, 'usuario_demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'normal', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pendiente','en progreso','completada') NOT NULL DEFAULT 'pendiente',
  `due_date` date NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `status`, `due_date`, `approved`, `assigned_to`) VALUES
(1, 2, 'Tarea de ejemplo', 'Esta es una tarea de demostración para probar el sistema', 'pendiente', '2025-09-01', 0, NULL),
(2, 2, 'Revisar documentación', 'Revisar toda la documentación del proyecto antes de la entrega', 'en progreso', '2025-08-30', 0, NULL),
(3, 2, 'Preparar presentación', 'Crear slides para la presentación del proyecto', 'completada', '2025-08-25', 0, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ========================================
-- INFORMACIÓN PARA DESARROLLADORES
-- ========================================
-- 
-- CREDENCIALES DE PRUEBA:
-- - Admin (jefe): usuario = "admin", contraseña = "password"  
-- - Usuario normal: usuario = "usuario_demo", contraseña = "password"
--
-- CONFIGURACIÓN RECOMENDADA (config/database.php):
-- - host: "localhost"
-- - db_name: "task_manager_v1"  
-- - username: "root"
-- - password: "" (vacío para XAMPP por defecto)
--
-- TAREAS INCLUIDAS:
-- - 3 tareas de ejemplo con diferentes estados
-- - Todas asignadas al usuario_demo para pruebas
--