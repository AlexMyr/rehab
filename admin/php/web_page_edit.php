<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "web_page_edit.html"));

if(!is_numeric($glob['web_page_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

$page_title="Edit Web Page Information";
$next_function='web_page-update';
$dbu->query("select cms_web_page.*, cms_page_type.name as page_type_name, cms_template.name as template_name,  cms_template.file_name as template_file_name
			 from cms_web_page
			 inner join cms_page_type on cms_web_page.page_type_id=cms_page_type.page_type_id
			 inner join cms_template on cms_template.template_id=cms_web_page.template_id
			 where web_page_id='".$glob['web_page_id']."'");

if(!$dbu->move_next())	
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

$ft->assign(array(
                   "DESCRIPTION"       =>        $dbu->gf('description'),
                   "TITLE"             =>        $dbu->gf('title'),
                   "KEYWORDS"          =>        $dbu->gf('keywords'),
                   "PAGE_TYPE"         =>        $dbu->f('page_type_name'),
                   "TEMPLATE_NAME"     =>        $dbu->f('template_name'),
                   "TEMPLATE_FILE_NAME" =>        $dbu->f('template_file_name'),
                   "NAME"              =>        $dbu->gf('name')
                  )
            );

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('WEB_PAGE_ID',$glob['web_page_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>