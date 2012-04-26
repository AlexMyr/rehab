<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class menu
{
  var $dbu;

function menu()
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
    $db=new mysql_db;           
	$ld['menu_id']=$this->dbu->query_get_id("insert into cms_menu (
                                                                       name,
                                                                       template_file_h,
                                                                       template_file_v,
                                                                       tag_h,
                                                                       tag_v,
                                                                       description,
                                                                       h_version,
                                                                       v_version
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['template_file_h']."',
                                                                        '".$ld['template_file_v']."',
                                                                        '".$ld['tag_h']."',
                                                                        '".$ld['tag_v']."',
                                                                        '".$ld['description']."',
                                                                        '".$ld['h_version']."',
                                                                        '".$ld['v_version']."'
                                                                        )
                                                                       ");
	if($ld['tag_h'])
	{
		$this->dbu->query("insert into cms_tag_library (
														tag,
														type,
														id,
														name,
														comments
													 )  values  (
														'".$ld['tag_h']."',
														'2',
														'".$ld['menu_id']."',
														'".$ld['name']."',
														'".$ld['description']."'
													 )
		
		");
	}
	
	if($ld['tag_v'])
	{
		$this->dbu->query("insert into cms_tag_library (
														tag,
														type,
														id,
														name,
														comments
													 )  values  (
														'".$ld['tag_v']."',
														'2',
														'".$ld['menu_id']."',
														'".$ld['name']."',
														'".$ld['description']."'
													 )
		
		");
	}
	$ld['error']="Menu Succesfully added.";
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
    $this->dbu->query("update cms_tag_library set 
    						name='".$ld['name']."',
    						comments='".$ld['description']."'
    						where
    						id='".$ld['menu_id']."' and type='2'
    		");
    		
    $this->dbu->query("update cms_menu set
                       name='".$ld['name']."',
                       description='".$ld['description']."'
                       where
                       menu_id='".$ld['menu_id']."'"
                      );
                      
    $ld['error'].="Menu has been successfully updated.";
    return true;
}

/****************************************************************
* function delete(&$ld)                                         *
****************************************************************/
function delete(&$ld)
{
		if(!$this->delete_validate($ld))
		{
			return false;
		}
        $this->dbu->query("delete from cms_menu_link where menu_id='".$ld['menu_id']."'");
        $this->dbu->query("delete from cms_tag_library where id='".$ld['menu_id']."' and type='2'");
        $this->dbu->query("delete from cms_menu_submenu where menu_id='".$ld['menu_id']."'");
        $this->dbu->query("delete from cms_menu where menu_id='".$ld['menu_id']."'");
        $ld['error'].="Menu has been successfully deleted.";
        return true;
}        

/****************************************************************
* function h_version_update(&$ld)                               *
****************************************************************/
function h_version_update(&$ld)
{
	if(!$this->h_version_update_validate($ld))
	{
		return false;
	}
    $this->dbu->query("select tag_h from cms_menu where menu_id='".$ld['menu_id']."'");
    $this->dbu->move_next();
    
    $tag_h=$this->dbu->f('tag_h'); 
    
    if($ld['tag_h'])
    {
    	if($tag_h)
    	{
    		$this->dbu->query("update cms_tag_library set
    						    name='".$ld['name']."',
    							tag='".$ld['tag_h']."',
    							comments='".$ld['description']."'
    							where
    							tag='".$tag_h."'
    		");
    	}
    	else 
    	{
			$this->dbu->query("insert into cms_tag_library (
															tag,
															type,
															id,
															name,
															comments
														 )  values  (
															'".$ld['tag_h']."',
															'2',
															'".$ld['menu_id']."',
															'".$ld['name']."',
															'".$ld['description']."'
														 )
			");
    	
    	}
    }
    else 
    {
        $this->dbu->query("delete from cms_tag_library where tag='".$ld['tag_h']."'");
    }
    
    $this->dbu->query("update cms_menu set
					   h_version='1', 
    				   template_file_h='".$ld['template_file_h']."',
                       tag_h='".$ld['tag_h']."'
                       where
                       menu_id='".$ld['menu_id']."'"
                      );
                      
    $ld['error'].="Menu has been successfully updated.";
    return true;
}
/****************************************************************
* function v_version_update(&$ld)                               *
****************************************************************/
function v_version_update(&$ld)
{
	if(!$this->v_version_update_validate($ld))
	{
		return false;
	}
    $this->dbu->query("select tag_v from cms_menu where menu_id='".$ld['menu_id']."'");
    $this->dbu->move_next();
    
    $tag_v=$this->dbu->f('tag_v'); 
    
    if($ld['tag_v'])
    {
    	if($tag_v)
    	{
    		$this->dbu->query("update cms_tag_library set 
    							name='".$ld['name']."',
    							tag='".$ld['tag_v']."',
    							comments='".$ld['description']."'
    							where
    							tag='".$tag_v."'
    		");
    	}
    	else 
    	{
			$this->dbu->query("insert into cms_tag_library (
															tag,
															type,
															id,
															name,
															comments
														 )  values  (
															'".$ld['tag_v']."',
															'2',
															'".$ld['menu_id']."',
															'".$ld['name']."',
															'".$ld['description']."'
														 )
			");
    	
    	}
    }
    else 
    {
        $this->dbu->query("delete from cms_tag_library where tag='".$ld['tag_v']."'");
    }
    
    $this->dbu->query("update cms_menu set
					   v_version='1', 
    				   template_file_v='".$ld['template_file_v']."',
                       tag_v='".$ld['tag_v']."'
                       where
                       menu_id='".$ld['menu_id']."'"
                      );
                      
    $ld['error'].="Menu has been successfully updated.";
    return true;
}

/****************************************************************
* function h_version_remove(&$ld)                               *
****************************************************************/
function h_version_remove(&$ld)
{
	if(!$this->h_version_remove_validate($ld))
	{
		return false;
	}
    $this->dbu->query("select tag_h from cms_menu where menu_id='".$ld['menu_id']."'");
    $this->dbu->move_next();
    
    $tag_h=$this->dbu->f('tag_h'); 
    $this->dbu->query("delete from cms_tag_library where tag='".$tag_h."'");
    
    
    $this->dbu->query("update cms_menu set
					   h_version='0', 
    				   template_file_h='',
                       tag_h=''
                       where
                       menu_id='".$ld['menu_id']."'"
                      );
                      
    $ld['error'].="Horizontal Version of this Menu has been successfully removed.";
    return true;
}
/****************************************************************
* function v_version_remove(&$ld)                               *
****************************************************************/
function v_version_remove(&$ld)
{
	if(!$this->v_version_remove_validate($ld))
	{
		return false;
	}
    $this->dbu->query("select tag_v from cms_menu where menu_id='".$ld['menu_id']."'");
    $this->dbu->move_next();
    
    $tag_v=$this->dbu->f('tag_v'); 
    $this->dbu->query("delete from cms_tag_library where tag='".$tag_v."'");
    
    
    $this->dbu->query("update cms_menu set
					   v_version='0', 
    				   template_file_v='',
                       tag_v=''
                       where
                       menu_id='".$ld['menu_id']."'"
                      );
                      
    $ld['error'].="Vertical Version of this Menu has been successfully removed.";
    return true;
}

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;
    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['h_version'] && !$ld['v_version'])
    {
        $ld['error'].="This menu needs at least one version (Horizontal or Vertical)! Please check Accordingly"."<br>";
        $is_ok=false;
    }
    
    if($ld['h_version'])
    {
    	if(!$ld['template_file_h'])
    	{
	        $ld['error'].="Horizontal Version Checked. Please select the Template File for the Horizontal version of this Menu!"."<br>";
	        $is_ok=false;
    	}
    	if(!$ld['tag_h'])
    	{
	        $ld['error'].="Horizontal Version Checked. Please enter the Alias Tag for Horizontal Version!"."<br>";
	        $is_ok=false;
    	}
    	elseif (!secure_cms_tag($ld['tag_h']))
    	{
	        $ld['error'].="Please enter a valid Alias Tag for Horizontal Version of this Menu! (Read Help)"."<br>";
	        $is_ok=false;
    	}
    }
    
    if($ld['v_version'])
    {
    	if(!$ld['template_file_v'])
    	{
	        $ld['error'].="Vertical Version Checked. Please select the Template File for the Vertical version of this Menu!"."<br>";
	        $is_ok=false;
    	}
    	if(!$ld['tag_v'])
    	{
	        $ld['error'].="Vertical Version Checked. Please enter the Alias Tag for Vertical Version!"."<br>";
	        $is_ok=false;
    	}
    	elseif (!secure_cms_tag($ld['tag_v']))
    	{
	        $ld['error'].="Please enter a valid Alias Tag for Vertical Version of this Menu! (Read Help)"."<br>";
	        $is_ok=false;
    	}
    }

    if(($ld['tag_h'] || $ld['tag_v']) && ($ld['tag_h'] == $ld['tag_v']))
    {
        $ld['error'].="Alias tags for Vertical and Horizontal version of this Menu should be different!"."<br>";
        $is_ok=false;
    }
	
    if($is_ok)
    {
    	if($ld['tag_h'])
    	{
    		$this->dbu->query("select tag_id from cms_tag_library where tag='".$ld['tag_h']."'");
    		if($this->dbu->move_next())
    		{
		        $ld['error'].="There is another CMS object with ".$ld['tag_h']." Alias Tag. Please change it."."<br>";
		        $is_ok=false;
    		}
    	}
    	if($ld['tag_v'])
    	{
    		$this->dbu->query("select tag_id from cms_tag_library where tag='".$ld['tag_v']."'");
    		if($this->dbu->move_next())
    		{
		        $ld['error'].="There is another CMS object with ".$ld['tag_v']." Alias Tag. Please change it."."<br>";
		        $is_ok=false;
    		}
    	}
    }
    
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }

    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name field."."<br>";
        return false;
    }

    return $is_ok;
}

/****************************************************************
* function h_version_update_validate(&$ld)                      *
****************************************************************/
function h_version_update_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id, tag_v from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }
	else 
	{
		$tag_v=$this->dbu->f('tag_v');
	}
    if(!$ld['name'])
    {
        $ld['error'].="Invalid ID."."<br>";
        return false;
    }

   	if(!$ld['template_file_h'])
   	{
        $ld['error'].="Please select the Template File for the Horizontal version of this Menu!"."<br>";
        $is_ok=false;
   	}
   	if($tag_v && ($tag_v == $ld['tag_h']))
    {
        $ld['error'].="Alias tags for Vertical and Horizontal version of this Menu should be different!"."<br>";
        $is_ok=false;
    }
    
   	if(!$ld['tag_h'])
   	{
        $ld['error'].="Please enter the Alias Tag for Horizontal version of this Menu!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag_h']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for Horizontal Version of this Menu! (Read Help)"."<br>";
        $is_ok=false;
   	}

    if($is_ok)
    {
    	if($ld['tag_h'])
    	{
    		$this->dbu->query("select tag_id, id, type from cms_tag_library where tag='".$ld['tag_h']."'");
    		if($this->dbu->move_next())
    		{
    			if($this->dbu->f('type')!=2 || $this->dbu->f('id')!=$ld['menu_id'])
    			{
			        $ld['error'].="There is another CMS object with ".$ld['tag_h']." Alias Tag. Please change it."."<br>";
			        $is_ok=false;
    			}
    		}
    		
    	}
    }
   	
   	
    return $is_ok;
}

/****************************************************************
* function v_version_update_validate(&$ld)                      *
****************************************************************/
function v_version_update_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id, tag_h from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }
	else 
	{
		$tag_h=$this->dbu->f('tag_h');
	}

    if(!$ld['name'])
    {
        $ld['error'].="Invalid ID."."<br>";
        return false;
    }

   	if(!$ld['template_file_v'])
   	{
        $ld['error'].="Please select the Template File for the Vertical version of this Menu!"."<br>";
        $is_ok=false;
   	}
   	if(!$ld['tag_v'])
   	{
        $ld['error'].="Please enter the Alias Tag for Vertical version of this Menu!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag_v']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for Vertical Version of this Menu! (Read Help)"."<br>";
        $is_ok=false;
   	}
   	
   	if($tag_h && ($tag_h == $ld['tag_v']))
    {
        $ld['error'].="Alias tags for Vertical and Horizontal version of this Menu should be different!"."<br>";
        $is_ok=false;
    }

    if($is_ok)
    {
    	if($ld['tag_v'])
    	{
    		$this->dbu->query("select tag_id, id, type from cms_tag_library where tag='".$ld['tag_v']."'");
    		if($this->dbu->move_next())
    		{
    			if($this->dbu->f('type')!=2 || $this->dbu->f('id')!=$ld['menu_id'])
    			{
			        $ld['error'].="There is another CMS object with ".$ld['tag_v']." Alias Tag. Please change it."."<br>";
			        $is_ok=false;
    			}
    		}
    		
    	}
    }
   	
   	
    return $is_ok;
}

/****************************************************************
* function h_version_remove_validate(&$ld)                      *
****************************************************************/
function h_version_remove_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id, tag_v from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }
	else 
	{
		$tag_v=$this->dbu->f('tag_v');
	}
    if(!$tag_v)
    {
        $ld['error'].="There is no Vertical version of this Menu!<br> You can not remove the Horizontal version without having a Vertical version!"."<br>";
        return false;
    }

    return $is_ok;
}

/****************************************************************
* function v_version_remove_validate(&$ld)                      *
****************************************************************/
function v_version_remove_validate(&$ld)
{
    $is_ok=true;
    
    if (!is_numeric($ld['menu_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id, tag_h from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }
	else 
	{
		$tag_h=$this->dbu->f('tag_h');
	}
    if(!$tag_h)
    {
        $ld['error'].="There is no Horizontal version of this Menu!<br> You can not remove the Vertical version without having a Horizontal version!"."<br>";
        return false;
    }

    return $is_ok;
}

/****************************************************************
* function delete_validate(&$ld)                         *
****************************************************************/
function delete_validate(&$ld)
{
	$is_ok=true;
    if (!is_numeric($ld['menu_id']))
    {
    	$ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select menu_id from cms_menu where menu_id='".$ld['menu_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }

    return $is_ok;
}


}//end class
?>

