<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_add_new.html"));

$count_exercise_select = '<select name="count_exercise" id="count_exercise_select">';
for($i=1;$i<=10;$i++)
{
	$count_exercise_select .= "<option value='$i'>$i</option>";
}
$count_exercise_select .= '</select>';
$ft->assign('COUNT_EXERCISE_SELECT', $count_exercise_select);

$dbu = new mysql_db();
$dbu2 = new mysql_db();
$categories = array();

$select_top_cat = "select category_id, category_name, category_level
from programs_category where category_level=0 order by sort_order asc
";
$dbu->query($select_top_cat);
while ($dbu->move_next())
{
	$categories[] = array('category_id'=>$dbu->f('category_id'),'category_name'=>$dbu->f('category_name'),'category_level'=>$dbu->f('category_level'),);
	
	$select_sub_cat = "select pc.category_id, pc.category_name, pc.category_level
	from programs_category pc left join programs_category_subcategory pcs on pc.category_id=pcs.category_id
	where category_level=1 and pcs.parent_id='".$dbu->f('category_id')."' order by sort_order asc
	";
	$dbu2->query($select_sub_cat);
	while ($dbu2->move_next())
	{
		$categories[] = array('category_id'=>$dbu2->f('category_id'),'category_name'=>$dbu2->f('category_name'),'category_level'=>$dbu2->f('category_level'),);
	}
}

$category_select = '<select name="category_id[]" class="DDBox1">';
foreach($categories as $cat)
{
	$disabled = '';
	if($cat['category_level'] == '0') $disabled = 'disabled';
	
	$category_select .= "<option $disabled value='".$cat['category_id']."'>".$cat['category_name']."</option>";
}
$category_select .= '</select>';

$ft->assign(array(
	'PAG' =>'programs_add_new',
	'ACT' =>'programs-add_mult',
	'MESSAGE' => $glob['error'],
	'CATEGORY' => $category_select,
	)
);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>