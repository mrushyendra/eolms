<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');
include_once('files_base.php');

if (isset($Permissions["files_upload"])) {
    $parent = isset($_POST["parent"]) ? $_POST["parent"] : "";
    $parentTruePath = truePath($parent);
    if (!isValidDir($parent) || !is_dir($parentTruePath)) {
        header("Location: files.php?err=1");
        die();
    }

    if (isset($_FILES["file"]["tmp_name"]) && $_FILES["file"]["size"] > 0) {
        $name = $_FILES["file"]["name"];
        $size = $_FILES["file"]["size"];

        $path = concatPath($parent, $name);
        if (!isValidName($name) || $path === FALSE) {
            header("Location: files.php?err=2&parent=".$parent);
            die();
        }

        if ($size > 104857600) {
            header("Location: files.php?err=3&parent=".$parent);
            die();
        }

        move_uploaded_file($_FILES["file"]["tmp_name"], truePath($path));

        $stmt = $mysql->stmt_init();
        if ($stmt->prepare("INSERT INTO files(path, uploader) VALUES(?, ?) ON DUPLICATE KEY UPDATE path = VALUES(path), uploader = VALUES(uploader);")) {
            $stmt->bind_param("ss", $path, $Username);
            $stmt->execute();
            $stmt->close();
            header("Location: files.php?&parent=".$parent);
            die();
        } else {
            debug_print("MySQL Error: ".$stmt->error);
        }
    } else {
        header("Location: files.php?err=4&parent=".$parent);
        die();
    }
}

?>