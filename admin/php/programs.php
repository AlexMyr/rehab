<?php

$ft=new FastTemplate(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "welcome.html"));


$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');


?>