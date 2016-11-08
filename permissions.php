<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');

$Permissions = array();

$stmt = $mysql->stmt_init();
if ($stmt->prepare("SELECT permission FROM permissions WHERE username = ?;")) {
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $stmt->bind_result($permission);
    $stmt->store_result();

    while ($stmt->fetch()) {
        $Permissions[$permission] = true;
    }
    
    $stmt->free_result();
    $stmt->close();
} else {
    debug_print("MySQL Error: ".$stmt3->error);
}
unset($stmt);