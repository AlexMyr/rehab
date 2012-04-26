<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
//$ft->define(array('main' => "profile.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();

if($dbu->f('active')==0) $page_title = "Your Trial Account Has Expired";
else $page_title = "Register Your Account";


$e_year=date("Y");
$e_month=date("n");

$ft->assign(array(
					'PRICE_ID'   => $glob['price_id'],
					'FIRST_NAME'     => $glob['first_name'] ? $glob['first_name'] : $dbu->f('first_name'),
					'SURNAME'     => $glob['surname'] ? $glob['surname'] : $dbu->f('surname'),
					'COUNTRY_DD'	=> build_country_list($glob['country_id']),	
//					'EMAIL'			=> $glob['email'],	
					'EMAIL'			=> $glob['email'] ? $glob['email'] : $dbu->f('email'),	
					'CREDIT_CARD_NO'			=> $glob['credit_card_no'],
					'CVV2'			=> $glob['cvv2'],	
					"E_YEAR"        =>        build_year_list(date("Y")-0, date('Y')+10,$e_year),
					"E_MONTH"       =>        build_month_list($e_month),						
					
	));

$ft->define(array('main' => "profile_do_payment.html"));

$ft->assign('CSS_PAGE', $glob['pag']);
$ft->assign('PAGE_TITLE', $page_title);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>