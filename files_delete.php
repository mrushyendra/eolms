<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');
include_once('files_base.php');

if (isset($Permissions["files_delete"])) {
    $parent = isset($_GET["parent"]) ? $_GET["parent"] : "";
    $parentTruePath = truePath($parent);
    if (!isValidDir($parent) || !is_dir($parentTruePath)) {
        header("Location: files.php?err=1");
        die();
    }

    $name = isset($_GET["name"]) ? $_GET["name"] : "";
    $path = concatPath($parent, $name);
    if (!isValidName($_GET["name"]) || $path === FALSE) {
        header("Location: files.php?err=5&parent=".$parent);
        die();
    }

    $truePath = truePath($path);

    if (is_dir($truePath)){
        $dir = new RecursiveDirectoryIterator($truePath);
        $iterator = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($iterator as $file) {
            $action = ($file->isDir() ? 'rmdir' : 'unlink');
            $action($file->getRealPath());
        }

        rmdir($truePath);
    } else {
        unlink($truePath);
    }

    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("DELETE FROM files WHERE path = ?;")) {
        $stmt->bind_param("s", $path);
        $stmt->execute();
        $stmt->close();
    } else {
        debug_print("MySQL Error: " + $stmt->error);
    }

    header("Location: files.php?parent=".$parent);
    die();
}

?>