<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "program_preview_exercise.html"));
$ft->define_dynamic('exercise_line','main');

$dbu = new mysql_db;

$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['program_id']." ");

if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";
global $script_path;

if(!$glob['mode'])
{
	$glob['mode'] = 'edit';
}

$ft->assign(array(
	'PROGRAM_ID' => $glob['program_id'],		
	'ACT' => 'client-update_program_exercise_plan',
	'EXERCISE_ID' => $glob['exercise_id'],
	'EXERCISE_PLAN_ID' => $glob['exercise_plan_id'],
	'PROGRAM_PLAN_ID'=>$glob['program_id'],
	'PAG' => $glob['pag'],
));

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

$ft->assign(array(
	'CURRENT_DATE' => '<span id="clientinfo" style="font-size:10pt; font-weight: bold;">'.date('d F Y',time()).'</span>',
	));

$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."'  AND exercise_program_plan_id='". $glob['program_id']."' ");
if(!$get_exercise_notes->next()  || !$get_exercise_notes->gf('exercise_notes'))
{
	$get_exercise_notes = $dbu->query("SELECT exercise_notes FROM exercise_notes WHERE trainer_id='".$_SESSION[U_ID]."'");
	$get_exercise_notes->next();
}

if(!empty($glob['exercise_notes']))	$exercise_notes = stripslashes($glob['exercise_notes']);
else if(empty($glob['exercise_notes'])) $exercise_notes = stripslashes($get_exercise_notes->gf('exercise_notes'));


if($glob['mode']== 'edit')
{
	$ft->assign(array(
		'DISCARD' 					=> '"index.php?pag=dashboard"',
		'HIDE_PDF'					=> '',
		'LINK_TEXT' 				=> 'Discard',
		'LINK_TEXT_3' 				=> '<< Change Exercise',
		'ADD_EXERCISE' 				=> 'index.php?pag=program_update_exercise&program_id='.$glob['program_id'],
		'MODE' 						=> 'preview',
		'VISIBILITY' 				=> '',
		'SPAN_VISIBILITY'	 		=> 'style="display:none;"',
		'VIS' 						=> 'none',
		'TARGET'					=> '_self',
		'MARGIN_BUTTONS'			=> '150px'
	));	
	$ft->assign(array( 'EXERCISE_NOTES'=> get_content_input_area(3, stripcslashes($exercise_notes), 'exercise_notes', ''), ));
}

if($glob['mode'] == 'preview')
{
	$ft->assign(array(
		'VISIBILITY' 				=> 'style="display:none;"',
		'SPAN_VISIBILITY' 			=> '',
		'LINK_TEXT_3' 				=> '<< Change Exercise',
		'ADD_EXERCISE' 				=> 'index.php?pag=program_update_exercise&program_id='.$glob['program_id'],
		'LINK_TEXT' 				=> 'Print', // edit Print to Print Preview
		'DISCARD' 					=> 'index.php?pag=exercisepdf&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
		'HIDE_PDF'					=> 'none',
		'LINK_MAKE MORE_CHANGES' 	=> 'Make More Changes',
		'MAKE_MORE_CHANGES' 		=> 'index.php?pag=program_preview_exercise&program_id='.$glob['program_id'].'&exercise_id='.$glob['exercise_id'].'&mode=edit',		
		'EMAIL_URL' 				=> 'index.php?pag=client_email&act=client-mail_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'],
		'TARGET'					=> '_blank',
		'HIDE_EMAIL'				=> 'none',
        'FINISH_URL'                => isset($_SESSION['modify_program_return_url']) ? $_SESSION['modify_program_return_url'] : 'index.php?pag=programs',
		'MARGIN_BUTTONS'			=> '150px'
		
	));
	$ft->assign(array( 'EXERCISE_NOTES'=> '<span class="exercise-desc" style="border:0px solid #ccc; width: 655px;"><strong>'.nl2br(stripcslashes($exercise_notes)).'</strong></span>', ));
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
		//'IMG' => $get_program->f($image_type) ? $get_program->f($image_type) : 'noimg256.gif',
		'IMG' => (file_exists('upload/'.$get_program->f($image_type)) && $get_program->f($image_type)) ? $get_program->f($image_type) : ($get_program->f('uploaded_pdf') ? 'pdf.png' : 'noimage.png'),
		'PROGRAM_ID' => $exercise[$i],
	));
    $get_descr = $dbu->query("SELECT description FROM programs
                                INNER JOIN programs_custom_descr AS custom_descr ON custom_descr.exercise_id = programs.programs_id
                                WHERE programs_id='".$exercise[$i]."'
                                    AND custom_descr.program_id=".$glob['program_id']);
    $get_descr->next();
	$get_data = $dbu->query("
							SELECT 
								exercise_plan_set.*, translate.*
							FROM 
								exercise_plan_set, programs
                            INNER JOIN
                                programs_translate_".$glob['lang']." AS translate on (translate.programs_id = programs.programs_id)
							WHERE
								is_program_plan = 1
							AND
								programs.programs_id = exercise_plan_set.exercise_program_id
							AND
								exercise_program_id=".$exercise[$i]." 
							AND
								exercise_plan_id=".$glob['program_id']." 
							AND 
								client_id=".$glob['program_id']." 
							");
                            
	$get_data->next();

    $description = $get_descr->f('description') ? $get_descr->f('description') : $get_program->gf('description');
    
	if($glob['mode']== 'edit')
	{
		if($get_data->gf('plan_description')) 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.$get_data->gf('programs_title').'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> get_content_input_area(3, $get_data->gf('plan_description'), 'description'.$exercise[$i],$params), ));	
		}
		else 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.$get_program->gf('programs_title').'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> get_content_input_area(3, $description, 'description'.$exercise[$i],$params), ));	
		}
		if(!empty($glob['both_sides'.$exercise[$i]])) $ft->assign('BOTH_SIDES' , ($glob['both_sides'.$exercise[$i]] ? 'checked' : ''));
		else if(empty($glob['both_sides'.$exercise[$i]])) $ft->assign('BOTH_SIDES', ($get_data->gf('both_sides') ? 'checked' : ''));
	}
	elseif($glob['mode'] == 'preview')
	{
		if($get_data->gf('plan_description'))
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.$get_data->gf('programs_title').'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> '<span class="exercise-desc" style="border:0px solid #ccc;"><strong>'.$get_data->gf('plan_description').'</strong></span>', ));
		}
		else 
		{
			$ft->assign(array( 'EXERCISE_TITLE'=> '<span style="margin-left:5px; font-size:15px;"><b>'.$get_program->gf('programs_title').'</b></span>'));	
			$ft->assign(array( 'EXERCISE_DESC'=> '<span class="exercise-desc" style="border:0px solid #ccc;"><strong>'.$description.'</strong></span>', ));
		}
		if(!empty($glob['both_sides'.$exercise[$i]])) $ft->assign('BOTH_SIDES_TEXT' , ($glob['both_sides'.$exercise[$i]] ? 'Perform both sides' : ''));
		else if(empty($glob['both_sides'.$exercise[$i]])) $ft->assign('BOTH_SIDES_TEXT', ($get_data->gf('both_sides') ? 'Perform both sides' : ''));
	}
	if(!empty($glob['sets'.$exercise[$i]]))	$ft->assign('SETS' , $glob['sets'.$exercise[$i]]);
	else if(empty($glob['sets'.$exercise[$i]])) $ft->assign('SETS' , $get_data->gf('plan_set_no'));
	if(!empty($glob['repetitions'.$exercise[$i]])) $ft->assign('REPETITIONS' , $glob['repetitions'.$exercise[$i]]);
	else if(empty($glob['repetitions'.$exercise[$i]])) $ft->assign('REPETITIONS' , $get_data->gf('plan_repetitions'));
	if(!empty($glob['time'.$exercise[$i]])) $ft->assign('TIME' , $glob['time'.$exercise[$i]]);
	else if(empty($glob['time'.$exercise[$i]])) $ft->assign('TIME' , $get_data->gf('plan_time'));
    
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