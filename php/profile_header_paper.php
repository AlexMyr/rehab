<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$dbu = new mysql_db();
			global $script_path;

//$dbu->query("select trainer.*, trainer_profile.*, trainer.email as main_mail from trainer 
//				INNER JOIN trainer_profile ON trainer.profile_id=trainer_profile.profile_id
//			where trainer.trainer_id=".$_SESSION[U_ID]." ");

$dbu->query("select trainer.*, trainer_header_paper.*, trainer.email as main_mail from trainer 
				INNER JOIN trainer_header_paper ON trainer.trainer_id=trainer_header_paper.trainer_id
			where trainer.trainer_id=".$_SESSION[U_ID]." ");


$dbu->move_next();

if(!$dbu->f('email')) $ft->assign('HIDE_CHANGE_EMAIL','none');
if(!$dbu->f('password')) $ft->assign('HIDE_CHANGE_PASS','none');

if($dbu->f('is_trial')==0 && $dbu->f('price_plan_id')!=0)
	{
		$cancel_form = '<form action="index.php" method="post">
							<input type="hidden" name="act" value="member-cancel_payment">
							<input type="hidden" name="pag" value="profile">
							<input type="hidden" name="pp_del_key" value="123delkey321">
							<div class="buttons floatRgt" style="margin-top:4px;"><button class="del" type="submit"><b>&nbsp;</b><span>Cancel my Payment</span></button></div>
						</form>';
		$ft->assign('CANCEL_PAYMENT',$cancel_form);	
	}
else
	{
		$ft->assign('CANCEL_PAYMENT','');
	}	
		$ft->define(array('main' => "profile_header_paper.html"));
		$image = '<img src="'.$script_path.UPLOAD_PATH.$dbu->gf('logo_image').'" alt="trainer_logo" style="display:block;"/>';
		$glob['delete_image'] = 0;
		$ft->assign(array(
			'FIRST_NAME'=>$dbu->f('first_name'),
			'SURNAME'=>$dbu->f('surname'),
			'COMPANY_NAME'=>$dbu->f('company_name'),
			'ADDRESS'=>$dbu->f('address'),
			'POST_CODE'=>$dbu->f('post_code'),
			'WEBSITE'=>$dbu->f('website'),
			'PHONE'=>$dbu->f('phone'),
			'MOBILE'=>$dbu->f('mobile'),
			'EMAIL' => $dbu->f('email'),
			'DELETE_IMAGE' => $glob['delete_image'] ? 'checked="checked"' : '',
			'IMG' => $dbu->f('logo_image') ? $image : '' ,
			'CITY' => $dbu->f('city'),
			'FAX' => $dbu->f('fax'),
		));

$site_meta_title=$meta_title." - Profile Header Paper";
$site_meta_keywords=$meta_keywords.", Profile Header Paper";
$site_meta_description=$meta_description." Profile Header Paper";

$ft->assign('PAG', $glob['pag']);

$ft->assign('CSS_PAGE', $glob['pag']);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>