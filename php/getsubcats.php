<?php
/************************************************************************
* @Author: 
************************************************************************/
if(isset($glob['catid']))
{
	$glo = array();
	$dbu = new mysql_db();
	$dbu->query("SELECT pc.category_id, pc.category_name
				 FROM programs_category_subcategory as pcs
				 LEFT JOIN programs_category as pc ON pc.category_id = pcs.category_id
				 WHERE pcs.parent_id = {$glob['catid']} AND pc.category_level > 0
				 ORDER BY pc.sort_order
				");
	
	while($dbu->move_next())
	{
		$cat_select .= '<option value="'.$dbu->f('category_id').'">'.$dbu->f('category_name').'</option>';
	}
	
	return $cat_select;
}
?>