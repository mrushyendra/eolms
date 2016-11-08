<?php
include_once('head_start.php');
    $PageTitle = "Indents";
?>
    <script type="text/javascript">
        var selectedRow = undefined;
        var lastClicked = 0;
        
        $(document).ready(function(){
            $('table.filesharing tr.selectable').click(function () {
                var dis = $(this);
                var now = $.now();
                
                if (dis.is(selectedRow)) {
                    if (now - lastClicked <= 500) {
                        var child = selectedRow.children(":first");

                        if (child.hasClass("i_folder")) {
                            var parent = $.trim($('#parent').val());
                            parent += (parent == "" ? "" :"/");

                            window.location.href = "files.php?parent=" + parent + child.text();
                        } else {
                            window.location.href = "files_download.php?parent=" + parent + "&name=" + child.text();
                        }

                        lastClicked = 0;
                    } else {
                        lastClicked = now;
                    }
                } else {
                    if (selectedRow !== undefined) {
                        selectedRow.css("background-color", "white");
                    }

                    selectedRow = dis;
                    dis.css("background-color", "#DDDDFF");
                    lastClicked = now;
                } 
            });
            
            $('button.deletebutton').click(function (){
               if (selectedRow !== undefined) {
                    var name = selectedRow.children(':first').text();
                    window.location.href = "files_delete.php?parent=" + $('#parent').val() + "&name=" + name;
               }
            });
            
            $('#fileform').submit(function () {
                var children = [];
                $('#filebody').children('tr').each(function (){
                   children.push($(this).children(':first')); 
                });
                
                var filename = $('#file').val().split('\\').pop().toLowerCase();
                var samename = false;
                for(i = 0; i < children.length; ++i) {
                    var child = children[i];
                    if (child.text().toLowerCase() == filename) {
                        samename = true;
                        break;
                    }
                }
                if (samename) {
                    return confirm("A file with the same name exists. Overwrite?");
                }
                
                return true;
            });
            
            $('button.newfolderbutton').click(function () {
                var name = prompt("Enter a name for the new folder:", "New Folder");
                window.location.href = "files_newfolder.php?parent=" + $('#parent').val() + "&name=" + name;
            });
            
            $('table.filesharing').disableSelection();
        });
    </script>

    <style type="text/css">
        table.filesharing {
            margin-bottom: 0px;
        }

        table.filesharing thead td {
            font-weight: bold;
        }

        table.filesharing td {
            text-align: left;
            padding-left: 1%;
            vertical-align: middle;
        }

        table.filesharing td.i_document {
            background: url(css/images/icons/dark/document.png) no-repeat 50% 50%;
            background-position: 1%;
            padding-left: 30px;
        }

        table.filesharing td.i_folder {
            background: url(css/images/icons/dark/folder.png) no-repeat 50% 50%;
            background-position: 1%;
            vertical-align: middle;
            padding-left: 30px;
        }
    </style>

<?php
include_once('scripts.php');
include_once('head_end.php');
include_once('body_start.php');
include_once('files_base.php');

?>    
<?php if (isset($Permissions["files_download"])) { ?>
    <div class="g12" style="padding:1px">
        <form id='fileform' method="POST" action="files_upload.php" enctype="multipart/form-data">
            <fieldset>
                <label>Files</label>
                <section>
                    <?php if(isset($_GET["err"]) && $_GET["err"] == 1) { ?>
                    <div style='color: red'>Error! Invalid parent directory given.</div>
                    <?php } else if (isset($_GET["err"]) && $_GET["err"] == 2) { ?>
                    <div style='color: red'>Error! Invalid file name or resulting file path too long.</div>
                    <?php } else if (isset($_GET["err"]) && $_GET["err"] == 3) { ?>
                    <div style='color: red'>Error! File size too big!</div>
                    <?php } else if (isset($_GET["err"]) && $_GET["err"] == 4) { ?>
                    <div style='color: red'>Error! No file received!</div>
                    <?php } ?>
                    <table class='filesharing'>
                        <thead>
                            <tr>
                                <td width="35%">Name</td>
                                <td width="25%">Uploaded By</td>
                                <td width="25%">Last Modified</td>
                                <td width="15%">Size</td>
                            </tr>
                        </thead>
                        <tbody id="filebody">
                            <?php
                            
                            $parent = isset($_GET["parent"]) ? $_GET["parent"] : "";
                            $parentTruePath = truePath($parent);
                            if (!isValidDir($parent) || !is_dir($parentTruePath)) {
                                header("Location: files.php?err=1");
                                die();
                            }
                            
                            $files = array();
                            
                            $iterator = new FilesystemIterator($parentTruePath);
                            foreach($iterator as $file) {
                                $key = $file->getFilename();
                                $key = ($file->isDir() ? "0" : "1").$key;
                                $files[$key] = $file;
                            }
                            
                            uksort($files, 'strcasecmp');
                            
                            $stmt = $mysql->stmt_init();
                            if (!$stmt->prepare("SELECT uploader FROM files WHERE path = ?;")) {
                                debug_print("MySQL Error: " + $stmt->error);
                                die();
                            }
                            
                            foreach($files as $file) {
                                $icon = $file->isDir() ? "i_folder" : "i_document";
                                $name = $file->getFilename();
                                $modified = $file->getMTime();
                                $size = $file->getSize();
                                $uploader = "-";
                                
                                $stmt->bind_param("s", concatPath($parent, $name));
                                $stmt->execute();
                                $stmt->bind_result($uploader);
                                $stmt->fetch();
                                
                                echo "<tr class='selectable'>"
                                    . " <td class='".$icon."'>"
                                        .$name
                                    . " </td>"
                                    . " <td>".$uploader."</td>"
                                    ." <td>".date("d/m/Y g:i A", $modified)."</td>"
                                    . " <td>".formatSize($size)."</td>"
                                    . "</tr>";
                            }
                            
                            $stmt->close();
                            ?>
                        </tbody>
                        
                        <tbody>
                            <tr>
                                <td colspan="4">
                                    <div><br /><br /></div>
                                    <div>
                                        <input type='hidden' id="parent" name='parent' value='<?php echo $parent; ?>'></input>
                                        
                                        <?php if (isset($Permissions["files_upload"])) { ?>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="104857600"></input>
                                        <input style='float: left;' type='file' name='file' id='file'></input>
                                        <button style='float: left; top:1px' class='submit'>Upload</button>
                                        <?php } ?>
                                        
                                        <?php if (isset($Permissions["files_delete"])) { ?>
                                        <button style='float: right; top:1px' class='deletebutton'>Delete</button>
                                        <?php } ?>
                                        
                                        <?php if (isset($Permissions["files_newfolder"])) { ?>
                                        <button style='float: right; top:1px' class='newfolderbutton'>New Folder</button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </section>
            </fieldset>
        </form>
    </div>
<?php } ?>

<?php
include_once('body_end.php');
?>