<?php
/* This file marks the start of the body section of a web page, and 
 * should be included in all pages that are accessible by anyone
 * who is logged in. This file should be included after the 
 * head_* files, and should be terminated by body_end.php file in 
 * a similar way as the head_* files. 
 * 
 * Note: This file should not be included by pages that is accessible 
 * by somebody who is not logged in e.g. login page.
 */
?>

<body>
<?php
include_once('verify_login.php');
include_once('nav.php');
?>
<section id="content">
