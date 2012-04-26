<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class menu_link
{
  var $dbu;

function menu_link()
{
    $this->dbu=new mysql_db;
}
/****************************************************************
* function add(&$ld)                                            *
****************************************************************/

function add(&$ld)
{
	if(!$this->add_validate($ld))
	{
		return false;
	}
                
	$ld['menu_link_id']=$this->dbu->query_get_id("insert into cms_menu_link (
                                                                       menu_id,
                                                                       name,
                                                                       url,
                                                                       sort_order,
                                                                       target,
                                                                       level
                														 )
                                                                        values
                                                                        (
                                                                        '".$ld['menu_id']."',
                                                                        '".$ld['name']."',
                                                                        '".$ld['url']."',
                                                                        '".$ld['sort_order']."',
                                                                        '".$ld['target']."',
                                                                        '".$ld['level']."'
                                                                        )
                                                                       ");
     
	if(!$ld['parent_id'])
    {
    	$ld['parent_id'] = $ld['menu_link_id']; 	
    }                                             
    
    $this->dbu->query("insert into cms_menu_submenu (parent_id, menu_link_id, menu_id) values ('".$ld['parent_id']."', '".$ld['menu_link_id']."', '".$ld['menu_id']."')");          
	$ld['error']="Menu Link successfully added.";
    return true;
}

/****************************************************************
* function update(&$ld)                                         *
****************************************************************/
function update(&$ld)
{
	if(!$this->update_validate($ld))
	{
		return false;
	}
       
    $this->dbu->query("update cms_menu_link set
                       menu_id='".$ld['menu_id']."',
                       name='".$ld['name']."',
                       url='".$ld['url']."',
                       sort_order='".$ld['sort_order']."',
                       target='".$ld['target']."',
                       level='".$ld['level']."'
                       where
                       menu_link_id='".$ld['menu_link_id']."'"
                      );
    if(!$ld['parent_id'])
    {
    	$ld['parent_id'] = $ld['menu_link_id']; 	
    }                                             
    
    $this->dbu->query("delete from cms_menu_submenu where menu_link_id='".$ld['menu_link_id']."'");          
    $this->dbu->query("insert into cms_menu_submenu (parent_id, menu_link_id, menu_id) values ('".$ld['parent_id']."', '".$ld['menu_link_id']."', '".$ld['menu_id']."')");          
    $ld['error'].="Menu Link successfully updated.";
    return true;
}


/****************************************************************
* function sort_order_update(&$ld)                              *
****************************************************************/
function sort_order_update(&$ld)
{
    if($ld['sort_order'])
		foreach ($ld['sort_order'] as $menu_link_id => $sort_order)
		{
		    $this->dbu->query("update cms_menu_link set
		                       sort_order='".$sort_order."'
		                       where
		                       menu_link_id='".$menu_link_id."'"
		                      );
		
		}
    return true;
}


/****************************************************************
* function delete(&$ld)                                         *
****************************************************************/
function delete(&$ld)
{
	global  $script_path;
	
    if(!$this->delete_validate($ld))
    {
    	return false;
    }
    $this->dbu->query("delete from cms_menu_submenu where menu_link_id='".$ld['menu_link_id']."'");          
    $this->dbu->query("delete from cms_menu_link where menu_link_id='".$ld['menu_link_id']."'");
    $ld['error'].="Menu Link successfully deleted.";
    return true;
}        

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Please select the Site Menu.<br>";
        return false;
    }
    $this->dbu->query("select menu_id from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid Site Menu ID.<br>";
        return false;
    }
    
    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name field."."<br>";
        $is_ok=false;
    }
    if($ld['sort_order']&& !is_numeric($ld['sort_order']))
    {
        $ld['error'].="Please fill in the Sort Order field with a numeric value."."<br>";
        $is_ok=false;
    }
    if(!$ld['url'])
    {
        $ld['error'].="Please fill in the Link field."."<br>";
        $is_ok=false;
    }
	if($ld['parent_id'])
	{
		$ld['level']=1;
	}
	else 
	{
		$ld['parent_id']=0;
		$ld['level']=0;
	}
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['menu_link_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select menu_link_id from cms_menu_link where menu_link_id='".$ld['menu_link_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        return $this->add_validate($ld);
}


/****************************************************************
* function delete_validate(&$ld)                         *
****************************************************************/
function delete_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['menu_link_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select menu_link_id from cms_menu_link where menu_link_id='".$ld['menu_link_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }

        $this->dbu->query("select menu_link_id from cms_menu_submenu where parent_id='".$ld['menu_link_id']."' and menu_link_id!='".$ld['menu_link_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="This Menu Link is parent for other menu links. Please delete them first.<br>";
            return false;
        }

        return $is_ok;
}

/****************************************************************
* function activate_validate(&$ld)                              *
****************************************************************/
function activate_validate(&$ld)
{
            $is_ok=true;
        if (!is_numeric($ld['cat_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select menu_link_id from menu_link where menu_link_id='".$ld['cat_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
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
        $f_ext=substr($_FILES['picture']['name'],strpos($_FILES['picture']['name'],"."));
        if(!$allowed[strtolower($f_ext)])
        {
        	$ld['error']="Only jpg, jpeg and gif files are accepted.";
        	return false;
        }
        
        if(!is_numeric($_SESSION[U_ID]))
        {
        	$ld['error'].="Error.".'<br>';
        	return false;
        }
        else 
        {
         	$this->dbu->query("select menu_link_id, picture, thumb from menu_link where menu_link_id='".$ld['menu_link_id']."'");
        	if(!$this->dbu->move_next())
        	{
	        	$ld['error'].="Error.".'<br>';
	        	return false;
        	}
        	else 
        	{
        		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('picture') );
				@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb') );
				$this->dbu->query("UPDATE menu_link SET picture=NULL, thumb=NULL where menu_link_id='".$ld['menu_link_id']."'");
        	}
        }
		
        $f_name="catmimg"."_".$ld['menu_link_id'].$f_ext;
        $t_name="catmthumb"."_".$ld['menu_link_id'].$f_ext;
        $f_out=$script_path.UPLOAD_PATH.$f_name;
        $t_out=$script_path.UPLOAD_PATH.$t_name;
        
        if(!$_FILES['picture']['tmp_name'])
        {
                 $ld['error'].="Please upload a file!"."<br>";
                 return false;
        }
        
        if(!$is_live || ($f_ext =='.gif'))
        {
	        if(FALSE === move_uploaded_file($_FILES['picture']['tmp_name'],$f_out))
	        {
	               // $ld['error'].="Unable to upload the file.  Move operation failed."."<!-- Check file permissions -->";
	                return false;
	        }
	        
        	$this->dbu->query("update menu_link set
	                           picture='".$f_name."',
	                           thumb='".$f_name."'
	                           where menu_link_id='".$ld['menu_link_id']."'" 
	                          );
			@chmod($f_out, 0664);
        	$ld['error'].="Image Succesfully saved.<br>";
        	return true;
        }
        else
        {
        	$this->resize($_FILES['picture']['tmp_name'], PICTURE_WIDTH, 0, $f_name);
        	$this->resize($_FILES['picture']['tmp_name'], 126, 0, $t_name);
	        @chmod($f_out, 0664);
	        @chmod($t_out, 0664);
        	$this->dbu->query("update menu_link set
	                           picture='".$f_name."',
	                           thumb='".$t_name."'
	                           where menu_link_id='".$ld['menu_link_id']."'" 
	                          );
	        $ld['error'].="Image Succesfully saved.".'<br>';
	        return true;
        }
        
 
}

/****************************************************************
* function erasepicture(&$ld)                                   *
****************************************************************/
function erasepicture(&$ld)
{
      	$this->dbu->query("select picture, thumb, menu_link_id from menu_link where menu_link_id='".$ld['menu_link_id']."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Invalid ID.<br>";
	        return false;
	    }
	    else 
	    {
			global $script_path;
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('picture') );
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('thumb') );
			$this->dbu->query("UPDATE menu_link SET picture=NULL, thumb=NULL WHERE menu_link_id='".$ld['menu_link_id']."'");
	    }
	
	
	$ld['error'] .= "Image Succesfully deleted!<br>";
	return true;
}

/****************************************************************
* function resize(&$ld)                                         *
****************************************************************/


function resize($original_image, $new_width, $new_height, $image_name) 

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
	
	ImageJPEG($image, $script_path.UPLOAD_PATH.$image_name) or die("Problem In saving"); 
}




}//end class
?>

