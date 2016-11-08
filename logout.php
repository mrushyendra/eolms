<?php

include_once('base.php');

unset($_SESSION["SessionId"]);
unset($_SESSION["Username"]);
unset($Username);
session_destroy();

header("Location: login.php");
die();

?>