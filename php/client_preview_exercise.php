<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array('main' => "client_preview_exercise.html"));
$ft->define_dynamic('exercise_line','main');

$dbu = new mysql_db;
//$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM client WHERE trainer_id='".$_SESSION[U_ID]."' AND client.client_id=".$glob['client_id']." ");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";
global $script_path;

	if(!$glob['mode'])
	{
		$glob['mode'] = 'edit';
	}

	$chk_trial = $dbu->field("SELECT is_trial FROM trainer WHERE trainer_id='".$_SESSION[U_ID]."'");
	$ft->assign(array(
		'CLIENT_ID' => $glob['client_id'],		
		'ACT' => 'client-update_exercise_plan',
		'EXERCISE_ID' => $glob['exercise_id'],
		'EXERCISE_PLAN_ID' => $glob['exercise_plan_id'],
		'PAG' => $glob['pag'],
	));
	
	//$dbu->query("SELECT * FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
	$dbu->query("SELECT * FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");
	$default_image = "<img class=\"header_logo\" src=\"".$script_path."tcpdf/images/pdfheader.jpg\" />";
	if($dbu->move_next())
	{
		$image = "<img class=\"header_logo\" src=\"".$script_path.UPLOAD_PATH.$dbu->gf('logo_image')."\" alt=\"".$dbu->gf('logo_image')."\" />";
		$theName = "";
		if($dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('first_name').' '.$dbu->gf('surname').'</div>'; 
		else if($dbu->gf('first_name') && !$dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('first_name').'</div>'; 
		else if(!$dbu->gf('first_name') && $dbu->gf('surname')) $theName = '<div class="name">'.$dbu->gf('surname').'</div>'; 
		
		$ft->assign(array(
			'THE_IMG'=> $dbu->gf('logo_image') ? $image : $default_image,
			'COMPANY' => $dbu->f('company_name') ? str_replace('’', '\'', htmlentities($dbu->gf('company_name'))) : ($theName ? $theName : ''),
			'ADDRESS' => $dbu->f('address') ? str_replace('’', '\'', htmlentities($dbu->gf('address'))) : '',
			'CITY' => $dbu->f('city') ? str_replace('’', '\'', htmlentities($dbu->gf('city'))) : '',
			'POST_CODE' => $dbu->f('post_code') ? str_replace('’', '\'', htmlentities($dbu->gf('post_code'))) : '',
			'PHONE' => $dbu->f('phone') ? 'Tel: '.str_replace('’', '\'', htmlentities($dbu->gf('phone'))) : '',
			'MOBILE' => $dbu->f('mobile') ? 'Mobile: '.str_replace('’', '\'', htmlentities($dbu->gf('mobile'))) : '',
			'FAX' => $dbu->f('fax') ? 'Fax: '.str_replace('’', '\'', htmlentities($dbu->gf('fax'))) : '',
			'EMAIL' => $dbu->f('email') ? str_replace('’', '\'', htmlentities($dbu->gf('email'))) : '',
			'WEBSITE' => $dbu->f('website') ? str_replace('’', '\'', htmlentities($dbu->gf('website'))) : '',
			
			//'COMPANY' => $dbu->gf('company_name') ? '<div>'.$dbu->gf('company_name').'</div>' : ($theName ? '<div>'.stripcslashes($theName).'</div>' : '<div>&nbsp;</div>'),
			//'NAME' => stripcslashes($theName),
			//'ADDRESS' => $dbu->gf('address') ? '<div class="address">'.stripcslashes($dbu->gf('address')).'</div>' : '<div>&nbsp;</div>',
			//'CITY' => $dbu->gf('city') ? '<div>'.stripcslashes($dbu->gf('city')).'</div>' : '<div>&nbsp;</div>',
			//'POST_CODE' => $dbu->gf('post_code') ? '<div>'.stripcslashes($dbu->gf('post_code')).'</div>' : '<div>&nbsp;</div>',
			//'PHONE' => $dbu->gf('phone') ? '<div>Tel: '.stripcslashes($dbu->gf('phone')).'</div>' : '<div>&nbsp;</div>',
			//'MOBILE' => $dbu->gf('mobile') ? '<div>Mobile: '.stripcslashes($dbu->gf('mobile')).'</div>' : '<div>&nbsp;</div>',
			//'FAX' => $dbu->gf('fax') ? '<div>Fax: '.stripcslashes($dbu->gf('fax')).'</div>' : '<div>&nbsp;</div>',
			//'EMAIL' => $dbu->gf('email') ? '<div>'.stripcslashes($dbu->gf('email')).'</div>' : '<div>&nbsp;</div>',
			//'WEBSITE' => $dbu->gf('website') ? '<div>'.stripcslashes($dbu->gf('website')).'</div>' : '<div>&nbsp;</div>',
			//'THE_IMG'=> $dbu->gf('logo_image') ? $image : $default_image,
		));
	}
	else
	{
		$ft->assign(array(
			'COMPANY' => $dbu->gf('company_name') ? '<div>'.$dbu->gf('company_name').'</div>' : '<div>Company name here</div>',
			'NAME' => $theName ? $theName : 'Your name here',
			'ADDRESS' => $dbu->gf('address') ? '<div class="address">'.$dbu->gf('address').'</div>' : '<div>Address here</div>',
			'CITY' => $dbu->gf('city') ? '<div>'.$dbu->gf('city').'</div>' : '<div>City here</div>',
			'POST_CODE' => $dbu->gf('post_code') ? '<div>'.$dbu->gf('post_code').'</div>' : '<div>Post code here</div>',
			'PHONE' => $dbu->gf('phone') ? '<div>Tel: '.$dbu->gf('phone').'</div>' : '<div>Phone number here</div>',
			'MOBILE' => $dbu->gf('mobile') ? '<div>Mobile: '.$dbu->gf('mobile').'</div>' : '<div>Mobile number here</div>',
			'EMAIL' => $dbu->gf('email') ? '<div>'.$dbu->gf('email').'</div>' : '<div>Email here</div>',
			'WEBSITE' => $dbu->gf('website') ? '<div>'.$dbu->gf('website').'</div>' : '<div>Website name here</div>',
			'THE_IMG'=> ''
		));
	}
	$get_client_infos = $dbu->query("SELECT first_name, surname, email FROM client WHERE client_id='".$glob['client_id']."' AND trainer_id='".$_SESSION[U_ID]."'");
	$get_client_infos->next();
	$has_email = $get_client_infos->gf('email') ? true : false;
		$ft->assign(array(
			'CLIENT_NAME' => '<span id="clientinfo" style="font-size:10pt; font-weight: bold;">'.$get_client_infos->gf('first_name')." ".$get_client_infos->gf('surname').'</span>',
			'CURRENT_DATE' => '<span id="clientinfo" style="font-size:10pt; font-weight: bold;">'.date('d F Y',time()).'</span>',
			));
	
	$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_plan WHERE trainer_id='".$_SESSION[U_ID]."'  AND client_id='". $glob['client_id']."' AND exercise_plan_id='". $glob['exercise_plan_id']."' ");

	if(!$get_exercise_notes->next() || !$get_exercise_notes->gf('exercise_notes'))
	{
		$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_notes WHERE trainer_id='".$_SESSION[U_ID]."'");
		$get_exercise_notes->next();
	}
	
	if(!empty($glob['exercise_notes']))	$exercise_notes = $glob['exercise_notes'];
	else if(empty($glob['exercise_notes'])) $exercise_notes = $get_exercise_notes->gf('exercise_notes');
	
	
	if($glob['mode']== 'edit'){
		$ft->assign(array(
			'DISCARD' 					=> '"index.php?pag=client&client_id='.$glob['client_id'].'"',
			'LINK_TEXT' 				=> 'Discard',
			'LINK_TEXT_3' 				=> '<< Change Exercise',
			'ADD_EXERCISE' 				=> 'index.php?pag=client_update_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
			'MODE' 						=> 'preview',
			'VISIBILITY' 				=> '',
			'SPAN_VISIBILITY'	 		=> 'style="display:none;"',
			'VIS' 						=> 'none',
			'TARGET'					=> '_self'
		));	

		$ft->assign(array( 'EXERCISE_NOTES'=> get_content_input_area(3, stripcslashes($exercise_notes), 'exercise_notes', ''), ));

	}
	if($glob['mode'] == 'preview'){
		$ft->assign(array(
			'VISIBILITY' 				=> 'style="display:none;"',
			'SPAN_VISIBILITY' 			=> '',
			'LINK_TEXT_3' 				=> '<< Change Exercise',
			'ADD_EXERCISE' 				=> 'index.php?pag=client_update_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
			'LINK_TEXT' 				=> 'Print', // edit Print to Print Preview
			'DISCARD' 					=> 'index.php?pag=exercisepdf&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
			'LINK_MAKE MORE_CHANGES' 	=> 'Make More Changes',
			'MAKE_MORE_CHANGES' 		=> 'index.php?pag=client_preview_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'].'&exercise_id='.$glob['exercise_id'].'&mode=edit',		
			'EMAIL_URL' 				=> 'index.php?pag=client_email&act=client-mail_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
			'TARGET'					=> '_blank',
			'HIDE_EMAIL'				=> $has_email ? '' : 'none',
			'FINISH'					=> 'index.php?pag=client&client_id='.$glob['client_id'],
			
		));
		$ft->assign(array( 'EXERCISE_NOTES'=> '<span class="exercise-desc" style="border:0px solid #ccc; width: 655px;"><strong>'.stripcslashes($exercise_notes).'</strong></span>', ));
	}
	
	$exercise = explode(',',$glob['exercise_id']);

$i = 0;

while($i<count($exercise))
{
	$get_program = $dbu->query("SELECT description, ".$image_type.", programs_title, uploaded_pdf FROM programs
                               INNER JOIN programs_translate_".$glob['lang']." USING(programs_id)
                               WHERE programs_id='".$exercise[$i]."'");
	$get_program->next();
	
	$ft->assign(array(
		
		'IMG' => (file_exists('upload/'.$get_program->f($image_type)) && $get_program->f($image_type)) ? $get_program->f($image_type) : ($get_program->f('uploaded_pdf') ? 'pdf.png' : 'noimage.png'),
		'PROGRAM_ID' => $exercise[$i],
	));	
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
								exercise_plan_id=".$glob['exercise_plan_id']." 
							AND 
								client_id=".$glob['client_id']."
							AND is_program_plan = 0
							");
	$get_data->next();
	if($glob['mode']== 'edit')
	{
		if($get_data->gf('plan_description')) 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.stripcslashes($get_data->gf('programs_title')).'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> get_content_input_area(3, stripcslashes($get_data->gf('plan_description')), 'description'.$exercise[$i],$params), ));	
		}
		else 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.stripcslashes($get_program->gf('programs_title')).'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> get_content_input_area(3, stripcslashes($get_program->gf('description')), 'description'.$exercise[$i],$params), ));	
		}
	}
	elseif($glob['mode'] == 'preview')
	{
		if($get_data->gf('plan_description'))
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.stripcslashes($get_data->gf('programs_title')).'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> '<span class="exercise-desc" style="border:0px solid #ccc;"><strong>'.stripcslashes($get_data->gf('plan_description')).'</strong></span>', ));
		}
		else 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.stripcslashes($get_program->gf('programs_title')).'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> '<span class="exercise-desc" style="border:0px solid #ccc;"><strong>'.stripcslashes($get_program->gf('description')).'</strong></span>', ));
		}
	}
	if(!empty($glob['sets'.$exercise[$i]]))	$ft->assign('SETS' , $glob['sets'.$exercise[$i]]);
	else if(empty($glob['sets'.$exercise[$i]])) $ft->assign('SETS' , stripcslashes($get_data->gf('plan_set_no')));
	if(!empty($glob['repetitions'.$exercise[$i]])) $ft->assign('REPETITIONS' , $glob['repetitions'.$exercise[$i]]);
	else if(empty($glob['repetitions'.$exercise[$i]])) $ft->assign('REPETITIONS' , stripcslashes($get_data->gf('plan_repetitions')));
	if(!empty($glob['time'.$exercise[$i]])) $ft->assign('TIME' , $glob['time'.$exercise[$i]]);
	else if(empty($glob['time'.$exercise[$i]])) $ft->assign('TIME' , stripcslashes($get_data->gf('plan_time')));
    $ft->parse('EXERCISE_LINE_OUT','.exercise_line');
    if($glob['mode'] == 'preview' && $glob['sets'.$exercise[$i]] == '0' && $glob['repetitions'.$exercise[$i]] == '0' && $glob['time'.$exercise[$i]] == '0' ){
    	$ft->assign(array(
    		'SPAN_VISIBILITY' => 'style="display:none;"',
    		'LABEL_VISIBILITY' => 'style="display:none;"',
    	));
    }
    $i++;
}

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('PAGE',$glob['pag']);
$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('content','main');
return $ft->fetch('content');

?>