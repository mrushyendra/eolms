<?php
/* This file handles database connection. Include at the start of 
 * files that require database connection.
 */


include_once('base.php');

$dbc_host = 'localhost';
$dbc_database = 'eolms_seag';
$dbc_user = 'eolms_admin';
$dbc_pass = 'j7dh3s1@a';

$mysql = new MySQLi($dbc_host, $dbc_user, $dbc_pass, $dbc_database);

if ($mysql->connect_errno) {
    debug_print("MySQL Error: ".$mysql->connect_error);
}