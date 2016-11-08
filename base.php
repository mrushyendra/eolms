<?php
/* This fils is to be included in any php file. You do not need to 
 * include this file if you already included head_start.php as it
 * automatically includes this file.
 */

date_default_timezone_set("Asia/Singapore");

session_start();

/*
 * This function should be called for print debug messages.
 * 
 * IMPORTANT: SET $debug TO FALSE IF NOT DEBUGGING AS YOU DO NOT 
 * WANT TO LEAK SENSITIVE INFORMATION TO CLIENT.
 */
function debug_print($message) {
    $debug = true;
    
    if ($debug) {
        print($message);
        die();
    } else {
        die("Internal server error.");
    }
}

?>