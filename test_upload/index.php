<?php
include "config.php";
include "functions.php";

$connection = connect_to_db();
grab_categories("categs.txt");
//new_grab_programs("exercises - UK_edited.txt");
//save_pictures("exercises_new_3.02.2012_upd.txt");
save_pictures("exercises - UK_edited.txt");// last string should be followed by \r\n adnd triple \t for correct parsing
//save_pictures("test.txt");

//grab_categories("categs.txt");
//save_pictures("prog_block/block13.txt");
//error_reporting(E_ALL);

//save_pictures("exercises_work.txt");
?>