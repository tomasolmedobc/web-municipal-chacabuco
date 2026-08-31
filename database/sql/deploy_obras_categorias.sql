-- =============================================================
-- DEPLOY: Módulo Obras Particulares - Categorías dinámicas
-- Ejecutar en phpMyAdmin en el orden indicado
-- =============================================================

-- PASO 1: Crear tabla obras_categorias
-- =============================================================
CREATE TABLE `obras_categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` smallint unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PASO 2: Insertar las 4 categorías base
-- =============================================================
INSERT INTO `obras_categorias` (`nombre`, `descripcion`, `orden`, `visible`, `created_at`, `updated_at`) VALUES
('Obras Particulares',     NULL, 1, 1, NOW(), NOW()),
('Balcones Gastronómicos', NULL, 2, 1, NOW(), NOW()),
('Mensura y Subdivisión',  NULL, 3, 1, NOW(), NOW()),
('Libre Deuda',            NULL, 4, 1, NOW(), NOW());

-- PASO 3: Agregar columna categoria_id en obras_procedimientos
-- =============================================================
ALTER TABLE `obras_procedimientos`
  ADD COLUMN `categoria_id` bigint unsigned NULL AFTER `id`,
  ADD CONSTRAINT `obras_procedimientos_categoria_id_foreign`
    FOREIGN KEY (`categoria_id`) REFERENCES `obras_categorias` (`id`) ON DELETE SET NULL;

-- PASO 4: Agregar columna categoria_id en obras_normativas
-- =============================================================
ALTER TABLE `obras_normativas`
  ADD COLUMN `categoria_id` bigint unsigned NULL AFTER `id`,
  ADD CONSTRAINT `obras_normativas_categoria_id_foreign`
    FOREIGN KEY (`categoria_id`) REFERENCES `obras_categorias` (`id`) ON DELETE SET NULL;

-- PASO 5: Migrar datos existentes (seccion → categoria_id)
-- =============================================================
UPDATE `obras_procedimientos`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Obras Particulares' LIMIT 1)
  WHERE `seccion` = 'obras';

UPDATE `obras_procedimientos`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Balcones Gastronómicos' LIMIT 1)
  WHERE `seccion` = 'balcones';

UPDATE `obras_procedimientos`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Mensura y Subdivisión' LIMIT 1)
  WHERE `seccion` = 'mensura';

UPDATE `obras_procedimientos`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Libre Deuda' LIMIT 1)
  WHERE `seccion` = 'libre_deuda';

UPDATE `obras_normativas`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Obras Particulares' LIMIT 1)
  WHERE `seccion` = 'obras';

UPDATE `obras_normativas`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Balcones Gastronómicos' LIMIT 1)
  WHERE `seccion` = 'balcones';

UPDATE `obras_normativas`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Mensura y Subdivisión' LIMIT 1)
  WHERE `seccion` = 'mensura';

UPDATE `obras_normativas`
  SET `categoria_id` = (SELECT `id` FROM `obras_categorias` WHERE `nombre` = 'Libre Deuda' LIMIT 1)
  WHERE `seccion` = 'libre_deuda';

-- PASO 6: Eliminar columna seccion (ya no se usa)
-- =============================================================
ALTER TABLE `obras_procedimientos` DROP COLUMN `seccion`;
ALTER TABLE `obras_normativas`     DROP COLUMN `seccion`;

-- PASO 7: Registrar migraciones en la tabla de Laravel
-- (para que artisan no las intente correr si algún día tenés acceso)
-- =============================================================
INSERT INTO `migrations` (`migration`, `batch`)
SELECT migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m2)
FROM (
  SELECT '2026_08_26_100001_create_obras_categorias_table'       AS migration
  UNION ALL
  SELECT '2026_08_26_100002_migrate_obras_seccion_to_categoria_id'
) t
WHERE migration NOT IN (SELECT migration FROM migrations);
