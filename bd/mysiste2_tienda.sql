-- -------------------------------------------------------------
-- TablePlus 6.6.9(633)
--
-- https://tableplus.com/
--
-- Database: mysiste2_tienda
-- Generation Time: 2025-08-17 16:07:02.4490
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `adm_login`;
CREATE TABLE `adm_login` (
  `adm_id` int NOT NULL AUTO_INCREMENT,
  `adm_nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adm_passw` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adm_est` int DEFAULT '1',
  `adm_login` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adm_email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`adm_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `caracteristicas_productos`;
CREATE TABLE `caracteristicas_productos` (
  `id_caracteristica` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aplicacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `software_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sysytem` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `columna1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_highway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `off_highway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_standar` decimal(10,2) DEFAULT NULL,
  `precio_medium` decimal(10,2) DEFAULT NULL,
  `precio_min` decimal(10,2) DEFAULT NULL,
  `tiempo_instalacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano_archivo_gb` decimal(5,2) DEFAULT NULL,
  `espacio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gb` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `computer_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `complejidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valoracion_cliente` int DEFAULT NULL,
  `database_language` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `implicaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description_en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `supported` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `compatible_interface` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `powerapps_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `top_sell` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_caracteristica`) USING BTREE,
  KEY `fk_producto` (`id_producto`) USING BTREE,
  CONSTRAINT `fk_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `carrito`;
CREATE TABLE `carrito` (
  `id_carrito` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_carrito`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `carrito_items`;
CREATE TABLE `carrito_items` (
  `id_item` int NOT NULL AUTO_INCREMENT,
  `id_carrito` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_item`) USING BTREE,
  KEY `id_carrito` (`id_carrito`) USING BTREE,
  KEY `id_producto` (`id_producto`) USING BTREE,
  CONSTRAINT `carrito_items_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `carrito_items_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_categoria`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cupones`;
CREATE TABLE `cupones` (
  `id_cupon` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('porcentaje','fijo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_cupon`) USING BTREE,
  UNIQUE KEY `codigo` (`codigo`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE `favoritos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_producto` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_producto`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `galeria_productos`;
CREATE TABLE `galeria_productos` (
  `gal_id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int DEFAULT NULL,
  `gal_img` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gal_est` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `gal_fech` date DEFAULT NULL,
  PRIMARY KEY (`gal_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mc_nomb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_mc_nomb` (`mc_nomb`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `marquesina`;
CREATE TABLE `marquesina` (
  `mq_id` int NOT NULL AUTO_INCREMENT,
  `mq_tit` varchar(350) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mq_url` varchar(350) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mq_est` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`mq_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orden_detalles`;
CREATE TABLE `orden_detalles` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_orden` int NOT NULL,
  `id_producto` int NOT NULL,
  `nombre_producto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS ((`precio_unitario` * `cantidad`)) STORED,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  KEY `idx_orden` (`id_orden`) USING BTREE,
  KEY `idx_producto` (`id_producto`) USING BTREE,
  CONSTRAINT `fk_detalles_orden` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalles_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orden_items`;
CREATE TABLE `orden_items` (
  `id_item` int NOT NULL AUTO_INCREMENT,
  `id_orden` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_item`) USING BTREE,
  KEY `id_orden` (`id_orden`) USING BTREE,
  KEY `id_producto` (`id_producto`) USING BTREE,
  CONSTRAINT `orden_items_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `orden_items_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ordenes`;
CREATE TABLE `ordenes` (
  `id_orden` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('cotizacion','paypal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','pagado','enviado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `codigo_cupon` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `invoice` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_orden`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc_ampliado` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  PRIMARY KEY (`id_producto`) USING BTREE,
  KEY `id_categoria` (`id_categoria`) USING BTREE,
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `referidos`;
CREATE TABLE `referidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_referido` int NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE,
  KEY `id_referido` (`id_referido`) USING BTREE,
  CONSTRAINT `referidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `referidos_ibfk_2` FOREIGN KEY (`id_referido`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reset_tokens`;
CREATE TABLE `reset_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `token` (`token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `sl_id` int NOT NULL AUTO_INCREMENT,
  `sl_tit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sl_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sl_img_mov` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sl_est` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  PRIMARY KEY (`sl_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_referido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_perfil` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  PRIMARY KEY (`id_usuario`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `adm_login` (`adm_id`, `adm_nombre`, `adm_passw`, `adm_est`, `adm_login`, `adm_email`) VALUES
(1, 'Andres Lopez Gomez', '$2y$10$snDZSIFH7NbV59zzcRn.FOkdqXY21ISKTc2VshOUxiDXckp20Iq..', 1, 'andreslgz', 'andreslg20@gmail.com');

INSERT INTO `caracteristicas_productos` (`id_caracteristica`, `id_producto`, `marca`, `aplicacion`, `software_type`, `sysytem`, `system`, `columna1`, `anio`, `on_highway`, `off_highway`, `precio_standar`, `precio_medium`, `precio_min`, `tiempo_instalacion`, `tamano_archivo_gb`, `espacio`, `gb`, `computer_requirements`, `complejidad`, `valoracion_cliente`, `database_language`, `implicaciones`, `description_en`, `supported`, `compatible_interface`, `powerapps_id`, `top_sell`) VALUES
(1, 21, 'ALLISON', 'TRUCKS', 'DIAGNOSTIC - REPROGRAMMING', NULL, 'TRANSMISSION', 'ALLISON TCM REFLASH 5.0', '2017', 'T', 'O', 70.00, 70.00, 70.00, '30 minutos', 1.10, '5', '', '\"• Ram: 4 – 8 GB\r\n • Free Hard Drive SSD: 5 GB\r\n • Windows® 7 64-bit\r\n • Windows® 10 64-bit\r\n • Windows® 11 64-bit\"', 'Alta', 0, 'ENGLISH', '', 'ALLISON WINRW32 used as flash module for allison transmission, flashing modules with multiple interface.', '\"ALLISON TCM REFLASH provides customers around the world with accurate, reliable, and traceable calibrations for use with allison transmissions using wtec ii, cec2, wtec iii, 1000 and 2000 tcm product families (pre-allison 4th generation controls), allison 4th generation controls tcms and\r\n Ep 40/50 systemstm (allison electric drivestm). The allison tcm reflash program allows users to download calibrations from the host web server and upload calibrations in the ecus and technical conservation measures, keeping a record of the activity. The program is mainly designed to be used by allison distributors and dealers, manufacturers, and allison personnel.\"', 'NEXIQ USB LINK 1, NEXIQ USB LINK 2, NEXIQ USB LINK 3, NOREGON DLA 2.0', 'b901f3c4-db4d-4f5e-82db-489811fcd683', 0),
(2, 22, 'NEXIQ', 'Test', 'test', NULL, 'test', 'test', '2018', 'T', 'T', 15.00, 12.00, 11.00, '1 H', 12.00, '1', '', 'test', 'Alta', 0, 'ENGLISH', 'test', 'test', 'test', 'test', '23242', 0);

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Electrónica', 'Dispositivos y gadgets', 'activo'),
(2, 'Ropa', 'Moda para todos', 'activo'),
(3, 'Hogar', 'Productos para el hogar', 'activo');

INSERT INTO `cupones` (`id_cupon`, `codigo`, `tipo`, `valor`, `activo`) VALUES
(1, 'DESC10', 'porcentaje', 10.00, 1),
(2, 'AHORRA50', 'fijo', 50.00, 1);

INSERT INTO `favoritos` (`id`, `id_usuario`, `id_producto`) VALUES
(31, 19, 4),
(32, 19, 5),
(43, 19, 22),
(45, 19, 21);

INSERT INTO `galeria_productos` (`gal_id`, `id_producto`, `gal_img`, `gal_est`, `gal_fech`) VALUES
(1, 22, '1.png', 'activo', '2025-08-01'),
(2, 22, '2.png', 'activo', '2025-08-01'),
(3, 22, '3.png', 'activo', '2025-08-01'),
(4, 22, '4.png', 'activo', '2025-08-01'),
(5, 22, '5.png', 'activo', '2025-08-01');

INSERT INTO `marcas` (`id`, `mc_nomb`) VALUES
(1, 'CAT'),
(2, 'JCB'),
(3, 'CUMMINS'),
(4, 'PACCAR'),
(5, 'NOREGON'),
(6, 'NEXIQ');

INSERT INTO `marquesina` (`mq_id`, `mq_tit`, `mq_url`, `mq_est`) VALUES
(1, '$$50 off your first purchase • Free shipping on orders over $100 • 30%', 'https://#', 'activo');

INSERT INTO `ordenes` (`id_orden`, `id_usuario`, `total`, `metodo_pago`, `estado`, `fecha`, `codigo_cupon`, `id_producto`, `invoice`, `comentario`) VALUES
(1, 3, 599.99, 'cotizacion', 'pendiente', '2025-07-25 15:50:57', NULL, 1, '1', NULL),
(2, 3, 849.98, 'cotizacion', 'pendiente', '2025-07-25 15:55:10', NULL, 2, '2', NULL),
(3, 3, 249.99, 'cotizacion', 'pendiente', '2025-07-25 15:57:22', NULL, 3, '3', NULL),
(4, 3, 89.99, 'cotizacion', 'pendiente', '2025-07-25 16:23:43', NULL, 4, '4', NULL),
(5, 3, 249.99, 'paypal', 'pagado', '2025-07-25 16:29:03', NULL, 5, '5', ''),
(6, 3, 724.97, 'cotizacion', 'pendiente', '2025-07-25 17:00:06', NULL, 6, '6', NULL),
(7, 3, 89.99, 'paypal', 'pendiente', '2025-07-25 18:00:03', NULL, 7, '7', NULL),
(8, 3, 89.99, 'paypal', 'pendiente', '2025-07-25 18:04:54', NULL, 8, '8', NULL),
(9, 3, 249.99, 'paypal', 'pendiente', '2025-07-25 18:05:08', NULL, 9, '9', NULL),
(10, 3, 129.99, 'cotizacion', 'pendiente', '2025-07-25 18:05:25', NULL, 10, '10', NULL),
(11, 3, 224.99, 'cotizacion', 'pagado', '2025-07-25 18:29:57', 'DESC10', 11, '11', NULL),
(12, 3, 485.97, 'cotizacion', 'pagado', '2025-07-25 18:49:24', 'DESC10', 12, '12', NULL),
(13, 3, 599.99, 'cotizacion', 'pendiente', '2025-07-25 16:12:58', NULL, 13, '13', NULL),
(14, 3, 539.99, 'cotizacion', 'pendiente', '2025-07-25 19:46:07', 'Desc10', 14, '14', NULL);

INSERT INTO `productos` (`id_producto`, `id_categoria`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `desc_ampliado`, `estado`) VALUES
(4, 1, 'Smartphone X1', 'Teléfono inteligente con cámara de 48MP y pantalla AMOLED.', 599.99, 15, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(5, 1, 'Auriculares Pro', 'Auriculares inalámbricos con cancelación de ruido.', 89.99, 30, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(6, 1, 'Tablet S10', 'Tablet de 10 pulgadas con 64GB de almacenamiento.', 249.99, 20, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(7, 2, 'Camiseta Básica Blanca', 'Camiseta de algodón suave, ideal para el día a día.', 14.99, 50, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(8, 2, 'Pantalón Jeans Slim', 'Jeans slim fit de mezclilla azul.', 39.99, 40, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(9, 3, 'Silla Gamer XR', 'Silla ergonómica para largas sesiones de juego.', 199.99, 10, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(10, 3, 'Lámpara LED', 'Lámpara LED de escritorio con brazo ajustable.', 24.99, 25, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(11, 3, 'Escritorio Minimalista', 'Escritorio moderno con acabado de madera clara.', 129.99, 12, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(12, 1, 'Auricular Bluetooth Sport', 'Auriculares deportivos resistentes al agua con conexión Bluetooth.', 45.50, 60, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(13, 1, 'Monitor 24 pulgadas', 'Monitor LED Full HD ideal para trabajo y entretenimiento.', 189.00, 25, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(14, 1, 'Teclado Mecánico RGB', 'Teclado mecánico retroiluminado con switches rojos.', 79.99, 40, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(15, 1, 'Mouse Inalámbrico', 'Mouse óptico inalámbrico con batería recargable.', 29.99, 80, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(16, 2, 'Sudadera Hoodie Negra', 'Sudadera con capucha, algodón premium, color negro.', 34.99, 70, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(17, 2, 'Zapatillas Urbanas', 'Zapatillas cómodas estilo urbano para uso diario.', 59.99, 50, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(18, 2, 'Chaqueta Impermeable', 'Chaqueta ligera e impermeable para exteriores.', 79.00, 35, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(19, 3, 'Cafetera Eléctrica', 'Cafetera de goteo con capacidad para 12 tazas.', 49.99, 20, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(20, 3, 'Aspiradora Portátil', 'Aspiradora compacta ideal para autos y oficinas.', 39.99, 30, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(21, 3, 'Alfombra Decorativa', 'Alfombra tejida a mano con diseño moderno.', 89.50, 15, 'foto.png', 'Este producto ha sido cuidadosamente diseñado para ofrecer calidad, funcionalidad y estilo en cada detalle. Ideal para quienes buscan una opción confiable y elegante, su fabricación con materiales duraderos garantiza un uso prolongado y resistente al desgaste. Perfecto para uso diario o como un regalo especial, combina diseño moderno con alto rendimiento. Disponible en variedad de colores y presentaciones para adaptarse a tus preferencias.', 'activo'),
(22, 1, 'Producto demo', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ', 500.00, 4, 'prod_68915e348f07b.jpg', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\nIt was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'activo');

INSERT INTO `slider` (`sl_id`, `sl_tit`, `sl_img`, `sl_img_mov`, `sl_est`) VALUES
(1, 'Slider 3', 'hero3.jpg', 'hero3.jpg', 'activo'),
(2, 'Slider 2', 'hero2.jpg', 'hero2.jpg', 'activo'),
(3, 'Slider 1', 'hero1.jpg', 'hero1.jpg', 'activo');

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellidos`, `email`, `password_hash`, `fecha_registro`, `telefono`, `direccion`, `ciudad`, `pais`, `codigo_referido`, `foto_perfil`, `estado`) VALUES
(3, 'Andres', 'Lopez Gomez', 'andreslg20@gmail.com', '$2y$10$uTxEyQQSlpZbJDwBa6qfIemD7zzD1eOHvA.ZYbdJ5tSbq2q8cGH5i', '2025-07-25 15:21:28', '946378945', 'Test 123', 'Lima', NULL, 'ABC123', '1753474470_3135768.png', 'activo'),
(4, 'Juan', 'Perez', 'juanperez@gmail.com', '$2y$10$vjcOq9OK5jIxnqRq8c241OtXC3N46Kzsa1BKQdmjThMpeP4FtsnM.', '2025-08-05 10:36:24', '931458355', 'test 123', 'Lima', 'Perú', NULL, 'uploads/clientes/cliente_689224f8600d13.24585449.jpg', 'activo'),
(17, 'Juan', 'Perez Castro', 'juanoerezz@gmail.com', '$2y$10$fdCe7IT0wC3IJEetU0L1pO.BtVfAYWZ38pTnbmdFwx9y0Yyx6bR5y', '2025-08-07 19:48:37', '123-123-1234', NULL, NULL, 'Perú', '12345', NULL, 'activo'),
(18, 'Karen', 'Pereda', 'karenpmar@gmail.com', '$2y$10$fyCDZkdOYgY42.mCU7cwmuF5qAkSkAWIRhUzG6N.OppjH.xRZ4WhW', '2025-08-07 20:01:33', '787-222-2222', NULL, NULL, 'Perú', '23423', NULL, 'activo'),
(19, 'Andres', 'Lopez Gomez', 'andreslg2k@gmail.com', '$2y$10$q5cvCc6VMcGSJ6yT7LNvKuSIoPhYqcukVwK2nHkS.oPO4dccBXHvi', '2025-08-15 20:45:22', '787-123-4564', NULL, NULL, 'Perú', '09090', NULL, 'activo');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;