<?php

/*
 * ITFlow - Database patch to version 3 (from 2)
 * Included by admin/database_updates.php - do not access directly
 */

defined('FROM_DB_UPDATER') || die("Direct file access is not allowed");

    // Remove database patch column from settings as now RKDB patching is versioned in-line with original schema

    mysqli_query($mysqli, "ALTER TABLE `settings` DROP COLUMN IF EXISTS `config_current_database_patch`");