<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "profile_payment_paynow.html"));

$dbu = new mysql_db();

$tags = get_template_tag('profile_payment', $glob['lang']);

foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$pag = isset($glob['pag']) ? $glob['pag'] : pathinfo(__FILE__, PATHINFO_FILENAME);

$ft->assign('CSS_PAGE', $glob['pag']);


$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>