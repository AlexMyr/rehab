<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "menu_add.html"));

if(!is_numeric($glob['menu_id']))
{
	$page_title="Add Site Menu";
	$next_function='menu-add';
	if($glob['h_version'])
	{
		$h_version_checked="checked";
	}
	else 
	{
		$h_version_checked="";
	}
	
	if($glob['v_version'])
	{
		$v_version_checked="checked";
	}
	else 
	{
		$v_version_checked="";
	}
	
    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '<!--',
                        "E_EDIT_MODE"       =>        '-->',
                        "S_ADD_MODE"        =>        '',
                        "E_ADD_MODE"        =>        '',
                        "NAME"              =>        $glob['name'],
                        "DESCRIPTION"       =>        $glob['description'],
                        "TEMPLATE_FILE_V"   =>        build_v_menu_template_list($glob['template_file_v']),
                        "TEMPLATE_FILE_H"   =>        build_h_menu_template_list($glob['template_file_h']),
                        "TAG_H"             =>        $glob['tag_h'],
                        "TAG_V"             =>        $glob['tag_v'],
                        "H_VERSION_CHECKED" =>        $h_version_checked,
                        "V_VERSION_CHECKED" =>        $v_version_checked
                     )
                );

    $ft->assign(array(
    					"S_H_V_TRUE"        =>        '',
                        "E_H_V_TRUE"        =>        '',
                        "S_H_V_FALSE"       =>        '',
                        "E_H_V_FALSE"       =>        '',
                     )
                );

    $ft->assign(array(
    					"S_V_V_TRUE"        =>        '',
                        "E_V_V_TRUE"        =>        '',
                        "S_V_V_FALSE"       =>        '',
                        "E_V_V_FALSE"       =>        '',
                     )
                );
}
else
{
	$active_checked='';
    $page_title="Edit Site Menu";
    $next_function='menu-update';
    $dbu->query("select *
    			 from cms_menu
    			 where menu_id='".$glob['menu_id']."'");
    $dbu->move_next();

    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '',
                        "E_EDIT_MODE"       =>        '',
                        "S_ADD_MODE"        =>        '<!--',
                        "E_ADD_MODE"        =>        '-->',
                        
                        "MENU_ID"           =>        $glob['menu_id'],
                        "NAME"              =>        $dbu->gf('name'),
                        "DESCRIPTION"       =>        $dbu->gf('description'),
                        
                        "T_FILE_V"          =>        $dbu->f('template_file_v'),
                        "T_FILE_H"          =>        $dbu->f('template_file_h'),
                        "TAG_H"             =>        $dbu->f('tag_h'),
                        "TAG_V"             =>        $dbu->f('tag_v')
                     )
                );
                
    if($dbu->f('h_version'))
    {
    	$ft->assign(array(
    					"S_H_V_TRUE"        =>        '',
                        "E_H_V_TRUE"        =>        '',
                        "S_H_V_FALSE"       =>        '<!--',
                        "E_H_V_FALSE"       =>        '-->',
                     )
                );
    	
    }
    else 
    {
    	$ft->assign(array(
    					"S_H_V_TRUE"        =>        '<!--',
                        "E_H_V_TRUE"        =>        '-->',
                        "S_H_V_FALSE"       =>        '',
                        "E_H_V_FALSE"       =>        '',
                     )
                );
    
    }
                
    if($dbu->f('v_version'))
    {
    	$ft->assign(array(
    					"S_V_V_TRUE"        =>        '',
                        "E_V_V_TRUE"        =>        '',
                        "S_V_V_FALSE"       =>        '<!--',
                        "E_V_V_FALSE"       =>        '-->',
                     )
                );
    	
    }
    else 
    {
    	$ft->assign(array(
    					"S_V_V_TRUE"        =>        '<!--',
                        "E_V_V_TRUE"        =>        '-->',
                        "S_V_V_FALSE"       =>        '',
                        "E_V_V_FALSE"       =>        '',
                     )
                );
    
    }

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MENU_ID',$glob['menu_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>