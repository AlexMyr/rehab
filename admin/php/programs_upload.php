<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_upload.html"));

$dbu = new mysql_db();

	//add
$ft->assign(array(
	'PAG' =>'programs_upload',
	'EDIT' =>'hide',
	'ACT' =>'programs-upload_excel',
	'LANG' =>$glob['lang'],
	'PAGE_TITLE' =>'Upload Programs',
	'MESSAGE' => $glob['error'],
	)
);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>