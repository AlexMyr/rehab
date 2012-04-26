<?php
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "price_list.html"));
$ft->define_dynamic('message_row','main');

$i=0; 
$dbu=new mysql_db;
$dbu->query("select * from price_plan_new");
while($dbu->move_next())
{
	$ft->assign('PRICE_PLAN_NAME',$dbu->f('price_plan_name'));
	$ft->assign('HAS_LOGO',($dbu->f('has_logo') ? '&#10003;' : ''));
	$ft->assign('CAN_CREATE_EXERCISE',($dbu->f('can_create_exercise') ? '&#10003;' : ''));
	$ft->assign('EMAIL',($dbu->f('email') ? '&#10003;' : ''));
	$ft->assign('PHOTO_LINEART',($dbu->f('photo_lineart') ? '&#10003;' : ''));
	$ft->assign('AMOUNT',$dbu->f('licence_amount'));
	$ft->assign('PERIOD',$dbu->f('licence_period'));
	$ft->assign('VALUE',$dbu->f('price_value'));
    $ft->assign('EDIT_LINK',"index.php?pag=price_add&price_id=".$dbu->f('price_id'));
	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
 	$ft->parse('MESSAGE_ROW_OUT','.message_row');
	$i++;
}

$ft->assign('MESSAGE',$glob['error']);
$ft->assign('PAGE_TITLE',"List Of Prices");
$ft->parse('content','main');
$ft->clear_dynamic('content','message_row');
return $ft->fetch('content');
?>