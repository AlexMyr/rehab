<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "profile_edit_email.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();
if($dbu->f('is_trial')==1)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(false));
}
else if($dbu->f('is_trial')==0)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true));
}


$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title." - Profile";
$site_meta_keywords=$meta_keywords.", Profile";
$site_meta_description=$meta_description." Profile";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>