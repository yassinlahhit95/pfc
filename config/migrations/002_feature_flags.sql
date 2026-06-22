-- Migration 002: add missing feature-flag columns to configuracion_centro
-- Run once on production: mysql -u user -p db < 002_feature_flags.sql
-- Safe to run multiple times (ADD COLUMN IF NOT EXISTS).

ALTER TABLE `configuracion_centro`
    ADD COLUMN IF NOT EXISTS `feature_anuncios`  tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_subida_tfg`,
    ADD COLUMN IF NOT EXISTS `feature_eventos`   tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_anuncios`,
    ADD COLUMN IF NOT EXISTS `feature_retos`     tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_eventos`,
    ADD COLUMN IF NOT EXISTS `feature_mensajes`  tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_retos`,
    ADD COLUMN IF NOT EXISTS `feature_pagos`     tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_mensajes`,
    ADD COLUMN IF NOT EXISTS `feature_gastos`    tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_pagos`,
    ADD COLUMN IF NOT EXISTS `feature_informes`  tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_gastos`;
