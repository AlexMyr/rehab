<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "web_page_add.html"));

if(!$glob['title'])
{
	$glob['title']=$meta_title;
}

if(!$glob['keywords'])
{
	$glob['keywords']=$meta_keywords;
}

if(!$glob['description'])
{
	$glob['description']=$meta_description;
}

$page_title="Add Web Page";
$next_function='web_page-add';
	
$ft->assign(array(
                   "DESCRIPTION"       =>        $glob['description'],
                   "TITLE"             =>        $glob['title'],
                   "KEYWORDS"          =>        $glob['keywords'],
                   "PAGE_TYPE"         =>        build_cms_page_type($glob['page_type_id']),
                   "NAME"              =>        $glob['name']
                  )
            );

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('WEB_PAGE_ID',$glob['web_page_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>