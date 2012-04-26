<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "price_add.html"));

if(!is_numeric($glob['price_id']))
{
	$page_title='Add Price';
	$next_function='price-add';
	
    $ft->assign(array(
                        "PRICE_PLAN_NAME"         =>        $glob['price_plan_name'],
                        "AMOUNT"       =>        $glob['licence_amount'],
                        "PERIOD" 	   =>        $glob['licence_period'],
                        //"CURRENCY"     =>        $glob['currency'],
                        "VALUE"        =>        $glob['price_value'],
						"HAS_LOGO"=>isset($glob['has_logo']) ? 'checked' : '',
						"CAN_CREATE_EXERCISE"=>isset($glob['can_create_exercise']) ? 'checked' : '',
						"EMAIL"=>isset($glob['email']) ? 'checked' : '',
						"PHOTO_LINEART"=>isset($glob['photo_lineart']) ? 'checked' : ''
                     )
                );
}
else 
{	
	$page_title='Edit Price';
	$next_function='price-update';
	$dbu->query("select * from price_plan_new 
    			 where price_id ='".$glob['price_id']."'");
    $dbu->move_next();
    $ft->assign(array(
                        "PRICE_ID"			=>        $glob['price_id'],
                        "PRICE_PLAN_NAME"            	=>        $dbu->gf('price_plan_name'),
                        "AMOUNT"          	=>        $dbu->gf('licence_amount'),
                        "PERIOD"			=>        $dbu->gf('licence_period'),
                        //"CURRENCY"			=>        $dbu->gf('currency'),
                        "VALUE"				=>        $dbu->gf('price_value'),
						"HAS_LOGO"=>$dbu->gf('has_logo') ? 'checked' : '',
						"CAN_CREATE_EXERCISE"=>$dbu->gf('can_create_exercise') ? 'checked' : '',
						"EMAIL"=>$dbu->gf('email') ? 'checked' : '',
						"PHOTO_LINEART"=>$dbu->gf('photo_lineart') ? 'checked' : ''
                     )
                );


}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('PRICE_ID',$glob['price_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>