<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "menu_list.html"));
$ft->define_dynamic('menu_row','main');

$dbu=new mysql_db;

$dbu->query("select * from cms_menu order by name");

$i=0;

while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
    $ft->assign('EDIT_LINK',"index.php?pag=menu_add&menu_id=".$dbu->f('menu_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=menu_list&act=menu-delete&menu_id=".$dbu->f('menu_id'));
    $ft->parse('menu_ROW_OUT','.menu_row');
    $i++;
}
              
$ft->assign('PAGE_TITLE',"List Of Site Menus");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','menu_row');
return $ft->fetch('CONTENT');






?>
