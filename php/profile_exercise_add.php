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

$dbu = new mysql_db();

$dbu->query("SELECT * FROM `programs_category` WHERE category_level=0");
$cat_options = '<option value="none">{T.SELECT_CAT}</option>';
while($dbu->move_next()){
    $cat_options .= '<option value="'.$dbu->f('category_id').'">'.$dbu->f('category_name').'</option>';
}
$ft->assign('CAT_OPTIONS', $cat_options);

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>