<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_list.html"));
$ft->define_dynamic('programs_list','main');

$dbu = new mysql_db();

$select_all_programs = "SELECT DISTINCT
							programs.*, programs_category.category_name, programs_category.category_id 
						FROM 
							programs 
								INNER JOIN programs_in_category 
									ON programs.programs_id=programs_in_category.programs_id
								INNER JOIN programs_category 
									ON programs_in_category.category_id=programs_category.category_id
						WHERE 1=1 
						AND main=1 
						";
$order_by = " ORDER BY programs.sort_order ASC";
if ($glob['search_key']) {
	$dbu->query($select_all_programs." AND ( programs_title LIKE '%".$glob['search_key']."%' OR description LIKE '%".$glob['search_key']."%' ) " .$order_by );
}
else {
	$dbu->query($select_all_programs ." ".$order_by );
}

$ft->assign(array(
	'MESSAGE' => $glob['error'],
	'PAGE_TITLE' => 'Program list',
));

$i=0;

while ($dbu->move_next())
{
	$i++;
	$ft->assign(array(
		'S_ORDER'=>'<input type="text" style="width:40px;" name="sort_order['.$dbu->f('programs_id').']" value="'.$dbu->f('sort_order').'" />',
		'PAG' =>'programs_list',
		'PROGRAMS_CODE' => $dbu->f('programs_code'),
		'PROGRAMS_TITLE' => $dbu->f('programs_title'),
		'DESCRIPTION' => $dbu->f('description'),
		'CATEGORY' => get_admin_category_path($dbu->f('category_id')),
//		'CATEGORY' => $dbu->f('category_name'),
		'EDIT_LINK' => 'index.php?pag=programs_add&programs_id='.$dbu->f('programs_id'),
		'DELETE_LINK' => 'index.php?pag=programs_list&programs_id='.$dbu->f('programs_id')."&act=programs-delete",
	));
	
	if($dbu->f('lineart'))
	{
		$path = $script_path.UPLOAD_PATH.$dbu->f('thumb_lineart');
		$ft->assign('LINEART','<img width="60" border="1" style="border-color: #000000; margin-left:5px;" vspace="1" hspace="1" src="'.$path.'" />');
	}
	else
	{
		$path = $script_path."img/na_small.gif";
		$ft->assign('LINEART','<img width="60" border="1" style="border-color: #000000; margin-left:5px;" vspace="1" hspace="1" src="'.$path.'" />');
	}
	if($dbu->f('image'))
	{
		$path = $script_path.UPLOAD_PATH.$dbu->f('thumb_image');
		$ft->assign('IMAGE','<img width="60" border="1" style="border-color: #000000; margin-left:5px;" vspace="1" hspace="1" src="'.$path.'" />');
	}
	else
	{
		$path = $script_path."img/na_small.gif";
		$ft->assign('IMAGE','<img width="60" border="1" style="border-color: #000000; margin-left:5px;" vspace="1" hspace="1" src="'.$path.'" />');
	}


	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}


	$ft->parse('PROGRAMS_LIST_OUT','.programs_list');

}
if ($i==0) {
	return '';
}

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>