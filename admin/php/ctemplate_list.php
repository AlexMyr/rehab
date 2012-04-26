<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "ctemplate_list.html"));
$ft->define_dynamic('template_row','main');

$dbu=new mysql_db;

$dbu->query("select * from cms_content_template order by name");

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
	
    $ft->assign('EDIT_LINK',"index.php?pag=ctemplate_add&content_template_id=".$dbu->f('content_template_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=ctemplate_list&act=content_template-delete&content_template_id=".$dbu->f('content_template_id'));
    $ft->parse('template_ROW_OUT','.template_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Content Box Templates in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Content Box Templates");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','template_row');
return $ft->fetch('CONTENT');






?>
