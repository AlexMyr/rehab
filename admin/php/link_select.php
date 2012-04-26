<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "link_select.html"));

$ft->assign('PAGE_TITLE','Get System Links');

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>