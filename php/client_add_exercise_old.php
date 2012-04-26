<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "client_add_exercise.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

/*check is finished exercise*/
/*$query_str = "SELECT `exercise_plan_id` FROM `exercise_plan` WHERE `trainer_id` = '".$_SESSION[U_ID]."' AND `client_id` = '".$glob['client_id']."' ORDER BY `exercise_plan_id` DESC";
$row1 = $dbu->row($query_str);
if($row1['exercise_plan_id'])
{
	unset($glob['redir']);
	$query_str = "SELECT COUNT(`exercise_set_id`) FROM `exercise_plan_set` WHERE `exercise_plan_id` = '".$row1['exercise_plan_id']."'";
	$row2 = $dbu->row($query_str);

	if(!$row2[0])
	{
		$glob['redir'] = 'index.php?pag=client_update_exercise&client_id='.$glob['client_id'].'&exercise_plan_id='.$row1['exercise_plan_id'];
		include("php/redirect.php");
	}
}
*/

//$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM client WHERE trainer_id='".$_SESSION[U_ID]."' AND client_id=".$glob['client_id']."");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";

$ft->assign('CLIENT_ID', $glob['client_id']);

$query = $dbu->query("select client.* from client where client.client_id=".$glob['client_id']." ");

if($query->next()) $ft->assign('CLIENT_NAME',$query->f('first_name')." ".$query->f('surname'));

/* make the category / subcategory menu */

	global $categ_array;
	$old_category_array=build_categ_list_array($excluded=0);

	if($old_category_array)
		$category_array=sort_categ_array($old_category_array);

	$out_str="";
	$parent = "";
	$ul = false;
	$firstSubCat = 0;
	if($category_array)
	foreach ($category_array as $key=>$cat_array)
	{
		
		$parent = $cat_array['parent'];
		$next = $category_array[$key+1]['parent'];

//		.str_repeat("&nbsp;&nbsp;",$cat_array['category_level']).$cat_array['category_name'].
		if($cat_array['category_level']==0)
			{
	    $out_str.="<li class=\"parent\" id=\"cat_".$cat_array['category_id']."\" ";//category id
//	    $out_str.=" cat_lvl=\"".$cat_array['category_level']."\" ";
//	    $out_str.=" parent=\"".$cat_array['parent']."\" ";
        $out_str.=">";
		$out_str.="<span>".$cat_array['category_name']."</span>";
			}
		if($parent!=0&&$ul!=true) 
		{
		$ul = true;
		$out_str.="<ul id=\"sCat_".$parent."\">";
		}
		if($cat_array['category_level']>0)
			{
				$dbu->query("SELECT COUNT(programs_id) AS category_items FROM programs_in_category WHERE 1=1 AND category_id=".$cat_array['category_id']."");
				$dbu->move_next();
				$firstSubCat++;
if(!isset($glob['catID'])&&$firstSubCat==1)
{
	header("location: index.php?pag=client_add_exercise&catID=".$cat_array['category_id']."&client_id=".$glob['client_id']);
}
else
{
	    $out_str.="<li id=\"".$cat_array['category_id']."\" ";//category id
//	    $out_str.=" cat_lvl=\"".$cat_array['category_level']."\" ";
//	    $out_str.=" parent=\"".$cat_array['parent']."\" ";
        $out_str.="><a href='index.php?pag=client_add_exercise&catID=".$cat_array['category_id']."&client_id=".$glob['client_id']."' >"
							.$cat_array['category_name']." (".$dbu->f('category_items').")</a></li>";
}
			}
		if($parent!=0&&$next==0&&$ul==true) 
		{
		$ul = false;
		$out_str.="</ul>";
		}
		if($next==0) $out_str.="</li>";//category
	}
	
$ft->assign('LIST',$out_str);
if($glob['catID']&&$glob['client_id']) 
{
$ft->assign('BREADCRUMB',get_category_path($glob['catID'],$glob['client_id']));


// the VIEW programs data

if(!isset($glob['view'])) $glob['view'] = "details";

$view_mode = '';
$view_url = "index.php?pag=".$glob['pag']
			."&catID=".$glob['catID']
			."&client_id=".$glob['client_id'];
$view_buttons = '';
if($glob['view']=="details")
	{
		$view_mode = 'exercise_details_line';
	}
else if($glob['view']=="compact")
	{
		$view_mode = 'exercise_compact_line';
	}

		$class_view = $glob['view'] == 'details' ? 'class="details current"' : 'class="details"';
		$class_compact = $glob['view'] == 'compact' ? 'class="compact current"' : 'class="compact"';

		$view_buttons.='<a href="'.$view_url.'&view=details" '.$class_view.'>&nbsp;</a>';
		$view_buttons.='<a href="'.$view_url.'&view=compact" '.$class_compact.'>&nbsp;</a>';

	
$ft->assign('VIEW_MODE',$view_buttons);

$ft->define_dynamic($view_mode,'main');

$program = $dbu->query("
				SELECT 
					programs.*, programs_in_category.category_id
				FROM
						programs 
					INNER JOIN
						programs_in_category on programs.programs_id=programs_in_category.programs_id
				WHERE
					programs_in_category.category_id=".$glob['catID']." 
					AND programs.active = 1
				ORDER BY programs.sort_order ASC
					");
$i=0;

while ($program->next())
	{
		if(($i+1)%3==0)
			{
				$last_css = " last";
				$clear_both = "<div class=\"clearAll\"></div>";
			}
		else
			{
				$last_css = "";
				$clear_both = "";
			}
		$ft->assign(array(
			'PROGRAM_ID'=>$program->f('programs_id'),
			'PROGRAM_TITLE'=>$program->f('programs_title'),
			'PROGRAM_DESCRIPTION'=>$program->f('description'),
			'PROGRAM_IMAGE'=>$program->f($image_type) ? $program->f($image_type) : 'noimg138.gif',
			'CAT_ID'=>$glob['catID'],
			'CLIENT_ID'=>$glob['client_id'],
			'CLEAR_BOTH'=> $clear_both,
			'LAST_CSS'=> $last_css,
		));
		$ft->parse(strtoupper($view_mode).'_OUT','.'.$view_mode);
		$i++;
	}
if ($i==0) 
	{
		//	return '';
		$glob['error'] = 'No Exercise available on this category. Please select from the left menu.';
	}
// end the VIEW programs data
}
else 
	{
		$glob['error'] = 'Please select a category from the left menu to begin.';
	}
if(!empty($_SESSION['pids']))
{
$ft->define_dynamic('selected_line','main');

	foreach($_SESSION['pids'] as $key=>$val)
		{
		$dbu = new mysql_db();
		
		$program = $dbu->query("
						SELECT 
							programs.*
						FROM
								programs 
						WHERE
							programs.programs_id='".$val."' 
							");
		$program->next();
		
		$ft->assign(array(
			'S_PROGRAM_ID' => $program->f('programs_id'),
			'S_PROGRAM_TITLE' => strip_tags($program->f('programs_title')),
			'S_PROGRAM_DESCRIPTION' => strip_tags($program->f('description')),
			'S_PROGRAM_IMAGE' => $program->f($image_type) ? $script_path.UPLOAD_PATH.$program->f($image_type) : $script_path.UPLOAD_PATH.'noimg64.gif',
			'S_PROGRAM_CATEGORY' => strip_tags(get_category_path(get_cat_ID($val),0)),
		));
	$ft->parse('SELECTED_LINE_OUT','.selected_line');
		}
}

$ft->assign('CSS_PAGE', $glob['pag']);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>