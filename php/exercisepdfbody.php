<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
session_start();
$_SESSION['uploaded_pdf'] = array();

$dbu=new mysql_db();
$himage_pos = $dbu->field("SELECT himage_pos FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");

$ft=new ft(ADMIN_PATH.MODULE."templates/");
if($himage_pos == 'left')
	$ft->define( array(main => "exercisepdf.html"));
else
	$ft->define( array(main => "exercisepdf_right.html"));

$ft->define_dynamic('exercise_line','main');

$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM client WHERE trainer_id='".$_SESSION[U_ID]."' AND client.client_id=".$glob['client_id']." ");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";

if(empty($glob['pag'])) $glob = $ld;

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));

	$chk_trial = $dbu->field("SELECT is_trial FROM trainer WHERE trainer_id='".$_SESSION[U_ID]."'");

	$dbu->query("SELECT * FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");

	$default_image = "<img src=\"".K_PATH_IMAGES.'pdfheader.jpg'."\" />";
	if($dbu->move_next())
	{
		$image = "<img style='border:1px solid #000000;' src=\"".$script_path.UPLOAD_PATH.$dbu->f('logo_image')."\" />";
		$theName = "";
		if($dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('first_name'))).' '.str_replace('’', '\'', htmlentities($dbu->gf('surname'))).'</div>'; 
		else if($dbu->gf('first_name') && !$dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('first_name'))).'</div>'; 
		else if(!$dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('surname'))).'</div>';
		
		$ft->assign(array(
			'THE_IMG'=> $dbu->gf('logo_image') ? $image : $default_image,
			'COMPANY' => $dbu->f('company_name') ? str_replace('’', '\'', htmlentities($dbu->gf('company_name'))) : ($theName ? $theName : ''),
			'ADDRESS' => $dbu->f('address') ? str_replace('’', '\'', htmlentities($dbu->gf('address'))) : '',
			/*'CITY' => $dbu->f('city') ? str_replace('’', '\'', htmlentities($dbu->gf('city'))) : '',
			'POST_CODE' => $dbu->f('post_code') ? str_replace('’', '\'', htmlentities($dbu->gf('post_code'))) : '',*/
			'PHONE' => $dbu->f('phone') ? 'Tel: '.str_replace('’', '\'', htmlentities($dbu->gf('phone'))) : '',
			'MOBILE' => $dbu->f('mobile') ? 'Mobile: '.str_replace('’', '\'', htmlentities($dbu->gf('mobile'))) : '',
			'FAX' => $dbu->f('fax') ? 'Fax: '.str_replace('’', '\'', htmlentities($dbu->gf('fax'))) : '',
			'EMAIL' => $dbu->f('email') ? str_replace('’', '\'', htmlentities($dbu->gf('email'))) : '',
			'WEBSITE' => $dbu->f('website') ? str_replace('’', '\'', htmlentities($dbu->gf('website'))) : '',
			'CITY' => $dbu->f('city') ? '<td width="210">'.str_replace('’', '\'', htmlentities($dbu->gf('city'))).'</td>' : '<td width="210"></td>',
			'POST_CODE' => $dbu->f('post_code') ? '<td width="330">'.str_replace('’', '\'', htmlentities($dbu->gf('post_code'))).'</td>' : '<td width="330"></td>',
		));
		
		if(isset($glob['lang']) && $glob['lang'] == 'us')
			$ft->assign(array(
				'CITY' => '<td width="210"></td>',
				'POST_CODE' => '<td width="330"></td>',
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
	$get_client_infos = $dbu->query("SELECT first_name, surname FROM client WHERE client_id='".$glob['client_id']."' AND trainer_id='".$_SESSION[U_ID]."'");
	$get_client_infos->next();

	$ft->assign(array(
		'CLIENT_NAME' => str_replace('’', '\'', htmlentities($get_client_infos->gf('first_name')))." ".str_replace('’', '\'', htmlentities($get_client_infos->gf('surname'))),
		'CURRENT_DATE' => date('d F Y',time()),
		));

	$get_exercises = $dbu->field("
							SELECT 
								exercise_plan.exercise_program_id 
							FROM 
								exercise_plan 
							WHERE 
								1=1
							AND
								exercise_plan_id=".$glob['exercise_plan_id']." 
							AND 
								client_id=".$glob['client_id']." 
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
	
	$get_program = $dbu->query("SELECT description, ".$image_type.", programs_title, programs.* FROM programs
                               INNER JOIN programs_translate_".$glob['lang']." USING(programs_id)
                                WHERE programs_id='".$exercise[$i]."'");
	$get_program->next();

	$print_image = ($get_program->f($image_type) && file_exists($script_path.UPLOAD_PATH.$get_program->f($image_type))) ? $script_path.UPLOAD_PATH.$get_program->f($image_type) : $script_path.UPLOAD_PATH.'noimage.png';
	
	if($get_program->f('owner') != -1 && $get_program->f('uploaded_pdf'))
	{
		$_SESSION['uploaded_pdf'][] = $get_program->f('uploaded_pdf');
	}
	
	$img = "<img src=\"".$print_image."\" width=\"224\" height=\"224\" align=\"left\" />";
	$ft->assign(array(
		'IMG' => $img,
	));
    
	$get_data = $dbu->query("
							SELECT 
								exercise_plan_set.*, translate.*
							FROM 
								exercise_plan_set, programs
                            INNER JOIN programs_translate_".$glob['lang']." AS translate USING(programs_id)
							WHERE 
								1=1 AND programs.programs_id = exercise_plan_set.exercise_program_id
							AND
								exercise_program_id=".$exercise[$i]." 
							AND
								exercise_plan_id=".$glob['exercise_plan_id']." 
							AND 
								client_id=".$glob['client_id']."
							AND
								is_program_plan = 0
							");
	$get_data->next();

	if($get_data->gf('plan_description'))
	{
		$programs_title = str_replace('’', '\'', htmlentities($get_data->gf('programs_title')));
		$plan_description = str_replace('’', '\'', htmlentities($get_data->gf('plan_description')));
		
		$programs_title = mb_eregi_replace('“', '"', $programs_title);
		$programs_title = mb_eregi_replace('”', '"', $programs_title);
		$plan_description = mb_eregi_replace('“', '"', $plan_description);
		$plan_description = mb_eregi_replace('”', '"', $plan_description);
		
		$ft->assign(array( 'EXERCISE_TITLE'=> $get_data->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $get_data->gf('plan_description') ? $plan_description : '', ));
	}
	else
	{
		$programs_title = str_replace('’', '\'', htmlentities($get_program->gf('programs_title')));
		$plan_description = str_replace('’', '\'', htmlentities($get_program->gf('plan_description')));
		
		$programs_title = mb_eregi_replace('“', '"', $programs_title);
		$programs_title = mb_eregi_replace('”', '"', $programs_title);
		$plan_description = mb_eregi_replace('“', '"', $plan_description);
		$plan_description = mb_eregi_replace('”', '"', $plan_description);
		
		$ft->assign(array( 'EXERCISE_TITLE'=> $get_program->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $get_program->gf('plan_description') ? $plan_description : '', ));
	}
	$ft->assign('SETS' , $get_data->gf('plan_set_no') ? "Sets: ".htmlentities($get_data->gf('plan_set_no')) : "");
	$ft->assign('REPETITIONS' , $get_data->gf('plan_repetitions') ? "Repetitions: ".htmlentities($get_data->gf('plan_repetitions')) : "");
	$ft->assign('TIME' , $get_data->gf('plan_time') ? "Time: ".$get_data->gf('plan_time') : "");
	$ft->assign('BOTH_SIDES', $get_data->gf('both_sides') ? "Perform both sides" : "");
    $ft->parse('EXERCISE_LINE_OUT','.exercise_line');
    
    $i++;
}
	$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_plan WHERE exercise_plan_id=".$glob['exercise_plan_id']." AND client_id=".$glob['client_id']."    ");
	$get_exercise_notes->next();
	
	if(!$get_exercise_notes->gf('exercise_notes'))
	{
		$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_notes WHERE trainer_id='".$_SESSION[U_ID]."'");
		$get_exercise_notes->next();
	}
	
	$ft->assign(array( 'EXERCISE_NOTES'=> nl2br($get_exercise_notes->gf('exercise_notes'))));
			
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');