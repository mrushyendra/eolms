<?php

include_once('base.php');
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

if (isset($Permissions["indents_upload"])) {

$titles = array("Indent ID", "Sports", "Hotel", "Venue", "H.A. Departure", "Hotel Arrival", "Hotel Departure", "Venue Arrival");


$date = "";
if (isset($_POST["upinddate"])) {
    $date = $_POST["upinddate"];
} else {
    header("Location: indents.php?uperrdate=1");
    die();
}


$xml = simplexml_load_file($_FILES["upindfile"]["tmp_name"]);

if ($xml) {
    $id = array();
    $sports = array();
    $loc1 = array();
    $loc2 = array();
    $time1 = array();
    $time2 = array();
    $time3 = array();
    $time4 = array();
    
    function addData($col, $data) {
        global $id;
        global $sports;
        global $loc1;
        global $loc2;
        global $time1;
        global $time2;
        global $time3;
        global $time4;

        switch($col) {
            case 1:
                array_push($id, $data);
                break;
            case 2:
                array_push($sports, $data);
                break;
            case 3:
                array_push($loc1, $data);
                break;
            case 4:
                array_push($loc2, $data);
                break;
            case 5:
                array_push($time1, $data);
                break;
            case 6:
                array_push($time2, $data);
                break;
            case 7:
                array_push($time3, $data);
                break;
            case 8:
                array_push($time4, $data);
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
                                                        header("Location: indents.php?uperrcell=".$cellNum."&uperrcellname=".$titles[$cellNum - 1]);
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
                            
                            // Add blank fields for missing columns.
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
    if ($stmt->prepare("DELETE FROM indents WHERE date = ?;")) {
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $mysql->stmt_init();
        if ($stmt->prepare("INSERT INTO indents(id, date, sports, locfrom, locto, exptime1, exptime2, exptime3, exptime4, direction) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);")) {
           for($i = 0; $i < count($id); ++$i) {
               $stmt->bind_param("sssssssss", $id[$i], $date, $sports[$i], $loc1[$i], $loc2[$i], $time1[$i], $time2[$i], $time3[$i], $time4[$i]);
               $stmt->execute();
           }
           
           $stmt->close();
       } else {
           debug_print("MySQL Error: ".$stmt->error);
       }
    } else {
        debug_print("MySQL Error: ".$stmt->error);
    }
    
    header("Location: indents.php?upinddate=".$date);
    die();
} else {
    header("Location: indents.php?uperrformat=1");
    die();
}

}