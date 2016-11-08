<?php
include_once('head_start.php');
    $PageTitle = "Admin Panel";
    
include_once('scripts.php');
?>
    <script type='text/javascript'>
        $(document).ready(function() {
            $("#permview").click(function () {
                window.location.replace("admin.php?pm_refresh=true&pm_username=" + encodeURIComponent($("#pm_username").val()));
            });
            
            $('#submitbutton').fadeTo(0, 0.5);
            
            function verify() {
                if ($('#confirm').val() != $('#password').val()) {
                   $('#pwerr').show()
                   $('#submitbutton').fadeTo(0, 0.5);
                   $('#submitbutton').attr('disabled', 'true');
               } else if($('#password').val().length > 0 && $('#username').val().length > 0) {
                   $('#pwerr').hide();
                   $('#submitbutton').fadeTo(0, 1);
                   $('#submitbutton').removeAttr('disabled');
               }
            }
            
            $('#confirm').keyup(verify);
            $('#password').keyup(verify);
        });
    </script>
<?php
include_once('head_end.php');
include_once('body_start.php');
?>    
<div class="g12" style="padding:1px">
    <?php if (isset($Permissions["admin_newuser"])) { ?>
    <form method="POST" action="admin_newuser.php">
        <fieldset>
            <label>Create User</label>
            <section>
                <label>Username:</label>
                <div>
                    <input id='username' name="username" type="text" maxlength="32" style="width: 100%"></input>
                    <?php if (isset($_GET["err"]) && $_GET["err"] == 1) { ?>
                    <div style='color:red'>Username too short. Minimum 4 characters.</div>
                    <?php } else if(isset($_GET["err"]) && $_GET["err"] == 3) { ?>
                    <div style='color:red'>Username is already taken!</div>
                    <?php } else { ?>
                    <br /><br/>
                    <?php } ?>
                </div>
            </section>
            
            <section>
                <label>Password:</label>
                <div>
                    <input id="password" name="password" type="password" maxlength="32" style="width: 100%"></input>
                    <?php if (isset($_GET["err"]) && $_GET["err"] == 2) { ?>
                    <div style='color:red'>Password too short. Minimum 8 characters.</div>
                    <?php } ?>
                </div>
            </section>
            <section>
                <label>Confirm Password: </label>
                <div>
                    <input id="confirm" name='confirm' type="password" maxlength="32" style="width: 100%"></input>
                </div>
                <div style='color:red' id='pwerr' hidden="true">
                    Password does not match!
                </div>
            </section>
            
            <section>
                <label></label>
                <div><button class="submit" id="submitbutton" disabled="true">Submit</button></div>
            </section>
        </fieldset>
    </form>
    <?php } ?>
    
    <?php if (isset($Permissions["admin_editperm"])) { ?>
    <form>
        <fieldset>
            <label>Edit Permissions</label>
            <?php
            if (isset($_GET["pm_username"])) {
                if (isset($_GET["pm_refresh"])) {
                    $permissions = array();
                    $stmt = $mysql->stmt_init();
                    if ($stmt->prepare("SELECT count(*) FROM users WHERE username = ?;")) {
                        $stmt->bind_param("s", $_GET["pm_username"]);
                        $stmt->execute();
                        $stmt->bind_result($count);
                        $stmt->fetch();
                        $stmt->close();

                        if ($count == 1) {
                            $stmt2 = $mysql->stmt_init();
                            if ($stmt2->prepare("SELECT permission FROM permissions WHERE username = ?;")) {
                                $stmt2->bind_param("s", $_GET["pm_username"]);
                                $stmt2->execute();
                                $stmt2->bind_result($permission);
                                $stmt2->store_result();

                                while ($stmt2->fetch()) {
                                    $permissions[$permission] = true;
                                }

                                $stmt2->free_result();
                                $stmt2->close();
                            } else {
                                debug_print("MySQL Error: ".$stmt2->error);
                            }
                        } else {
                            $error = true;
                        }
                    } else {
                        debug_print("MySQL Error: ".$stmt->error);
                    }
                } else {
                    $newperm = array();
                    $newperm["admin_newuser"] = isset($_GET["pm_admin_newuser"]) && ($_GET["pm_admin_newuser"] == "true" || $_GET["pm_admin_newuser"] == "on");
                    $newperm["admin_editperm"] = isset($_GET["pm_admin_editperm"]) && ($_GET["pm_admin_editperm"] == "true" || $_GET["pm_admin_editperm"] == "on");
                    $newperm["opslog_read"] = isset($_GET["pm_opslog_read"]) && ($_GET["pm_opslog_read"] == "true" || $_GET["pm_opslog_read"] == "on");
                    $newperm["opslog_write"] = isset($_GET["pm_opslog_write"]) && ($_GET["pm_opslog_write"] == "true" || $_GET["pm_opslog_write"] == "on");
                    $newperm["indents_read"] = isset($_GET["pm_indents_read"]) && ($_GET["pm_indents_read"] == "true" || $_GET["pm_indents_read"] == "on");
                    $newperm["indents_upload"] = isset($_GET["pm_indents_upload"]) && ($_GET["pm_indents_upload"] == "true" || $_GET["pm_indents_upload"] == "on");
                    $newperm["schedule_read"] = isset($_GET["pm_schedule_read"]) && ($_GET["pm_schedule_read"] == "true" || $_GET["pm_schedule_read"] == "on");
                    $newperm["schedule_upload"] = isset($_GET["pm_schedule_upload"]) && ($_GET["pm_schedule_upload"] == "true" || $_GET["pm_schedule_upload"] == "on");

                    $stmtInsert = $mysql->stmt_init();
                    if ($stmtInsert->prepare("INSERT IGNORE INTO permissions(username, permission) VALUES(?, ?);")) {
                        foreach($newperm as $perm => $hasperm) {
                            if ($hasperm) {
                                $stmtInsert->bind_param("ss", $_GET["pm_username"], $perm);
                                $stmtInsert->execute();
                            }
                        }
                        $stmtInsert->close();
                    } else {
                        debug_print("MySQL Error: ".$stmtInsert->error);
                    }

                    $stmtRemove = $mysql->stmt_init();
                    if ($stmtRemove->prepare("DELETE FROM permissions WHERE username = ? AND permission = ?;")) {
                        foreach($newperm as $perm => $hasperm) {
                            if (!$hasperm) {
                                $stmtRemove->bind_param("ss", $_GET["pm_username"], $perm);
                                $stmtRemove->execute();
                            }
                        }
                        $stmtRemove->close();
                    } else {
                        debug_print("MySQL Error: ".$stmtRemove->error);
                    }

                    $_GET["pm_username"] = $_GET["pm_username"];
                }
            }
            ?>
            
            <section>
                <label>Username:</label>
                <div>
                    <input id="pm_username" name="pm_username" type="text"></input>
                    <div style="color:red"><?php if (isset($error)) { echo "Invalid username."; } ?></div>
                </div>
            </section>
            
            <section>
                <label>Administration:</label>
                <div>
                    <input name="pm_admin_newuser" type="checkbox" <?php if (isset($permissions["admin_newuser"])) { echo "checked='true'"; }?>></input>
                    <label>Create User</label>
                    <br />
                    <input name="pm_admin_editperm" type="checkbox" <?php if (isset($permissions["admin_editperm"])) { echo "checked='true'"; }?>></input>
                    <label>Edit Permissions</label>
                </div>
            </section>
            
            <section>
                <label>Operations Log:</label>
                <div>
                    <input name="pm_opslog_read" type="checkbox" <?php if (isset($permissions["opslog_read"])) { echo "checked='true'"; }?>></input>
                    <label>Read</label>
                    <br />
                    <input name="pm_opslog_write" type="checkbox" <?php if (isset($permissions["opslog_write"])) { echo "checked='true'"; }?>></input>
                    <label>Write</label>
                </div>
            </section>
            
            <section>
                <label>Indents:</label>
                <div>
                    <input name="pm_indents_read" type="checkbox" <?php if (isset($permissions["indents_read"])) { echo "checked='true'"; }?>></input>
                    <label>Read</label>
                    <br />
                    <input name="pm_indents_upload" type="checkbox" <?php if (isset($permissions["indents_upload"])) { echo "checked='true'"; }?>></input>
                    <label>Upload</label>
                </div>
            </section>
            
            <section>
                <label>Schedule:</label>
                <div>
                    <input name="pm_schedule_read" type="checkbox" <?php if (isset($permissions["schedule_read"])) { echo "checked='true'"; }?>></input>
                    <label>Read</label>
                    <br />
                    <input name="pm_schedule_upload" type="checkbox" <?php if (isset($permissions["schedule_upload"])) { echo "checked='true'"; }?>></input>
                    <label>Upload</label>
                </div>
            </section>
            
            <section>
                <label></label>
                <div>
                    <button id="permview">View</button>
                    <button class="submit">Submit</button>
                </div>
            </section>
        </fieldset>
    </form>
    <?php } ?>
</div>

<?php
include_once('body_end.php');
?>