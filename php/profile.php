<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$dbu = new mysql_db();

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();

if(!$dbu->f('email')) $ft->assign('HIDE_CHANGE_EMAIL','none');
if(!$dbu->f('password')) $ft->assign('HIDE_CHANGE_PASS','none');

$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true, $glob['lang']));

//if($dbu->gf('is_trial')==1)
//{
	//$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(false));
	//$ft->assign('AFFILIATES_CODE','');
//}
//else if($dbu->gf('is_trial')==0)
//{
	//$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true));
	
	/*require_once ('misc/PapApi.class.php');
	// login (as merchant)
				
	$session = new Gpf_Api_Session(AFFILIATES_API_M_URL);
	if(!$session->login(AFFILIATES_API_M_USERNAME, AFFILIATES_API_M_PASSWORD))
		{
			die("Cannot login. Message: ".$session->getMessage());
		}
	$request = new Pap_Api_AffiliatesGrid($session);
	$request->addFilter("search", Gpf_Data_Filter::LIKE, $dbu->f('username'));
	$request->sendNow();
	$grid = $request->getGrid();
	$recordset = $grid->getRecordset();
	foreach($recordset as $rec) $refferer_id = $rec->get('refid');
	$affiliate = '<h2>Your affiliate code</h2>';
	$affiliate .= '<label class="label" for="affiliate">code</label>';
	$affiliate .= '<input class="textField" id="affiliate" type="text" readonly="readonly" value="'.$site_url.'/affiliate/scripts/click.php?a_aid='.$refferer_id.'">';
	$ft->assign('AFFILIATES_CODE',$affiliate);*/
//}

if($dbu->gf('is_trial')==0 && $dbu->gf('price_plan_id')!=0)
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

//if ($dbu->gf('profile_id')==0) 
//	{
//		$ft->define(array('main' => "profile_add.html"));
//		$ft->assign(array(
//			'TEXT'=>$dbu->gf('profile_id'),
//			'FIRST_NAME'=>$dbu->gf('first_name'),
//			'SURNAME'=>$dbu->gf('surname'),
//			'COMPANY_NAME'=>$glob['company_name'],
//			'ADDRESS'=>$glob['address'],
//			'CITY'=>$glob['city'],
//			'POST_CODE'=>$glob['post_code'],
//			'WEBSITE'=>$glob['website'],
//			'PHONE'=>$glob['phone'],
//			'MOBILE'=>$glob['mobile'],
//		));
//	$ft->assign(array( 'EXERCISE_NOTES'=> ''.get_content_input_area(1, $glob['exercise_notes'], 'exercise_notes',$params), ));
//	}
//else 
//	{
//		
$dbu2 = new mysql_db();

$dbu2->query("select * from exercise_notes INNER JOIN trainer USING(trainer_id) where trainer_id=".$_SESSION[U_ID]." ");

$dbu2->move_next();

		$ft->define(array('main' => "profile.html"));
		//$ft->assign(array(
		//	'FIRST_NAME'=>$dbu2->gf('first_name'),
		//	'SURNAME'=>$dbu2->gf('surname'),
		//	'COMPANY_NAME'=>$dbu2->gf('company_name'),
		//	'ADDRESS'=>$dbu2->gf('address'),
		//	'CITY'=>$dbu2->gf('city'),
		//	'POST_CODE'=>$dbu2->gf('post_code'),
		//	'WEBSITE'=>$dbu2->gf('website'),
		//	'PHONE'=>$dbu2->gf('phone'),
		//	'MOBILE'=>$dbu2->gf('mobile'),
		//	'IMAGE_TYPE'=>build_print_image_type_list($dbu2->gf('print_image_type')),
		//	'EXERCISE_NOTES'=> $dbu2->gf('exercise_notes')
		//));
		$ft->assign(array(
			'EXERCISE_NOTES'=> $dbu2->gf('exercise_notes'),
            'LANG_EN' => $dbu2->f('lang') == 'en' ? 'selected' : '',
            'LANG_US' => $dbu2->f('lang') == 'us' ? 'selected' : ''
		));
//	}

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('CSS_PAGE', $glob['pag']);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>