<?php

defined('FROM_POST_HANDLER') || die("Direct file access is not allowed");

if (isset($_GET['send_failed_mail'])) {

    validateCSRFToken();

    $email_id = intval($_GET['send_failed_mail']);

    // Claim only a failed row. The status condition makes this atomic with the
    // cron sender and prevents a delivered or in-flight message being requeued.
    mysqli_query($mysqli,"UPDATE email_queue SET email_status = 0, email_attempts = 3, email_failed_recipients = NULL WHERE email_id = $email_id AND email_status = 2");
    if (mysqli_affected_rows($mysqli) !== 1) {
        flashAlert("That email is no longer failed and cannot be resent.", 'error');
        redirect();
    }

    logAudit("Email", "Send", "$session_name attempted to force send email id: $email_id in the mail queue", 0, $email_id);

    flashAlert("Email Force Sent, give it a minute to resend");

    redirect();

}

if (isset($_GET['cancel_mail'])) {

    validateCSRFToken();

    $email_id = intval($_GET['cancel_mail']);

    // A message already claimed by cron (status 1) may be delivered at any
    // moment, so only queued and failed rows are safe to cancel.
    mysqli_query($mysqli,"UPDATE email_queue SET email_status = 2, email_attempts = 99, email_failed_at = NOW() WHERE email_id = $email_id AND email_status IN (0, 2)");
    if (mysqli_affected_rows($mysqli) !== 1) {
        flashAlert("That email is already sending or delivered and cannot be cancelled.", 'error');
        redirect();
    }

    logAudit("Email", "Send", "$session_name canceled send email id: $email_id in the mail queue", 0, $email_id);

    flashAlert("Email cancelled and marked as failed.", 'error');

    redirect();

}

if (isset($_POST['bulk_cancel_emails'])) {

    validateCSRFToken();

    if (isset($_POST['email_ids'])) {

        $count = 0;

        // Cycle through array and mark each email as failed
        foreach ($_POST['email_ids'] as $email_id) {

            $email_id = intval($email_id);
            mysqli_query($mysqli,"UPDATE email_queue SET email_status = 2, email_attempts = 99, email_failed_at = NOW() WHERE email_id = $email_id AND email_status IN (0, 2)");

            if (mysqli_affected_rows($mysqli) === 1) {
                $count++;
                logAudit("Email", "Cancel", "$session_name cancelled email id: $email_id in the mail queue", 0, $email_id);
            }

        }

        logAudit("Email", "Bulk Cancel", "$session_name cancelled $count email(s) in the mail queue");

        flashAlert("Cancelled <strong>$count</strong> email(s)", 'error');

    }

    redirect();

}

if (isset($_POST['bulk_delete_emails'])) {

    validateCSRFToken();

    if (isset($_POST['email_ids'])) {

        $count = 0;

        // Cycle through array and delete each email
        foreach ($_POST['email_ids'] as $email_id) {

            $email_id = intval($email_id);
            mysqli_query($mysqli,"DELETE FROM email_queue WHERE email_id = $email_id AND email_status IN (0, 2)");

            if (mysqli_affected_rows($mysqli) === 1) {
                $count++;
                logAudit("Email", "Delete", "$session_name deleted email id: $email_id from the mail queue");
            }

        }

        logAudit("Email", "Bulk Delete", "$session_name deleted $count email(s) from the mail queue");

        flashAlert("Deleted <strong>$count</strong> email(s)", 'error');

    }

    redirect();

}
