<?php

/*
 * ITFlow - Database update to version 2.6.7.3 (from 2.6.7.2)
 * Included by admin/database_updates.php - do not access directly
 */

defined('FROM_DB_UPDATER') || die("Direct file access is not allowed");

    // Retain rejected recipients when SMTP accepts only part of a To/Cc group,
    // allowing partial delivery to remain terminal without retrying recipients
    // that have already received the message.
    mysqli_query($mysqli, "ALTER TABLE `email_queue`
        ADD COLUMN `email_failed_recipients` text DEFAULT NULL AFTER `email_cc`");
