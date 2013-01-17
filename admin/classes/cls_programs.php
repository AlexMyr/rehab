<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class programs
{
  var $dbu;

	function programs()
	{
		$this->dbu = new mysql_db();
	}

	/****************************************************************
	* function add(&$ld)                                            *
	****************************************************************/
	
	function add_mult(&$ld)
	{
	  if(isset($ld['count_exercise']) && $ld['count_exercise'])
	  {
		$count_exercises = $ld['count_exercise'];
		$not_added_programs = array();
		
		for($i=0;$i<$count_exercises;$i++)
		{
		  $programs_code = mysql_real_escape_string($ld['programs_code'][$i]);
		  $programs_title = mysql_real_escape_string($ld['programs_title'][$i]);
		  $description = mysql_real_escape_string($ld['description'][$i]);
		  $programs_title_us = mysql_real_escape_string($ld['programs_title_us'][$i]);
		  $description_us = mysql_real_escape_string($ld['description_us'][$i]);
		  $category_id = mysql_real_escape_string($ld['category_id'][$i]);

		  //check program code
		  if($this->dbu->field("select count(*) from programs where programs_code='$programs_code'"))
		  {
			$not_added_programs[] = $programs_code;
			continue;
		  }
		  
		  $ld['programs_id'][$i] = $programs_id = $this->dbu->query_get_id("
													  INSERT INTO 
																  programs 
													  SET 
																  programs_code='".$programs_code."', 
																  sort_order='0', 
																  active = '1' 
													  ");
		  $this->dbu->query("insert into programs_translate_en set programs_id=$programs_id, programs_title='$programs_title', description='$description'");
		  if($programs_title_us)
			$this->dbu->query("insert into programs_translate_us set programs_id=$programs_id, programs_title='$programs_title_us', description='$description_us'");
		  else
			$this->dbu->query("insert into programs_translate_us set programs_id=$programs_id, programs_title='$programs_title', description='$description'");
			
		  $this->dbu->query("update programs set sort_order=".($programs_id*10)." where programs_id=$programs_id");
		  
		  $this->dbu->query("insert into programs_in_category (
													  programs_id,
													  category_id,
													  main )
													  values (
													  '".$programs_id."',
													  '".$category_id."',
													  '1'
													  )
							  ");
		  $this->upload_file_mult($ld, $i);
		}
		if(!empty($not_added_programs))
		{
		  $ld['error']="Not added programs: ".implode($not_added_programs);
		  return true;
		}
	  }
	  $ld['error']="Program Succesfully added.";
	  return true;
	}
	
	function upload_file_mult(&$ld, $position)
	{
	  //var_dump($_FILES, $ld);exit;
	  global $_FILES, $script_path;
	  $allowed['.jpg']=1;
	  $allowed['.jpeg']=1;
	  $allowed['.png']=1;
	  
	  if(!is_numeric($_SESSION[U_ID]))
	  {
		$ld['error'].="Error.".'<br>';
		return false;
	  }
	//  else 
	//  {
	//	$this->dbu->query("select programs_id, lineart, thumb_lineart, image, thumb_image from programs where programs_id='".$ld['programs_id'][$position]."'");
	//	if(!$this->dbu->move_next())
	//	{
	//	  $ld['error'].="Error.".'<br>';
	//	  return false;
	//	}
	//	else 
	//	{
	//	  if(!empty($_FILES['lineart']['tmp_name'][$position]))
	//	  {
	//		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('lineart') );
	//		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_lineart') );
	//		$this->dbu->query("UPDATE programs SET lineart=NULL, thumb_lineart=NULL where programs_id='".$ld['programs_id'][$position]."'");
	//	  }
	//	  elseif(!empty($_FILES['image']['tmp_name'][$position]))
	//	  {
	//		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('image') );
	//		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_image') );
	//		$this->dbu->query("UPDATE programs SET image=NULL, thumb_image=NULL where programs_id='".$ld['programs_id'][$position]."'");
	//	  }
	//	}
	//  }
	//
	  $has_lineart = false;
	  $has_image = false;
	  
	  if(!empty($_FILES['lineart']['tmp_name'][$position]))
		$has_lineart = true;
	  if(!empty($_FILES['image']['tmp_name'][$position])) 
		$has_image = true;
	  
	  if($has_lineart) 
		$f_lineart_ext=substr($_FILES['lineart']['name'][$position],strrpos($_FILES['lineart']['name'][$position],"."));
	  else
		$f_lineart_ext='.jpg';
		
	  if($has_image)
		$f_image_ext=substr($_FILES['image']['name'][$position],strrpos($_FILES['image']['name'][$position],"."));
	  else
		$f_image_ext='.jpg';
	  
	  $f_lineart_title=$ld['programs_code'][$position].'L'.$f_lineart_ext;
	  $t_lineart_title=$ld['programs_code'][$position].'L (small)'.$f_lineart_ext;
	  $f_lineart_out=$script_path.UPLOAD_PATH.$f_lineart_title;
	  $t_lineart_out=$script_path.UPLOAD_PATH.$t_lineart_title;
	  $this->dbu->query("update programs set
						   lineart='".$f_lineart_title."',
						   thumb_lineart='".$t_lineart_title."'
						   where programs_id='".$ld['programs_id'][$position]."'" 
						  );
	  
	  $f_image_title=$ld['programs_code'][$position].'P'.$f_image_ext;
	  $t_image_title=$ld['programs_code'][$position].'P (small)'.$f_image_ext;
	  $f_image_out=$script_path.UPLOAD_PATH.$f_image_title;
	  $t_image_out=$script_path.UPLOAD_PATH.$t_image_title;
	  $this->dbu->query("update programs set
						   image='".$f_image_title."',
						   thumb_image='".$t_image_title."'
						   where programs_id='".$ld['programs_id'][$position]."'" 
						  );
	  
	  $owner = $this->dbu->field("select owner from programs where programs_id='".$ld['programs_id'][$position]."'");
	
	  if($has_lineart)
	  {
		$this->resize($_FILES['lineart']['tmp_name'][$position], 500, 0, $f_lineart_title);
		$this->resize($_FILES['lineart']['tmp_name'][$position], 150, 0, $t_lineart_title);
		@chmod($f_lineart_out, 0777);
		@chmod($t_lineart_out, 0777);
		
		if(!$has_image)
		{
		  $this->resize($_FILES['lineart']['tmp_name'][$position], 500, 0, $f_image_title);
		  $this->resize($_FILES['lineart']['tmp_name'][$position], 150, 0, $t_image_title);
		  @chmod($f_lineart_out, 0777);
		  @chmod($t_lineart_out, 0777);
		}
	  }
	  if($has_image)
	  {
		$this->resize($_FILES['image']['tmp_name'][$position], 500, 0, $f_image_title);
		$this->resize($_FILES['image']['tmp_name'][$position], 150, 0, $t_image_title);
		@chmod($f_image_out, 0777);
		@chmod($t_image_out, 0777);
		
		if(!$has_lineart)
		{
		  $this->resize($_FILES['image']['tmp_name'][$position], 500, 0, $f_lineart_title);
		  $this->resize($_FILES['image']['tmp_name'][$position], 150, 0, $t_lineart_title);
		  @chmod($f_image_out, 0777);
		  @chmod($t_image_out, 0777);
		}
	  }
	  
	  
	  return true;
	}
	
	function add(&$ld)
	{

		if(!$this->add_validate($ld))
		{
			return false;
		}
		if(!$this->image_validate($ld))
		{
			return false;
		}

		$ld['programs_id']=$this->dbu->query_get_id("
													INSERT INTO 
																programs 
													SET 
																programs_code='".$ld['programs_code']."', 
																programs_title='".addslashes($ld['programs_title'])."', 
																description='".addslashes($ld['description'])."', 
																sort_order='".$ld['sort_order']."', 
																active = '".$ld['active']."' 
													");
		$this->dbu->query("insert into programs_in_category (
                									programs_id,
                									category_id,
                									main )
                									values (
                									'".$ld['programs_id']."',
                									'".$ld['category_id']."',
                									'1'
                									)
                			");
		$this->upload_file($ld);
		$ld['error']="Program Succesfully added.";

	    return true;
	}
	
	function add_validate(&$ld)
	{
		$is_ok = true;	

	    if($ld['category_id'] && empty($ld['category_id']))
	    {
	        $ld['error'].="Please select a Category for the new program first."."<br>";
	        $is_ok=false;
	    }
	    elseif($ld['category_id'] && !is_numeric($ld['category_id']))
	    {
	        $ld['error'].="Invalid Category."."<br>";
	        $is_ok=false;
	    }
		if ($ld['sort_order'] && !is_numeric($ld['sort_order'])) 
		{
			$ld['error']="Please fill in just with numbers";
			$is_ok = false;
		}
	    if(!$ld['programs_title'])
	    {
	        $ld['error'].="Please enter a title for the program."."<br>";
	        $is_ok=false;
	    }
	    if(!$ld['description'])
	    {
	        $ld['error'].="Please enter some content in the Description field."."<br>";
	        $is_ok=false;
	    }
	    else 
	    {
	    	$ld['description']=str_replace('/admin/ktmlpro/includes/ktedit/', '', $ld['description']);
	    }

		return $is_ok;
	}
	function image_validate(&$ld)
	{
		$is_ok = true;
/*
		if(!$_FILES['lineart']['tmp_name'])
		{
			$ld['error'].="Please upload a lineart image!"."<br>";
			return false;
		}
		if(!$_FILES['image']['tmp_name'])
		{
			$ld['error'].="Please upload a image file!"."<br>";
			return false;
		}
*/
        $allowed['.gif']=1;
        $allowed['.jpg']=1;
        $allowed['.jpeg']=1;
        $allowed['.png']=1;
        
        if(!empty($_FILES['lineart']['name']))
        {
	        $f_lineart_ext=substr($_FILES['lineart']['name'],strrpos($_FILES['lineart']['name'],"."));
	        if(!$allowed[strtolower($f_lineart_ext)])
	        {
	        	$ld['error']="Only jpg, jpeg and gif files are accepted.";
	        	return false;
	        }
        }
        if(!empty($_FILES['image']['name']))
        {
	        $f_image_ext=substr($_FILES['image']['name'],strrpos($_FILES['image']['name'],"."));
	        if(!$allowed[strtolower($f_image_ext)])
	        {
	        	$ld['error']="Only jpg, jpeg and gif files are accepted.";
	        	return false;
	        }
        }

		return $is_ok;
	}

	function update(&$ld)
	{
		if(!$this->update_validate($ld))
		{
			return false;
		}
	
		if(!$_FILES['lineart']['tmp_name'] && !$_FILES['image']['tmp_name'])
		{
			$this->dbu->query("UPDATE programs SET programs_code='".$ld['programs_code']."', sort_order='".addslashes($ld['sort_order'])."' WHERE programs_id='".$ld['programs_id']."'");
            $this->dbu->query("UPDATE programs_translate_".$ld['lang']." SET  programs_title='".addslashes($ld['programs_title'])."', description='".addslashes($ld['description'])."' WHERE programs_id='".$ld['programs_id']."'");
			$this->dbu->query("UPDATE programs_in_category SET category_id='".$ld['category_id']."' WHERE programs_id='".$ld['programs_id']."' AND main='1' ");
			$this->dbu->query("DELETE FROM programs_in_category WHERE programs_id='".$ld['programs_id']."' AND category_id='".$ld['category_id']."' AND main='0' ");
		}
		else 
			{
				if(!$this->image_validate($ld))
				{
					return false;
				}	
				$this->dbu->query("UPDATE programs SET programs_code='".$ld['programs_code']."', sort_order='".addslashes($ld['sort_order'])."' WHERE programs_id='".$ld['programs_id']."'");
                $this->dbu->query("UPDATE programs_translate_".$ld['lang']." SET  programs_title='".addslashes($ld['programs_title'])."', description='".addslashes($ld['description'])."' WHERE programs_id='".$ld['programs_id']."'");
                $this->dbu->query("UPDATE programs_in_category SET category_id='".$ld['category_id']."' WHERE programs_id='".$ld['programs_id']."' AND main='1' ");
                $this->dbu->query("DELETE FROM programs_in_category WHERE programs_id='".$ld['programs_id']."' AND category_id='".$ld['category_id']."' AND main='0' ");
				$this->upload_file($ld);
			}
	    
		$ld['error']="Program Succesfully Updated.";
	
	    return true;
	}
	
	function update_validate(&$ld)
	{
		$is_ok = true;
		
	    if(!$ld['category_id'])
	    {
	        $ld['error'].="Please select a Category for the new program."."<br>";
	        $is_ok=false;
	    }
	    if(!$ld['programs_title'])
	    {
	        $ld['error'].="Please enter a title for the program."."<br>";
	        $is_ok=false;
	    }
        if(!$ld['lang'])
	    {
	        $ld['error'].="Wrong lang parameter."."<br>";
	        $is_ok=false;
	    }
	    if(!$ld['description'])
	    {
	        $ld['error'].="Please enter some content in the Description field."."<br>";
	        $is_ok=false;
	    }
	    else 
	    {
	    	$ld['description']=str_replace('/admin/ktmlpro/includes/ktedit/', '', $ld['description']);
	    }
	    
		return $is_ok;
	}

	/****************************************************************
	* function delete(&$ld)                                            *
	****************************************************************/
	
	function delete(&$ld)
	{
		if(!$this->delete_validate($ld))
		{
			return false;
		}
		
		$this->erasepicture($ld);
		
		$this->dbu->query("DELETE FROM programs WHERE programs_id='".$ld['programs_id']."'");
	    
		$ld['error']="Program Succesfully deleted.";
	
	    return true;
	}
	
	function delete_validate(&$ld)
	{
		$is_ok = true;
		
		return $is_ok;
	}

	
	/****************************************************************
	* function programs_category_add(&$ld)                           *
	****************************************************************/
	
	function programs_category_add(&$ld)
	        {
	                if(!$this->programs_category_add_validate($ld))
	                {
	                     return false;
	                }
	
	                $this->dbu->query("insert into programs_in_category (
	                									programs_id,
	                									category_id,
	                									main )
	                									values (
	                									'".$ld['programs_id']."',
	                									'".$ld['category_id']."',
	                									'0'
	                									)
	                			");
	
					$ld['error']="Program added to selected category.";
	                return true;
	        }
	
	/****************************************************************
	* function programs_category_delete(&$ld)                        *
	****************************************************************/
	
	function programs_category_delete(&$ld)
	        {
	                if(!$this->programs_category_delete_validate($ld))
	                {
	                     return false;
	                }
					$this->dbu->query("delete from programs_in_category where programs_id='".$ld['programs_id']."' and category_id='".$ld['category_id']."'");
	
					$ld['error']="Program Removed from Category.";
	                return true;
	        }
	
	/****************************************************************
	* function programs_category_add_validate(&$ld)                  *
	****************************************************************/
	function programs_category_add_validate(&$ld)
	{
	        $is_ok=true;
	        if (!is_numeric($ld['programs_id']))
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	        $this->dbu->query("select programs_id from programs where programs_id='".$ld['programs_id']."'");
	        if(!$this->dbu->move_next())
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	        
	        if (!is_numeric($ld['category_id']))
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	        
	        $this->dbu->query("select category_id from programs_category where category_id='".$ld['category_id']."'");
	        if(!$this->dbu->move_next())
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	 		
	        $this->dbu->query("select category_id from programs_in_category where category_id='".$ld['category_id']."' and programs_id='".$ld['programs_id']."'");
	        if($this->dbu->move_next())
	        {
	            $ld['error'].="This Product allready exist in the selected category.<br>";
	            return false;
	        }
	 		
	        return $is_ok;
	}
	
	
	/****************************************************************
	* function programs_category_delete_validate(&$ld)               *
	****************************************************************/
	function programs_category_delete_validate(&$ld)
	{
	        $is_ok=true;
	        if (!is_numeric($ld['programs_id']))
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	        if (!is_numeric($ld['category_id']))
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	 		
	        $this->dbu->query("select category_id from programs_in_category where category_id='".$ld['category_id']."' and programs_id='".$ld['programs_id']."' and main='1'");
	        if($this->dbu->move_next())
	        {
	            $ld['error'].="Invalid ID.<br>";
	            return false;
	        }
	 		
	        return $is_ok;
	} 
	
/****************************************************************
* function upload_file(&$ld)                                    *
****************************************************************/
function upload_file(&$ld)
{
        global $_FILES, $script_path, $is_live;
        $allowed['.gif']=1;
        $allowed['.jpg']=1;
        $allowed['.jpeg']=1;
        $allowed['.png']=1;
        
        if(!is_numeric($_SESSION[U_ID]))
        {
        	$ld['error'].="Error.".'<br>';
        	return false;
        }
        else 
        {
         	$this->dbu->query("select programs_id, lineart, thumb_lineart, image, thumb_image from programs where programs_id='".$ld['programs_id']."'");
        	if(!$this->dbu->move_next())
        	{
	        	$ld['error'].="Error.".'<br>';
	        	return false;
        	}
        	else 
        	{
        		if(!empty($_FILES['lineart']['tmp_name']))
        		{
        		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('lineart') );
				@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_lineart') );
				$this->dbu->query("UPDATE programs SET lineart=NULL, thumb_lineart=NULL where programs_id='".$ld['programs_id']."'");
        		}
        		if(!empty($_FILES['image']['tmp_name']))
        		{
        		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('image') );
				@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_image') );
				$this->dbu->query("UPDATE programs SET image=NULL, thumb_image=NULL where programs_id='".$ld['programs_id']."'");
        		}
        	}
        }
		
        if(!empty($_FILES['lineart']['tmp_name'])) 
        {
        $f_lineart_ext=substr($_FILES['lineart']['name'],strrpos($_FILES['lineart']['name'],"."));
        $f_lineart_title="programs_lineart"."_".$ld['programs_id'].$f_lineart_ext;
        $t_lineart_title="programs_thumb_lineart"."_".$ld['programs_id'].$f_lineart_ext;
        $f_lineart_out=$script_path.UPLOAD_PATH.$f_lineart_title;
        $t_lineart_out=$script_path.UPLOAD_PATH.$t_lineart_title;
        }
        
        if(!empty($_FILES['image']['tmp_name'])) 
        {
        $f_image_ext=substr($_FILES['image']['name'],strrpos($_FILES['image']['name'],"."));
        $f_image_title="programs_image"."_".$ld['programs_id'].$f_image_ext;
        $t_image_title="programs_thumb_image"."_".$ld['programs_id'].$f_image_ext;
        $f_image_out=$script_path.UPLOAD_PATH.$f_image_title;
        $t_image_out=$script_path.UPLOAD_PATH.$t_image_title;
        }

		$owner = $this->dbu->field("select owner from programs where programs_id='".$ld['programs_id']."'");

        if(!$is_live || (strtolower($f_lineart_ext) =='.gif') || (strtolower($f_lineart_ext) =='.png'))
        {
        	if(FALSE === move_uploaded_file($_FILES['lineart']['tmp_name'],$f_lineart_out))
	        {
	               // $ld['error'].="Unable to upload the file.  Move operation failed."."<!-- Check file permissions -->";
	                return false;
	        }
	        
        	$this->dbu->query("update programs set
	                           lineart='".$f_lineart_title."',
	                           thumb_lineart='".$t_lineart_title."'
	                           where programs_id='".$ld['programs_id']."'" 
	                          );
			@chmod($f_lineart_out, 0777);
        	$ld['error'].="Image Succesfully saved.<br>";
        	return true;
        }
        if(!$is_live || (strtolower($f_image_ext) =='.gif') || (strtolower($f_image_ext) =='.png'))
        {
        	if(FALSE === move_uploaded_file($_FILES['image']['tmp_name'],$f_image_out))
	        {
	               // $ld['error'].="Unable to upload the file.  Move operation failed."."<!-- Check file permissions -->";
	                return false;
	        }

        	$this->dbu->query("update programs set
	                           image='".$f_image_title."',
	                           thumb_image='".$t_image_title."'
	                           where programs_id='".$ld['programs_id']."'" 
	                          );
			if($owner > -1)
			{
			  $this->dbu->query("update programs set
	                           lineart='".$f_image_title."',
	                           thumb_lineart='".$t_image_title."'
	                           where programs_id='".$ld['programs_id']."'" 
	                          );
			}
			
			@chmod($f_image_out, 0777);
        	$ld['error'].="Image Succesfully saved.<br>";
        	return true;
        }
        else
        {
//        	$this->resize($_FILES['image']['tmp_name'], PROGRAMS_PICTURE_WIDTH, 0, $f_title);
//        	$this->resize($_FILES['image']['tmp_name'], PROGRAMS_THUMBNAIL_WIDTH, 0, $t_title);
       		if(!empty($_FILES['lineart']['tmp_name']))
       		{
        	$this->resize($_FILES['lineart']['tmp_name'], 500, 0, $f_lineart_title);
        	$this->resize($_FILES['lineart']['tmp_name'], 150, 0, $t_lineart_title);
	        @chmod($f_lineart_out, 0777);
	        @chmod($t_lineart_out, 0777);
        	$this->dbu->query("update programs set
	                           lineart='".$f_lineart_title."',
	                           thumb_lineart='".$t_lineart_title."'
	                           where programs_id='".$ld['programs_id']."'" 
	                          );
       		}
       		if(!empty($_FILES['image']['tmp_name']))
       		{
        	$this->resize($_FILES['image']['tmp_name'], 500, 0, $f_image_title);
        	$this->resize($_FILES['image']['tmp_name'], 150, 0, $t_image_title);
	        @chmod($f_image_out, 0777);
	        @chmod($t_image_out, 0777);
        	$this->dbu->query("update programs set
	                           image='".$f_image_title."',
	                           thumb_image='".$t_image_title."'
	                           where programs_id='".$ld['programs_id']."'" 
	                          );
       		}
	        $ld['error'].="Image Succesfully saved.".'<br>';
	        return true;
        }
 
}
/****************************************************************
* function erasepicture(&$ld)                                   *
****************************************************************/
function erasepicture(&$ld)
{
      	$this->dbu->query("select lineart, thumb_lineart, image, thumb_image, programs_id from programs where programs_id='".$ld['programs_id']."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Invalid ID.<br>";
	        return false;
	    }
	    else 
	    {
			global $script_path;
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('lineart') );
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_lineart') );
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('image') );
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb_image') );
			$this->dbu->query("UPDATE programs SET lineart=NULL, thumb_lineart=NULL, image=NULL, thumb_image=NULL WHERE programs_id='".$ld['programs_id']."'");
	    }
	$ld['error'] .= "Image Succesfully deleted!<br>";
	return true;
}
	/****************************************************************
	* function resize(&$ld)                                         *
	****************************************************************/

	function resize($original_image, $new_width, $new_height, $image_title) 
	{
		global $script_path;
		$original_image=ImageCreateFromJPEG($original_image);
		$aspect_ratio = imagesx($original_image) / imagesy($original_image); 
		if (empty($new_width)) 
		{ 
			$new_width = $aspect_ratio * $new_height; 
		}
		elseif (empty($new_height)) 
		{ 
			$new_height= $new_width / $aspect_ratio; 
		}
		if (imageistruecolor($original_image))	
		{ 
			$image = imagecreatetruecolor($new_width, $new_height); 
		} 
		else 
		{ 
			$image = imagecreate($new_width, $new_height); 
		} 
		// copy the original image onto the smaller blank 
		imagecopyresampled($image, $original_image, 0, 0, 0, 0, $new_width, $new_height, imagesx($original_image), imagesy($original_image));
		ImageJPEG($image, $script_path.UPLOAD_PATH.$image_title) or die("Problem In saving"); 
	}
	
	function change_sort_order(&$ld)
	{
	  foreach($ld['sort_order'] as $program_id => $sort)
	  {
		$this->dbu->query("UPDATE programs SET sort_order=$sort WHERE programs_id='".$program_id."'");
	  }
	  return true;
	}

}//end class

?>