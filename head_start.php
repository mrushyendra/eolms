<?php 
/* Include this file at the start of every web page, followed 
 * by any extra code that should belong in the head tag, and 
 * finally head_end.php. 
 * 
 * Example:
 * <?php
 *  include_once("head_start.php");
 *  $PageTitle = "Home";
 * 
 *  // Custom Code here.
 *  
 *  include_once("head_end.php");
 * ?>
 * 
 * Note that $PageTitle is a special variable used by 
 * head_end.php to set the title.
 */
?>

<?php
include_once('base.php');
?>

<!doctype html>

<html lang="en-us">
    
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon-precomposed" href="img/icon.png">
    <link rel="apple-touch-startup-image" href="img/startup.png">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no,maximum-scale=1">

    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:regular,bold">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/light/theme.css" class="theme">
	
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(
            function() {
                $('body').fadeTo(0, 0.0).fadeTo(500, 1);
            }
        );
    </script>


    