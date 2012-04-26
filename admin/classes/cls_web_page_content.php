<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class web_page_content
{
  var $dbu;

function web_page_content()
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
	$ld['web_page_content_id']=$this->dbu->query_get_id("insert into cms_web_page_content (
                                                                       web_page_id,
                                                                       sort_order,
                                                                       date,
                                                                       title,
                                                                       subtitle,
                                                                       headline,
                                                                       content,
                                                                       template_czone_id,
                                                                       content_template_id,
                                                                       mode
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['web_page_id']."',
                                                                        '".$ld['sort_order']."',
                                                                        '".$now."',
                                                                        '".$ld['title']."',
                                                                        '".$ld['subtitle']."',
                                                                        '".$ld['headline']."',
                                                                        '".$ld['content']."',
                                                                        '".$ld['template_czone_id']."',
                                                                        '".$ld['content_template_id']."',
                                                                        '".$ld['mode']."'
                                                                        )
                                                                       ");
     
	$ld['error']="Content Succesfully added.";
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
       
    $this->dbu->query("update cms_web_page_content set
                       sort_order='".$ld['sort_order']."',
                       title='".$ld['title']."',
                       subtitle='".$ld['subtitle']."',
                       headline='".$ld['headline']."',
                       content='".$ld['content']."',
                       content_template_id='".$ld['content_template_id']."',
                       mode='".$ld['mode']."'
                       where
                       web_page_content_id='".$ld['web_page_content_id']."'"
                      );
                      
    $ld['error'].="Content successfully updated.";
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
    $this->dbu->query("delete from cms_web_page_content where web_page_content_id='".$ld['web_page_content_id']."'");
    $ld['error'].="Content has been successfully deleted.";
    return true;
}        

/****************************************************************
* function sort_order_update(&$ld)                              *
****************************************************************/
function sort_order_update(&$ld)
{
    if($ld['sort_order'])
		foreach ($ld['sort_order'] as $web_page_content_id => $sort_order)
		{
		    $this->dbu->query("update cms_web_page_content set
		                       sort_order='".$sort_order."'
		                       where
		                       web_page_content_id='".$web_page_content_id."'"
		                      );
		
		}
    return true;
}

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
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
    
    if (!is_numeric($ld['template_czone_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select template_czone_id from cms_template_czone where template_czone_id='".$ld['template_czone_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
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
    
    if($ld['sort_order'] && !is_numeric($ld['sort_order']))
    {
        $ld['error'].="Please fill in the Sort Order Field with a numeric value."."<br>";
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
    
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['web_page_content_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select web_page_content_id from cms_web_page_content where web_page_content_id='".$ld['web_page_content_id']."'");
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
        if (!is_numeric($ld['web_page_content_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select web_page_content_id from cms_web_page_content where web_page_content_id='".$ld['web_page_content_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
 
        return $is_ok;
}

}//end class
?>

