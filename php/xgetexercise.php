<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
if(!$_SESSION['pids']) $_SESSION['pids'] = array();

if(isset($glob['cid']) && isset($glob['pid']) && !in_array($glob['pid'],$_SESSION['pids']))
{
	$dbu = new mysql_db();

	$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM client WHERE trainer_id='".$_SESSION[U_ID]."' AND client.client_id=".$glob['cid']." ");
	if($get_exercise_image_type==0) $image_type = "lineart";
	else if($get_exercise_image_type==1) $image_type = "image";
	
	$left_join = " LEFT JOIN programs_translate_".$_COOKIE['language']." AS programs_loc ON programs_loc.programs_id=programs.programs_id";

	$program = $dbu->query("
					SELECT 
						programs.*, programs_loc.programs_title, programs_loc.description
					FROM
						programs
					$left_join
					WHERE
						programs.programs_id='".$glob['pid']."' 
						");
	$glo = array();
	$program->next();
	$the_image = (file_exists('upload/'.$program->f($image_type)) && $program->f($image_type)) ? $program->f($image_type) : 'noimage.png';

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
if(!$_SESSION['pids'] || !count($_SESSION['pids'])) $_SESSION['pids'] = array(false);
return $glo;