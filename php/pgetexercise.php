<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/

if(!$_SESSION['pids']) $_SESSION['pids'] = array();

if(isset($glob['epid']) && isset($glob['pid']) && !in_array($glob['pid'],$_SESSION['pids']))
{
	$dbu = new mysql_db();

	$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['epid']." ");
	if($get_exercise_image_type==0) $image_type = "lineart";
	else if($get_exercise_image_type==1) $image_type = "image";

	$program = $dbu->query("
							SELECT 
								programs.*
							FROM
									programs 
							WHERE
								programs.programs_id='".$glob['pid']."' 
							");
	$glo = array();
	$program->next();
	$the_image = $program->f($image_type) ? $program->f($image_type) : 'noimg64.gif';

	$glo['PROGRAM_ID'] = $program->f('programs_id');
	$glo['PROGRAM_TITLE'] = strip_tags($program->f('programs_title'));
	$glo['PROGRAM_DESCRIPTION'] = strip_tags($program->f('description'));
	$glo['PROGRAM_IMAGE'] = $script_path.UPLOAD_PATH.$the_image;
	$glo['PROGRAM_CATEGORY'] = strip_tags(get_category_path(get_cat_ID($glob['pid']),0));
	$glo['err'] = '200';
	$_SESSION['pids'][] = $glob['pid'];
	unset($glob['pid']);
}
else if(isset($glob['rm_pid']) && in_array($glob['rm_pid'],$_SESSION['pids']))
{
	$glo['err'] = '200';
	while(in_array($glob['rm_pid'], $_SESSION['pids'])) 
	{
		$key = array_search($glob['rm_pid'], $_SESSION['pids']);
		unset($_SESSION['pids'][$key]);
	}
	unset($glob['rm_pid']);
}
else
{
	$glo['err'] = '404';
}
return $glo;