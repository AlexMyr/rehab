<?php
include "config.php";
include "functions.php";

$dHandler = opendir(UPLOADS_DIR);
while (false !== ($entry = readdir($dHandler))) {
    if (strpos($entry, 'JPG') && substr_count($entry, 'small') && $entry != "." && $entry != "..") {
    		unlink(UPLOADS_DIR.'/'.$entry);
    }
}
closedir($dHandler);
echo '<span style="color:green;">Directory cleared.</span><br/>';

/*connect_to_db();
mysql_query("TRUNCATE TABLE ".PROGRAMS_TABLE);
mysql_query("TRUNCATE TABLE ".CATEGORIES_TABLE);
echo '<span style="color:green;">Tables cleared.</span><br/>';*/
?>