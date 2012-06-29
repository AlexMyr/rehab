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

$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true, $glob['lang']));

if(isset($glob['paym']))
{
	if($glob['paym'])
	{
		$glob['error'] = 'Thank you for paying.';
		$glob['success'] = true;
	}
	else
	{
		$glob['error'] = 'Error. Please try again or contact administration.';
		$glob['success'] = false;
	}
}

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
  
$dbu2 = new mysql_db();

$dbu2->query("select * from exercise_notes INNER JOIN trainer USING(trainer_id) where trainer_id=".$_SESSION[U_ID]." ");

$dbu2->move_next();

$ft->define(array('main' => "profile.html"));
$ft->assign(array(
	'EXERCISE_NOTES'=> stripcslashes($dbu2->gf('exercise_notes')),
	'LANG_EN' => $dbu2->f('lang') == 'en' ? 'selected' : '',
	'LANG_US' => $dbu2->f('lang') == 'us' ? 'selected' : ''
));

if($dbu2->f('title_set'))
  $ft->assign('CHECKED_SECOND_TITLE', 'checked');
else
  $ft->assign('CHECKED_FIRST_TITLE', 'checked');

if(!$dbu2->f('email_set'))
  $ft->assign('CHECKED_FIRST_EMAIL', 'checked');
else
  $ft->assign('CHECKED_SECOND_EMAIL', 'checked');  
  

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('CSS_PAGE', $glob['pag']);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>