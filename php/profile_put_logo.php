<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$dbu = new mysql_db();
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$glob['lang'] = $glob['lang'] ? $glob['lang'] : 'en';

$ft->define(array('main' => "profile_put_logo.html"));

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

//get test url
$programs = $dbu->query("select exercise_plan.* from exercise_plan where 1=1 AND exercise_plan.trainer_id=".$_SESSION[U_ID]." AND exercise_plan.client_id=(select client_id from client where trainer_id=".$_SESSION[U_ID]." order by create_date asc limit 0,1) order by exercise_plan.date_created asc limit 0,1");
$dbu->move_next();
$test_url = "index.php?pag=exercisepdf&client_id=".$programs->f('client_id')."&exercise_plan_id=".$programs->f('exercise_plan_id')."";

$ft->assign('HIMAGE_POSITION_LEFT', 'checked');
$ft->assign('HIMAGE_POSITION_RIGHT', '');
$ft->assign('TEST_URL', $test_url);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>