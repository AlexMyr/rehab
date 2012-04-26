<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_add.html"));

$dbu = new mysql_db();

if(!$glob['programs_id'])
{
	//add
	if(!$glob['active'])
	{
		$glob['active'] = 1;
	}
	$ft->assign(array(
		'PAG' =>'programs_add',
		'EDIT' =>'hide',
		'ACT' =>'programs-add',
		'PAGE_TITLE' =>'Add Programs',
		'MESSAGE' => $glob['error'],
//		'CATEGORY' => build_programs_category_select($glob['programs_id']),
		'PROGRAMS_CODE' => $glob['programs_code'] ? $glob['programs_code'] : '' ,
		'PROGRAMS_TITLE' => $glob['programs_title'] ? $glob['programs_title'] : '' ,
		'CATEGORY' => build_category_list($glob['category_id']),		
		'THUMB_LINEART' => '',	
		'THUMB_IMAGE' => '',	
		'ACTIVE_CHECKED' => $glob['active'] ? 'checked="checked"' : '',
		'SORT_ORDER' => $glob['sort_order'] ? $glob['sort_order'] : '0',
		)
	);

    $ft->assign('DESCRIPTION_TEXTAREA',get_content_input_area(1, $glob['description'] ? $glob['description'] : '', 'description',$params));
}
else 
{
	//edit
	$dbu->query("SELECT programs.programs_id, programs.programs_title,programs.description,programs.lineart,programs.thumb_lineart,
		 programs.image,programs.thumb_image,programs.active,programs.sort_order,programs.programs_code, programs_category.category_id,
		 programs_category.category_name,programs_category.category_level,programs_category.active,programs_category.sort_order as pc_sort_order
					FROM programs 
					INNER JOIN programs_in_category ON programs.programs_id=programs_in_category.programs_id 
					INNER JOIN programs_category ON programs_in_category.category_id=programs_category.category_id 
					WHERE programs.programs_id='".$glob['programs_id']."'
					AND programs_in_category.main!='0'
				");	
	
	//$dbu->query("SELECT * 
	//				FROM programs 
	//				INNER JOIN programs_in_category ON programs.programs_id=programs_in_category.programs_id 
	//				INNER JOIN programs_category ON programs_in_category.category_id=programs_category.category_id 
	//				WHERE programs.programs_id='".$glob['programs_id']."'
	//				AND programs_in_category.main!='0'
	//			");	
	$dbu->move_next();
	$description =  $dbu->f('description');
	
	$ft->assign(array(
		'PAGE_TITLE' =>'Edit Programs',
		'PAG' =>'programs_add',
		'ACT' =>'programs-update',
		'PROGRAMS_ID' =>$dbu->f('programs_id'),
		'PROGRAMS_CODE' =>$dbu->f('programs_code'),
		'PROGRAMS_TITLE' =>$dbu->f('programs_title'),
		'CATEGORY' => build_category_list($dbu->f('category_id')),
		'ACTIVE_CHECKED' => $dbu->gf('active') ? 'checked="checked"' : '',
		'SORT_ORDER' => $dbu->gf('sort_order'),
	));
	if($dbu->f('lineart'))
	{
		$path = $script_path.UPLOAD_PATH.$dbu->f('thumb_lineart');
		$ft->assign('THUMB_LINEART','<img src="'.$path.'" />');
	}
	else
	{
		$path = $script_path."img/na_small.gif";
		$ft->assign('THUMB_LINEART','<img src="'.$path.'" />');
	}
	if($dbu->f('image'))
	{
		$path = $script_path.UPLOAD_PATH.$dbu->f('thumb_image');
		$ft->assign('THUMB_IMAGE','<img src="'.$path.'" />');
	}
	else
	{
		$path = $script_path."img/na_small.gif";
		$ft->assign('THUMB_IMAGE','<img src="'.$path.'" />');
	}
		
	$ft->assign('CATEGORIES_LINK', 'index.php?pag=multiple_category&programs_id='.$glob['programs_id']);
	
   if(!is_numeric($glob['mode']))
    {
    	$glob['mode']=1;
    }
    $dbu->query("select * from cms_web_page_content 
    			 where 1=1 ");
    $dbu->move_next();
    $ft->assign('DESCRIPTION_TEXTAREA',get_content_input_area($dbu->gf('mode'), $description, 'description',$params));
}

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>