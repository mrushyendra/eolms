<?php
include_once('verify_login.php');
include_once('permissions.php');
?>

<div id="pageoptions">
    <ul>
        <li><a href="logout.php">Logout</a></li>
        <?php if (isset($Permissions["admin_newuser"]) || isset($Permissions["admin_editperm"])) { ?>
        <li><a href="admin.php">Admin Panel</a></li>
        <?php } ?>
        <li style="float: left"><b>You are logged in as <?php echo $Username ?>.</b></li>
    </ul>
</div>

<header>
    <div id="logo"></div>
</header>

<nav>
    <ul id="nav">
        <li class="i_house">
            <a href="home.php">
                <span>
                    Home
                </span>
            </a>
        </li>
        
        <?php if(isset($Permissions["opslog_read"]) || isset($Permissions["opslog_write"])) {  ?>
        <li class="i_book">
            <a href="opslog.php">
                <span>
                    Operations Log
                </span>
            </a>
        </li>
        <?php } ?>
        
        <?php if(isset($Permissions["indents_read"]) || isset($Permissions["indents_upload"])) {  ?>
        <li class="i_car">
            <a href="indents.php">
                <span>
                    Bus Indents
                </span>
            </a>
        </li>
        <?php } ?>
        
        <?php if(isset($Permissions["schedule_read"]) || isset($Permissions["schedule_upload"])) {  ?>
        <li class="i_calendar">
            <a href="schedule.php">
                <span>
                    Schedule
                </span>
            </a>
        </li>
        <?php } ?>
        
        <?php if (isset($Permissions["files_download"])) { ?>
        <li class="i_folder">
            <a href="files.php">
                <span>
                    Files
                </span>
            </a>
        </li>
        <?php } ?>
    </ul>
</nav>

