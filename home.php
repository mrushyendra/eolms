<?php
include_once("head_start.php");
    $PageTitle = "Home";
    
include_once("scripts.php");
include_once("head_end.php");
include_once("body_start.php");
?>

<div class='g12' style='margin:0px;'>
    <form>
        <div style='width:100%; overflow: auto'>
        <fieldset>
            <label>Key Glance For Today</label>
            <div>
                <table>
                    <?php
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 0 AND acttime4 <= exptime4;")) {
                        $stmt->execute();
                        $stmt->bind_result($count1);
                        $stmt->fetch();
                        echo $mysql->error;
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 0 AND acttime4 > exptime4;")) {
                        $stmt->execute();
                        $stmt->bind_result($count2);
                        $stmt->fetch();
                        echo $mysql->error;
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 0 AND acttime1 IS NOT NULL AND acttime4 IS NULL;")) {
                        $stmt->execute();
                        $stmt->bind_result($count3);
                        $stmt->fetch();
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 0 AND acttime1 IS NULL;")) {
                        $stmt->execute();
                        $stmt->bind_result($count4);
                        $stmt->fetch();
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 1 AND acttime4 <= exptime4;")) {
                        $stmt->execute();
                        $stmt->bind_result($count1r);
                        $stmt->fetch();
                        echo $mysql->error;
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 1 AND acttime4 > exptime4;")) {
                        $stmt->execute();
                        $stmt->bind_result($count2r);
                        $stmt->fetch();
                        echo $mysql->error;
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 1 AND acttime1 IS NOT NULL AND acttime4 IS NULL;")) {
                        $stmt->execute();
                        $stmt->bind_result($count3r);
                        $stmt->fetch();
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(id) FROM indents WHERE date = CURDATE() AND direction = 1 AND acttime1 IS NULL;")) {
                        $stmt->execute();
                        $stmt->bind_result($count4r);
                        $stmt->fetch();
                    } else {
                        debug_print("MySQL Error: " . $stmt->error);
                    }
                    $stmt->close();
                    ?>
                    <thead>
                        <tr>
                            <th width="50%" style="font-size: 120%">Forward Trip</td>
                            <th width="50%" style="font-size: 120%">Return Trip</td>
                        </tr>
                    </thead>
                    <tr>
                        <td>
                            <table class='chart' data-type='pie' data-colors='[\"#22bb22\",\"#ffa500\",\"#ff0000\",\"#000000\"]' data-legend='true' data-tooltip-pattern='%1 buses'>
                                <tbody>
                                    <tr>
                                        <th>Arrived on time.</th>
                                        <td><?php echo $count1; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Arrived late.</th>
                                        <td><?php echo $count2; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Yet to arrive.</th>
                                        <td><?php echo $count3; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Yet to set off.</th>
                                        <td><?php echo $count4; ?></td>
                                    </tr>
                                <tbody>
                            </table>
                        </td>
                        
                        <td>
                            <table class='chart' data-type='pie' data-colors='[\"#22bb22\",\"#ffa500\",\"#ff0000\",\"#000000\"]' data-legend='true' data-tooltip-pattern='%1 buses'>
                                <tbody>
                                    <tr>
                                        <th>Arrived on time.</th>
                                        <td><?php echo $count1r; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Arrived late.</th>
                                        <td><?php echo $count2r; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Yet to arrive.</th>
                                        <td><?php echo $count3r; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Yet to set off.</th>
                                        <td><?php echo $count4r; ?></td>
                                    </tr>
                                <tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
                            echo "<b>".$count1."</b> buses have arrived on time.<br/>";
                            echo "<b>".$count2."</b> buses have arrived late.<br/>";                            
                            echo "<b>".$count3."</b> buses have yet to arrive.<br/>";                            
                            echo "<b>".$count4."</b> buses have yet to set off.<br/>";
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo "<b>".$count1r."</b> buses have arrived on time.<br/>";
                            echo "<b>".$count2r."</b> buses have arrived late.<br/>";                            
                            echo "<b>".$count3r."</b> buses have yet to arrive.<br/>";                            
                            echo "<b>".$count4r."</b> buses have yet to set off.<br/>";
                            ?>
                        </td>
                    </tr>
                </table>
                
            </div>
        </fieldset>
        </div>
    </form>                 
</div>

<?php
include_once("body_end.php");