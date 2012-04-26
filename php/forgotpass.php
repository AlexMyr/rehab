<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "forgotpass.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

//$dbu->query("select name from cms_menu where menu_id=".$glob['menu_id']);

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title." - Forgot Passsword";
$site_meta_keywords=$meta_keywords.", Forgot Passsword";
$site_meta_description=$meta_description." Forgot Passsword";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>