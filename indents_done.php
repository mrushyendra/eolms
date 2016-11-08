<?php
include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

$columnIndex = isset($_GET["column"]) && is_numeric($_GET["column"]) ? $_GET["column"] : null;
$done = isset($_GET["done"]) && ($_GET["done"] == "true" || $_GET["done"] == "false") ? $_GET["done"] : null;
$done = $done == "true" ? true : false;
$id = isset($_GET["id"]) ? $_GET["id"] : NULL;

if ($columnIndex === NULL || $done === NULL || $columnIndex < 0 || $columnIndex > 3) {
    echo "(error1)";
    die();
}

$column = "acttime".($columnIndex + 1);
$time = time();

$stmt = $mysql->stmt_init();
if ($stmt->prepare("UPDATE indents SET ".$column." = ? WHERE id = ?;")) {
    $stmt->bind_param("ss", $done ? date("1899-12-31\TH:i:s.000",$time) : NULL, $id);
    $stmt->execute();
    $stmt->close();
} else {
    echo "(error2)";
    die();
}

if ($done) {
    echo "(".date("h:i A").")";
} else {
    echo "( - )";
}


?>