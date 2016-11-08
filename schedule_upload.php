<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

if(isset($Permissions["schedule_upload"])) {

$titles = array("Id", "From", "To", "Event", "Location", "Agency", "Safety", "Remarks");


$date = "";
if (isset($_POST["upschdate"])) {
    $date = $_POST["upschdate"];
} else {
    header("Location: schedule.php?upscherrdate=1");
    die();
}


$xml = simplexml_load_file($_FILES["upschfile"]["tmp_name"]);

if ($xml) {
    $from = array();
    $to = array();
    $event = array();
    $location = array();
    $agency = array();
    $safety = array();
    $remarks = array();
    
    function addData($col, $data) {
        global $from;
        global $to;
        global $event;
        global $location;
        global $agency;
        global $safety;
        global $remarks;

        switch($col) {
            case 2:
                array_push($from, $data);
                break;
            case 3:
                array_push($to, $data);
                break;
            case 4:
                array_push($event, $data);
                break;
            case 5:
                array_push($location, $data);
                break;
            case 6:
                array_push($agency, $data);
                break;
            case 7:
                array_push($safety, $data);
                break;
            case 8:
                array_push($remarks, $data);
                break;
        }
    }
    
    
    // Process only worksheets.
    foreach($xml->children() as $child) {
        if ($child->getName() == "Worksheet") {
            // NOTE: Var currently unused.
            $worksheet = $child;
            
            // Find the name of this worksheet.
            $sheetName = "<Unnamed>";
            foreach($worksheet->attributes("ss", TRUE) as $attr) {
                if ($attr->getName() == "Name") {
                    $sheetName = $attr;
                }
            }
            
            // Process only tables.
            foreach ($worksheet->children() as $child) {
                if ($child->getName() == "Table") {
                    $table = $child;
                    
                    $rowNum = 0;
                    // Process only rows.
                    foreach($table->children() as $child) {
                        if ($child->getName() == "Row") {
                            $row = $child;
                            $rowNum++;
                            
                            $cellNum = 0;
                            // Process only cells.
                            foreach($row->children() as $child) {
                                if ($child->getName() == "Cell") {
                                    $cell = $child;
                                    $cellNum++;
                                    
                                    // Process only datas.
                                    foreach ($cell->children() as $child) {
                                        if ($child->getName() == "Data") {
                                            $data = $child;
                                            
                                            // Ignore everything else that we don't want.
                                            if ($cellNum - 1 < count($titles)) {
                                                // Title row
                                                if ($rowNum == 1) {
                                                    $strippedTitle = strtolower(trim($titles[$cellNum - 1]));
                                                    $strippedData = strtolower(trim($data));
                                                    
                                                    // Error, title is different.
                                                    if ($strippedData != $strippedTitle) {
                                                        header("Location: schedule.php?upscherrcell=".$cellNum."&upscherrcellname=".$titles[$cellNum - 1]);
                                                        die();
                                                    }
                                                } else {
                                                    $data = empty($data) ? "" : (string)$data;
                                                    addData($cellNum, $data);
                                                }
                                            }                                            
                                        }
                                    }
                                }
                            }
                            
                            // Add blank fields;
                            for(; $cellNum - 1 < count($titles); ++$cellNum) {
                                addData($cellNum, "");
                            }
                            
                        }
                    }
                }                
            }
            break; // At this point, handles only one worksheet.
        }
    }
    
    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("DELETE FROM schedules WHERE date = ?;")) {
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $mysql->stmt_init();
        if ($stmt->prepare("INSERT INTO schedules(date, timefrom, timeto, event, location, agency, safety, remarks) VALUES(?, ?, ?, ?, ?, ?, ?, ?) ")) {
           for($i = 0; $i < count($from); ++$i) {
               $stmt->bind_param("ssssssss", $date, $from[$i], $to[$i], $event[$i], $location[$i], $agency[$i], $safety[$i], $remarks[$i]);
               $stmt->execute();
           }
           
           $stmt->close();
       } else {
           debug_print("MySQL Error: ".$stmt->error);
       }
    } else {
        debug_print("MySQL Error: ".$stmt->error);
    }
    
    header("Location: schedule.php?schdate=".$date);
    die();
} else {
    header("Location: schedule.php?upscherrformat=1");
    die();
}

}