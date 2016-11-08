<?php
include_once('head_start.php');
    $PageTitle = "Indents";
?>
<script type="text/javascript">
    $(document).ready(function() {
        var btnDone = [$('button.btnDone1'),
            $('button.btnDone2'),
            $('button.btnDone3'),
            $('button.btnDone4')];
        
        function informDoneUndo() {
            var button = $(this);
            var done = shouldDisplayDone(button);
            var column = -1;
            
            for(var i = 0; i < btnDone.length; ++i) {
                if (button.is(btnDone[i])) {
                    column = i;
                    button.fadeTo(0, 0.5);
                    button.attr('disabled', 'true');
                    break;
                }
            }
            
            var idCol = button.parent().parent().children().first();
            $.get('indents_done.php', {
                    done: done,
                    column: column,
                    id: idCol.text() },
                function(data) {
                    /*if (data.length == 0 || data.charAt(0) != '(') {
                        window.location.replace("login.php?login=timeout");
                        return;
                    }*/
                    var color = done ? "#090" : "#900";
                    button.prev().prev().text(data).html();
                    button.prev().prev().css("color", color);
                    updateText(button);
                    button.fadeTo(0, 1.0);
                    button.removeAttr('disabled');
                }
            );
        }
        
        function shouldDisplayDone(btn) {
            return btn.prev().prev().text() == "( - )";
        }
        
        function updateText(button) {
            button.text(shouldDisplayDone(button) ? "DONE" : "UNDO");
        }
        
        for(var i = 0; i < btnDone.length; ++i) {
            var button = btnDone[i];
            button.click(informDoneUndo);
            button.each(function() {
                updateText($(this));
            });
        }
    });
</script>

<?php
include_once('scripts.php');
include_once('head_end.php');
include_once('body_start.php');
?>

<?php if (isset($Permissions["indents_upload"])) { ?>
    <div class="g12" style="padding:1px;">
        <form method="POST" enctype="multipart/form-data" action="indents_upload.php">
            <fieldset>
                <label>Upload Indent</label>
                <section>
                    <label>Date:</label>
                    <div>
                        <input class="date" type="text" name="upinddate"></input>
                    </div>
                    <?php if(isset($_GET["uperrdate"])) { ?>
                    <div style='color: red'>Error! Please use the picker or the format yyyy-mm-dd.</div>
                    <?php } ?>
                </section>
                <section>
                    <label>XML File:</label>
                    <div>
                        <input type="hidden" name="MAX_FILE_SIZE" value="1048576"></input>
                        <input type="file" name="upindfile"></input>
                        <?php if(isset($_GET["uperrformat"])) { ?>
                        <div style='color: red'>Error! Please upload only XML files exported from Excel spreadsheet.</div>
                        <?php } else if (isset($_GET["uperrcell"])) {
                            $celltitle = isset($_GET["uperrcellname"]) ? $_GET["uperrcellname"] : "";
                            echo "<div style='color: red'>Table Error! The system expected column ".$_GET["uperrcell"]." to be '".$celltitle."'.</div>";
                        } ?>
                    </div>
                </section>
                <section>
                    <label></label>
                    <div>
                        <button class="submit">Submit</button>
                        <br />
                        <br />
                        <div style='color: red; font-style: oblique'>Note: Clicking 'submit' will remove all existing indents for the same date.</div>
                    </div>
                </section>
            </fieldset>
        </form>
<?php } ?>
        
<?php if (isset($Permissions["indents_read"])) { ?>
        <form method="GET" action="indents.php">
            <fieldset>
                <label>View Indents</label>
                <section>
                    <label>Date:</label>
                    <div>
                        <input class="date" type="text" name="inddate"></input>
                    </div>
                </section>
                <section>
                    <label>Sports:</label>
                    <div>
                        <input type="text" name="indsports"></input>
                    </div>
                    <div>
                        <input type="checkbox" checked="true" name="indsportsexact" />
                        <label>Exact Match</label>
                    </div>
                </section>
                <?php
                    $dir = isset($_GET["indtype"])? $_GET["indtype"] : null;
                ?>
                <section>
                    <label>Type:</label>
                    <div>
                        <select name="indtype">
                            <option value="Forward Trip" <?php if($dir == "Arrival") echo "selected='selected'";?>>Forward Trip</option>
                            <option value="Return Trip" <?php if($dir == "Departure") echo "selected='selected'";?>>Return Trip</option>
                        </select>
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
        <div style='width:100%; overflow: auto'>
            <fieldset>
                <section>
                    <table>
                        <thead>
                            <tr>
                                <th width='8%'>ID</th>
                                <th width='14%'>Sports</th>
                                <th width='15%'>Hotel</th>
                                <th width='15%'>Venue</th>
                                <th width='12%'>H.A. Departure</th>
                                <th width='12%'>
                                    <?php if($dir == "Return Trip") {
                                            echo "Venue Arrival";
                                    } else {
                                            echo "Hotel Arrival";
                                    } ?>
                                </th>
                                <th width='12%'>
                                    <?php if($dir == "Return Trip") {
                                            echo "Venue Departure";
                                    } else {
                                            echo "Hotel Departure";
                                    } ?>
                                </th>
                                <th width='12%'>
                                    <?php if($dir == "Return Trip") {
                                            echo "Hotel Arrival";
                                    } else {
                                            echo "Venue Arrival";
                                    } ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET["inddate"])) {
                                $filterSports = "%";
                                if(isset($_GET["indsports"]) && strlen($_GET["indsports"]) > 0) {
                                    $filterSports = $_GET["indsports"];
                                }
                                if (isset($_GET["indsportsexact"])) {
                                    $filterExact = $_GET["indsportsexact"];
                                    if ($filterExact == "false") {
                                        $filterSports = "%".$filterSports."%";
                                    }
                                }
                                
                                $date = $_GET["inddate"];
                                $dirInt = $dir == "Return Trip" ? 1 : 0;
                                
                                $stmt = $mysql->stmt_init();
                                if ($stmt->prepare("SELECT id, sports, locfrom, locto, exptime1, exptime2,"
                                        . " exptime3, exptime4, acttime1, acttime2, acttime3, acttime4 FROM indents WHERE date = ? AND direction = ? AND sports LIKE ?;")) {
                                    $stmt->bind_param("sis", $date, $dirInt, $filterSports);
                                    $stmt->execute();
                                    $stmt->bind_result($id, $sports, $from, $to, $exptime1, $exptime2, $exptime3, $exptime4,
                                            $acttime1, $acttime2, $acttime3, $acttime4);
                                    
                                    $i = 0;
                                    while($stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>".$id."</td>";
                                        echo "<td>".$sports."</td>";
                                        echo "<td>".$from."</td>";
                                        echo "<td>".$to."</td>";
                                        echo "<td>".date_format(date_create($exptime1), "h:i A");
                                        echo "<br/><span style='color:".($acttime1 === NULL ? "#900'>( - " : "#090'>(".date_format(date_create($acttime1), "h:i A")).")</span>";
                                        echo "<br/><button class='btnDone1'></button></td>";
                                        echo "<td>".date_format(date_create($exptime2), "h:i A");
                                        echo "<br/><span style='color:".($acttime2 === NULL ? "#900'>( - " : "#090'>(".date_format(date_create($acttime2), "h:i A")).")</span>";
                                        echo "<br/><button class='btnDone2'></button></td>";
                                        echo "<td>".date_format(date_create($exptime3), "h:i A");
                                        echo "<br/><span style='color:".($acttime3 === NULL ? "#900'>( - " : "#090'>(".date_format(date_create($acttime3), "h:i A")).")</span>";
                                        echo "<br/><button class='btnDone3'></button></td>";
                                        echo "<td>".date_format(date_create($exptime4), "h:i A");
                                        echo "<br/><span style='color:".($acttime4 === NULL ? "#900'>( - " : "#090'>(".date_format(date_create($acttime4), "h:i A")).")</span>";
                                        echo "<br/><button class='btnDone4'></button></td>";
                                        echo "</tr>";
                                        $i++;
                                    }
                                    
                                    if ($i == 0) {
                                        echo "<tr><td colspan='8'>No data available.</td></tr>";
                                    }
                                    
                                    $stmt->close();
                                } else {
                                    debug_print("MySQL Error: ".$stmt->error);
                                }
                            } else {
                                echo "<tr><td colspan='8'>No data available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </section>
            </fieldset>
        </div>
        </form>
<?php } ?>
    </div>
<?php
include_once('body_end.php');
?>