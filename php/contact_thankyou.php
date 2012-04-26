<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/

$ft=new ft(ADMIN_PATH.MODULE."");
$ft->define(array('main' => "contact_thankyou.html"));

$site_meta_title=$meta_title;
$site_meta_keywords=$meta_keywords;
$site_meta_description=$meta_description;

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>