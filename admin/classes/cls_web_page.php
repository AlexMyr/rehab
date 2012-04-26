<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class web_page
{
  var $dbu;

function web_page()
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
	$now=strtotime("now");
    $db=new mysql_db;           
	$ld['web_page_id']=$this->dbu->query_get_id("insert into cms_web_page (
                                                                       name,
                                                                       template_id,
                                                                       page_type_id,
                                                                       title,
                                                                       keywords,
                                                                       description,
                                                                       date
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['template_id']."',
                                                                        '".$ld['page_type_id']."',
                                                                        '".$ld['title']."',
                                                                        '".$ld['keywords']."',
                                                                        '".$ld['description']."',
                                                                        '".$now."'
                                                                        )
                                                                       ");
	$i=0;
	$this->dbu->query("select * from cms_page_type_czone where page_type_id='".$ld['page_type_id']."'");
	while($this->dbu->move_next())
	{
		
		if($this->dbu->f('prefilled')==1)
		{
			$zone_name="Content ".$i;
			$db->query("insert into cms_web_page_content (
												web_page_id,
												sort_order,
												date,
												title,
												content,
												template_czone_id,
												mode
												) values (
												'".$ld['web_page_id']."',
												'0',
												'".$now."',
												'".$zone_name."',
												'".$this->dbu->f('default_data')."',
												'".$this->dbu->f('template_czone_id')."',
												'".$this->dbu->f('mode')."'
												)
						");
			$i++;
		}
		
	}
     
	$ld['error']="Web Page Succesfully added.";
	$ld['pag']='web_page_update';
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
       
    $this->dbu->query("update cms_web_page set
                       name='".$ld['name']."',
                       title='".$ld['title']."',
                       keywords='".$ld['keywords']."',
                       description='".$ld['description']."'
                       where
                       web_page_id='".$ld['web_page_id']."'"
                      );
                      
    $ld['error'].="Web Page has been successfully updated.";
	$ld['pag']='web_page_update';
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
    $this->dbu->query("delete from cms_web_page_content where web_page_id='".$ld['web_page_id']."'");
    $this->dbu->query("delete from cms_web_page where web_page_id='".$ld['web_page_id']."'");
    $ld['error'].="Web Page has been successfully deleted.";
    return true;
}        

/**********************************************************************
* function admin_update(&$ld)                                         *
**********************************************************************/
function set_home(&$ld)
	{

	   if(!$this->set_home_validate($ld))
	   	{
	   		return false;
	   	}
	   	
		$this->dbu->query("update settings set 
						   value='".$ld['web_page_id']."'
						   where
	   					   constant_name='CMS_HOME_PAGE'"
	   					);	
	   					
		$ld['error'].="Settings successfully updated.";
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
    if($ld['act']=='web_page-add')
    {
	    if(!is_numeric($ld['page_type_id']))
	    {
	        $ld['error'].="Please select the Page Type."."<br>";
	        $is_ok=false;
	    }
	    else
	    {
	    	$this->dbu->query("select page_type_id, template_id from cms_page_type where page_type_id='".$ld['page_type_id']."'");
	    	if(!$this->dbu->move_next())
	    	{
		        $ld['error'].="Invalid Page Type ID."."<br>";
		        $is_ok=false;
	    	}
	    	else 
	    	{
	    		$ld['template_id']=$this->dbu->f('template_id');
	    	}
	    }
    }
    if(!$ld['title'])
    {
        $ld['error'].="Please fill in the Meta Title field."."<br>";
        $is_ok=false;
    }
    if(!$ld['keywords'])
    {
        $ld['error'].="Please fill in the Meta Keywords field."."<br>";
        $is_ok=false;
    }
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Meta Description field."."<br>";
        $is_ok=false;
    }
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['web_page_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select web_page_id from cms_web_page where web_page_id='".$ld['web_page_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        return $this->add_validate($ld);
}


/****************************************************************
* function delete_validate(&$ld)                                *
****************************************************************/
function delete_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['web_page_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        /*
        if($ld['web_page_id'] == CMS_HOME_PAGE)
        {
            $ld['error'].="You can not delete the CMS Home Page.<br>";
            return false;
        }
        */
        $this->dbu->query("select web_page_id, no_delete from cms_web_page where web_page_id='".$ld['web_page_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
		elseif ($this->dbu->f('no_delete') == 1)
		{
            $ld['error'].="You are not allowed to delete this page. It was marked as Read / Edit Only.<br> It means that this page is important for functionality of this site, and by deleting it you would cause ireparable damage.<br>";
            return false;
		} 
        return $is_ok;
}

/****************************************************************
* function set_home_validate(&$ld)                                *
****************************************************************/
function set_home_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['web_page_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select web_page_id from cms_web_page where web_page_id='".$ld['web_page_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
 
        return $is_ok;
}

}//end class
?>

