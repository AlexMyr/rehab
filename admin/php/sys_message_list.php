<?php
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "sys_message_list.html"));
$ft->define_dynamic('message_row','main');

$i=0; 
$dbu=new mysql_db;
$dbu->query("select * from sys_message order by title ASC");
while($dbu->move_next())
{
	$ft->assign('TAG',$dbu->f('name'));
	$ft->assign('NAME',$dbu->f('title'));
    $ft->assign('EDIT_LINK',"index.php?pag=sys_message_add&sys_message_id=".$dbu->f('sys_message_id'));
	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
 	$ft->parse('messega_ROW_OUT','.message_row');
	$i++;
}

$ft->assign('MESSAGE',$glob['error']);
$ft->assign('PAGE_TITLE',"List Of Sys Messages");
$ft->parse('content','main');
$ft->clear_dynamic('content','message_row');
return $ft->fetch('content');
?>