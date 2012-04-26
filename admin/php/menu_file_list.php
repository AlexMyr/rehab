<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "menu_file_list.html"));
$ft->define_dynamic('template_row','main');

$dbu=new mysql_db;

$dbu->query("select * from cms_menu_template_file order by name");

$i=0;

while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('FILE_NAME',$dbu->f('file_name'));
	$ft->assign('TYPE',get_menu_template_type($dbu->f('type')));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
    $ft->assign('EDIT_LINK',"index.php?pag=menu_file_add&menu_template_file_id=".$dbu->f('menu_template_file_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=menu_file_list&act=menu_template_file-delete&menu_template_file_id=".$dbu->f('menu_template_file_id'));
    $ft->parse('template_ROW_OUT','.template_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Menu Template Files in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Menu Template Files");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','template_row');
return $ft->fetch('CONTENT');






?>
