<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "program_update_exercise.html"));

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$dbu = new mysql_db();
//change exercise image type
if(isset($glob['image_view_type']))
{
	$exercise_image_type = $glob['image_view_type'] == 'lineart' ? 0 : 1;
	$dbu->query("UPDATE exercise_program_plan SET print_image_type = '$exercise_image_type' WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['program_id']." ");
}

$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['program_id']." ");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";

$change_image_type = $image_type == 'lineart' ? 'image' : 'lineart';

$get_program_name = $dbu->field("SELECT program_name FROM exercise_program_plan WHERE trainer_id='".$_SESSION[U_ID]."' AND exercise_program_plan_id=".$glob['program_id']." ");

$ft->assign('PROGRAM_NAME', $get_program_name);

/* make the category / subcategory menu */

global $categ_array;
$old_category_array=build_categ_list_array($excluded=0);
	
if($old_category_array)
	$category_array=sort_categ_array($old_category_array);
	
foreach($category_array as &$cat){
	$counter = 0;
	if($cat['status'] == '1'){
		if($cat['category_name'] == 'All'){
			$q = $dbu->query("SELECT `category_id` FROM `programs_category_subcategory` WHERE `parent_id`=".$cat['parent']);
			while($q->next())
				$ids[] = $q->f('category_id');
			
			$c = $dbu->query("SELECT COUNT(distinct `programs_id`) AS category_items FROM `programs_in_category`
							 WHERE 1=1 AND `category_id` IN (".implode(', ', $ids).")");
			unset($ids);
			$c->next();
			$counter = $c->f('category_items');
		}
		else{
			$c = $dbu->query("SELECT COUNT(`programs_id`) AS category_items FROM `programs_in_category` WHERE 1=1 AND `category_id`=".$cat['category_id']);
			$c->next();
			$counter += $c->f('category_items');
		}
		$cat['count'] = $counter;
		
	}
}

$out_str="";
$parent = "";
$ul = false;
$firstSubCat = 0;
if($category_array)
{
	foreach ($category_array as $key=>$cat_array)
	{
		
		$parent = $cat_array['parent'];
		$next = $category_array[$key+1]['parent'];

		if($cat_array['category_level']==0)
		{
			$sub_cat_style = 'display: none;';
			if(isset($_COOKIE['cat_'.$cat_array['category_id']]) && $_COOKIE['cat_'.$cat_array['category_id']] == 'off')
			{
				$sub_cat_style = '';
				$out_str.="<li class=\"parent\" id=\"cat_".$cat_array['category_id']."\" ";//category id
			}
			else
				$out_str.="<li class=\"parent on\" id=\"cat_".$cat_array['category_id']."\" ";//category id
			
	        $out_str.=">";
			$out_str.="<span>".$cat_array['category_name']."</span>";
		}
		
		if($parent!=0&&$ul!=true) 
		{
			$ul = true;
			$out_str.="<ul id=\"sCat_".$parent."\" style=\"$sub_cat_style\">";
		}
		
		if($cat_array['category_level']>0)
		{
			$dbu->query("SELECT COUNT(programs_id) AS category_items FROM programs_in_category WHERE 1=1 AND category_id=".$cat_array['category_id']."");
			$dbu->move_next();
			$firstSubCat++;
			if(!isset($glob['catID'])&&$firstSubCat==1)
			{
				header("location: index.php?pag=program_update_exercise&catID=".$cat_array['category_id']."&program_id=".$glob['program_id']);
				exit;
			}
			else
			{
				$current_class = '';
				if($glob['catID'] == $cat_array['category_id'])
				{
					$current_class = 'curCategory';
				}
			    $out_str.="<li id=\"".$cat_array['category_id']."\" ";//category id
		        $out_str.="><a class=\"$current_class\" href='index.php?pag=program_update_exercise&catID=".$cat_array['category_id']."&program_id=".$glob['program_id']."' >"
                            .$cat_array['category_name']." (".$cat_array['count'].$current.")</a></li>";
			}
		}
		if($parent!=0&&$next==0&&$ul==true) 
		{
			$ul = false;
			$out_str.="</ul>";
		}
		if($next==0) $out_str.="</li>";//category
	}
}
	
$ft->assign('LIST',$out_str);
if($glob['catID']&&$glob['program_id']) 
{
	$ft->assign('BREADCRUMB',get_category_path($glob['catID'],$glob['program_id']));


	// the VIEW programs data
	if(isset($_COOKIE['currentExerciseViewType']))
		$glob['view'] = $_COOKIE['currentExerciseViewType'];

	if(!isset($glob['view'])) $glob['view'] = "compact";

	$view_mode = '';
	$view_url = "index.php?pag=".$glob['pag']
				."&catID=".$glob['catID']
				."&program_id=".$glob['program_id'];
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

	$view_buttons.='<a title="Single View" href="'.$view_url.'&view=details" '.$class_view.'>&nbsp;</a>';
	$view_buttons.='<a title="Multiple View" href="'.$view_url.'&view=compact" '.$class_compact.'>&nbsp;</a>';

	$change_image_link = "<a class='changeViewBtn' href='$view_url&image_view_type=$change_image_type'><span>Show The ".ucfirst($change_image_type)."</span></a>";

	$ft->assign('VIEW_MODE',$view_buttons);
	$ft->assign('EXERCISE_PLAN_ID',$glob['exercise_plan_id']);
	
	$ft->define_dynamic($view_mode,'main');
	
	$cat_info = $dbu->row("SELECT `category_name`, `parent_id` FROM `programs_category`
						INNER JOIN `programs_category_subcategory` USING (`category_id`)
						WHERE `category_id`=".$glob['catID']);
	if($cat_info['category_name'] == 'All'){
		$q = $dbu->query("SELECT `category_id` FROM `programs_category_subcategory` WHERE `parent_id`=".$cat_info['parent_id']);
		while($q->next()){
			$subcats[] = $q->f('category_id');
		}
		$where = 'programs_in_category.category_id IN ('.implode(', ', $subcats).') ';
	}
	else
		$where = "programs_in_category.category_id=".$glob['catID'];

  $program = $dbu->query("
							SELECT 
								programs.*, programs_in_category.category_id, translate.*
							FROM
								programs
							INNER JOIN
								programs_in_category on programs.programs_id=programs_in_category.programs_id
                            INNER JOIN
                                programs_translate_".$glob['lang']." AS translate on (translate.programs_id = programs_in_category.programs_id)
							WHERE
								".$where." 
								AND programs.active = 1
								AND (programs.owner = -1 OR programs.owner = ".$_SESSION[U_ID].")
							GROUP BY programs.programs_id
							ORDER BY programs.owner, programs.sort_order ASC
							");

	$i=0;
	
	$start_user_exercise = false;
	while ($program->next())
	{
        $title = $program->f('programs_title');
        
		if($program->f('owner')!=-1 && !$start_user_exercise)
		{
		  $start_user_exercise = true;
		  $user_break_line = '<div class="clearAllUser">Own exercises</div>';
          $i = 0;
		}
		else
		{
		  $user_break_line = '';
		}
		
		if(($i+1)%3==0)
		{
			$last_css = ' last';
			$clear_both = '<div class="clearAll"></div>';
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
			//'PROGRAM_IMAGE'=>$program->f($image_type) ? $program->f($image_type) : 'noimg138.gif',
			'PROGRAM_IMAGE'=>file_exists('upload/'.$program->f($image_type)) ? $program->f($image_type) : 'noimage_middle.png',
			'CAT_ID'=>$glob['catID'],
			'PROGRAM_PLAN_ID'=>$glob['program_id'],
			'LAST_CSS'=> $last_css,
			'CLEAR_BOTH'=> $clear_both,
			'USER_BREAK_LINE'=> $user_break_line,
		));
		$ft->parse(strtoupper($view_mode).'_OUT','.'.$view_mode);
		$i++;
	}
	
	
	if ($i==0) 
	{
		$glob['error'] = $tags['T.NO_EXERCISE'];
	}
 //end the VIEW programs data
}
else 
{
	$glob['error'] = $tags['T.SELECT'];
}

//if(!count($_SESSION['ppids'])) $_SESSION['ppids'] = array('0'=>'0');

if(!$_SESSION['ppids'] || empty($_SESSION['ppids']))
{
	$_SESSION['ppids'] = array();
	$session_register = $dbu->query("
								SELECT 
									exercise_program_id 
								FROM 
									exercise_program_plan
								WHERE 
									trainer_id='".$_SESSION[U_ID]."'
									AND
									exercise_program_plan_id = ".$glob['program_id']." 
								");
	$i =0;
	while ($session_register->next())
	{
		$sess_edit = explode(",",$session_register->f('exercise_program_id'));
		foreach($sess_edit as $sVal)
		{
			$_SESSION['ppids'][] = $sVal;
		}
		$i++;
	}
}

if(!empty($_SESSION['ppids']))
{
	$ft->define_dynamic('selected_line','main');
  
	$dbu = new mysql_db();
	
	$left_join = " LEFT JOIN programs_translate_".$_COOKIE['language']." AS programs_loc ON programs_loc.programs_id=programs.programs_id";
	foreach($_SESSION['ppids'] as $key=>$val)
	{
		if(!$val)continue;

		$program = $dbu->query("
						SELECT 
							programs.*, programs_loc.programs_title, programs_loc.description
						FROM
								programs
						$left_join
						WHERE
							programs.programs_id='".$val."' 
							");
		$program->next();
	
		$ft->assign(array(
			'S_PROGRAM_ID' => $program->f('programs_id'),
			'S_PROGRAM_TITLE' => strip_tags($program->f('programs_title')),
			'S_PROGRAM_DESCRIPTION' => strip_tags($program->f('description')),
			'S_PROGRAM_IMAGE' => file_exists('upload/'.$program->f($image_type)) ? $script_path.UPLOAD_PATH.$program->f($image_type) : $script_path.UPLOAD_PATH.'noimage_small.png',
			'S_PROGRAM_CATEGORY' => strip_tags(get_category_path(get_cat_ID($val),0)),
		));
		$ft->parse('SELECTED_LINE_OUT','.selected_line');
	}
}

$ft->assign('IMAGE_TYPE_CHANGE', $change_image_link);
$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');


$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>