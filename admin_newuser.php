<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

if (isset($Permissions["admin_newuser"])) {
    $username = "";
    if (isset($_POST['username']) && strlen($_POST['username']) >= 4) {
        $username = $_POST['username'];
    } else {
        header("Location: admin.php?err=1");
        die();
    }

    $password = "";
    if (isset($_POST['password']) && strlen($_POST['password']) >= 8) {
        $password = $_POST['password'];
    } else {
        header("Location: admin.php?err=2");
        die();
    }

    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("INSERT INTO users VALUES(?, ?);")) {
        $stmt->bind_param("ss", $username, hash("sha512", $username . $password));
        $stmt->execute();

        if ($stmt->error) {
            header("Location: admin.php?err=3");
            die();
        }

        $stmt->close();
    } else {
        debug_print("MySQL Error: " + $stmt->error);
    }

    header("Location: admin.php");
    die();
}

?>