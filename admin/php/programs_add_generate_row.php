<?php
$dbu = new mysql_db();
$dbu2 = new mysql_db();

if(isset($_POST['cur_count']) && isset($_POST['new_count']))
{
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

	$response = '';
	$count_add = intval($_POST['new_count']) - intval($_POST['cur_count']);
	for($i=0;$i<$count_add;$i++)
	{
		$response .= '
			<table width="620" border="0" cellspacing="0" cellpadding="0" class="exercise_row_counter">
				<tr>
					<td>&nbsp;</td>
					<td><strong>Program Title : </strong></td>
					<td colspan="2" style="padding-top: 5px;"><input type="text" name="programs_title[]" value="" class="txtField" size="38" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td bgcolor="#F5F5F5" valign="top"><strong>Program Text : </strong></td>
					<td bgcolor="#F5F5F5" colspan="2"><textarea name="description[]"></textarea></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td bgcolor="#F5F5F5" valign="top"><strong>Show US section : </strong></td>
					<td bgcolor="#F5F5F5" colspan="2"><input type="checkbox" class="section_us_display" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr class="section_us section_hide">
					<td>&nbsp;</td>
					<td ><strong>Program Title US: </strong></td>
					<td colspan="2"><input type="text" name="programs_title_us[]" value="" class="txtField" size="38" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr class="section_us section_hide">
					<td>&nbsp;</td>
					<td bgcolor="#F5F5F5" valign="top"><strong>Program Text US: </strong></td>
					<td bgcolor="#F5F5F5" colspan="2"><textarea name="description_us[]"></textarea></td>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><strong>Category :</strong></td>
					<td colspan="2">
						'.$category_select.'
					</td>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td bgcolor="#F5F5F5"><strong>Program Code :</strong></td>
					<td bgcolor="#F5F5F5" colspan="2"><input type="text" name="programs_code[]" value="" class="txtField" size="38" /></td>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><strong>Browse for lineart image : </strong></td>
					<td><input type="file" name="lineart[]" maxlength="255" size="30"  class="txtField" /></td>
					<td align="right"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td bgcolor="#F5F5F5"><strong>Browse for exercise image : </strong></td>
					<td bgcolor="#F5F5F5"><input type="file" name="image[]" maxlength="255" size="30"  class="txtField" /></td>
					<td bgcolor="#F5F5F5" align="right"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="border-bottom: 1px solid;" bgcolor="#F5F5F5" colspan="3" height="5">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		';
	}
	
	echo $response;
	exit;
}



?>