-- Migration: add license token columns to configuracion_centro
-- Run this on AulaPro's production database (yassjjzw_pfc) via phpMyAdmin
-- Safe to run multiple times — ADD COLUMN IF NOT EXISTS won't error if column already exists

ALTER TABLE `configuracion_centro`
    ADD COLUMN IF NOT EXISTS `license_token`     TEXT         DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `license_token_exp` DATETIME     DEFAULT NULL;
