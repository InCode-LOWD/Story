<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>About</title>
    </head>
    
    <body>
        <div class="header">
        <h1 style="font-size:80px" id="name">About</h1>
        <p><a href="index.php">Home</a> / <a href="admin.php?action=login">Admin Login</a></p>
        <p>Powered by <a href="http://www.incode-labs.com/story">Story</a></p>
        </div>
        <div class="block">
        <?php
        $html = fopen("constructor/about.txt", "r");
        echo fread($html,filesize("constructor/about.txt"));
        fclose($html);
        ?>
        </block>
    </body>
</html>