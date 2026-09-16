<?php

// RKDB fork helper functions

function getEmailFooter() {
    global $mysqli;

    $row = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT company_name,company_legal_name FROM companies WHERE company_id = 1 LIMIT 1"));

    $footer = "<br>--<br>";
    $footer .= $row['company_name'];

    if (!empty($row['company_legal_name']))
        $footer .= "<br>by $row[company_legal_name]";

    return $footer;
}