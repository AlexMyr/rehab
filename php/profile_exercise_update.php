<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "profile_exercise_update.html"));

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$dbu = new mysql_db();



$ft->assign('PROGRAMS_ID', $glob['programs_id']);

$dbu->query("select * from programs as p left join programs_translate_en as pte on p.programs_id = pte.programs_id where p.owner=".$_SESSION[U_ID]." AND p.programs_id = {$glob['programs_id']} ");
if($dbu->move_next())
{
    $has_image = ($dbu->f('image')) ? true :false;
	$has_pdf = ($dbu->f('uploaded_pdf')) ? true :false;
	$ft->assign(array(
	  'name'=>stripslashes($dbu->f('programs_title')),
	  'description'=>stripslashes($dbu->f('description')),
	  'PROGRAM_PHOTO'=> ($has_image) ? '<img src="phpthumb/phpThumb.php?src=../upload/'.$dbu->f('image').'&amp;wl=69&amp;hp=69" class="image_preview" alt="photo" />' : '',
	  'PHOTO_DELETE' => ($has_image) ? '<img src="img/delete_red.png" class="delete_image" style="position: absolute; right: 0px; cursor:pointer;" />' : '',
	  'PDF_UPLOADED' => $has_pdf ? '<a href="pdf/uploaded_pdf/user_program_'.$glob['programs_id'].'.pdf">Uploaded pdf file</a>' : '',
	  'PDF_DELETE' => $has_pdf ? '<img src="img/delete_red.png" class="delete_image" style="cursor:pointer;" />' : '',
	));
}

$selected_cat = -1;
$selected_subcat = -1;
$dbu->query("select parent_id, category_id from programs_category_subcategory where category_id = (select category_id from programs_in_category where programs_id = {$glob['programs_id']})");
if($dbu->move_next())
{
  $selected_cat = $dbu->f('parent_id');
  $selected_subcat = $dbu->f('category_id');
}

$dbu->query("SELECT * FROM `programs_category` WHERE category_level=0");
$cat_options = '<option value="-1">'.get_template_tag($glob['pag'], $glob['lang'], 'T.SELECT_CAT').'</option>';
while($dbu->move_next()){
    $cat_options .= '<option value="'.$dbu->f('category_id').'" '.($dbu->f('category_id') == $selected_cat ? 'selected' : '').' >'.$dbu->f('category_name').'</option>';
}
$ft->assign('CAT_OPTIONS', $cat_options);

if($selected_cat > -1)
{
  $dbu->query("SELECT pc.category_id, pc.category_name
				 FROM programs_category_subcategory as pcs
				 LEFT JOIN programs_category as pc ON pc.category_id = pcs.category_id
				 WHERE pcs.parent_id = {$selected_cat} AND pc.category_level > 0
				 ORDER BY pc.sort_order
				");
	
  while($dbu->move_next())
  {
    $subcat_select .= '<option value="'.$dbu->f('category_id').'" '.($dbu->f('category_id') == $selected_subcat ? 'selected' : '').'>'.$dbu->f('category_name').'</option>';
  }
}
else
{
  $subcat_select .= '<option value="-1">Select top category</option>';
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