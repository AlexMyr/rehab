<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "printpdf.html"));

$ft->assign('PDF_URL' , 'index.php?pag=exercisepdf&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'] );
$ft->assign('EMAIL_URL' , 'index.php?pag=client_email&act=client-mail_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$glob['exercise_plan_id'] );

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');