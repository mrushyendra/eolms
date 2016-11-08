<?php
include_once('database_connect.php');
include_once('verify_login.php');
include_once('permissions.php');

if (isset($Permissions["opslog_read"])) {
    // Max num of messages to show per page.
    $entriesPerPage = 20;

    // Set the received GET parameters.
    $searchUser = "%";
    if(isset($_GET["filteruser"]) && strlen($_GET["filteruser"]) > 0) {
        $searchUser = $_GET["filteruser"];
    }
    $searchImportance = 0;
    if (isset($_GET["filterimpt"]) && is_numeric($_GET["filterimpt"])) {
        $searchImportance = intval($_GET["filterimpt"]);
    }
    if (isset($_GET["filteruserexact"])) {
        $searchUserExact = $_GET["filteruserexact"];
        if ($searchUserExact == "false") {
            $searchUser = "%".$searchUser."%";
        }
    }
    $page = 1;
    if (isset($_GET["opsPage"]) && is_numeric($_GET["opsPage"])) {
        $page = intval($_GET["opsPage"]);
    }


    $totalPages = 1;

    // Count total number of pages of messages we have.
    $stmtCount = $mysql->stmt_init();
    if ($stmtCount->prepare("SELECT COUNT(*) FROM opslog WHERE poster LIKE ? AND importance >= ?;")) {
        $stmtCount->bind_param("si", $username, $searchImportance);
        $stmtCount->execute();
        $stmtCount->bind_result($numEntries);
        $stmtCount->fetch();
        $stmtCount->close();
        $totalPages = (int)(($numEntries - 1) / $entriesPerPage) + 1;
    } else {
        debug_print("MySQL Error: " . $stmtCount->error);
    }

    // Check (and if need be, force) the page to be between 1 and total num of pages.
    if ($page < 1) {
        $page = 1;
    } else if ($page > $totalPages) {
        $page = $totalPages;
    }

    // Calculate offset to start retrieving messages from.
    $offset = ($page - 1) * $entriesPerPage;


    // Retrieve messages from database.
    $stmt = $mysql->stmt_init();
    if ($stmt->prepare("SELECT poster, message, time, importance FROM opslog WHERE poster LIKE ? AND importance >= ? ORDER BY time DESC, id ASC LIMIT ? OFFSET ?;")) {
        $stmt->bind_param("siii", $searchUser, $searchImportance, $entriesPerPage, $offset);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($username, $message, $time, $importance);

        $numRows = $stmt->num_rows();

        // No messages retrieved.
        if($numRows == 0) { ?>
            <table>
                <tr><th>No Messages</th></tr>
            </table>
        <?php 
        // Message(s) retrieved.
        } else { 
        ?>
            <table style="margin-bottom:0px;">
                <thead>
                    <tr style="font-size:11px;">
                        <th colspan="2">
                            <script type="text/javascript">
                                function previousOpsPage() {
                                    currentOpsPage -= 1;
                                    refreshOpsLog();
                                }
                                function nextOpsPage() {
                                    currentOpsPage += 1;
                                    refreshOpsLog();
                                }
                            </script>

                            <?php 
                                // Show 'next' and 'previous' links to client for page traversall.
                                echo "Page " . $page . " / " . $totalPages . ""; 

                                $prevStr = "<div style='float:left;margin-left:8px'>";
                                $nextStr = "<div style='float:right;margin-right:8px'>";

                                if ($page > 1) {
                                    $prevStr .= "<a onclick='previousOpsPage()'>&lt; Prev</a></div>";
                                } else {
                                    $prevStr .= "&lt; Prev</div>";
                                }

                                if ($page < $totalPages) { 
                                    $nextStr .= "<a onclick='nextOpsPage()'>Next ></a></div>";
                                } else {
                                    $nextStr .= "Next ></div>";
                                }

                                echo $prevStr;
                                echo $nextStr;
                            ?>
                        </th>
                    </tr>
                    <tr>
                        <th width='100'>User</th>
                        <th>Message</th>
                    </tr>
                <thead>
                <tbody> <?php
                // Show the messages in a table format.
                    $trBgcolor = "#ffffff";

                    while($stmt->fetch()) {
                        if ($importance == 1) {
                            $postFix = "IMPORTANT";
                            $postFixColor = "orange";
                        } else if ($importance >= 2) {
                            $postFix = "VERY IMPORTANT";
                            $postFixColor = "red";
                        } else {
                            $postFix = "";
                            $postFixColor = "";
                        }

                        echo "<tr bgcolor='" . $trBgcolor . "'>
                                <td>" . $username . " </td>
                                <td style='text-align:left;'>
                                <div>" . $message . "</div>
                                <div style='font-size:9px;color:#888'>Posted on " . date('l, d F Y, H:i:s',$time) . ".</div>
                                <div style='font-size:8px;color:" . $postFixColor . "'>" . $postFix . "</div>
                                </div>
                                </td>
                            </tr>";
                    }
                ?> </tbody>
            </table>
            <?php
            $stmt->free_result();
            $stmt->close();
        }
    } else {
        debug_print("MySQL Error: ".$stmt->error);  
    }

}

?>