<?php

/*
 * ITFlow - Database patch to version 2 (from 1)
 * Included by admin/database_updates.php - do not access directly
 */

defined('FROM_DB_UPDATER') || die("Direct file access is not allowed");

    mysqli_query($mysqli, "
        CREATE TABLE `pbs_usage_reports` (
            `report_id` int(11) NOT NULL AUTO_INCREMENT,
            `server_name` varchar(60) NOT NULL,
            `report_date` datetime NOT NULL,
            `namespace_path` varchar(200) NOT NULL,
            `unique_size_gib` decimal(9,3) UNSIGNED NOT NULL,
            PRIMARY KEY (`report_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");