<?php
    $gsfile = fopen("globalSettings", "r");
    $gsraw = fread($gsfile,filesize("globalSettings"));
    fclose($gsfile);
    parse_str($gsraw, $globalSettings);
    
    function saveGS($newSettings) {
        $legacy = "";
        $keys = array_keys($newSettings);
        $vals = array_values($newSettings);
        if($keys[0]==null||$vals[0]==null) {
            echo '<div class="fatalError"><b>WARNING:</b> Error in saving new settings; killing page to prevent damages. Please report this as an error on Github (Settings parse, saving)</div>';
            die();
        }
        for($x = 0; $x < count($keys); $x++) {
            $legacy.=$keys[$x]."=".$vals[$x]."&";
        }
        $gsfile = fopen("globalSettings", "w");
        fwrite($gsfile,$legacy);
        fclose($gsfile);
    }
?>