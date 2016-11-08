<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');
include_once('files_base.php');

if (isset($Permissions["files_download"])) {
    $parent = isset($_POST["parent"]) ? $_POST["parent"] : "";
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

    if(file_exists($truePath)) {
        header('Content-Disposition: attachment; filename='.$name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($truePath));
        flush();
        ob_end_clean();
        readfile($truePath);
        exit;
    }
}

?>