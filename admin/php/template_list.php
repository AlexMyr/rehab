<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "template_list.html"));
$ft->define_dynamic('template_row','main');

$dbu=new mysql_db;

$dbu->query("select * from cms_template order by name");

$i=0;

while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('FILE_NAME',$dbu->f('file_name'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
    $ft->assign('EDIT_LINK',"index.php?pag=template_add&template_id=".$dbu->f('template_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=template_list&act=template-delete&template_id=".$dbu->f('template_id'));
    $ft->parse('template_ROW_OUT','.template_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Web Page Templates in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Web Page Templates");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','template_row');
return $ft->fetch('CONTENT');






?>
