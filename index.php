<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <?php
            if($_REQUEST["lo"]=="true") {
                $message = "Logout Successful";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        ?>
        <title>Storybook</title>
    </head>
    <body>
        <div class="header">
        <h1 style="font-size:80px" id="name">My New Story</h1>
        <p><a href="about.php">About</a> / <a href="admin.php?action=login">Admin Login</a></p>
        <p>Powered by <a href="http://www.incode-labs.com/story">Story</a></p>
        </div>
        <?php
        $html = fopen("story.txt", "r");
        echo fread($html,filesize("story.txt"));
        fclose($html);
        ?>
        <div class="block">
        <h1>You've Reached the End</h1>
        </div>
        <div style="height:20%;"></div>
    </body>
</html>