<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
session_start();
$_SESSION['uploaded_pdf'] = array();

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "exercisepdf.html"));

$ft->define_dynamic('exercise_line','main');

$dbu=new mysql_db();
$image_type = "image";

if(empty($glob['pag'])) $glob = $ld;

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));

$dbu->query("SELECT * FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");

$default_image = "<img src=\"".K_PATH_IMAGES.'pdfheader.jpg'."\" />";
if($dbu->move_next())
{
    $image = "<img width='240' style='border:1px solid #000000;' heigth='30' src=\"".$script_path.UPLOAD_PATH.$dbu->f('logo_image')."\" />";
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
    'CLIENT_NAME' => 'John Smith',
    'CURRENT_DATE' => date('d F Y',time()),
    ));

$get_exercises = $dbu->query("SELECT * FROM `programs`
                                WHERE active =1 AND owner = -1
                                ORDER BY RAND( ) LIMIT 0, 3");
while($dbu->move_next()){
    $exercise[] = $dbu->f('programs_id');
}

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
    
    $get_data = $dbu->query("SELECT translate.*
                            FROM programs
                                INNER JOIN programs_translate_".$glob['lang']." AS translate USING(programs_id)
                            WHERE 1=1 AND programs.programs_id = ".$exercise[$i]);
    $get_data->next();

    $programs_title = str_replace('�', '\'', htmlentities($get_data->gf('programs_title')));
    $description = str_replace('�', '\'', htmlentities($get_data->gf('description')));
    
    $programs_title = mb_eregi_replace('�', '"', $programs_title);
    $programs_title = mb_eregi_replace('�', '"', $programs_title);
    $description = mb_eregi_replace('�', '"', $description);
    $description = mb_eregi_replace('�', '"', $description);
    
    $ft->assign(array( 'EXERCISE_TITLE'=> $programs_title ));
    $ft->assign(array( 'EXERCISE_DESC'=> $plan_description ));

    $ft->assign('SETS' , "Sets: ".htmlentities(rand(1,5)));
    $ft->assign('REPETITIONS' , "Repetitions: ".htmlentities(rand(1,5)));
    $ft->assign('TIME' , "Time: ".htmlentities(rand(5,60)));
    $ft->parse('EXERCISE_LINE_OUT','.exercise_line');
    
    $i++;
}

$ft->assign('EXERCISE_NOTES', 'Here would be the text of exercise notes');
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');