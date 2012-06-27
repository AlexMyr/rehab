<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "profile_exercise_add.html"));

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign(array(
    'name'=>stripslashes($glob['name']),
    'description'=>stripslashes($glob['description']),
  ));

$dbu = new mysql_db();

$dbu->query("SELECT * FROM `programs_category` WHERE category_level=0");
$cat_options = '<option value="-1">'.get_template_tag($glob['pag'], $glob['lang'], 'T.SELECT_CAT').'</option>';
while($dbu->move_next()){
    $cat_options .= '<option value="'.$dbu->f('category_id').'" '.(isset($glob['category']) && $dbu->f('category_id') == $glob['category'] ? 'selected' : '').' >'.$dbu->f('category_name').'</option>';
}
$ft->assign('CAT_OPTIONS', $cat_options);

if(isset($glob['category']) && $glob['category'] > -1)
{
  $dbu->query("SELECT pc.category_id, pc.category_name
				 FROM programs_category_subcategory as pcs
				 LEFT JOIN programs_category as pc ON pc.category_id = pcs.category_id
				 WHERE pcs.parent_id = {$glob['category']} AND pc.category_level > 0
				 ORDER BY pc.sort_order
				");
	
  while($dbu->move_next())
  {
    $subcat_select .= '<option value="'.$dbu->f('category_id').'" '.($dbu->f('category_id') == $glob['subcategory'] ? 'selected' : '').'>'.$dbu->f('category_name').'</option>';
  }
}
else
{
  $subcat_select .= '<option value="-1">Select category first</option>';
}
$ft->assign('SUBCAT_OPTIONS', $subcat_select);

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>