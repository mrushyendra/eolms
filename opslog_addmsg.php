<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

// Check if user has permission to write to ops log.
if(isset($Permissions["opslog_write"])) {   
    $message = htmlspecialchars($_POST["postmsg"] ?: "", ENT_QUOTES);
    $message = trim($message);
    $importance = is_numeric($_POST["postimpt"]) ? $_POST["postimpt"] : 0;

    if (strlen($message) > 3) {
        $stmt = $mysql->stmt_init();
        if ($stmt->prepare("INSERT INTO opslog(poster, message, time, importance) VALUES(?, ?, ?, ?)")) {
            $stmt->bind_param("ssii", $Username, $message, time(), $importance);
            $stmt->execute();
            $stmt->close();
        } else {
            debug_print("MySQL Error: ".$stmt->error);
        }
    } else {
        header("Location: opslog.php?postmsgerr=1");
        die();
    }
}

header("Location: opslog.php");
die();

?>