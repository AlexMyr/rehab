<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
//$ft->define(array('main' => "profile.html"));

//$page_title='Login Member';
//$next_function ='auth-login';


$dbu = new mysql_db();

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();


$ft->define(array('main' => "profile_choose_clinic.html"));

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title." - Profile";
$site_meta_keywords=$meta_keywords.", Profile";
$site_meta_description=$meta_description." Profile";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>