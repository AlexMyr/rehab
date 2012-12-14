<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$glob['lang'] = $glob['lang'] ? $glob['lang'] : 'en';

$ft->define(array('main' => "profile_put_contact.html"));

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign(array(
    'WEBSITE' => $glob['website'] ? $glob['website'] : '',
    'PHONE' => $glob['phone'] ? $glob['phone'] : '',
    'MOBILE' => $glob['mobile'] ? $glob['mobile'] : '',
    'EMAIL' => $glob['email'] ? $glob['email'] : '',
    'FAX' => $glob['fax'] ? $glob['fax'] : '',
));

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>