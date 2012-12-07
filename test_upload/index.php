<?php
include "config.php";
include "functions.php";
error_reporting(E_ALL);

$connection = connect_to_db();
save_exercises("exercises - UK_21.11.2012.txt");
save_us_description("exercises - US_21.11.2012.txt");


?>