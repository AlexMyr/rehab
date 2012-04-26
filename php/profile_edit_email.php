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

$dbu->query("
			SELECT trainer.* 
			FROM trainer 
			WHERE trainer.trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();
if($dbu->f('is_trial')==1)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(false));
}
else if($dbu->f('is_trial')==0)
{
	$ft->assign('HEADER_PAPER_SECTION',build_header_paper_button(true));
}

if(!$dbu->f('email')) $ft->assign('HIDE_CHANGE_EMAIL','none');
if(!$dbu->f('password')) $ft->assign('HIDE_CHANGE_PASS','none');

if($dbu->f('is_trial')==0 && $dbu->f('price_plan_id')!=0)
	{
		$cancel_form = '<form action="index.php" method="post">
							<input type="hidden" name="act" value="member-cancel_payment">
							<input type="hidden" name="pag" value="profile">
							<input type="hidden" name="pp_del_key" value="123delkey321">
							<div class="buttons floatRgt" style="margin-top:4px;"><button class="del" type="submit"><b>&nbsp;</b><span>'.$tags['T.CANCEL'].'</span></button></div>
						</form>';
		$ft->assign('CANCEL_PAYMENT',$cancel_form);	
	}
else
	{
		$ft->assign('CANCEL_PAYMENT','');
	}

		$ft->define(array('main' => "profile_edit_email.html"));
		$ft->assign(array(
			'EMAIL'=>$dbu->f('email'),
		));

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>