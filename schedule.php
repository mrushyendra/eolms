<?php
include_once('head_start.php');
    $PageTitle = "Indents";
    
include_once('scripts.php');
include_once('head_end.php');
include_once('body_start.php');
?>

<?php if (isset($Permissions["schedule_upload"])) { ?>
    <div class="g12" style="padding:1px">
        <form method="POST" enctype="multipart/form-data" action="schedule_upload.php">
            <fieldset>
                <label>Upload Schedule</label>
                <section>
                    <label>Date:</label>
                    <div>
                        <input class="date" type="text" name="upschdate"></input>
                    </div>
                    <?php if(isset($_GET["upscherrdate"])) { ?>
                    <div style='color: red'>Error! Please enter the date in format yyyy-mm-dd.</div>
                    <?php } ?>
                </section>
                <section>
                    <label>XML File:</label>
                    <div>
                        <input type="hidden" name="MAX_FILE_SIZE" value="1048576"></input>
                        <input type="file" name="upschfile"></input>
                        <?php if(isset($_GET["upscherrformat"])) { ?>
                        <div style='color: red'>Error! Please upload only XML files exported from Excel spreadsheet.</div>
                        <?php } else if (isset($_GET["upscherrcell"])) {
                            $celltitle = isset($_GET["upscherrcellname"]) ? $_GET["upscherrcellname"] : "";
                            echo "<div style='color: red'>Incorrect Column! The system expected column ".$_GET["upscherrcell"]." to be '".$celltitle."'.</div>";
                        } ?>
                    </div>
                </section>
                <section>
                    <label></label>
                    <div>
                        <button class="submit">Submit</button>
                    </div>
                </section>
            </fieldset>
        </form>
<?php } ?>

<?php if (isset($Permissions["schedule_read"])) { ?>
        <form method="GET" action="schedule.php">
            <fieldset>
                <label>View Schedule</label>
                <section>
                    <label>Date:</label>
                    <div>
                        <input class="date" type="text" name="schdate"></input>
                    </div>
                </section>
                <section>
                    <label></label>
                    <div>
                        <button class="submit">Submit</button>
                    </div>
                </section>
            </fieldset>
        </form>
        
        <form>
            <fieldset>
                <section>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th width='8%'>From</th>
                                <th width='8%'>To</th>
                                <th width='20%'>Event</th>
                                <th width='19%'>Location</th>
                                <th width='15%'>Primary Agency</th>
                                <th width='15%'>Safety Measures</th>
                                <th width='15%'>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET["schdate"])) {
                                $date = $_GET["schdate"];
                                
                                $stmt = $mysql->stmt_init();
                                if ($stmt->prepare("SELECT timefrom, timeto, event, location, agency, safety, remarks FROM schedules WHERE date = ? ORDER BY id ASC;")) {
                                    $stmt->bind_param("s", $date);
                                    $stmt->execute();
                                    $stmt->bind_result($from, $to, $event, $location, $agency, $safety, $remarks);
                                    
                                    $i = 0;
                                    while($stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>".date_format(date_create($from), "H:i")."</td>";
                                        echo "<td>".date_format(date_create($to), "H:i")."</td>";
                                        echo "<td>".$event."</td>";
                                        echo "<td>".$location."</td>";
                                        echo "<td>".$agency."</td>";
                                        echo "<td>".$safety."</td>";
                                        echo "<td>".$remarks."</td>";
                                        echo "</tr>";
                                        $i++;
                                    }
                                    
                                    if ($i == 0) {
                                        echo "<tr><td colspan='7'>---</td></tr>";
                                    }
                                    
                                    $stmt->close();
                                } else {
                                    debug_print("MySQL Error: ".$stmt->error);
                                }
                            } else {
                                echo "<tr><td colspan='7'>---</td></tr>";
                            }
                            ?>
                        </tbody>
                    
                    </table>
                </section>
            </fieldset>
        </form>
<?php } ?>
    </div>


<?php
include_once('body_end.php');
?>




