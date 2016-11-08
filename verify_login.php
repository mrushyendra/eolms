<?php

include_once('base.php');
include_once('database_connect.php');

$Username = NULL;

if (!isset($_SESSION["SessionId"]) Or !isset($_SESSION["Username"])) {
    header("Location: login.php");
    die();
} else {
    // Verify login
    $sessionId = $_SESSION["SessionId"];
    $username = $_SESSION["Username"];
    
    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("SELECT lastActivity FROM sessions WHERE username = ? AND sessionId = ?;")) {
        $stmt->bind_param("ss", $username, $sessionId);
        $stmt->execute();
        $stmt->bind_result($lastActivity);
        $result = $stmt->fetch();
        $stmt->close();
        
        if ($result) {
            // Check session timed out.
            if (time() - $lastActivity < 30 * 60) { // 30 minutes
                $Username = $username;
                $stmt = $mysql->stmt_init();
                if ($stmt->prepare("UPDATE sessions SET lastActivity = ? WHERE username = ?;")) {
                    $stmt->bind_param("is", time(), $username);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    debug_print("MySQL Error: ".$stmt->error);
                }
            } else {
                unset($_SESSION["SessionId"]);
                unset($_SESSION["Username"]);
                unset($Username);
                session_destroy();
                header("Location: login.php?login=timeout");
                die();
            }
        } else {
            unset($_SESSION["SessionId"]);
            unset($_SESSION["Username"]);
            unset($Username);
            session_destroy();
            header("Location: login.php");
            die();
        }
    } else {
        debug_print("MySQL Error: ".$stmt->error);
    }
}