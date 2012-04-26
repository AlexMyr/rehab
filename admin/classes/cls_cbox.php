<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class cbox
{
  var $dbu;

function cbox()
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
	$ld['content_box_id']=$this->dbu->query_get_id("insert into cms_content_box (
                                                                       tag,
                                                                       title,
                                                                       subtitle,
                                                                       headline,
                                                                       content,
                                                                       content_template_id,
                                                                       mode
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['tag']."',
                                                                        '".$ld['title']."',
                                                                        '".$ld['subtitle']."',
                                                                        '".$ld['headline']."',
                                                                        '".$ld['content']."',
                                                                        '".$ld['content_template_id']."',
                                                                        '".$ld['mode']."'
                                                                        )
                                                                       ");
     
	if($ld['tag'])
	{
		$this->dbu->query("insert into cms_tag_library (
														tag,
														type,
														id,
														name,
														comments
													 )  values  (
														'".$ld['tag']."',
														'1',
														'".$ld['content_box_id']."',
														'".$ld['title']."',
														'".$ld['title']."'
													 )
		
		");
	}
	
	$ld['error']="Content Box Succesfully added.";
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
	
    $this->dbu->query("select tag from cms_content_box where content_box_id='".$ld['content_box_id']."'");
    $this->dbu->move_next();
    
    $tag=$this->dbu->f('tag'); 
    
    if($ld['tag'])
    {
    	if($tag)
    	{
    		$this->dbu->query("update cms_tag_library set
    						    name='".$ld['title']."',
    							tag='".$ld['tag']."',
    							comments='".$ld['title']."'
    							where
    							tag='".$tag."'
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
															'".$ld['tag']."',
															'1',
															'".$ld['content_box_id']."',
															'".$ld['title']."',
															'".$ld['title']."'
														 )
			");
    	
    	}
    }
    else 
    {
        $this->dbu->query("delete from cms_tag_library where tag='".$ld['tag']."'");
    }
       
    $this->dbu->query("update cms_content_box set
                       tag='".$ld['tag']."',
                       title='".$ld['title']."',
                       subtitle='".$ld['subtitle']."',
                       headline='".$ld['headline']."',
                       content='".$ld['content']."',
                       content_template_id='".$ld['content_template_id']."',
                       mode='".$ld['mode']."'
                       where
                       content_box_id='".$ld['content_box_id']."'"
                      );
                      
    $ld['error'].="Content Box successfully updated.";
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
	$this->dbu->query("delete from cms_tag_library where id='".$ld['content_box_id']."' and type='1'");
    $this->dbu->query("delete from cms_content_box where content_box_id='".$ld['content_box_id']."'");
    $ld['error'].="Content has been successfully deleted.";
    return true;
}        

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;

    if(is_numeric($ld['content_template_id']))
    {
	    $this->dbu->query("select content_template_id from cms_content_template where content_template_id='".$ld['content_template_id']."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Please select a valid Content Template.<br>";
	        return false;
	    }
    }
    
    if(!$ld['title'])
    {
        $ld['error'].="Please fill in the Title Field."."<br>";
        $is_ok=false;
    }
    
   	if(!$ld['tag'])
   	{
        $ld['error'].="Please enter the Alias Tag for this Content Box!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for this Content Box! (Read Help)"."<br>";
        $is_ok=false;
   	}
    
    if(!$ld['content'])
    {
        $ld['error'].="Please enter some content in the Content field."."<br>";
        $is_ok=false;
    }
    else 
    {
    	$ld['content']=str_replace('/admin/ktmlpro/includes/ktedit/', '', $ld['content']);
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
function update_validate(&$ld)
{
    $is_ok=true;
    if (!is_numeric($ld['content_box_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select content_box_id from cms_content_box where content_box_id='".$ld['content_box_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    
    if ($ld['content_template_id'] && !is_numeric($ld['content_template_id']))
    {
        $ld['error'].="Please select a valid Content Template.<br>";
        return false;
    }
    if(is_numeric($ld['content_template_id']))
    {
	    $this->dbu->query("select content_template_id from cms_content_template where content_template_id='".$ld['content_template_id']."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Please select a valid Content Template.<br>";
	        return false;
	    }
    }
    
    if(!$ld['title'])
    {
        $ld['error'].="Please fill in the Title Field."."<br>";
        $is_ok=false;
    }
    
   	if(!$ld['tag'])
   	{
        $ld['error'].="Please enter the Alias Tag for this Content Box!"."<br>";
        $is_ok=false;
   	}
   	elseif (!secure_cms_tag($ld['tag']))
   	{
        $ld['error'].="Please enter a valid Alias Tag for this Content Box! (Read Help)"."<br>";
        $is_ok=false;
   	}
    
    if(!$ld['content'])
    {
        $ld['error'].="Please enter some content in the Content field."."<br>";
        $is_ok=false;
    }
    else 
    {
    	$ld['content']=str_replace('/admin/ktmlpro/includes/ktedit/', '', $ld['content']);
    }
    
    if($is_ok)
    {
    	if($ld['tag'])
    	{
    		$this->dbu->query("select tag_id, id, type from cms_tag_library where tag='".$ld['tag']."'");
    		if($this->dbu->move_next())
    		{
    			if($this->dbu->f('type')!=1 || $this->dbu->f('id')!=$ld['content_box_id'])
    			{
			        $ld['error'].="There is another CMS object with ".$ld['tag']." Alias Tag. Please change it."."<br>";
			        $is_ok=false;
    			}
    		}
    	}
    }
    
    return $is_ok;
}


/****************************************************************
* function delete_validate(&$ld)                                *
****************************************************************/
function delete_validate(&$ld)
{
	$is_ok=true;
    if (!is_numeric($ld['content_box_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select content_box_id from cms_content_box where content_box_id='".$ld['content_box_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
 
    return $is_ok;
}

}//end class
?>

