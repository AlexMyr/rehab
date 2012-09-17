<?php

function grab_categories($filename)
{
    if(file_exists($filename))
    {
        //empty table
        //my_mysql_query("TRUNCATE TABLE categs_test");
        //my_mysql_query("TRUNCATE TABLE programs_category");
        //my_mysql_query("TRUNCATE TABLE programs_category_subcategory");
        //my_mysql_query("TRUNCATE TABLE ".PROGRAMS_TABLE." ");
        //my_mysql_query("TRUNCATE TABLE ".CATEGORIES_TABLE." "); 
        $categories = array();
        $pattern_block = "~(.+?)(\t+\r\n|\r\n$)~s";
        $file = file_get_contents($filename);
        preg_match_all($pattern_block, $file, $tmp_res);

        foreach($tmp_res[1] as $categ_block)
        {
            $top_categ = preg_match("~([^\t]+?)\t~", $categ_block, $cur_top) ? $cur_top[1] : '';
            $categories[] = array('cat_name'=>trim($top_categ), 'cat_type'=>'top', 'top_cat'=>trim($top_categ));
            $categ_block = str_replace($top_categ, '', $categ_block);
            $sub_cats = preg_split("/\r\n/", trim($categ_block));
            foreach($sub_cats as $cat)
            {
                $categories[] = array('cat_name'=>trim($cat), 'cat_type'=>'sub', 'top_cat'=>trim($top_categ));
            }
        }

        $top_iterator = 0;
        $sub_iterator = array();
        
        foreach($categories as $cat)
        {
            my_mysql_query("INSERT INTO categs_test SET cat_name = '".$cat['cat_name']."', cat_type = '".$cat['cat_type']."', top_cat='".$cat['top_cat']."'");
            //get order level
			
			if($cat['cat_type'] == 'top')
			{
				$query_str = "INSERT INTO `programs_category` SET `category_name` = '".$cat['cat_name']."', category_level=0, active=1, sort_order=$top_iterator";
				my_mysql_query($query_str);
				$new_id = mysql_insert_id();
				my_mysql_query("INSERT INTO programs_category_subcategory SET parent_id=$new_id, category_id=$new_id");
				$top_iterator++;
			}
			else
			{
				if(!isset($sub_iterator[$cat['top_cat']]))
					$sub_iterator[$cat['top_cat']] = 0;
				
				$query_str = "INSERT INTO `programs_category` SET `category_name` = '".$cat['cat_name']."', category_level=1, active=1, sort_order=".$sub_iterator[$cat['top_cat']];
				my_mysql_query($query_str);
				$new_id = mysql_insert_id();
				//get parent id
				$query_str = "SELECT category_id FROM programs_category WHERE `category_name` = '".$cat['top_cat']."' AND category_level=0";
				$parent_id = mysql_result(my_mysql_query($query_str), 0);
				my_mysql_query("INSERT INTO programs_category_subcategory SET parent_id=$parent_id, category_id=$new_id");
				$sub_iterator[$cat['top_cat']]++;
			}
        }
    }
    
}

function get_category($cat_sub_str, $cat_top)
{

    $res_categ_array = array();
    $cat_sub_array = explode(",", $cat_sub_str);

    $sub_categs_for_top = array();
    //get sub cats for top
    $query_str = "SELECT cat_name FROM categs_test WHERE  LOWER(`top_cat`) = '".strtolower(trim($cat_top))."'";
    $top_query = my_mysql_query($query_str);
    while($row = mysql_fetch_assoc($top_query))
    {
        $sub_categs_for_top[] = $row['cat_name'];
    }

    //get list of top categs
    $query_str = "SELECT cat_name FROM categs_test WHERE  cat_type = 'top'";
    $top_query = my_mysql_query($query_str);
    while($row = mysql_fetch_assoc($top_query))
    {
        $top_categs_list[] = $row['cat_name'];
    }

    foreach($cat_sub_array as $sub_cat){

        if(trim(strtolower($sub_cat)) == 'all' || !preg_match('/\ball\b/i', $sub_cat))
        {
            //check is top cat
            foreach($top_categs_list as $top_categ)
            {
                $str1 = strtolower($top_categ);
                $str2 = trim(strtolower($sub_cat));
				$has_spaces = preg_match('~\s~', $str2, $tmp_array) ? true : false;
				
                similar_text($str1, $str2, $similarity);
                if($similarity>90)
                {
                    $res_categ_array[] = get_cat_array($top_categ, 'All');
                    break;
                }
                elseif(stripos($str2, $str1) !== false && $has_spaces)
                {
                    $cur_sub_cat = trim(str_ireplace($str1, '', $str2));
                    $res_categ_array[] = get_cat_array($top_categ, $cur_sub_cat);
                    break;
                }
            }

            //sub cat of current top cat, get sub cat
            foreach($sub_categs_for_top as $sub_categ_for_top)
            {
                $str1 = strtolower($sub_categ_for_top);
                $str2 = trim(strtolower($sub_cat));

                similar_text($str1, $str2, $similarity);
                if($similarity>90)
                {
                    $res_categ_array[] = get_cat_array($cat_top, $sub_categ_for_top);
                    break;
                }
            }

        }
        else
        {
            $sub_cat = preg_replace("/\ball\b/i", '', $sub_cat);
            foreach($top_categs_list as $top_categ)
            {
                $str1 = strtolower($top_categ);
                $str2 = trim(strtolower($sub_cat));
                similar_text($str1, $str2, $similarity);
                if($similarity>90)
                {
                    $res_categ_array[] = get_cat_array($top_categ, 'All');
                }
            }
            
            //sub cat of current top cat, get sub cat
            foreach($sub_categs_for_top as $sub_categ_for_top)
            {
                $str1 = strtolower($sub_categ_for_top);
                $str2 = trim(strtolower($sub_cat));
            
                similar_text($str1, $str2, $similarity);
                if($similarity>90)
                {
                    $res_categ_array[] = get_cat_array($cat_top, $sub_categ_for_top);
                }
            }
        }
    }
    //leave only unique pairs
    $unique = array(array_shift($res_categ_array));
    foreach($res_categ_array as $cat){
        $clone = false;
        foreach($unique as $one){
            if($one['top_cat_id'] == $cat['top_cat_id'] && $one['sub_cat_id'] == $cat['sub_cat_id'])
                $clone = true;
        }
        if(!$clone)
            $unique[] = $cat;
    }
    return $unique;
}

function get_cat_array($top_cat, $sub_cat)
{
    //select top cat id
    $query_str = "SELECT `category_id` FROM `programs_category` WHERE LOWER(`category_name`) = '".strtolower($top_cat)."' AND category_level = 0 LIMIT 0,1";
    $top_cat_id = mysql_result(my_mysql_query($query_str),0);
    $query_str = "SELECT pc.category_id FROM programs_category as pc, programs_category_subcategory as pcs WHERE pc.category_id = pcs.category_id AND pcs.parent_id = $top_cat_id AND pcs.category_id IN (SELECT category_id  FROM programs_category WHERE LOWER(`category_name`) = '".strtolower($sub_cat)."')";
    $sub_cat_id = mysql_result(my_mysql_query($query_str), 0);
    return array('top_cat_id'=>$top_cat_id, 'sub_cat_id'=>$sub_cat_id);
}

function new_grab_programs($filename)
{

    if(file_exists($filename))
    {
        //$pattern_block = "~([\w\s\\/]+)\t+[\r\n](.*?)\t{6,}~s";
        $pattern_block = "~([\w\s\\/]+)\t+[\r\n](.*?)\t{3,}~s";
        $pattern_string = "~[^\r\n]+~i";
        $pattern_elements = "~^([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)~";
        //$pattern_elements = "~^([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)~";
        
        
        $file = file_get_contents($filename);
        preg_match_all($pattern_block, $file, $tmp_res);

        $results = array();
        foreach($tmp_res[1] as $id=>$cat)
        {
            $programs = array();
            preg_match_all($pattern_string, $tmp_res[2][$id], $temp_res2);
            foreach($temp_res2[0] as $string)
            {
                preg_match($pattern_elements, trim($string), $tmp_res3);
                $programs[] = array(
                    'programs_code'=>$tmp_res3[1],
                    'programs_title'=>$tmp_res3[3],
                    'description'=>$tmp_res3[4],
                    'lineart'=>$tmp_res3[1].'L',
                    'thumb_lineart'=>$tmp_res3[1].'L (small)',
                    'image'=>$tmp_res3[1].'P',
                    'thumb_image'=>$tmp_res3[1].'P (small)',
                    'category'=>$tmp_res3[2]
                );
            }
            $results[trim($cat)] = $programs;
        }
  
        return $results;
    }
    else return array();
}

function grab_programs($filename)
{
    if(file_exists($filename))
    {
        $pattern_string = "~[^\r\n]+~i";
        //$pattern_elements = "~^([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t~";
        $pattern_elements = "~^([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)?\t([^\t]+)~";
        $file = file_get_contents($filename);
        $strings = preg_match_all($pattern_string, $file, $tmp_res) ? $tmp_res[0] : array();
        $results = array();
        
        foreach($strings as $string)
        {
            $tmp_result = preg_match($pattern_elements, $string, $tmp_res) ? $tmp_res : array();
            $results[] = array(
                'programs_code'=>$tmp_result[1],
                'programs_title'=>$tmp_result[7],
                'description'=>$tmp_result[9],
                'lineart'=>$tmp_result[1].'L',
                'thumb_lineart'=>$tmp_result[1].'L (small)',
                'image'=>$tmp_result[1].'P',
                'thumb_image'=>$tmp_result[1].'P (small)',
                'category'=>$tmp_result[4]
            );
        }
        return $results;
    }
    else return array();
    
}

function save_pictures($fileProgramsName)
{
    $programs = new_grab_programs($fileProgramsName);
    if(!empty($programs))
    {
        $top_categories = array_unique(array_merge_recursive(array_keys($programs), get_top_categories_from_db()));
        
        foreach($programs as $top_cat_name => $res_array)
        {

            foreach($res_array as $program_info_array)
            {
                foreach($program_info_array as $program_info)
                {
                    if(file_exists(DIRNAME."/$program_info.jpg") || file_exists(DIRNAME."/$program_info.JPG"))
                    {
                    	if(file_exists(DIRNAME."/$program_info.jpg"))
                    	{
                    		copy(DIRNAME."/$program_info.jpg", UPLOADS_DIR."/$program_info.jpg");
                        	echo '<span style="color:green;">File '.$program_info.'.jpg copied.</span><br/>';
                    	}
                    	elseif(file_exists(DIRNAME."/$program_info.JPG"))
                    	{
                    		copy(DIRNAME."/$program_info.JPG", UPLOADS_DIR."/$program_info.jpg");
                        	echo '<span style="color:green;">File '.$program_info.'.JPG copied.</span><br/>';
                        }
                    }
                    elseif(file_exists(DIRNAME.'/'.trim(str_ireplace('(small)', '', $program_info)).'.jpg') || file_exists(DIRNAME.'/'.trim(str_ireplace('(small)', '', $program_info)).'.JPG'))
                    {
                    	if(file_exists(DIRNAME.'/'.trim(str_ireplace('(small)', '', $program_info)).'.jpg'))
                    	{
                    		resize_img(trim(str_ireplace('(small)', '', $program_info)).'.jpg', "$program_info.jpg", 150, 150);
                        	echo '<span style="color:green;">File '.$program_info.'.jpg created.</span><br/>';
                    	}
                    	elseif(file_exists(DIRNAME.'/'.trim(str_ireplace('(small)', '', $program_info)).'.JPG'))
                    	{
                    		resize_img(trim(str_ireplace('(small)', '', $program_info)).'.JPG', "$program_info.jpg", 150, 150);
                        	echo '<span style="color:green;">File '.$program_info.'.jpg created.</span><br/>';
                    	}
                        
                    }
                }
                
            }

            save_program_to_db($top_cat_name, $res_array, $top_categories, 'en');
        }
    }
    else
        echo '<span style="color:red;">0 program exercise grabbed</span><br/>';
}

function resize_img($img_name, $filename, $new_width, $new_height, $quality = 100)
{
    $image_info = getimagesize(DIRNAME."/$img_name");
    $image_type = $image_info[2];
    $image = imagecreatefromjpeg(DIRNAME."/$img_name");
    $width = imagesx($image);
    $height = imagesy($image);
    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($new_image, UPLOADS_DIR."/$filename", $quality);
}


function connect_to_db()
{
    $connection = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD, true) or die(mysql_errno()." : ".mysql_error().' line '.__LINE__);
    mysql_select_db(DB_NAME, $connection) or die(mysql_errno()." : ".mysql_error().' line '.__LINE__);
    return $connection;
}

function my_mysql_query($sql)
{
    $result = mysql_query($sql);

    if(2006 == mysql_errno())
    {
        $connection = connect_to_db();
        $result = mysql_query($sql, $connection) or die(mysql_errno().':'.mysql_error().' line '.__LINE__);
    }
    elseif(mysql_errno())
    {
        die(mysql_errno()." : ".mysql_error().'<br /><em>'. nl2br(htmlentities($sql)) .'</em>');
    }
    
    return $result;
}


function save_program_to_db($top_cat_name, $program_arrays, $top_categories, $lang = 'us')
{

    $top_cat_id = get_top_cat_id(trim($top_cat_name));
    foreach($program_arrays as $p_array)
    {
        //not changed
		$programs_id = 0;
		$programs_id = @mysql_result(my_mysql_query("SELECT programs_id FROM programs WHERE programs_code = '".$p_array['programs_code']."'"), 0);

		if($programs_id)
		{
			if(@mysql_result(my_mysql_query("SELECT programs_id FROM programs_translate_".$lang." WHERE programs_id = $programs_id "), 0))
			{
				my_mysql_query("
						UPDATE 
									programs_translate_".$lang." 
						SET 
									programs_title='".addslashes($p_array['programs_title'])."', 
									description='".addslashes($p_array['description'])."'
						WHERE
									programs_id = $programs_id
				");
			}
			else
			{
				my_mysql_query("
						INSERT INTO 
									programs_translate_".$lang." 
						SET 
									programs_title='".addslashes($p_array['programs_title'])."', 
									description='".addslashes($p_array['description'])."',
									programs_id = $programs_id
				");
			}
			
		}
		else
		{
			if($lang == 'en')
			{
				my_mysql_query("
						INSERT INTO 
									".PROGRAMS_TABLE." 
						SET 
									programs_code='".$p_array['programs_code']."', 
									lineart='".$p_array['lineart'].".jpg',
									thumb_lineart='".$p_array['thumb_lineart'].".jpg',
									image='".$p_array['image'].".jpg',
									thumb_image='".$p_array['thumb_image'].".jpg',
									sort_order='0', 
									active = '1' 
				");
				$programs_id = mysql_insert_id();
				
				my_mysql_query("
						INSERT INTO 
									programs_translate_en 
						SET 
									programs_id = $programs_id,
									programs_title='".addslashes($p_array['programs_title'])."', 
									description='".addslashes($p_array['description'])."'
									
				");
				
				
			}
			else
			{
				//get program id for us
				$programs_id = @mysql_result(my_mysql_query("SELECT programs_id FROM programs WHERE programs_code = '".$p_array['programs_code']."'"), 0);
				my_mysql_query("
						INSERT INTO 
									programs_translate_us 
						SET 
									programs_id = $programs_id,
									programs_title='".addslashes($p_array['programs_title'])."', 
									description='".addslashes($p_array['description'])."'
									
				");
			}
		}
		
		$cat_array = get_category($p_array['category'], $top_cat_name);
		
		foreach($cat_array as $cur_cat)
		{
			my_mysql_query("
						INSERT INTO
							".CATEGORIES_TABLE."
						SET
							programs_id='".$programs_id."',
							category_id='".$cur_cat['sub_cat_id']."',
							main='1'
						");
		}
		
		echo '<span style="color:green;">Program '.$p_array['programs_code'].' added</span><br/><br/>';
    }
    
}

function get_top_cat_id($top_cat_name)
{
    $query_str = "SELECT `category_id` FROM `programs_category` WHERE LOWER(`category_name`) = '".strtolower($top_cat_name)."' LIMIT 0,1";
    $cat_id = @mysql_fetch_array(my_mysql_query($query_str));
    if(!$cat_id)
    {
        //create top category
        my_mysql_query("INSERT INTO programs_category SET category_name = '".ucfirst(strtolower($top_cat_name))."', category_level=0, active=1, sort_order=1");
        $cat_id = mysql_insert_id();
    }
    else
    {
        $cat_id = $cat_id[0];
    }
    return $cat_id;
}

function is_top_cat($cat_name)
{
    $cat_id = @mysql_fetch_array(my_mysql_query("SELECT category_id FROM programs_category WHERE category_level=0 AND LOWER(`category_name`) = '".strtolower($cat_name)."' LIMIT 0,1"));
    if(!$cat_id)
    {
        //create top category
        my_mysql_query("INSERT INTO programs_category SET category_name = '".ucfirst(strtolower($cat_name))."', category_level=0, active=1, sort_order=1");
        $cat_id = mysql_insert_id();
    }
    else
    {
        $cat_id = $cat_id[0];
    }
    return $cat_id;
}

function create_all_cat($cat_id)
{
    $is_exist = @mysql_result(my_mysql_query("SELECT `category_id` FROM `programs_category` WHERE `category_name` = 'All' AND category_id IN (SELECT category_id FROM programs_category_subcategory WHERE parent_id = $cat_id) LIMIT 0,1"),0);

    if(!$is_exist)
    {
        my_mysql_query("INSERT INTO programs_category SET category_name = 'All', category_level=1,	active=1, sort_order=1");
        $new_id = mysql_insert_id();
        my_mysql_query("INSERT INTO programs_category_subcategory SET parent_id=$cat_id, category_id=$new_id");
        return $new_id;
    }
    return $is_exist;
}

function get_subcat_id($top_cat_id, $subcat_name)
{
    $query_str = "SELECT pc.category_id FROM programs_category as pc, programs_category_subcategory as pcs WHERE pc.category_id = pcs.category_id AND pcs.parent_id = $top_cat_id AND pcs.category_id IN (SELECT category_id  FROM programs_category WHERE LOWER(`category_name`) = '".strtolower($subcat_name)."')";
    $cat_id = mysql_fetch_array(my_mysql_query($query_str));
    return $cat_id[0];
}

function get_cat_count($cat_name)
{
    $query_str = "SELECT COUNT(`category_id`) FROM `programs_category` WHERE LOWER(`category_name`) = '".strtolower($cat_name)."'";
    $query = my_mysql_query($query_str) or die(mysql_errno().' : '.mysql_error() . ' echo');
    $cat_count = mysql_result($query,0);
    return $cat_count;
}

function get_cat_id($cat_name)
{
    $query_str = "SELECT `category_id` FROM `programs_category` WHERE LOWER(`category_name`) = '".strtolower($cat_name)."' LIMIT 0,1";
    $cat_id = mysql_result(my_mysql_query($query_str),0);
    return $cat_id;
}

function create_subcat($cat_name, $top_cat_id)
{
    //get order level
    $cat_order = mysql_result(my_mysql_query("SELECT MAX(sort_order) FROM programs_category WHERE category_id=$top_cat_id"), 0);
    $query_str = "INSERT INTO `programs_category` SET `category_name` = '".ucfirst(strtolower($cat_name))."', category_level=1, active=1, sort_order=$cat_order";
    $new_id = mysql_insert_id();
    //check existing link
    $link_count = mysql_result(my_mysql_query("SELECT COUNT(*) FROM programs_category_subcategory WHERE parent_id=$top_cat_id AND category_id=$new_id"), 0);
    if(!$link_count && $new_id)
        my_mysql_query("INSERT INTO programs_category_subcategory SET parent_id=$top_cat_id, category_id=$new_id");
    
    return $new_id;
}

function get_top_categories_from_db()
{
    $cats = array();
    $query_str = "SELECT DISTINCT `category_name` FROM `programs_category` WHERE category_level = 0";
    $query = my_mysql_query($query_str);
    while($cat = mysql_fetch_array($query))
    {
        $cats[] = $cat[0];
    }
    
    return $cats;
}

function is_contain_top_cat($cat_name, $top_categories)
{
    foreach($top_categories as $tc)
    {
        if(stripos($cat_name, $tc))
            return array('top_category'=>$tc, 'subcategory' => trim(str_ireplace($tc, '', $cat_name)));
    }
    return false;
}
?>