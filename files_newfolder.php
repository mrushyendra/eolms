<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');
include_once('files_base.php');

if (isset($Permissions["files_newfolder"])) {
    $parent = isset($_GET["parent"]) ? $_GET["parent"] : "";
    $parentTruePath = truePath($parent);
    if (!isValidDir($parent) || !is_dir($parentTruePath)) {
        header("Location: files.php?err=1");
        die();
    }

    $name = isset($_GET["name"]) ? $_GET["name"] : "";
    $path = concatPath($parent, $name);
    if (!isValidName($_GET["name"]) || $path === FALSE) {
        header("Location: files.php?err=2&parent=".$parent);
        die();
    }

    $truePath = truePath($path);
    mkdir($truePath, 0777, TRUE);

    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("INSERT INTO files(path, uploader) VALUES(?, ?) ON DUPLICATE KEY UPDATE path = VALUES(path), uploader = VALUES(uploader);")) {
        $stmt->bind_param("ss", $path, $Username);
        $stmt->execute();
        $stmt->close();
        header("Location: files.php?parent=".$parent);
        die();
    } else {
        debug_print("MySQL Error: ".$stmt->error);
    }

    header("Location: files.php?parent=".$parent);
    die();
}

?>