<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "ctemplate_add.html"));

if(!is_numeric($glob['content_template_id']))
{
	$page_title="Content Box Template Add";
	$next_function='content_template-add';
	
    $ft->assign(array(
    					"DESCRIPTION"       =>        $glob['description'],
                        "FILE_NAME"         =>        $glob['file_name'],
                        "NAME"              =>        $glob['name']
                     )
                );
}
else
{
	$active_checked='';
    $page_title="Content Box Template Edit";
    $next_function='content_template-update';
    $dbu->query("select * from cms_content_template 
    			 where content_template_id='".$glob['content_template_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "CONTENT_TEMPLATE_ID" =>        $glob['content_template_id'],
                        "DESCRIPTION"      =>        $dbu->gf('description'),
                        "FILE_NAME"        =>        $dbu->gf('file_name'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('CONTENT_TEMPLATE_ID',$glob['content_template_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>