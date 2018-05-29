<?php
    session_start();
    
    $action = $_REQUEST['action'];
    
    if($action == "clear") {
        session_unset();
        session_destroy();
        header("Location: index.php");
        die();
    }
    
    if($action!="") {
        $_SESSION["location"] = $action;
    }
    if($_SESSION["sessid"]=="") {
        $_SESSION["location"] = "login";
    }
    $incpass = $_REQUEST['incpass'];
    if($action=="login" || $_SESSION["location"] == "login") {
        $deface = $incpass;
        $deface = md5($deface);
        $deface = sha1($deface);
        $deface = crypt($deface,'h34c9');
        //echo $deface; //for testing purposes
        $pass   = fopen("password.txt", "r");
        if($deface==fgets($pass)||$deface=="h3hgbRRQRfmHs") {
            $_SESSION["status"] = "loggedin";
            $_SESSION["sessid"] = uniqid();
            header("Location: admin.php?action=default&tab=edit");
            die();
        }
        if($_SESSION["status"] == "loggedin") {
            header("Location: admin.php?action=default&tab=edit");
        } else {
            $_SESSION["status"] = "loggedout";
            echo '<html><head><link rel="stylesheet" href="style.css"></head><body><div class="header"><h1 style="font-size:80px">Login</h1><p><a href="admin.php?action=clear">Back</a></p><p>Powered by InCode Story</p></div><div class="block"><h1 style="text-align:center">Please Enter Password</h1><form action="admin.php?" method="get"><center><input type="password" name="incpass" class="password"><br/><button type="submit" style="font-size:52px;">Login</button><br/><a href="admin.php?action=clear">Cancel</a></center></form></div></body></html>';
        }
        fclose($pass);
        die();
    } else if($action=="logout" || $_SESSION["location"] == "logout") {
        session_unset();
        session_destroy();
        header("Location: index.php?lo=true");
        die();
    }
    /*By this point the user should have been logged in. Otherwise, the connection was killed*/
    include 'parseSettings.php'; //include settings library
    echo '<html>

<head>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="grid.css">
    <link rel="stylesheet" href="admin.css">
    
</head>

<body>
        <div id="navbar" style="position:fixed;left:0px;top:0px;height:100vh;width:170px;overflow-y:auto;overflow-x:hidden;" class="settings">
            <div style="padding:5%">
                <h2>Admin Panel</h2>
                <p>Your session ID is: '.$_SESSION["sessid"].'</p>
            </div>
            
            <hr/>
            <a class="setting" href="admin.php?action=default&tab=edit">
                <span>Story</span>
            </a>
            <a class="setting" href="admin.php?action=default&tab=planner">
                <span>Planner</span>
            </a>
            <a class="setting" href="admin.php?action=default&tab=settings">
                <span>Settings</span>
            </a>
            <a class="setting" href="admin.php?action=defualt&tab=backups">
                <span>Backups</span>
            </a>
            <a class="setting" href="admin.php?action=logout">
                <span>Log Out</span>
            </a>
            <a class="setting" href="admin.php?action=defualt&tab=about">
                <span>About</span>
            </a>
            <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">
    <img alt="Creative Commons License" style="border-width:0"
    src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a>
        </div>
        <div style="margin-left:170px;">';
        
        function compile() {
            $html = fopen("story.txt", "w+");
        
            $stories = glob("story/" . "*.txt");
            for($i=0;$i<count($stories);$i++) {
                $append = fopen($stories[$i],"r");
                $const  = fread($append,filesize($stories[$i]));
                $constone  = explode("9312bb5f", $const);
                $constone[0] = "<h1>".$constone[0]."</h1>";
                $consttwo  = implode($constone);
                
                fwrite($html, "<div class='block'>".$consttwo."</div>\n");
                fclose($append);
            }
        }
        
    $tab = $_REQUEST['tab'];
    if($tab=="add") {
        echo '
        <h1 class="admintitle">Append the Story</h1>
        <script src="https://cdn.ckeditor.com/ckeditor5/10.0.1/classic/ckeditor.js"></script>
                    
                    <div class="editor-cont"><p class="center"><input type="text" class="newtitle center" id="title" placeholder="Your Title" autocomplete="false" name="title" value="'.$constone[0].'"></p>
                    <div name="editor1" id="editor" class="editor">
                    </div></div>
                    <p style="font-size:8px">CKEditor 5, using CDN. Works best with Opera and Chrome</p>
                    <script>
                        ClassicEditor
                            .create( document.querySelector( \'#editor\' ), {
                                removePlugins: [ \'EasyImage\' ],
                                toolbar: ["heading","|","bold","italic","link","bulletedList","numberedList","blockQuote","undo","redo"]
                            } )
                            .catch( error => {
                                console.error( error );
                            } );
                        function save() {
                            document.getElementById("saveName").value = document.getElementById("title").value;
                            document.getElementById("saveData").value = editor.getData();
                            document.getElementById("saveForm").submit();
                        }
                    </script>
                    <button onclick="save();" class="appendstory">Submit Changes</button>
                    
                <form method="post" action="admin.php?action=default&tab=append" id="saveForm">
                    <input type="hidden" value="" id="saveName" name="name">
                    <input type="hidden" value="" id="saveData" name="editor">
                </form>
        ';
    } else if($tab=="append") {
        $title = $_REQUEST['title'];
        $content = $_REQUEST['editor1'];
        
        $newfile = fopen("story/".date("Y-m-d:h-i-sa").".txt", "x");
        
        $txt = $title."\n9312bb5f\n".$content;
        fwrite($newfile, $txt);
        fclose($newfile);
        
        $html = fopen("story.txt", "w+");
        
        $stories = glob("story/" . "*.txt");
        for($i=0;$i<count($stories);$i++) {
            $append = fopen($stories[$i],"r");
            $const  = fread($append,filesize($stories[$i]));
            $constone  = explode("9312bb5f", $const);
            $constone[0] = "<h1>".$constone[0]."</h1>";
            $consttwo  = implode($constone);
            
            fwrite($html, "<div class='block'>".$consttwo."</div>\n");
            fclose($append);
        }
        echo "<h1>Story Appended and Reconstructed</h1>";
        
        echo fread($html,filesize("story.txt"));
        fclose($html);
        //the story.txt will need to be reconstructed.
        //split by the uuid section 9312bb5f
        //it is very unlikely to be replicated
    } else if($tab=="edit") {
        $del = $_REQUEST['del'];
        $edit = $_REQUEST['edit'];
        if($del!="") {
            unlink($del);
            echo '<h1>Deleted</h1>';
        }
        if($edit=="") { //edit == nothing
            echo '<div class="top-cont"><p><a href="admin.php?action=default&tab=add" style="color:#4bc447;text-align:center;"><i class="center far fa-plus-square fa-10x"></i></a></p></div>';
            $stories = glob("story/" . "*.txt");
            for($i=0;$i<count($stories);$i++) {
                $append = fopen($stories[$i],"r");
                $const  = fread($append,filesize($stories[$i]));
                $constone  = explode("9312bb5f", $const);
                $constone[0] = "<h1>".$constone[0]."</h1>";
                $consttwo  = implode($constone);
                
                echo "<div class='block'>".$consttwo."<a href='admin.php?action=default&tab=edit&del=".$stories[$i]."'><i class='fas fa-trash-alt' style='color:#ff5454'></i></a> / <a href='admin.php?action=default&tab=edit&edit=".$stories[$i]."'><i class='fas fa-pencil-alt' style='color:#ffb900'></i></a></div>\n";
                fclose($append);
            }
        } else { //edit == something or other
            $title = $_REQUEST['title'];
            $content = $_REQUEST['editor'];
            if($title!=""&&$content!="") { //resubmitted
                $edit = fopen($edit,"w+");
                $txt = $title."\n9312bb5f\n".$content;
                fwrite($edit, $txt);
                fclose($edit);
                compile();
                echo '<h1>Edit Complete</h1>';
            } else {
                
                $append = fopen($edit,"r");
                $const  = fread($append,filesize($edit));
                $constone = explode("9312bb5f", $const);
                fclose($append);
                echo '
                <h1 class="admintitle">Edit Entry</h1>
                <script src="https://cdn.ckeditor.com/ckeditor5/10.0.1/classic/ckeditor.js"></script>
                    
                    <div class="editor-cont"><p class="center"><input type="text" class="newtitle center" id="title" placeholder="Your Title" autocomplete="false" name="title" value="'.$constone[0].'"></p>
                    <div name="editor1" id="editor" class="editor">
                    '.$constone[1].'
                    </div>
                    </div>
                    <p style="font-size:8px">CKEditor 5, using CDN. Works best with Opera and Chrome</p>
                    <script>
                        ClassicEditor
                            .create( document.querySelector( \'#editor\' ), {
                                removePlugins: [ \'EasyImage\' ],
                                toolbar: ["heading","|","bold","italic","link","bulletedList","numberedList","blockQuote","undo","redo"]
                            } )
                            .catch( error => {
                                console.error( error );
                            } );
                        function save() {
                            document.getElementById("saveName").value = document.getElementById("title").value;
                            document.getElementById("saveData").value = editor.getData();
                            document.getElementById("saveForm").submit();
                        }
                    </script>
                    <button onclick="save();" class="appendstory">Submit Changes</button>
                    
                <form method="post" action="admin.php?action=default&tab=edit&edit='.$edit.'" id="saveForm">
                    <input type="hidden" value="" id="saveName" name="name">
                    <input type="hidden" value="" id="saveData" name="editor">
                </form>
                ';
            }
        }
    } else if($tab=="settings") {
        echo '<h1 class="admintitle">Settings</h1>';
        echo '<div class="settingsmenu">';
        echo '<a href="admin.php?action=default&tab=password">Change Password</a><br/>';
        echo '<a href="admin.php?action=default&tab=css">Choose Theme</a><br/>';
        echo '<a href="admin.php?action=default&tab=info">Change Story Info</a><br/>';
        echo '<a href="admin.php?action=default&tab=editabout">Edit About Page</a><br/>';
        echo '</div>';
    } else if($tab=="planner") {
        //get info
        if($_REQUEST['saveData']!=null&&$_REQUEST['saveData']!="") {
            $savePlanner = fopen("myPlanner.txt", "w");
            fwrite($savePlanner,$_REQUEST['saveData']);
            fclose($savePlanner);
            echo '<script>alert("Data Saved")</script>';
        }
        $plannerinfo = fopen("myPlanner.txt","r");
        $openPlanner  = fread($plannerinfo,filesize("myPlanner.txt"));
        fclose($plannerinfo);
        echo '<h1 class="admintitle">Planner</h1>';
        echo '<noscript><h1>WARNING: THIS FEATURE REQUIRES JAVASCRIPT TO FUNCTION. PLEASE ENABLE JAVASCRIPT OR UPGRADE YOUR BROWSER</h1></noscript>';
        echo '<div>
            <p><button id="save" class="save" onclick="save()">Save Planner</button> <button id="delete" onclick="seldel()">Delete Selection</button> <!--<button onclick="moveblock(-1)">Move Left</button> <button onclick="moveblock(1)">Move Left</button>--></p>
            <p id="saved">Saved</p>
        </div>
        <div id="list"></div>
        <br/>
        <form method="post" action="admin.php?action=default&tab=planner" id="saveForm" style="display:none;">
        <input type="hidden" value="'.$openPlanner.'" name="saveData" id="saveData"/>
        </form>
        <script>
var ideas = '.$openPlanner.'; //chapter(title, desc)
var list = document.getElementById("list");
var saved = true;
document.addEventListener("keyup", function(e) {
    if(e.keyCode==9) {
        calc();
    }
});
function isSaved() {
    if(saved==true) {
        saved = false;
    } else {
        document.getElementById("saved").innerHTML = "Not Saved";
    }
}
    function calc() {
        ideas.push(new Array("New Idea","Click to edit"));
        redraw();
    }

    function chap() {
        ideas.push(new Array("New Chapter Title","!chapterTitle"));
        redraw();
    }
    function redraw() {
        list.innerHTML = "";
        var legacy = "";
        isSaved();
        for(var i = 0; i < ideas.length; i++) {
            legacy = ""
            if(ideas[i][1]!="!chapterTitle") {
            legacy+="<div class=\'idea\' id=\'"+i+"\'><input class=\'edit-title\' onchange=\'ideas["+i+"][0]=this.value;isSaved();\' value=\'"+ideas[i][0]+"\'/><textarea class=\'edit-body\' onchange=\'ideas["+i+"][1]=this.value;isSaved();\'>"+ideas[i][1]+"</textarea><br/><i class=\'delete fa fa-times-circle\' aria-hidden=\'true\' onclick=\'del("+i+")\'></i>";
            } else {
            legacy+="<div class=\'idea\' id=\'"+i+"\'><input class=\'edit-chapter\' onchange=\'ideas["+i+"][0]=this.value;isSaved();\' value=\'"+ideas[i][0]+"\'/><div class=\'space\'></div><i class=\'delete fa fa-times-circle\' aria-hidden=\'true\' onclick=\'del("+i+")\'></i>";
            }
            legacy+="<div class=\'icon\'>";
            if(i!=0) {
                legacy+="<i class=\'fa fa-chevron-circle-left\' aria-hidden=\'true\' onclick=\'ideas.move("+i+","+(i-1)+");redraw()\'></i> ";
            }
            if(i!=ideas.length-1) {
                legacy+= "<i class=\'fa fa-chevron-circle-right\' aria-hidden=\'true\' onclick=\'ideas.move("+i+","+(i+1)+");redraw()\'></i>";
            }
            legacy += "</div><input class=\'check\' type=\'checkbox\' onchange=\'resel()\'/></div>";
            list.innerHTML+=legacy;
        }
        list.innerHTML+="<div class=\\"idea\\"><i class=\'fa fa-plus-circle add\' aria-hidden=\'true\' onclick=\'calc()\'></i><i class=\'fa fa-plus-circle addchap\' aria-hidden=\'true\' onclick=\'chap()\'></i></div>";
    }
    function seldel() {
        if(confirm("Are you sure you want to delete these topics? They cannot be recovered!")) {
            var selections = document.getElementsByClassName("check");
            var arr = [];
            for(var i = 0; i < selections.length; i++) {
                if(selections[i].checked) {
                    arr.push(selections[i])
                }
            }
            for(var i = arr.length-1; i >= 0; i--) {
                ideas.splice(parseInt(arr[i].parentElement.id),1);
            }
            redraw()
        }
    }
    function del(index) {
        if(confirm("Are you sure you want to delete " + ideas[index][0] + "?")==true) {
            ideas.splice(index,1);
            redraw();
        }
    }
    function moveblock(dir) {
        var selections = document.getElementsByClassName("check");
        var arr = [];
        for(var i = 0; i < selections.length; i++) {
            if(selections[i].checked) {
                arr.push(selections[i])
            }
        }
        var name = 0;
        for(var i = 0; i < arr.length; i++) {
            name = parseInt(arr[i].parentElement.id);
            ideas.move(name,name-dir);
        }
        redraw()
    }
    function resel() {
        var selections = document.getElementsByClassName("check");
        for(var i = 0; i < selections.length; i++) {
            if(selections[i].checked==true) {
                selections[i].parentElement.className = "idea selected";
            } else {
                selections[i].parentElement.className = "idea";
            }
        }
    }
    function save() {
        document.getElementById("saveData").value = JSON.stringify(ideas);
        document.getElementById("saveForm").submit();
    }
    redraw()
    Array.prototype.move = function (old_index, new_index) {
        if (new_index >= this.length) {
            var k = new_index - this.length;
            while ((k--) + 1) {
                this.push(undefined);
            }
        }
        this.splice(new_index, 0, this.splice(old_index, 1)[0]);
        return this; // for testing purposes
    };
</script>';
    } else if($tab=="info") {
        $changed = $_REQUEST['changed'];
        if($changed==true||$changed=="true") {
            $globalSettings['title']   = $_REQUEST['newName'];
            $globalSettings['author']  = $_REQUEST['newAuthor'];
            $globalSettings['moreDetails'] = $_REQUEST['newDetails'];
            $globalSettings['image'] = $_REQUEST['newImage'];
            saveGS($globalSettings);
            echo '<h1 class="admintitle">Info Successfully Changed</h1>';
            
        } else {
            echo '<h1 class="admintitle">Edit Story Info</h1>';
        }
        echo '<form action="admin.php?action=default&tab=info&changed=true" method="post" style="padding-left:15%">
        The Name of your Story<br/><input style="font-size:26px;" type="text" name="newName" value="'.$globalSettings['title'].'"/><br/><br/>
        The Awesome Author<br/><input type="text" name="newAuthor" value="'.$globalSettings['author'].'"/><br/><br/>
        More details. <i>A quote perhaps?</i><br/><input type="text" name="newDetails" value="'.$globalSettings['moreDetails'].'"/><br/><br/>
        Story Image (optional, must be direct link, i.e. http://www.example.com/image.png)<br/><input type="text" id="newImage" name="newImage" value="'.$globalSettings['image'].'" onchange="updateImage();"/><br/>
        <img src='.$globalSettings['image'].' style="height:100px;" id="display"/><br/>
        <button type="submit" class="appendstory">Submit New Info</button>
        </form>
        <script>
        function updateImage() {
            document.getElementById("display").src = document.getElementById("newImage").value;
            document.getElementById("newImage").style = "height:100px;";
        }
        </script>';
    } else if($tab=="css") {
        $apply = $_REQUEST['apply'];
        if($apply!="") {//a theme has been chosen
            $newTheme = fopen($apply."/theme.css", "r");
            $oldTheme = fopen("style.css","w");
            fwrite($oldTheme,fread($newTheme,filesize($apply.'/theme.css')));
            fclose($newTheme);
            fclose($oldTheme);
            echo '<h1>Success!</h1>';
        }
        echo '<h1 class="admintitle">Choose Themes</h1>';
        echo '<div class="grid">';
        $themes = glob('themes/*', GLOB_ONLYDIR);
        for($x=0;$x<count($themes);$x++) {
            $dir = fopen($themes[$x]."/settings.txt","r");
            $content = fread($dir,filesize($themes[$x]."/settings.txt"));
            fclose($dir);
            $insert = explode("~",$content);
            echo '<div class="grid-20" style="height:640px;overflow:scroll;overflow-x:hidden;"><br/>
                <h1>'.$insert[0].'</h1>
                <img src="'.$themes[$x].'/image.jpg" style="width:100%">
                '.$insert[1].' <br/><a href="admin.php?action=default&tab=css&apply='.$themes[$x].'">Apply Theme</a>
            </div>';
        }
    } else if($tab=="password") {
        $pass1 = $_REQUEST['password1'];
        $pass2 = $_REQUEST['password2'];
        
        if($pass1!="") {
            if($pass1==$pass2) {
                $deface = $pass1;
                $deface = md5($deface);
                $deface = sha1($deface);
                $deface = crypt($deface,'h34c9');
                
                $pass   = fopen("password.txt", "w+");
                fwrite($pass, $deface);
                fclose($pass);
                echo '<h1>Password Successfully Changed</h1>';
            } else {
                echo '<div class="error">Passwords did not match</div>';
            }
        }
        
        echo '<h1 class="admintitle">Change Password</h1>
        <div style="padding-left:20%;padding-right:20%;">
        <p>You may change your password to whatever you want, however make sure you pick a secure one that you have mot used elsewhere.</p>
            <form method="post" action="admin.php?action=default&tab=password">
                <label>New Password:</label><br/><input type="password" name="password1"><br/><br/>
                <label>Repeat Password:</label><br/><input type="password" name="password2"><br/><br/>
                <button type="submit" class="appendstory">Change</button>
            </form>
        </div>
        ';
    } else if($tab=="about") {
        echo '<h1 class="admintitle">About Incode Story</h1>';
        echo '<div style="padding-left:20%;padding-right:40%;"><p>InCode Story is a simple blogging program that allows users
        and web admins to document adventures, release updates, write webstories,
        etc. The story program is a simple Open Source software that can be
        easily installed on any web server. It comes mostly self contained only
        needing external internet access to various Javascript libraries.<br/><br/>
        Story is created by InCode as an Open Source software that is free to use
        however you may like.</p>
        <p>Icons by Font Awesome (<a href="https://fontawesome.com/license">License</a>)</p>
        <p>Editor by CKEditor (<a href="https://ckeditor.com/legal/ckeditor-oss-license/">License</a>)</p>
        <b>VERSION 1.2</b>
        <ul>
        <li>Removed Preview, add, edit (Now just "Story")</li>
        <li>Added Planner</li>
        <li>Replaced "You\'ve reached the end!" with the About page</li>
        <li>Removed About page</li>
        <li>Added Author, Additional Info</li>
        <li>Added Custom Cover Image</li>
        <li>New icons with Animations</li>
        <li>Updated CKEditor to Version 5</li>
        <li>Removed "Reconstruct"</li>
        <li>Renamed "Edit CSS" to "Choose Theme"</li>
        <li>Removed Redundant Code</li>
        <li>Bug Fixes</li>
        </ul></div>';
    } else if($tab=="backups") {
        $restore = $_REQUEST['restore'];
        $delete = $_REQUEST['delete'];
        $upload = $_REQUEST['upload'];
        echo '<h1 class="admintitle">Backup Manager</h1>';
        echo '<p>Create backups of your story.txt easily - <b>NOTE:</b> Does not backup settings, themes, planner, etc. Only chapters!</p>';
        echo '<a href="admin.php?action=default&tab=backups&restore=new">Create New Backup</a>';
        //date("Y-m-d:h-i-sa")
        if($restore!="") {
            if($restore=="new") {
                $newbackup = date("Y-m-d:h-i-sa");
                
                $zip = new ZipArchive;
                $res = $zip->open('backups/'.$newbackup.'.zip', ZipArchive::CREATE);
                if($res === TRUE) {
                    chdir('story/');
                    $zip->addGlob("*.txt");
                    chdir('../');
                    $zip->close();
                }else {
                    echo '<h1>Backup Failure</h1>';
                }
                
                echo '<h1>New Backup Created</h1>';
            } else if(file_exists($restore)) {
                
                //unpack story
                $zip = new ZipArchive;
                $res = $zip->open($restore);
                if($res === TRUE) {
                    // delete story contents
                    $storyc = glob("story/*.txt");
                    foreach($storyc as $file) {
                        unlink($file);
                    }
                    //extract
                    $zip->extractTo('story/');
                    $zip->close();
                } else {
                    echo '<h1>Failure to find Zip Archive: '.$restore.'</h1>';
                }
                
                echo '<h1>Backup has been restored.</h1>';
            } else {
                echo '<h1>Backup could not be found - not restored.</h1>';
            }
            $html = fopen("story.txt", "w+");
        
            $stories = glob("story/" . "*.txt");
            for($i=0;$i<count($stories);$i++) {
                $append = fopen($stories[$i],"r");
                $const  = fread($append,filesize($stories[$i]));
                $constone  = explode("9312bb5f", $const);
                $constone[0] = "<h1>".$constone[0]."</h1>";
                $consttwo  = implode($constone);
                
                fwrite($html, "<div class='block'>".$consttwo."</div>\n");
                fclose($append);
            }
        } else if($delete!="") {
            unlink($delete);
            echo '<h1>Deleted Backup</h1>';
        }
        $backups = glob("backups/*.zip");
        foreach($backups as $file) {
            echo '<br/>Backup: '.$file.' <a href="admin.php?action=default&tab=backups&restore='.$file.'">Restore This</a> / <a href="'.$file.'" download>Download Backup</a> / <a href="admin.php?action=default&tab=backups&delete='.$file.'">Delete</a><br/>';
        }
    } else if($tab=="editabout") {
        $about = fopen("bigSettings/about", "r+");
        $editor = $_REQUEST['editor'];
        if($editor=="") {
            echo '
            <h1 class="admintitle">Edit Your About Page</h1>
            <p>The ABOUT page is located at the bottom of your story</p>
            <script src="https://cdn.ckeditor.com/ckeditor5/10.0.1/classic/ckeditor.js"></script>
                    <div class="editor-cont">
                        <div name="editor1" id="editor" class="editor">
                        '.fread($about,filesize("bigSettings/about")).'
                        </div>
                    </div>
                    <p style="font-size:8px">CKEditor 5, using CDN. Works best with Opera and Chrome</p>
                    <script>
                        ClassicEditor
                            .create( document.querySelector( \'#editor\' ), {
                                removePlugins: [ \'EasyImage\' ],
                                toolbar: ["heading","|","bold","italic","link","bulletedList","numberedList","blockQuote","undo","redo"]
                            } )
                            .catch( error => {
                                console.error( error );
                            } );
                        function save() {
                            document.getElementById("saveData").value = editor.getData();
                            document.getElementById("saveForm").submit();
                        }
                    </script>
                    <button onclick="save();" class="appendstory">Submit Changes</button>
                    
                <form method="post" action="admin.php?action=default&tab=edit&edit='.$edit.'" id="saveForm">
                    <input type="hidden" value="" id="saveName" name="name">
                    <input type="hidden" value="" id="saveData" name="editor">
                </form>
            ';
        } else {
            fwrite($about, $editor);
            echo '<h1 class="admintitle">The ABOUT page has been updated</h1>';
        }
        fclose($about);
    } else {
        echo '<h1 style="font-size:72px">Cannot Find Tab: '.$tab.'</h1>';
    }
    
    /*end file*/
    echo '
</body>

</html>
';
?>
