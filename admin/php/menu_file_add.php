<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "menu_file_add.html"));

if(!is_numeric($glob['menu_template_file_id']))
{
	$page_title="Add Menu Template File";
	$next_function='menu_template_file-add';
	
    $ft->assign(array(
    					"TYPE"              =>        build_menu_template_type_list($glob['type']),
                        "FILE_NAME"         =>        $glob['file_name'],
                        "NAME"              =>        $glob['name']
                     )
                );
}
else
{
	$active_checked='';
    $page_title="Edit Menu Template File";
    $next_function='menu_template_file-update';
    $dbu->query("select * from cms_menu_template_file 
    			 where menu_template_file_id='".$glob['menu_template_file_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "MENU_TEMPLATE_FILE_ID" =>        $glob['menu_template_file_id'],
                        "TYPE"             =>        build_menu_template_type_list($dbu->gf('type')),
                        "FILE_NAME"        =>        $dbu->gf('file_name'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MENU_TEMPLATE_FILE_ID',$glob['menu_template_file_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>