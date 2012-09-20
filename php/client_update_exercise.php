<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "client_update_exercise.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();
//change exercise image type
if(isset($glob['image_view_type']))
{
	$exercise_image_type = $glob['image_view_type'] == 'lineart' ? 0 : 1;
	$dbu->query("UPDATE client SET print_image_type = '$exercise_image_type' WHERE trainer_id='".$_SESSION[U_ID]."' AND client.client_id=".$glob['client_id']." ");
}

//$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
$get_exercise_image_type = $dbu->field("SELECT print_image_type FROM client WHERE trainer_id='".$_SESSION[U_ID]."' AND client.client_id=".$glob['client_id']." ");
if($get_exercise_image_type==0) $image_type = "lineart";
else if($get_exercise_image_type==1) $image_type = "image";

$change_image_type = $image_type == 'lineart' ? 'image' : 'lineart';

$ft->assign('CLIENT_ID', $glob['client_id']);

$query = $dbu->query("select client.* from client where client.client_id=".$glob['client_id']." ");

if($query->next()) $ft->assign('CLIENT_NAME',stripcslashes($query->f('first_name')." ".$query->f('surname')));

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
            
            $c = $dbu->query("SELECT COUNT(distinct pic.`programs_id`) AS category_items FROM `programs_in_category` pic left join programs p on p.programs_id=pic.programs_id
                         WHERE 1=1 AND `category_id` IN (".implode(', ', $ids).") AND (owner=-1 or owner=".$_SESSION[U_ID].")");
            unset($ids);
            $c->next();
            $counter = $c->f('category_items');
        }
        else{
            $c = $dbu->query("SELECT COUNT(pic.`programs_id`) AS category_items FROM `programs_in_category` pic left join programs p on p.programs_id=pic.programs_id WHERE 1=1 AND (owner=-1 or owner=".$_SESSION[U_ID].") AND `category_id`=".$cat['category_id']);
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
foreach ($category_array as $key=>$cat_array)
{
    $parent = $cat_array['parent'];
    $next = $category_array[$key+1]['parent'];

//		.str_repeat("&nbsp;&nbsp;",$cat_array['category_level']).$cat_array['category_name'].
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
        
        
//	    $out_str.=" cat_lvl=\"".$cat_array['category_level']."\" ";
//	    $out_str.=" parent=\"".$cat_array['parent']."\" ";
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
            //header("location: index.php?pag=client_update_exercise&catID=".$cat_array['category_id']."&client_id=".$glob['client_id']."&exercise_plan_id=".$glob['exercise_plan_id']);
        }
        else
        {
            $current_class = '';
            if($glob['catID'] == $cat_array['category_id'])
            {
                $current_class = 'curCategory';
            }
            $out_str.="<li id=\"".$cat_array['category_id']."\" ";//category id
        //	    $out_str.=" cat_lvl=\"".$cat_array['category_level']."\" ";
        //	    $out_str.=" parent=\"".$cat_array['parent']."\" ";
            $out_str.="><a class=\"$current_class\" href='index.php?pag=client_update_exercise&catID=".$cat_array['category_id']."&client_id=".$glob['client_id']."&exercise_plan_id=".$glob['exercise_plan_id']."' >"
            //					.$cat_array['category_name']." (".$dbu->f('category_items').")</a></li>";
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
	
$ft->assign('LIST',$out_str);
$ft->assign('EXERCISE_PLAN_ID',$glob['exercise_plan_id']);
if($glob['catID']&&$glob['client_id']) 
{
    $ft->assign('BREADCRUMB',get_category_path($glob['catID'],$glob['client_id']));


	// the VIEW programs data
	if(isset($_COOKIE['currentExerciseViewType']))
		$glob['view'] = $_COOKIE['currentExerciseViewType'];

	//if(!isset($glob['view'])) $glob['view'] = "details";
	if(!isset($glob['view'])) $glob['view'] = "compact";

	$view_mode = '';
	$view_url = "index.php?pag=".$glob['pag']
			."&catID=".$glob['catID']
			."&client_id=".$glob['client_id']
			."&exercise_plan_id=".$glob['exercise_plan_id'];
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
    
    if(isset($glob['query']) && $glob['query'])
    {
      $where = " translate.programs_title LIKE '%".mysql_escape_string($glob['query'])."%' ";
    }
    
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
                    ORDER BY programs.owner, programs.sort_order ASC
                        ");
    $i=0;
    
    $start_user_exercise = false;
    while ($program->next())
        {
            if($program->f('owner')!=-1 && !$start_user_exercise)
            {
                $i = 0;
                $start_user_exercise = true;
                $user_break_line = '<div class="clearAllUser">Own exercises</div>';
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
                'PROGRAM_IMAGE'=>(file_exists('upload/'.$program->f($image_type)) && $program->f($image_type)) ? $program->f($image_type) : ($program->f('uploaded_pdf') ? 'pdf_middle.png' : 'noimage_middle.png'),
                'CAT_ID'=>$glob['catID'],
                'CLIENT_ID'=>$glob['client_id'],
                'LAST_CSS'=> $last_css,
                'CLEAR_BOTH'=> $clear_both,
                'USER_BREAK_LINE'=> $user_break_line,
            ));
            $ft->parse(strtoupper($view_mode).'_OUT','.'.$view_mode);
            $i++;
        }
    if ($i==0) 
        {
            //	return '';
            $glob['error'] = $tags['T.NO_EXERCISE'];
        }
    // end the VIEW programs data
}
else 
{
	$glob['error'] = $tags['T.SELECT_CAT'];
    $msg = '<p style="color: white; font-size: 1.5em; margin: 90px 30px;">
                Please select an exercise category, or search for an exercise in the search box above.</p>';
    $ft->assign('NO_DATA_FOUND', $msg);
}

$descr = $dbu->query("
					SELECT *
					FROM exercise_plan
					WHERE  exercise_plan_id=".$glob['exercise_plan_id']."
						AND trainer_id='".$_SESSION[U_ID]."'
						AND client_id= ".$glob['client_id']." 
					");
$descr->next();
$exer_descr = $descr->f('exercise_desc') != '' ? $descr->f('exercise_desc') : 'Notes';

$ft->assign('EXERCISE_DESC', $exer_descr);

if(!$_SESSION['pids'] || empty($_SESSION['pids']))
{
    $_SESSION['pids'] = array();
    $session_register = $dbu->query("
                                    SELECT 
                                        exercise_program_id 
                                    FROM 
                                        exercise_plan
                                    WHERE 
                                        exercise_plan_id=".$glob['exercise_plan_id']."
                                        AND
                                        trainer_id='".$_SESSION[U_ID]."'
                                        AND
                                        client_id= ".$glob['client_id']." 
                                    ");
    $i =0;
    
    while ($session_register->next())
    {
        $sess_edit = explode(",",$session_register->f('exercise_program_id'));
        foreach($sess_edit as $sVal)
            {
                $_SESSION['pids'][] = $sVal;
            }
        $i++;
    }
}
if(!empty($_SESSION['pids']))
{
    $ft->define_dynamic('selected_line','main');
	
	$dbu = new mysql_db();
	
	$left_join = " LEFT JOIN programs_translate_".$_COOKIE['language']." AS programs_loc ON programs_loc.programs_id=programs.programs_id";
	foreach($_SESSION['pids'] as $key=>$val)
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
			//'S_PROGRAM_IMAGE' => $program->f($image_type) ? $script_path.UPLOAD_PATH.$program->f($image_type) : $script_path.UPLOAD_PATH.'noimg64.gif',
			'S_PROGRAM_IMAGE' => (file_exists('upload/'.$program->f($image_type)) && $program->f($image_type)) ? $script_path.UPLOAD_PATH.$program->f($image_type) : ($program->f('uploaded_pdf') ? $script_path.UPLOAD_PATH.'pdf_small.png' : $script_path.UPLOAD_PATH.'noimage_small.png'),
			'S_PROGRAM_CATEGORY' => strip_tags(get_category_path(get_cat_ID($val),0)),
		));
		$ft->parse('SELECTED_LINE_OUT','.selected_line');
	}
}

$ft->assign('IMAGE_TYPE_CHANGE', $change_image_link);
$ft->assign('CSS_PAGE', $glob['pag']);
$ft->assign('SEARCH_LINK', "index.php?pag=client_add_exercise&catID=".$glob['catID']."&client_id=".$glob['client_id']."&exercise_plan_id=".$glob['exercise_plan_id']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>