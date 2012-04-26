<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "exercisepdf.html"));

$ft->define_dynamic('exercise_line','main');

$dbu=new mysql_db();
$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['program_id']." ");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";

if(empty($glob['pag'])) $glob = $ld;

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));

$dbu->query("SELECT * FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");

	$default_image = "<img src=\"".K_PATH_IMAGES.'pdfheader.jpg'."\" />";
	if($dbu->move_next())
	{
		$image = "<img width='240' heigth='30' src=\"".$script_path.UPLOAD_PATH.$dbu->f('logo_image')."\" />";
		$theName = "";
		if($dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('first_name').' '.$dbu->gf('surname').'</div>'; 
		else if($dbu->gf('first_name') && !$dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('first_name').'</div>'; 
		else if(!$dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('surname').'</div>'; 
		
		$ft->assign(array(
			'THE_IMG'=> $dbu->gf('logo_image') ? $image : $default_image,
			'COMPANY' => $dbu->f('company_name') ? $dbu->f('company_name') : ($theName ? $theName : ''),
			'ADDRESS' => $dbu->f('address') ? $dbu->f('address') : '',
			'CITY' => $dbu->f('city') ? $dbu->f('city') : '',
			'POST_CODE' => $dbu->f('post_code') ? $dbu->f('post_code') : '',
			'PHONE' => $dbu->f('phone') ? $dbu->f('phone') : '',
			'MOBILE' => $dbu->f('mobile') ? $dbu->f('mobile') : $dbu->f('fax'),
			'FAX' => $dbu->f('mobile') ? '<tr><td>&nbsp;</td><td align="right">'.$dbu->f('fax').'</td></tr>' : '',
			'EMAIL' => $dbu->f('email') ? $dbu->f('email') : '',
			'WEBSITE' => $dbu->f('website') ? $dbu->f('website') : '',
		));
	}
	else {
		$ft->assign(array(
			'THE_IMG'=> $default_image,
			'COMPANY' => $dbu->f('company_name') ? $dbu->f('company_name') : 'Company Name here.',
			'FIRST_NAME' => $dbu->f('first_name') ? $dbu->f('first_name') : 'First Name here.',			
			'SURNAME' => $dbu->f('surname') ? $dbu->f('surname') : 'Surname here.',
			'ADDRESS' => $dbu->f('address') ? $dbu->f('address') : 'Your address here.',
			'CITY' => $dbu->f('city') ? $dbu->f('city') : 'Your city here.',
			'POST_CODE' => $dbu->f('post_code') ? $dbu->f('post_code') : 'Your post code here.',
			'PHONE' => $dbu->f('phone') ? 'Tel: '.$dbu->f('phone') : 'Your phone number here.',
			'MOBILE' => $dbu->f('mobile') ? $dbu->f('mobile') : 'Your mobile number here.',
			'EMAIL' => $dbu->f('email') ? $dbu->f('email') : 'Your e-mail address here.',
			'WEBSITE' => $dbu->f('website') ? $dbu->f('website') : 'Your website here.',
		));		
	}
	
	$ft->assign(array(
		'CLIENT_NAME' => $glob['first_name']." ".$glob['surname'],
		'CURRENT_DATE' => date('d F Y',time()),
		));

	$get_exercises = $dbu->field("
							SELECT 
								exercise_program_plan.exercise_program_id 
							FROM 
								exercise_program_plan 
							WHERE 
								1=1
							AND
								exercise_program_plan_id=".$glob['program_id']." 
							");
	$exercise = explode(',',$get_exercises);

$i = 0;
$count_break = 1;
while($i<count($exercise))
{
	if($count_break%3 == 0 && $count_break < count($exercise))
		$ft->assign('BREAK_LINE' ,'<br pagebreak="true" />');
	else
		$ft->assign('BREAK_LINE',"");
	$count_break++;
	
	$get_program = $dbu->query("SELECT description, ".$image_type.", programs_title FROM programs WHERE programs_id='".$exercise[$i]."'");
	$get_program->next();
	
	$print_image = $get_program->f($image_type) ? $script_path.UPLOAD_PATH.$get_program->f($image_type) : $script_path.UPLOAD_PATH.'noimg256.gif';

	$img = "<img src=\"".$print_image."\" width=\"224\" height=\"224\" align=\"left\" />";
	$ft->assign(array(
		'IMG' => $img,
	));	
	$get_data = $dbu->query("
							SELECT 
								exercise_plan_set.*, programs.programs_title
							FROM 
								exercise_plan_set, programs 
							WHERE 
								1=1 AND programs.programs_id = exercise_plan_set.exercise_program_id
							AND
								exercise_program_id=".$exercise[$i]." 
							AND
								exercise_plan_id=".$glob['program_id']." 
							AND 
								client_id=".$glob['program_id']."
							AND
								is_program_plan = 1
							");
	$get_data->next();
	if($get_data->gf('plan_description'))
	{
		$programs_title = str_replace('�', '\'', $get_data->gf('programs_title'));
		$plan_description = str_replace('�', '\'', $get_data->gf('plan_description'));

		$ft->assign(array( 'EXERCISE_TITLE'=> $get_data->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $get_data->gf('plan_description') ? $plan_description : '', ));
	}
	else
	{
		$programs_title = str_replace('�', '\'', $get_program->gf('programs_title'));
		$plan_description = str_replace('�', '\'', $get_program->gf('plan_description'));
		
		$ft->assign(array( 'EXERCISE_TITLE'=> $get_program->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $get_program->gf('plan_description') ? $plan_description : '', ));
	}
	$ft->assign('SETS' , $get_data->gf('plan_set_no') ? "Sets: ".$get_data->gf('plan_set_no') : "");
	$ft->assign('REPETITIONS' , $get_data->gf('plan_repetitions') ? "Repetitions: ".$get_data->gf('plan_repetitions') : "");
	$ft->assign('TIME' , $get_data->gf('plan_time') ? "Time: ".$get_data->gf('plan_time') : "");
    $ft->parse('EXERCISE_LINE_OUT','.exercise_line');
    
    $i++;
}
	$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_program_plan WHERE exercise_program_plan_id=".$glob['program_id']." ");
	$get_exercise_notes->next();
	
	if(!$get_exercise_notes->gf('exercise_notes'))
	{
		$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_notes WHERE trainer_id='".$_SESSION[U_ID]."'");
		$get_exercise_notes->next();
	}
	
	$ft->assign(array( 'EXERCISE_NOTES'=> $get_exercise_notes->gf('exercise_notes')));	
			
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');