<?php

/*
 * ITFlow - Database patch to version 1 (from 0)
 * Included by admin/database_updates.php - do not access directly
 */

defined('FROM_DB_UPDATER') || die("Direct file access is not allowed");

    mysqli_query($mysqli, "ALTER TABLE `companies` ADD `company_abbreviation` VARCHAR(10) DEFAULT NULL AFTER `company_name`");
    mysqli_query($mysqli, "ALTER TABLE `companies` ADD `company_legal_name` VARCHAR(200) DEFAULT NULL AFTER `company_abbreviation`");