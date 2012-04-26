<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;
$dbu2=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "template_czone_add.html"));

if(!is_numeric($glob['template_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
	$dbu->query("select name from cms_template
    			 where template_id='".$glob['template_id']."'");
	$dbu->move_next();
	$template_name=$dbu->f('name');
}

if(!is_numeric($glob['template_czone_id']))
{
	$page_title="Add Content Zone for ".$template_name;
	$next_function='template-czone_add';
	
    $ft->assign(array(
                        "TEMPLATE_ID"       =>        $glob['template_id'],
                        "DESCRIPTION"       =>        $glob['description'],
                        "TAG"               =>        $glob['tag'],
                        "NAME"              =>        $glob['name']
                     )
                );

}
else
{
	$active_checked='';
    $page_title="Edit Content Zone";
    $next_function='template-czone_update';
    $dbu->query("select * from cms_template_czone 
    			 where template_czone_id='".$glob['template_czone_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TEMPLATE_ID"      =>        $glob['template_id'],
                        "TEMPLATE_CZONE_ID" =>        $glob['template_czone_id'],
                        "DESCRIPTION"      =>        $dbu->gf('description'),
                        "TAG"              =>        $dbu->gf('tag'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );
}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('TEMPLATE_NAME',$template_name);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('TEMPLATE_ID',$glob['template_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>