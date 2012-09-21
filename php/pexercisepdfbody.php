<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
session_start();
$_SESSION['uploaded_pdf_program'] = array();

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
		
		if($dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('first_name'))).' '.str_replace('’', '\'', htmlentities($dbu->gf('surname'))).'</div>'; 
		else if($dbu->gf('first_name') && !$dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('first_name'))).'</div>'; 
		else if(!$dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.str_replace('’', '\'', htmlentities($dbu->gf('surname'))).'</div>';
		
		$ft->assign(array(
			'THE_IMG'=> $dbu->gf('logo_image') ? $image : $default_image,
			'COMPANY' => $dbu->f('company_name') ? str_replace('’', '\'', htmlentities($dbu->gf('company_name'))) : ($theName ? $theName : ''),
			'ADDRESS' => $dbu->f('address') ? str_replace('’', '\'', htmlentities($dbu->gf('address'))) : '',
			'CITY' => $dbu->f('city') ? ', '.str_replace('’', '\'', htmlentities($dbu->gf('city'))) : '',
			'POST_CODE' => $dbu->f('post_code') ? ', '.str_replace('’', '\'', htmlentities($dbu->gf('post_code'))) : '',
			'PHONE' => $dbu->f('phone') ? str_replace('’', '\'', htmlentities($dbu->gf('phone'))) : '',
			'MOBILE' => $dbu->f('mobile') ? str_replace('’', '\'', htmlentities($dbu->gf('mobile'))) : str_replace('’', '\'', htmlentities($dbu->gf('fax'))),
			'FAX' => $dbu->f('fax') ? '<tr><td></td><td align="right">'.str_replace('’', '\'', htmlentities($dbu->gf('fax'))).'</td></tr>' : '',
			'EMAIL' => $dbu->f('email') ? str_replace('’', '\'', htmlentities($dbu->gf('email'))) : '',
			'WEBSITE' => $dbu->f('website') ? str_replace('’', '\'', htmlentities($dbu->gf('website'))) : '',
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
		'CLIENT_NAME' => str_replace('’', '\'', htmlentities($glob['first_name']))." ".str_replace('’', '\'', htmlentities($glob['surname'])),
        //,'CURRENT_DATE' => date('d F Y',time()),
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
	
	$get_program = $dbu->query("SELECT description, ".$image_type.", programs_title, programs.* FROM programs
                               INNER JOIN programs_translate_".$glob['lang']." USING(programs_id)
                               WHERE programs_id='".$exercise[$i]."'");
	$get_program->next();
	$print_image = ($get_program->f($image_type) && file_exists($script_path.UPLOAD_PATH.$get_program->f($image_type))) ? $script_path.UPLOAD_PATH.$get_program->f($image_type) : $script_path.UPLOAD_PATH.'noimage.png';

	if($get_program->f('owner') != -1 && $get_program->f('uploaded_pdf'))
	{
		$_SESSION['uploaded_pdf_program'][] = $get_program->f('uploaded_pdf');
	}

	$img = "<img src=\"".$print_image."\" width=\"224\" height=\"224\" align=\"left\" />";
	$ft->assign(array(
		'IMG' => $img,
	));
    $get_descr = $dbu->field("SELECT description FROM programs
                                INNER JOIN programs_custom_descr AS custom_descr ON custom_descr.exercise_id = programs.programs_id
                                WHERE programs_id='".$exercise[$i]."'
                                    AND custom_descr.program_id=".$glob['program_id']);
    
	$get_data = $dbu->query("
							SELECT 
								exercise_plan_set.*, translate.*
							FROM 
								exercise_plan_set, programs
                            INNER JOIN
                                programs_translate_".$glob['lang']." AS translate on (translate.programs_id = programs.programs_id)
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
    
    if($get_descr)
        $description = $get_descr;
    elseif($get_data->gf('plan_description'))
        $description = $get_data->gf('plan_description');
    else
        $description = $get_program->gf('plan_description');
    
	if($get_data->gf('plan_description'))
	{
		$programs_title = str_replace('’', '\'', htmlentities($get_data->gf('programs_title')));
		$plan_description = str_replace('’', '\'', htmlentities($description));
		
		$programs_title = mb_eregi_replace('“', '"', $programs_title);
		$programs_title = mb_eregi_replace('”', '"', $programs_title);
		$plan_description = mb_eregi_replace('“', '"', $plan_description);
		$plan_description = mb_eregi_replace('”', '"', $plan_description);

		$ft->assign(array( 'EXERCISE_TITLE'=> $get_data->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $plan_description ? $plan_description : '', ));
	}
	else
	{
		$programs_title = str_replace('’', '\'', htmlentities($get_program->gf('programs_title')));
		$plan_description = str_replace('’', '\'', htmlentities($description));
		
		$programs_title = mb_eregi_replace('“', '"', $programs_title);
		$programs_title = mb_eregi_replace('”', '"', $programs_title);
		$plan_description = mb_eregi_replace('“', '"', $plan_description);
		$plan_description = mb_eregi_replace('”', '"', $plan_description);
		
		$ft->assign(array( 'EXERCISE_TITLE'=> $get_program->gf('programs_title') ? $programs_title : '', ));
		$ft->assign(array( 'EXERCISE_DESC'=> $plan_description ? $plan_description : '', ));
	}
	$ft->assign('SETS' , $get_data->gf('plan_set_no') ? "Sets: ".htmlentities($get_data->gf('plan_set_no')) : "");
	$ft->assign('REPETITIONS' , $get_data->gf('plan_repetitions') ? "Repetitions: ".htmlentities($get_data->gf('plan_repetitions')) : "");
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