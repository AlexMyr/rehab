<?php
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "program_add_patient.html"));

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->define_dynamic('client_line','main');
$dbu=new mysql_db();

$exerciseString = $dbu->field("SELECT exercise_program_id
					FROM 
						exercise_program_plan 
					WHERE 
						1=1
					AND
						exercise_program_plan_id=".$glob['program_id']." ");

$alert = "index.php?pag=pexercisepdf&program_id=".$glob['program_id'];
$blank = 'target="_blank"';
if(!$exerciseString)
{
		$blank = "";
		$alert = "javascript: alert('".$tags['T.ADD_EXERCISE']."');";
}

if(isset($glob['client_id']) && $ld['client_id'] != '')
{
  //$button_line = '<a style="width:50px; float:left; margin:0px;" '.$blank.' class="moreBtn" href="'.$alert.'"><span>Print</span></a><button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span>Send Email</span></button>';
    $button_line = '<button style="display:inline;" type="submit" name="print"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.PRINT'].'</span></button>
                    <button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.SEND_EMAIL'].'</span></button>';
  
    $dbu->query("select first_name, surname, email, appeal
             FROM client WHERE client_id = {$glob['client_id']} ");
    
    if(isset($_SESSION['modify_program_return_url']))
        parse_str($_SESSION['modify_program_return_url'], $vals);
    if(is_array($vals))
        array_walk($vals, create_function('&$val', '$val = urldecode($val);'));
    unset($_SESSION['modify_program_return_url']);
    
    if($dbu->move_next())
    {
      $ft->assign(array(
        'FIRST_NAME' => isset($vals['first']) ? $vals['first'] : $dbu->f('first_name'),
        'SURNAME' => isset($vals['surname']) ? $vals['surname'] : $dbu->f('surname'),
        'EMAIL' => isset($vals['email']) ? $vals['email'] : $dbu->f('email'),
        'APPEAL' => isset($vals['appeal']) ? $vals['appeal'] : $dbu->f('appeal'),
		'CLIENT_ID' => $glob['client_id'],
      ));
    }
}
elseif($glob['mode'] == 'email')
{
  //$button_line = '<button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span style="margin-right:10px;">Send Email</span></button><a style="width:50px; float:left; margin:0px;" '.$blank.' class="moreBtn" href="'.$alert.'"><span>Print</span></a>';
  $button_line = '<button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.SEND_EMAIL'].'</span></button>
				  <button style="display:inline;" type="submit" name="print"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.PRINT'].'</span></button>';
}
else
{
  //$button_line = '<a style="width:50px; float:left; margin:0px;" '.$blank.' class="moreBtn" href="'.$alert.'"><span>Print</span></a><button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span>Send Email</span></button>';
  $button_line = '<button style="display:inline;" type="submit" name="print"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.PRINT'].'</span></button>
				  <button style="display:inline;" type="submit" name="mail"><b>&nbsp;</b><span style="margin-right:10px;">'.$tags['T.SEND_EMAIL'].'</span></button>';
}
$add_patient = get_template_tag('dashboard', $glob['lang'], 'T.ADD_PATIENT');
$popup_title = get_template_tag('programs', $glob['lang'], 'T.POPUP_TITLE');

$dbu->query("select * from exercise_program_plan where exercise_program_plan.trainer_id=".$_SESSION[U_ID]." AND exercise_program_plan_id='".$glob['program_id']."' ORDER BY program_name ASC ");

if($dbu->move_next())
{
		$ft->assign(array(
			'PROGRAM_ID'=>$dbu->f('exercise_program_plan_id'),
			'PROGRAM_NAME'=>$dbu->f('program_name'),
			'BUTTON_LINE'=>$button_line,
            'ADD_PATIENT' => $add_patient,
            'T.POPUP_TITLE' => $popup_title,
            'MODIFY_PROGRAM' => isset($glob['client_id'])
								? '<a href="index.php?pag=program_update_exercise&act=client-modify_program&program_id='.$glob['program_id'].'&client_id='.$glob['client_id'].'" id="modify_program_button" class="moreBtn" style="clear: both;"><span>Modify</span></a>'
								: '<a href="javascript:void(0);" id="modify_program_button" class="moreBtn" style="clear: both; opacity:0.5; filter:alpha(opacity=50);"><span>Modify</span></a>'
		));
	$ft->parse('CLIENT_LINE_OUT','.client_line');
}

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');


$ft->assign('CSS_PAGE', $glob['pag']);
$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>