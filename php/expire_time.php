<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
//$ft->define(array('main' => "profile.html"));

//$page_title='Login Member';
//$next_function ='auth-login';
$ft->define(array('main' => "expire_time.html"));

if($_SESSION[U_ID])
{
$dbu = new mysql_db();

$get_time = $dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$get_time->next();
if($get_time->f('is_trial')==1)
{
$expire_time = (strtotime($get_time->f('expire_date'))-time());

$expire_days = intval(intval($expire_time) / (3600 * 24));
$expire_hours = intval(intval($expire_time) / 3600);
$expire_minutes = (intval(intval($expire_time) / 60) % 60);

if($expire_days>0 && $expire_days>1) $time_remained = "in <strong>".$expire_days." days</strong>"; 
else if($expire_days>0 && $expire_days==1) $time_remained = "in <strong>".$expire_days." day</strong>"; 

else if($expire_days<1 && $expire_minutes>0) $time_remained = "<strong>today</strong>"; 

//else if($expire_days>0 && $expire_days==1) $time_remained = "in ".$expire_days." day"; 
//else if($expire_days<1 && $expire_hours>0 && $expire_hours>1) $time_remained = "in ".$expire_hours." hours"; 
//else if($expire_days<1 && $expire_hours>0 && $expire_hours==1) $time_remained = "in ".$expire_hours." hour"; 
//else if($expire_days<1 && $expire_hours<1 && $expire_minutes>0 && $expire_minutes>1) $time_remained = "in ".$expire_minutes." minutes"; 
//else if($expire_days<1 && $expire_hours<1 && $expire_minutes>0 && $expire_minutes==1) $time_remained = "in ".$expire_minutes." minute"; 

if($expire_days<1 && $expire_minutes<1)
{
	$dbu->query("UPDATE trainer SET active=0 WHERE trainer_id=".$_SESSION[U_ID]." AND active!=0 ");
$ft->assign(array(
		'EXPIRE_DATE' => "<span class='expire_time'><span>Your FREE TRIAL account has expired</span></span>",
));
}
else
{
$ft->assign(array(
		'EXPIRE_DATE' => "<span class='expire_time'><span>Your FREE TRIAL account will expire ".$time_remained."</span></span>",
//		'FREE_TRIAL' => "<span style='background-color:#ff0000; padding:10px; color:#fff; top:-20px; right:-5px; position:absolute; display:block; width:auto;'>Your FREE TRIAL account will expire in ".$time_remained."</span>",
//		'ACCOUNT_STATUS' => "<h3>".$expire_time."</h3>",
//		'TIME_H' => "<p>".intval(intval($expire_time) / 3600)."</p>",
//		'TIME_M' => "<p>".((intval($expire_time) / 60) % 60)."</p>",
//		'TIME_S' => "<p>".(intval($expire_time) % 60)."</p>",
));
}

if((strtotime($get_time->f('expire_date'))<time()) && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment')); }
else if((strtotime($get_time->f('expire_date'))<time()) && $get_time->f('active')==0 && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment')); }
else { $ft->assign('REDIRECT', ''); }

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
}
else { return; }
}
else { return; }
?>