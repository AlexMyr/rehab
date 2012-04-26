<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "tag_select2.html"));

$ft->assign('PAGE_TITLE','List Of Alias Tags');

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>