<?php

/*
 * ITFlow - Database update to version 2.6.7.2 (from 2.6.7.1)
 * Included by admin/database_updates.php - do not access directly
 */

defined('FROM_DB_UPDATER') || die("Direct file access is not allowed");

    // Optional CC recipients are stored as a JSON array so a single queue row
    // can be delivered as one message to the ticket contact and collaborators.
    mysqli_query($mysqli, "ALTER TABLE `email_queue`
        ADD COLUMN `email_cc` text DEFAULT NULL AFTER `email_content`");
