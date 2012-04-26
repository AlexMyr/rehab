<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "menu_h_version.html"));

if(!is_numeric($glob['menu_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");

}
else
{
	$active_checked='';
    $next_function='menu-h_version_update';
    $dbu->query("select *
    			 from cms_menu
    			 where menu_id='".$glob['menu_id']."'");
    $dbu->move_next();
	$menu_name= $dbu->f('name');
    $ft->assign(array(
                        "MENU_ID"           =>        $glob['menu_id'],
                        
                        "TEMPLATE_FILE_H"   =>        build_h_menu_template_list($dbu->gf('template_file_h')),
                        "TAG_H"             =>        $dbu->gf('tag_h'),
                        "DESCRIPTION"       =>        $dbu->f('description')
                       
                     )
                );
                
 
    
}

$page_title="Edit Vertical Version of ".$menu_name;
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('MENU_NAME',$menu_name);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MENU_ID',$glob['menu_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>