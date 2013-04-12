<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_upload.html"));

$dbu = new mysql_db();

	//add
$ft->assign(array(
	'PAG' =>'programs_tags_upload',
	'EDIT' =>'hide',
	'ACT' =>'programs-upload_tags',
	'LANG' =>$glob['lang'],
	'PAGE_TITLE' =>'Upload Programs Tags',
	'MESSAGE' => $glob['error'],
	)
);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>