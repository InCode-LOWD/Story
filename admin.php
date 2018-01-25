<?php
    session_start();
    
    $action = $_REQUEST['action'];
    
    if($action == "clear") {
        session_unset();
        session_destroy();
        header("Location: index.php");
        die();
    }
    /*so far recovery does not work. */
    if($action == "recover") {
        $emailfile = fopen("recovery/email.txt", "r");
        $email = fgets($emailfile);
        fclose($emailfile);
        
        //create unique ID
        $tmppass = uniqid();
        $mash = crypt(sha1(md5($tmppass)),'h34c9');
        $recpass = fopen("recovery/recovery.txt", "w+");
        fwrite($recpass, $mash);
        fclose($recpass);
        
        //email
        $headers = 'From: support@incode-labs.com' . "\r\n" .
        'Reply-To: support@incode-labs.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
        
        mail($email, "Password Recovery", "A password reset has been requested.\nYou are being emailed a temporary passcode to let you in.
        \nOnce in again, it is reccommended that you immidietly create a new password as these tokens are one-use only.\n\n
        Your token:".$tmppass, $headers);
        
        //confirm
        echo '<h1>Password Reset Requested to: '.$email.'</h1>';
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
            header("Location: admin.php?action=default&tab=preview");
            die();
        }
        if($_SESSION["status"] == "loggedin") {
            header("Location: admin.php?action=default&tab=preview");
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
    /*By this point the user should have been logged in*/
    echo '<html>

<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="grid.css">
    <link rel="stylesheet" href="admin.css">
    
    <script src="scrollspy.js"></script>
    
</head>

<body>
    <div class="grid">
        <div class="grid-10 settings" id="navbar">
            <div style="padding:5%">
                <h2>Admin Panel</h2>
                <p>Your session ID is: '.$_SESSION["sessid"].'</p>
            </div>
            
            <hr/>
            <a class="setting" href="admin.php?action=default&tab=preview">
                <span>Preview</span>
            </a>
            <a class="setting" href="admin.php?action=default&tab=add">
                <span>Add</span>
            </a>
            <a class="setting" href="admin.php?action=default&tab=edit">
                <span>Edit</span>
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
            <a class="setting" href="admin.php?action=defualt&tab=compile">
                <span>Reconstruct</span>
            </a>
            <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">
    <img alt="Creative Commons License" style="border-width:0"
    src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a>
        </div>
        <div class="grid-90" style="min-height:100%;max-width:90%;">';
        
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
    if($tab=="preview") {
        $html = fopen("story.txt", "r");
        if(filesize("story.txt")!=0)
        echo fread($html,filesize("story.txt"));
        fclose($html);
    } else if($tab=="add") {
        echo '
        <h1 class="admintitle">Append the Story</h1>
        <script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
        <form method="post" action="admin.php?action=default&tab=append">
            <input type="text" class="newtitle" placeholder="Your Title" autocomplete="false" name="title">
            <textarea name="editor1" id="editor1" placeholder="Once upon a time...">
            </textarea>
            <p style="font-size:8px">CKEditor, using CDN. Works best with Opera and Chrome</p>
            <script>
                CKEDITOR.replace( \'editor1\' );
            </script>
            <button type="submit" class="appendstory">Add to the Story!</button>
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
            $stories = glob("story/" . "*.txt");
            for($i=0;$i<count($stories);$i++) {
                $append = fopen($stories[$i],"r");
                $const  = fread($append,filesize($stories[$i]));
                $constone  = explode("9312bb5f", $const);
                $constone[0] = "<h1>".$constone[0]."</h1>";
                $consttwo  = implode($constone);
                
                echo "<div class='block'>".$consttwo."<a href='admin.php?action=default&tab=edit&del=".$stories[$i]."'>Delete</a> / <a href='admin.php?action=default&tab=edit&edit=".$stories[$i]."'>Edit</a></div>\n";
                fclose($append);
            }
        } else { //edit == something or other
            $title = $_REQUEST['title'];
            $content = $_REQUEST['editor1'];
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
                <script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
                <form method="post" action="admin.php?action=default&tab=edit&edit='.$edit.'">
                    <input type="text" class="newtitle" placeholder="Your Title" autocomplete="false" name="title" value="'.$constone[0].'">
                    <textarea name="editor1" id="editor1" placeholder="Once upon a time...">
                    '.$constone[1].'
                    </textarea>
                    <p style="font-size:8px">CKEditor, using CDN. Works best with Opera and Chrome</p>
                    <script>
                        CKEDITOR.replace( \'editor1\' );
                    </script>
                    <button type="submit" class="appendstory">Submit Changes</button>
                </form>
                ';
            }
        }
    } else if($tab=="settings") {
        echo '<h1 class="admintitle">Settings</h1>';
        echo '<div class="settingsmenu">';
        echo '<a href="admin.php?action=default&tab=password">Change Password</a><br/>';
        echo '<a href="admin.php?action=default&tab=css">Edit CSS</a><br/>';
        echo '<a href="admin.php?action=default&tab=name">Change Name</a><br/>';
        echo '<a href="admin.php?action=default&tab=editabout">Edit About Page</a><br/>';
        echo '</div>';
    } else if($tab=="name") {
        $name = $_REQUEST['newName'];
        if($name=="") {
            echo '<h1 class="admintitle">Edit Site Name</h1>';
            echo '<form action="admin.php?action=default&tab=name" method="post">
            <input style="font-size:26px;" type="text" name="newName" />
            <button style="font-size:20px" type="submit">Change Name</button>
            </form>';
        } else {
            $indexfile= fopen("constructor/index.txt",  "r" );
            
            $indexcontents = fread($indexfile,filesize("constructor/index.txt"));
            $indexcontents = str_replace("NAME GOES HERE", $name, $indexcontents);
            
            $homefile = fopen("index.php",              "w+");
            fwrite($homefile, $indexcontents);
            fclose($indexfile);
            fclose($homefile);
            echo '<h1 class="admintitle">Name Successfully Changed</h1>';
        }
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
        echo '<h1 class="admintitle">CSS Themes</h1>';
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
                echo '<div class="error"><h1>Passwords did not match</h1></div>';
            }
        }
        
        echo '<h1 class="admintitle">Change Password</h1>
        <div style="padding-left:20%;padding-right:20%;">
        <p>You may change your password to whatever you want, however make sure you pick a secure one that you have mot used elsewhere.</p>
            <form method="post" action="admin.php?action=default&tab=password">
                <label>New Password:</label><br/><input type="password" name="password1"><br/><br/>
                <label>Repeat Password:</label><br/><input type="password" name="password2"><br/><br/>
                <button type="submit">Change</button>
            </form>
        </div>
        ';
    } else if($tab=="about") {
        echo '<h1 class="admintitle">About Incode Story</h1>';
        echo '<p style="padding-left:20%;padding-right:40%;">InCode Story is a simple blogging program that allows users
        and web admins to document adventures, release updates, write webstories,
        etc. The story program is a simple Open Source software that can be
        easily installed on any web server. It comes mostly self contained only
        needing external internet access to various Javascript libraries.<br/><br/>
        Story is created by InCode as an Open Source software that is free to use
        however you may like. You may be subject to the licenses of Cloud9\'s ACE editor, and CKEditor</p>';
    } else if($tab=="backups") {
        $restore = $_REQUEST['restore'];
        $delete = $_REQUEST['delete'];
        $upload = $_REQUEST['upload'];
        echo '<h1 class="admintitle">Backup Manager</h1>';
        echo '<p>Create backups of your story.txt easily</p>';
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
        $about = fopen("constructor/about.txt", "r+");
        $editor = $_REQUEST['editor1'];
        if($editor=="") {
            echo '
            <h1 class="admintitle">Edit Your About Page</h1>
            <script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
            <form method="post" action="admin.php?action=default&tab=editabout">
                <textarea name="editor1" id="editor1">
                    '.fread($about,filesize("constructor/about.txt")).'
                </textarea>
                <p style="font-size:8px">CKEditor, using CDN. Works best with Opera and Chrome</p>
                <script>
                    CKEDITOR.replace( \'editor1\' );
                </script>
                <button type="submit" class="appendstory">Update About Page</button>
            </form>
            ';
        } else {
            fwrite($about, $editor);
            echo '<h1 class="admintitle">The ABOUT page has been updated</h1><br/><p style="padding-left:20%">Would you like to <a href="about.php">visit it</a> or <a href="admin.php?action=default&tab=editabout">edit it some more?</a>';
        }
        fclose($about);
    } else if($tab=="compile") {
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
        echo '<h1 class="admintitle">Story has been updated and reconstructed to reflect the "story" folder</h1>';
    } else {
        echo '<h1 style="font-size:72px">Cannot Find Tab: '.$tab.'</h1>';
    }
    
    /*end file*/
    echo '
    
    </div>
    <script>
    var menu = document.querySelector("navbar");

    scrollSpy(menu);
    </script>
</body>

</html>
';
    /*
    if($site=="") {

    } else {
    echo "<h1>Thank you for your submission!</h1>";
    $data = fopen("database.txt", "a+");
    $txt = "\nAccessed <b>" . $site . "</b> on <b>".$date."</b> because <b>".$reason."</b> - Catagorized as <b>".$catag."</b> - Site is used for: <b>".$purp."</b><br />";
    fwrite($data, $txt);
    //}
    fclose($data);
    }
    $data = fopen("database.txt", "a+");
    echo fread($data,filesize("database.txt"));
    fclose($data);*/
?>