<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class tag
{
  var $dbu;

function tag()
{
    $this->dbu=new mysql_db;
}
/****************************************************************
* function add(&$ld)                                            *
****************************************************************/

function action_box_add(&$ld)
{
	if(!$this->action_box_add_validate($ld))
	{
		return false;
	}
	$ld['tag_id']=$this->dbu->query_get_id("insert into cms_tag_library (
														tag,
														type,
														id,
														name,
														file_name,
														comments
													 )  values  (
														'".$ld['tag']."',
														'3',
														'0',
														'".$ld['name']."',
														'".$ld['file_name']."',
														'".$ld['description']."'
													 )
		
		");
	
	$ld['error']="Dynamic Box Succesfully added.";
    return true;
}

/****************************************************************
* function update(&$ld)                                         *
****************************************************************/
function action_box_update(&$ld)
{
	if(!$this->action_box_update_validate($ld))
	{
		return false;
	}
	
	$this->dbu->query("update cms_tag_library set
    						    name='".$ld['name']."',
    							file_name='".$ld['file_name']."',
    							tag='".$ld['tag']."',
    							comments='".$ld['description']."'
    							where
    							tag_id='".$ld['tag_id']."'
    		");
		
    $ld['error'].="Dynamic Box successfully updated.";
    return true;
}

/****************************************************************
* function delete(&$ld)                                         *
****************************************************************/
function action_box_delete(&$ld)
{
	if(!$this->action_box_delete_validate($ld))
	{
		return false;
	}
	$this->dbu->query("delete from cms_tag_library where tag_id='".$ld['tag_id']."'");
	
    $ld['error'].="Dynamic Box successfully deleted.";
    return true;
}        

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function action_box_add_validate(&$ld)
{
    $is_ok=true;
    
    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['file_name'])
    {
        $ld['error'].="Please fill in the File Name Field."."<br>";
        $is_ok=false;
    }
    
   	if(!$ld['tag'])
   	{
        $ld['error'].="Please enter the Alias Tag for this Dynamic Box!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for this Dynamic Box! (Read Help)"."<br>";
        $is_ok=false;
   	}
    
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Description Field."."<br>";
        $is_ok=false;
    }
    
    if($is_ok)
    {
    	if($ld['tag'])
    	{
    		$this->dbu->query("select tag_id from cms_tag_library where tag='".$ld['tag']."'");
    		if($this->dbu->move_next())
    		{
		        $ld['error'].="There is another CMS object with ".$ld['tag']." Alias Tag. Please change it."."<br>";
		        $is_ok=false;
    		}
    	}
    }
    
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function action_box_update_validate(&$ld)
{
    $is_ok=true;
    if (!is_numeric($ld['tag_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select tag_id from cms_tag_library where tag_id='".$ld['tag_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    
    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['file_name'])
    {
        $ld['error'].="Please fill in the File Name Field."."<br>";
        $is_ok=false;
    }
    
   	if(!$ld['tag'])
   	{
        $ld['error'].="Please enter the Alias Tag for this Dynamic Box!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for this Dynamic Box! (Read Help)"."<br>";
        $is_ok=false;
   	}
    
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Description Field."."<br>";
        $is_ok=false;
    }
    
    if($is_ok)
    {
    	if($ld['tag'])
    	{
    		$this->dbu->query("select tag_id from cms_tag_library where tag='".$ld['tag']."' and tag_id!='".$ld['tag_id']."'");
    		if($this->dbu->move_next())
    		{
		        $ld['error'].="There is another CMS object with ".$ld['tag']." Alias Tag. Please change it."."<br>";
		        $is_ok=false;
    		}
    	}
    }
    
    return $is_ok;
}


/****************************************************************
* function delete_validate(&$ld)                                *
****************************************************************/
function action_box_delete_validate(&$ld)
{
	$is_ok=true;
    if (!is_numeric($ld['tag_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select tag_id from cms_tag_library where tag_id='".$ld['tag_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
 
    return $is_ok;
}

}//end class
?>

