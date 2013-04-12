<?php
/**
 * @return unknown
 * @param $link unknown
 * @desc Enter description here...
 */
function get_link ($link)
{
	global $rewrite_url;
	if($rewrite_url != 1)
	{
		return $link;
	}
	else 
	{
		$new_link='';
		if((substr($link, 0, 13) != 'index.php?pag') || ($link=='index.php'))
		{
			return $link;
		}
		elseif (strstr($link, "_")) 
		{
			return $link;
		}
		else
		{
			
			$new_link = str_replace('index.php?', '', $link);
			$new_link = str_replace('/','-', $new_link);
			$new_link = str_replace('&','_', $new_link);
			$new_link = str_replace('=','_', $new_link);
			return $new_link;
		}
	}
}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function bulid_simple_dropdown($opt, $selected=0)
{
	if($opt)
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.="<option value=\"".$key."\" ";//options values
		$out_str.=($key==$selected?" SELECTED ":"");//if selected
		$out_str.=">".$val."</option>";//options names
	}
	return $out_str;
}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function page_redirect ($page)
{
echo '
<script language="javascript">
<!-- 

location.replace("'.$page.'");

-->
</script>
';
}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function get_safe_text($text)
{
	if($text)
	{
		$ret_text='';
		
	    $text=str_replace("\n", "<br> ", $text);
		$tmp_text=explode(" ",$text);
	    
	    
	    foreach ($tmp_text as $key => $value)
	    {
	    	$lenght=strlen($tmp_text[$key]);
	    	
	    	if($lenght > 40)
	    	{
	    		$split_number=(int)($lenght/40);
	    		for ($i=1; $i<=$split_number; $i++)
	    		{
	    			$replace_number=($i*40);
	    			$tmp_text[$key][$replace_number]=' ';
	    		}
	    		
	    	}
	    	$ret_text.=$tmp_text[$key]." ";
	    }
	
		return $ret_text;
	}
	else 
	{
	    return ' ';
	}	
}


function get_sys_message($name)
{
$_db=new mysql_db();
$_db->query("select * from sys_message where name='".$name."'");
$_db->move_next();
$msg['text']=$_db->f('text');
$msg['from_email']=$_db->f('from_email');
$msg['from_name']=$_db->f('from_name');
$msg['subject']=$_db->f('subject');
return $msg;

}

/**
 * @return unknown
 * @param $sel_year unknown
 * @desc Enter description here...
 */
function build_year_list($start_year, $end_year, $sel_year=0)
        {
        		global $vars;
                 for ($i=$start_year;$i<=$end_year;$i++)
                         {
                                 $ret.="<Option ";
                                if($i==$sel_year)
                                        {
                                                $ret.="selected";
                                        }
                                $ret.=" value='$i'>$i</option>\n";
                         }
                 return $ret;
        }


/**
 * @return unknown
 * @param $sel_year unknown
 * @desc Enter description here...
 */

function get_error_message($error_msg)
{
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "error_msg.html"));

$ft->assign('ERROR_MSG',$error_msg);
                        

$ft->parse('content','main');
return $ft->fetch('content');

}

/**
 * @return unknown
 * @param $sel_year unknown
 * @desc Enter description here...
 */

function get_pagination_dd($selected_offset, $num_rows, $row_per_page, $glob)
{
	$excluded['ofs']=1;
	$excluded['offset']=1;
	$excluded['act']=1;
	$excluded['o']=1;
	$excluded['error']=1;
	
    if($num_rows>0)
    {
        $pages=ceil($num_rows/$row_per_page);
    }
    else
    {
        $pages=0;
    }
    $args="";
    foreach($glob as $key => $value)
    { 
    	if(!$excluded[$key])
    	{
    		$args.=' <input type="hidden" name="'.$key.'" value="'.$value.'"> ';
    	}
    }
    
    
    if($pages <=1)
    {
        return  "<img src=\"img/spacer.gif\" width=\"35\" height=\"1\">";
    }
    
    $out_str=$args." Jump To <SELECT name=\"ofs\" onChange=\"form.submit();\" class=txtField>";
    $ofs=0;
    for($i=1;$i<=$pages;$i++)
    {
        $out_str.="<option value=$ofs ";
        $out_str.=($ofs == $selected_offset ? " SELECTED " : "");
		$out_str.=">Page ".$i."</option>";//option name
		$ofs+=$row_per_page;
    }
    $out_str.="</SELECT>";
    return $out_str;
}



/**
 * @return unknown
 * @param $sel_day unknown
 * @desc Enter description here...
 */
function build_day_list($sel_day=0)
	{
		 global $vars;
		 for ($i=1;$i<=(31);$i++)
		 	{
		 		$ret.="<Option ";
				if($i==$sel_day)
					{
						$ret.="selected";
					}
				$ret.=" value='$i'>$i</option>\n";
		 	}
		 return $ret;
	}		

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function build_month_list($selected=0)
{

	$opt=array(

            	1	=> 'January',
            	2	=> 'February',
            	3	=> 'March',
            	4	=> 'April',
            	5	=> 'May',
            	6	=> 'June', 
            	7	=> 'July', 
            	8	=> 'August',
            	9	=> 'September',
            	10	=> 'October', 
            	11	=> 'November',
            	12	=> 'December'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.="<option value=\"".$key."\" ";//options values
		$out_str.=($key==$selected?" SELECTED ":"");//if selected
		$out_str.=">".$val."</option>";//options names
	}
	return $out_str;
}
require(ADMIN_PATH."misc/cmslib.php");
/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function get_month($selected)
{

	$opt=array(

            	1	=> 'January',
            	2	=> 'February',
            	3	=> 'March',
            	4	=> 'April',
            	5	=> 'May',
            	6	=> 'June', 
            	7	=> 'July', 
            	8	=> 'August',
            	9	=> 'September',
            	10	=> 'October', 
            	11	=> 'November',
            	12	=> 'December'
  			  );	
  	$out_str=$opt[$selected];		  		
	return $out_str;
}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function build_yesno_list($selected)
{
        $out_str="";
        $opt=array(
                    "No"           => '0',
                    "Yes"           => '1'
                            );
    foreach ($opt as $key=>$val)
        {
                $out_str.="<option value=\"".$val."\" ";//options values
                $out_str.=($val==$selected?" SELECTED ":"");//if selected
                $out_str.=">".$key."</option>";//options names
        }
        return $out_str;
}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function build_numbers_list($start_num, $end_num, $selected)
{
        $out_str="";
    for ($i=$start_num;$i<=$end_num;$i++)
        {
                $out_str.="<option value=\"".$i."\" ";//options values
                $out_str.=($i==$selected?" SELECTED ":"");//if selected
                $out_str.=">".$i."</option>";//options names
        }
        return $out_str;
}
function limitare($str,$limit=200)
{
   $array = preg_split('/ /', $str, -1, PREG_SPLIT_OFFSET_CAPTURE);
   $return = '';
   foreach($array as $k => $val)
     {
   $return .= $val[0]." ";
   if($val[1]>$limit) {$return .= "..."; break;}
     }
return $return;
}



function parseCMSTag($sTag)
{
	$cms_tags = get_cms_tags_from_content($sTag);
	$tag_list = $sTag;
	if($cms_tags)
	{
		foreach ($cms_tags as $key => $cms_tag_params)
		{
			$tag_list=str_replace($cms_tag_params['tag'], get_cms_tag_content($cms_tag_params), $tag_list);
		}
	}
	return $tag_list;
} 
function get_error($message, $success = false, $messageType = ''){
	$out_str = '';
	if(strlen($message) != 0){
		if(!empty($messageType)){
			$out_str = '<div class="'.$messageType.'"><a href="#">Close</a>'.$message.'</div>';
		}else{
			$out_str = '<div class="'.($success ? 'success': 'error').'"><a href="#">Close</a>'.$message.'</div>';
		}
	}
	return $out_str;
} 

function build_optgroup($label, $selected ,$children = array()){
	return '<optgroup label="'.$label.'" rel="group">'.bulid_simple_dropdown($children,$selected).'</optgroup>';
}

/* NEW FOR CATEGORY - SUBCATEGORY LISTING */

function build_category_list($selected, $excluded=0)
{
	global $categ_array;
	$old_category_array=build_categ_list_array($excluded);

	if($old_category_array)
		$category_array=sort_categ_array($old_category_array);
//	var_dump($category_array);
//	die();

	$out_str="";
	if($category_array)
	foreach ($category_array as $key=>$cat_array)
	{
/*		
		if($cat_array['parent']!=0)
		{
	    $out_str.=build_optgroup($cat_array['category_name'],'',$cat_array);
		}
		else
		{
	    $out_str.="<option value=\"".$cat_array['category_id']."\" ";//options values
        $out_str.=($cat_array['category_id']==$selected?" SELECTED ":"");//if selected
        $out_str.=">".str_repeat("&nbsp;&nbsp;",$cat_array['level']).$cat_array['category_name']."</option>";//options names
		}
*/		
	    $out_str.="<option value=\"".$cat_array['category_id']."\" ";//options values
        $out_str.=($cat_array['category_id']==$selected?" SELECTED ":"");//if selected
        $out_str.=">".str_repeat("&nbsp;&nbsp;",$cat_array['category_level']).$cat_array['category_name']."</option>";//options names
	}

	$categ_array=$category_array;
	return $out_str;
}

function build_categ_list_array($excluded=0)
{

    $db=new mysql_db;
    $db->query("
    			SELECT 
    					programs_category.category_name, 
    					programs_category.category_id, 
    					programs_category.active, 
    					programs_category.sort_order, 
    					programs_category_subcategory.parent_id 
    			FROM 
    					programs_category
        		INNER JOIN 
        				programs_category_subcategory 
        					ON 
        						programs_category.category_id = programs_category_subcategory.category_id
        		WHERE 
        				programs_category.category_id!='".$excluded."' 
        			AND	
        				programs_category_subcategory.parent_id!='".$excluded."'
        		ORDER BY 
        				programs_category.sort_order, 
        				programs_category.category_name 
        		");

    $out=array();
    while($db->move_next())
    {
        if($db->f('parent_id') != $db->f('category_id'))
        {
        	$parent=$db->f('parent_id');
        }
        else
        {
        	$parent=0;
        }

        	$out['category_id'][$db->f('category_id')]=$db->f('category_id');
			$out[$db->f('category_id')]['category_id']=$db->f('category_id');
			$out[$db->f('category_id')]['parent']=$parent;
			$out[$db->f('category_id')]['status']=$db->f('active');
			$out[$db->f('category_id')]['sort_order']=$db->f('sort_order');
			$out[$db->f('category_id')]['category_name']=$db->f('category_name');
    }
    return $out;
}

function sort_categ_array($category_array)
{

	$category_level='';
	$level=0;
	$excluded='0';
	$out='';
	$i=0;
	foreach ($category_array['category_id'] as $category_id => $categ_id)
	{
		if($category_array[$category_id]['parent']==0)
		{
			$out[$i]['category_id']=$category_id;
			$out[$i]['category_level']=$level;
			$out[$i]['category_name']=$category_array[$category_id]['category_name'];
			$out[$i]['status']=$category_array[$category_id]['status'];
			$out[$i]['sort_order']=$category_array[$category_id]['sort_order'];
			$out[$i]['parent']=$category_array[$category_id]['parent'];
			$out[$i]['detail']=$category_array[$category_id]['detail'];
			$i++;

			if(subcateg_exist($category_array, $category_id, $excluded))
			{
				$subcateg_exist=true;
			}
			else
			{
				$subcateg_exist=false;
			}

			$category_level[$level]=$level;
			$parent_id=$category_id;

			$excluded.=",".$parent_id;

			while($subcateg_exist)
			{
				$result=get_subcateg_array($category_array, $parent_id, $excluded);
	            if($result)
	            {
	            	$category_level[$level]=$parent_id;
	            	$level++;

					$out[$i]['category_id']=$result['out_id'];
					$out[$i]['category_level']=$level;
					$out[$i]['category_name']=$category_array[$result['out_id']]['category_name'];
					$out[$i]['status']=$category_array[$result['out_id']]['status'];
					$out[$i]['sort_order']=$category_array[$result['out_id']]['sort_order'];
					$out[$i]['parent']=$category_array[$result['out_id']]['parent'];
					$out[$i]['detail']=$category_array[$result['out_id']]['detail'];
					$i++;

	            	$parent_id=$result['next_parent'];
		           	$excluded.=",".$result['next_parent'];
	            }
		        else
		        {
		           	$level--;
		           	$parent_id=$category_level[$level];
		        }
		        if($level < 0)
		        {
		        	$subcateg_exist=false;
		           	$level=0;
		        }
			}

		}
	}

    return $out;
}


function get_subcateg_array($category_array, $parent_id, $excluded=0)
{
	$out_id="";
	$next_parent="";
	$prev_parent="";
	$exclude_keys = split(",",$excluded);
	foreach ($exclude_keys as $key=>$value)
	{
		$exclude[$value]=1;
	}
	foreach ($category_array['category_id'] as $c_key => $c_id)
	{
        if(!$exclude[$c_key] && ($category_array[$c_id]['parent'] == $parent_id))
        {
        	$return['out_id']=$c_id;
			$return['next_parent']=$c_id;
			$return['prev_parent']=$parent_id;
			return $return;
        }
	}

	return false;
}


function subcateg_exist($category_array, $parent_id, $excluded=0)
{
	$exclude_keys = split(",",$excluded);
	foreach ($exclude_keys as $key=>$value)
	{
		$exclude[$value]=1;
	}

	foreach ($category_array['category_id'] as $c_key => $c_id)
	{
        if(!$exclude[$c_key] && ($category_array[$c_id]['parent'] == $parent_id))
        {

			return true;
        }
	}

	return false;
}

/*
With this function we get an array of the
selected category subcategories on all levels
*/
function get_subcategory_array($main_category_id)
{
	global $categ_array;

	$m_cat_level=-2;
	$out=array();
	$new_key=0;
	// if the global $categ_array is not yet initiated, we initiate it
	if(!$categ_array)
	{
		build_active_category_list_blank();
	}
	if($categ_array)
	foreach ($categ_array as $key=>$cat_array)
	{
		if($cat_array['category_id']==$main_category_id) // we have found what we are looking for
	    {
	    	$m_cat_level=$cat_array['category_level'];
	    	$tmp=array();
	    	$tmp[$new_key]=$cat_array;
	    	$out=$out+$tmp;
	    	unset($tmp);
	    	$new_key++;
	    }

	    if($m_cat_level != -2)
	    {
	    	if($m_cat_level < $cat_array['category_level'])
	    	{
		    	$tmp=array();
		    	$tmp[$new_key]=$cat_array;
		    	$out=$out+$tmp;
		    	unset($tmp);
		    	$new_key++;
	    	}
	    	elseif ($cat_array['category_id'] != $main_category_id)
	    	{
	    		$m_cat_level=-2;
	    	}
	    }
	}

    return $out;
}

//**Used on front end - build the array of active categories ****
function build_active_category_list_blank() //use this to generate the global categ_array
{
	global $categ_array;

	$old_category_array=build_active_categ_list_array($excluded);

	if($old_category_array)
		$category_array=sort_categ_array($old_category_array);

	$categ_array=$category_array;
	return true;
}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function get_category_path($catID, $client_id)
{
	if(!$catID)
		return '';
	
    $db=new mysql_db;
    $i=0;
    $path_array='';
//    $return='<a href="index.php" class="LinkStyle">Home</a> &gt;&nbsp;';
    $subcat_exist=true;

	while($subcat_exist)
	{
		 $db->query("
    			SELECT 
    					programs_category.category_name, 
    					programs_category.category_id, 
    					programs_category_subcategory.parent_id 
    			FROM 
    					programs_category
        		INNER JOIN 
        				programs_category_subcategory 
        					ON 
        						programs_category.category_id = programs_category_subcategory.category_id
        		WHERE 
        				programs_category.category_id=".$catID."");
		 $db->move_next();

		 $path_array[$i]['name']=$db->f('category_name');
		 $path_array[$i]['url']=get_link("index.php?pag=client_add_exercise&catID=".$db->f('category_id')."&client_id=".$client_id);

		 $catID=$db->f('parent_id');
		 if($db->f('parent_id') == $db->f('category_id'))
		 {
		 	$subcat_exist=false;
		 }
		 else
		 {
		 	$i++;
		 }
	}

	for ($j=$i; $j>=0; $j--)
	{
		if ($j)
		{
//			$return.='<a href="'.$path_array[$j]['url'].'" class="LinkStyle">'.$path_array[$j]['name'].'</a>&nbsp;';
			$return.='<span class="current_item">'.$path_array[$j]['name'].'</span>&nbsp;';
		}
		else
		{
			$return.='<span class="current_item">'.$path_array[$j]['name'].'</span>';
		}

		if($j != 0 )
		{
			$return.='&gt; ';
		}
	}

	return $return;
}

function get_cat_ID($pID)
{
    $db=new mysql_db;
$catID = $db->field("
    			SELECT 
    					category_id
    			FROM 
    					programs_in_category
        		WHERE 
        				programs_id=".$pID."
        		AND
        				main='1'
        			");

	return $catID;
}

function get_admin_category_path($catID)
{
    $db=new mysql_db;
    $i=0;
    $path_array='';
//    $return='<a href="index.php" class="LinkStyle">Home</a> &gt;&nbsp;';
    $subcat_exist=true;

	while($subcat_exist)
	{
		 $db->query("
    			SELECT 
    					programs_category.category_name, 
    					programs_category.category_id, 
    					programs_category_subcategory.parent_id 
    			FROM 
    					programs_category
        		INNER JOIN 
        				programs_category_subcategory 
        					ON 
        						programs_category.category_id = programs_category_subcategory.category_id
        		WHERE 
        				programs_category.category_id=".$catID."");
		 $db->move_next();

		 $path_array[$i]['name']=$db->f('category_name');

		 $catID=$db->f('parent_id');
		 if($db->f('parent_id') == $db->f('category_id'))
		 {
		 	$subcat_exist=false;
		 }
		 else
		 {
		 	$i++;
		 }
	}

	for ($j=$i; $j>=0; $j--)
	{
		if ($j)
		{
//			$return.='<a href="'.$path_array[$j]['url'].'" class="LinkStyle">'.$path_array[$j]['name'].'</a>&nbsp;';
			$return.='<strong>'.$path_array[$j]['name'].'</strong>&nbsp;';
		}
		else
		{
			$return.=$path_array[$j]['name'];
		}

		if($j != 0 )
		{
			$return.='- ';
		}
	}

	return $return;
}

/* Used for Dashboard */
function count_exercise($trainer_id=0,$client_id=0)
{

    $db=new mysql_db;
	$db->query("select count(exercise_plan_id) as cnt from exercise_plan where trainer_id=".$trainer_id." AND client_id=".$client_id." ");

    $out="";
    if($db->move_next()) $out=$db->f('cnt');
    return $out;
}

//Build Country DD
function build_country_list($selected ='')
{
	$db=new mysql_db;
	if(!$selected)
    {
    	$selected = MAIN_COUNTRY;
    }
	$db->query("select * from country order by is_main DESC, name");
	$out_str="";
	while($db->move_next()){
	        $out_str.="<option value=\"".$db->f('country_id')."\" ";//options values
	        $out_str.=($db->f('country_id')==$selected?" SELECTED ":"");//if selected
	        $out_str.=">".$db->f('name')."</option>";//options names
	}
	return $out_str;
}

function get_country_code($country_id)
{
        $db=new mysql_db;
        $db->query("SELECT country.* FROM country WHERE country_id='".$country_id."'");
        $db->move_next();
        $name=$db->f('code');
        return $name;
}

function build_print_image_type_list($selected)
{
	$image_type = array(
						'0' => 'LineArt',
						'1' => 'Photo'
						);
	$out_str="";
	foreach ($image_type as $key=>$val)
	{
	    $out_str.="<option value=\"".$key."\" ";//options values
        $out_str.=($key==$selected?" SELECTED ":"");//if selected
        $out_str.=">".$val."</option>";
	}

	return $out_str;
}

function build_header_paper_button($has_access, $curr_lang = 'en'){
    $tags = get_template_tag('header_paper', $curr_lang);
    $out_str="";
    $out_str.="<h2>".(isset($tags['T.PERSONALISE']) ? $tags['T.PERSONALISE'] : 'Personalise your Headed Paper')."</h2>";
    if($has_access==true)
    {
        $out_str.= "<a class=\"blueGlassBtn moreBtn\" href=\"index.php?pag=profile_header_paper\"><span class=\"curvyCorner\">".(isset($tags['T.EDIT']) ? $tags['T.EDIT'] : 'Edit Headed Paper')."</span></a>";
    }
    else if($has_access==false)
    {
        $error_msg = isset($tags['T.PAID']) ? $tags['T.PAID'] : 'Only paid accounts have access to this section!';
        $out_str.= "<a class=\"blueGlassBtn moreBtn\" href=\"javascript: void(0);\" onclick=\"alert('".$error_msg."');\"><span class=\"curvyCorner\">".(isset($tags['T.EDIT']) ? $tags['T.EDIT'] : 'Edit Headed Paper')."</span></a>";
    }
	return $out_str;
}
	
function check_ip ($ip, $user){
	$db=new mysql_db;
    $db->query("SELECT ip FROM trainer WHERE trainer_id='".$user."'");
    $db->move_next();
    if($db->f('ip') != $ip ){	
    	session_register(UID);
		$_SESSION[UID]=0;
		$_SESSION[U_ID]=0;
		$_SESSION[ACCESS_LEVEL]=4;
	/*	if (isset($_COOKIE[session_name()])){
			    $params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"],$params["secure"], $params["httponly"]);
			}*/
		session_destroy();
    }
    return true;
}

?>