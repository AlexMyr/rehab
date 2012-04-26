<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/


$fts=new ft(ADMIN_PATH.MODULE."templates/");
$fts->define(array('main' => "contact.html"));

$fts->assign(array(
                  "SUBJECT"         =>        $glob['subject'],
                  "NAME"            =>        $glob['name'],
                  "EMAIL"           =>        $glob['email'],
                  "PHONE"           =>        $glob['phone'],
                  "COMMENTS"        =>        $glob['comments']
                 )
           );

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$fts->assign('PAGE',$glob['pag']);
$fts->assign('ID',$glob['id']);
$fts->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$fts->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $fts->fetch('CONTENT');

?>