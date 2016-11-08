<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

function truePath($path) {
    if ($path === NULL || $path == "") {
        return "../files";
    } else {
        return "../files/" . $path;
    }
}

function concatPath($parent, $name) {
    if ($parent == "") {
        return $name;
    } else if ($name == "") {
        return $parent;
    } else {
        $out = $parent . "/" . $name;
        if (strlen($out) > 250) {
            return FALSE;
        }

        return $out;
    }
}

function isValidDir($path) {
    return 
        strpos($path, "..") === FALSE &&
        strpos($path, "//") === FALSE &&
        preg_match("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-_\/ ]/", $path) == 0;
}

function isValidName($name) {
    return 
        strlen(trim($name)) > 0 &&
        strpos($name, "..") === FALSE &&
        preg_match("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-_ ]/", $name) == 0;
}

function formatSize($size) {
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;
    
    if ($size > $tb) {
        $size = round($size / $tb);
        return $size." TB";
    } else if ($size > $gb) {
        $size = round($size / $gb);
        return $size." GB";
    } else if ($size > $mb) {
        $size = round($size / $mb);
        return $size." MB";
    } else if ($size > $kb) {
        $size = round($size / $kb);
        return $size." KB";
    } else if ($size == 0) {
        return "-";
    } else {
        return $size." B";
    }
}