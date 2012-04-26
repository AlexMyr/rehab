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

if($dbu->f('active')==0) $page_title = "Your Trial Account Has Expired";
else $page_title = "Choose a Payment Plan";

$ft->define(array('main' => "profile_licence.html"));

$ft->assign('CSS_PAGE', $glob['pag']);


		$ft->assign(array(
			'ALERT'=>$price->f('licence_amount'),
			));


$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

return $ft->fetch('CONTENT');

?>