<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
//$ft->define(array('main' => "contact_thankyou2.html"));
$ft->define(array('main' => "contact.html"));

$site_meta_title=$meta_title." - Contact";
$site_meta_keywords=$meta_keywords.", Contact";
$site_meta_description=$meta_description." Contact";


$ft->assign('PAGE',$glob['pag']);
$ft->assign('ID',$glob['id']);
$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');
?>