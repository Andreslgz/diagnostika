-- -------------------------------------------------------------
-- TablePlus 6.6.9(633)
--
-- https://tableplus.com/
--
-- Database: mysiste2_tienda
-- Generation Time: 2025-08-17 16:09:04.7600
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



CREATE PROCEDURE `contar_ordenes_pagadas`()
    SQL SECURITY INVOKER
BEGIN
    SELECT COUNT(*) AS total
    FROM ordenes
    WHERE estado = 'pagado';
END

CREATE PROCEDURE `contar_usuarios`()
    SQL SECURITY INVOKER
BEGIN
    SELECT COUNT(*) AS total
    FROM usuarios;
END

CREATE PROCEDURE `filtrar_productos_con_paginacion`(
    IN in_nombre        VARCHAR(100),
    IN in_id_categoria  INT,
    IN in_estado        VARCHAR(20),
    IN in_offset        INT,
    IN in_limit         INT
)
    SQL SECURITY INVOKER
BEGIN
    -- Normalizar strings vacíos a NULL para tratar igual que "sin filtro"
    IF in_nombre IS NOT NULL AND TRIM(in_nombre) = '' THEN
        SET in_nombre = NULL;
    END IF;
    IF in_estado IS NOT NULL AND TRIM(in_estado) = '' THEN
        SET in_estado = NULL;
    END IF;

    SELECT 
        p.*,
        c.nombre AS categoria_nombre
    FROM productos AS p
    INNER JOIN categorias AS c 
        ON p.id_categoria = c.id_categoria
    WHERE 
        (in_nombre IS NULL OR p.nombre LIKE CONCAT('%', in_nombre, '%'))
        AND (in_id_categoria IS NULL OR p.id_categoria = in_id_categoria)
        AND (in_estado IS NULL OR p.estado = in_estado)
    ORDER BY p.id_producto DESC
    LIMIT in_limit OFFSET in_offset;
END

CREATE PROCEDURE `listar_ultimas_ordenes`()
    SQL SECURITY INVOKER
BEGIN
    SELECT 
        id_orden,
        id_usuario,
        total,
        metodo_pago,
        estado,
        fecha,
        codigo_cupon
    FROM ordenes
    ORDER BY fecha DESC
    LIMIT 10;
END

CREATE PROCEDURE `sumar_ordenes_pagadas`()
    SQL SECURITY INVOKER
BEGIN
    SELECT IFNULL(SUM(total), 0) AS total
    FROM ordenes
    WHERE estado = 'pagado';
END





