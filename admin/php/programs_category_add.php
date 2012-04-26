<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_category_add.html"));

$dbu = new mysql_db();

if(!is_numeric($glob['category_id']))
{
	// add
	if(!$glob['active'])
	{
		$glob['active'] = 1;
	}
	$ft->assign(array(
		'PAG' =>'programs_category_add',
		'ACT' =>'programs_category-add',
		'PAGE_TITLE' =>'Add Programs Category',
		'MESSAGE' => $glob['error'],
		'CATEGORY_ID' => '',
		'CATEGORY_NAME' => '',
		'CATEGORY' => build_category_list(0, 0),
 	   	'SORT_ORDER'  => '0',
 		'ACTIVE_CHECKED'  => 'checked="checked"',
//		'CATEGORY' => build_programs_category_select($glob['category_id']),
		)
	);
}
else 
{
	// edit
	$dbu->query("
					SELECT 
						programs_category.*, programs_category_subcategory.parent_id 
					FROM 
						programs_category 
					INNER JOIN 
							programs_category_subcategory 
						ON 
							programs_category_subcategory.category_id=programs_category.category_id
					WHERE 
						programs_category.category_id='".$glob['category_id']."'
				");
	if(!$dbu->move_next())
	{
		unset($ft);
		return get_error_message('Invalid ID');
	}
/*
	$dbu->query("SELECT * FROM programs_category WHERE category_id='".$glob['category_id']."'");	
	$dbu->move_next();
*/	
	$ft->assign(array(
		'PAGE_TITLE' =>'Edit Programs Category',
		'PAG' =>$glob['pag'],
		'ACT' =>'programs_category-update',
		'MESSAGE' => $glob['error'],
		'CATEGORY_ID' =>$dbu->gf('category_id'),
		'CATEGORY_NAME' => stripslashes($dbu->gf('category_name')),
		'CATEGORY' => build_category_list($dbu->gf('parent_id'), $glob['category_id']),
 	   	'SORT_ORDER'  => $dbu->gf('sort_order'),
 		'ACTIVE_CHECKED'  => $dbu->gf('active') ? 'checked="checked"' : '',
//		'CATEGORY' => build_programs_category_select($dbu->gf('category_id')),
	));
}

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>