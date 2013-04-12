<?php
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_user.html"));

$ft->define_dynamic('client_line','main');

$dbu = new mysql_db();

$dbu->query("select * from programs as p left join programs_translate_".$_COOKIE['language']." as pt on p.programs_id = pt.programs_id where p.owner=".$_SESSION[U_ID]." ORDER BY p.sort_order ASC ");

$i=0;

while ($dbu->move_next())
{
		$ft->assign(array(
			'PROGRAM_ID'=>$dbu->f('programs_id'),
			'PROGRAM_NAME'=>$dbu->f('programs_title'),
			'PROGRAM_DESC'=>$dbu->f('description'),
		));
	$ft->parse('CLIENT_LINE_OUT','.client_line');
	$i++;
}

$ft->assign('FIRST_NAME', $glob['first_name']);
$ft->assign('SURNAME', $glob['surname']);
$ft->assign('EMAIL', $glob['email']);
//$ft->assign('IMAGE_TYPE', $glob['print_image_type']);
$ft->assign('CLIENT_NOTE', $glob['client_note']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>