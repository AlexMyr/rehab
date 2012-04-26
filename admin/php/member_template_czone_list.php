<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "member_template_czone_list.html"));
$ft->define_dynamic('czone_row','main');

$dbu=new mysql_db;

$dbu->query("select * from trainer_dashboard_template_czone order by name");

$i=0;

while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('TAG',$dbu->f('tag'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
    $ft->assign('EDIT_LINK',"index.php?pag=member_template_czone_add&template_czone_id=".$dbu->f('template_czone_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=member_template_czone_list&act=member_template_czone-delete&template_czone_id=".$dbu->f('template_czone_id'));
    $ft->parse('czone_row_OUT','.czone_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Member Template Content Zones in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Member Template Content Zones");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','czone_row');
return $ft->fetch('CONTENT');






?>
