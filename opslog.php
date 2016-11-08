<?php
include_once("head_start.php");
    $PageTitle = "Home";

include_once('scripts.php');
?>
    <script type='text/javascript'>
        var currentOpsPage = 1;
        var filteruser;
        var filterimportance;
        var filteruserexact;
        
        function refreshOpsLog() {
            $.get('opslog_retrievemsg.php', {
                filteruser:filteruser,
                filterimpt:filterimportance,
                filteruserexact:filteruserexact,
                opsPage:currentOpsPage },
                function(data) {
                    $('#opslogcontent').empty().append(data);
                }
            );
        }
        
        function loopRefresh() {
            refreshOpsLog();
            setTimeout('loopRefresh()', 5000);
        }
        
        function submitFilter(scroll) {
            filteruser = $('#filteruser').val();
            filterimportance = $('#filterimpt').val();
            filteruserexact = $('#filteruserexact').is(":checked");
            refreshOpsLog();
            
            if (scroll) {
                $('html, body').animate({
                    scrollTop: $('#opslogheader').offset().top
                }, 500);
            }
        }
        
        $(document).ready(function() {
            submitFilter(false);
            setTimeout('loopRefresh()', 5000);
            
            $('#filterbutton').click(function(){
                submitFilter(true);
            });
        });
    </script>
<?php
include_once("head_end.php");
include_once('body_start.php');
include_once('permissions.php');

    if(isset($Permissions["opslog_write"])) {  
?>
    <div class="g12" style="padding:1px">
        <form method="POST" action="opslog_addmsg.php">
            <fieldset>
                <label>Post Message</label>
                <section>
                    <label for="postmsg">Message:</label>
                    <div>
                        <textarea id="postmsg" name="postmsg" maxlength="255" row="1"></textarea>
                        <?php if(isset($_GET["postmsgerr"])) { ?>
                        <div style='color: red'>Error! Please enter at least 4 characters.</div>
                        <?php } ?>
                    </div>
                </section>
                
                <section>
                    <label>Importance:</label>
                    <div>
                        <select id="postimpt" name="postimpt">
                            <option value="0" id="0">Low</option>
                            <option value="1" id="1">Medium</option>
                            <option value="2" id="2">High</option>
                        </select>
                    </div>
                </section>
                
                <section>
                    <label></label>
                    <div><button class="submit">Submit</button></div>
                </section>
            </fieldset>
        </form>
    <?php
    }
    
    if(isset($Permissions["opslog_read"])) {  
    ?>
        <form>
            <fieldset id="settings">
                <label>Filter</label>
                <section>
                    <label for="filteruser">User:</label>
                    <div>
                        <input type="text" id="filteruser" name="filteruser" />
                    </div>
                    <div>
                        <input type="checkbox" checked="true" id="filteruserexact" name="filteruserexact" />
                        <label for="filteruserexact">Exact Match</label>
                    </div>
                </section>
                <section>
                    <label for="filterimpt">Importance:</label>
                    <div>
                        <select id="filterimpt" name="filterimpt">
                            <option value="0">All</option>
                            <option value="1">Medium And High</option>
                            <option value="2">High Only</option>
                        </select>
                    </div>
                </section>
                <section>
                    <label></label>
                    <div>
                        <button id="filterbutton">Change</button>
                    </div>
                </section>
            </fieldset>
        </form>
        
        <form>
            <fieldset>
                <label id="opslogheader">Operations Log</label>
                <div id="opslogcontent">
                </div>
            </fieldset>
        </form>
    <?php } ?>
    </div>
<?php 
include_once('body_end.php');
?>