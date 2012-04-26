<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "client_delete.html"));
//$ft->assign('MESSAGE', get_error($glob['error']));

//$page_title='Login Member';
//$next_function ='auth-login';

//$dbu = new mysql_db();

$ft->assign('CLIENT_NAME',$glob['client_name']);

$site_meta_title=$meta_title." - Patient Record";
$site_meta_keywords=$meta_keywords.", Patient Record";
$site_meta_description=$meta_description." Patient Record";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>