<html>
    <head>
        <link rel="stylesheet" href="style.css">
        
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
            include 'parseSettings.php';
            if($_REQUEST["lo"]=="true") {
                $message = "Logout Successful";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        ?>
        <title>Storybook</title>
    </head>
    <body>
        
        <div class="header">
            <?php
            if($globalSettings['image']!=null) {
                echo '<style>
                .mainImage {
                    position:fixed;
                    top:5%;
                    left:15px;
                    width:15%;
                }
                @media (max-width:1279px) {
                    .mainImage {
                        position:initial;
                        top:initial;
                        left:initial;
                        width:100%;
                    }
                }
                </style>
                <img src="'.$globalSettings['image'].'" class="mainImage"\>';
            }
        ?>
        <h1 style="font-size:80px" id="name"><?php echo $globalSettings['title']; ?></h1>
        <p><a href="#about">About</a> / <a href="admin.php?action=login">Admin Login</a></p>
        <p>Powered by <a href="http://www.incode-labs.com/story">Story</a><span style="margin-left:50px;"></span>Written By <?php echo $globalSettings['author']; ?></p>
        <p><br/><i><?php echo $globalSettings['moreDetails']; ?></i></p>
        </div>
        <?php
        $html = fopen("story.txt", "r");
        echo fread($html,filesize("story.txt"));
        fclose($html);
        ?>
        <div class="block" id="about">
            <h1>About</h1>
        <?php
        $html = fopen("bigSettings/about", "r");
        echo fread($html,filesize("bigSettings/about"));
        fclose($html);
        ?>
        </div>
        <div style="height:20%;"></div>
    </body>
</html>