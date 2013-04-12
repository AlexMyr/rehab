<?php
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "construction.html"));

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>