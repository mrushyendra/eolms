<?php

include_once('base.php');
include_once('database_connect.php');

if(isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $passwordHash = hash("sha512", $username . $password);
    
    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND password = ?;")) {
        $stmt->bind_param("ss", $username, $passwordHash);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        if($count == 1) {
            $randomString = openssl_random_pseudo_bytes(256);
            $sessionId = hash("sha256", $randomString);
            $time = time();

            $stmt = $mysql->stmt_init();
            if ($stmt->prepare("INSERT INTO sessions(username, sessionId, lastActivity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE sessionId = VALUES(sessionId), lastActivity = VALUES(lastActivity);")) {
                $stmt->bind_param("ssi", $username, $sessionId, $time);
                $stmt->execute();
                $stmt->close();
            } else {
                debug_print("MySQL Error: " . $stmt->error);
            }

            $_SESSION['SessionId'] = $sessionId;
            $_SESSION['Username'] = $username;

            header("Location: home.php");
            die();
        } else {
            header("Location: login.php?login=failed");
            die();
        }
    } else {
        debug_print("MySQL Error: " . $stmt->error);
    }
} else {
    header("Location: login.php");
    die();
}

?>
