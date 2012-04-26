<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$dbu = new mysql_db();

$dbu->query("select trainer.*, trainer_profile.* from trainer 
				INNER JOIN trainer_profile ON trainer.profile_id=trainer_profile.profile_id
			where trainer.trainer_id=".$_SESSION[U_ID]." ");
if($dbu->gf('is_trial')==1)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(false));
}
else if($dbu->gf('is_trial')==0)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true));
}
if($dbu->f('is_trial')==0 && $dbu->gf('price_plan_id')!=0)
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
$dbu->move_next();


		$ft->define(array('main' => "profile_edit.html"));
		$ft->assign(array(
			'FIRST_NAME'=>$dbu->gf('first_name'),
			'SURNAME'=>$dbu->gf('surname'),
			'COMPANY_NAME'=>$dbu->gf('company_name'),
			'ADDRESS'=>$dbu->gf('address'),
			'CITY'=>$dbu->gf('city'),
			'POST_CODE'=>$dbu->gf('post_code'),
			'WEBSITE'=>$dbu->gf('website'),
			'PHONE'=>$dbu->gf('phone'),
			'MOBILE'=>$dbu->gf('mobile'),
			'IMAGE_TYPE'=>$dbu->gf('print_image_type'),
			'PROFILE_ID'=>$dbu->gf('profile_id'),
		));

	$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_notes WHERE trainer_id='".$_SESSION[U_ID]."'");
	$get_exercise_notes->next();
	
	$ft->assign(array( 'EXERCISE_NOTES'=> ''.get_content_input_area(1, $get_exercise_notes->gf('exercise_notes'), 'exercise_notes',$params), ));
		
$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title." - Edit Profile";
$site_meta_keywords=$meta_keywords.", Edit Profile";
$site_meta_description=$meta_description." Edit Profile";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>