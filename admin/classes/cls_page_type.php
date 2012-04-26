<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class page_type
{
  var $dbu;

function page_type()
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
	$ld['page_type_id']=$this->dbu->query_get_id("insert into cms_page_type (
                                                                       name,
                                                                       template_id,
                                                                       description
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['template_id']."',
                                                                        '".$ld['description']."'
                                                                        )
                                                                       ");
	$this->dbu->query("select * from cms_template_czone where template_id='".$ld['template_id']."'");
	while($this->dbu->move_next())
	{
		$db->query("insert into cms_page_type_czone (
											page_type_id,
											template_czone_id,
											default_data,
											mode
											) values (
											'".$ld['page_type_id']."',
											'".$this->dbu->f('template_czone_id')."',
											'',
											'2'
											)
					");
	}
     
	$ld['error']="Page Type Succesfully added.";
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
       
    $this->dbu->query("update cms_page_type set
                       name='".$ld['name']."',
                       description='".$ld['description']."'
                       where
                       page_type_id='".$ld['page_type_id']."'"
                      );
                      
    $ld['error'].="Page Type has been successfully updated.";
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
    $this->dbu->query("delete from cms_page_type_czone where page_type_id='".$ld['page_type_id']."'");
    $this->dbu->query("delete from cms_web_page where page_type_id='".$ld['page_type_id']."'");
    $this->dbu->query("delete from cms_page_type where page_type_id='".$ld['page_type_id']."'");
    $ld['error'].="Page Type has been successfully deleted.";
    return true;
}        

/****************************************************************
* function czone_update(&$ld)                                   *
****************************************************************/
function czone_update(&$ld)
{
	if(!$this->czone_update_validate($ld))
	{
		return false;
	}
       
    $this->dbu->query("update cms_page_type_czone set
                       default_data='".$ld['default_data']."',
    				   prefilled='1',
    				   mode='".$ld['mode']."'
                       where
                       page_type_czone_id='".$ld['page_type_czone_id']."'"
                      );
                      
    $ld['error'].="Page Type Content Zone has been successfully updated.";
    return true;
}

/****************************************************************
* function czone_empty(&$ld)                                    *
****************************************************************/
function czone_empty(&$ld)
{
	if(!$this->czone_empty_validate($ld))
	{
		return false;
	}
       
    $this->dbu->query("update cms_page_type_czone set
                       default_data='',
    				   prefilled='0',
    				   mode='3'
                       where
                       page_type_czone_id='".$ld['page_type_czone_id']."'"
                      );
                      
    $ld['error'].="Page Type Content Zone has been successfully updated.";
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
    if($ld['act']=='page_type-add')
    {
	    if(!is_numeric($ld['template_id']))
	    {
	        $ld['error'].="Please select the Web Page Template."."<br>";
	        $is_ok=false;
	    }
	    else
	    {
	    	$this->dbu->query("select template_id from cms_template where template_id='".$ld['template_id']."'");
	    	if(!$this->dbu->move_next())
	    	{
		        $ld['error'].="Invalid Web Page Template ID."."<br>";
		        $is_ok=false;
	    	}
	    }
    }
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Description field."."<br>";
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
        if (!is_numeric($ld['page_type_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select page_type_id from cms_page_type where page_type_id='".$ld['page_type_id']."'");
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
        if (!is_numeric($ld['page_type_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select page_type_id from cms_page_type where page_type_id='".$ld['page_type_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }

        $this->dbu->query("select page_type_id from cms_web_page where page_type_id='".$ld['page_type_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Web Pages built with this Page Type.<br> Please delete them first.<br>";
            return false;
        }

 
        return $is_ok;
}

/****************************************************************
* function czone_update_validate(&$ld)                          *
****************************************************************/
function czone_update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['page_type_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        if (!is_numeric($ld['page_type_czone_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select page_type_czone_id from cms_page_type_czone where page_type_czone_id='".$ld['page_type_czone_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        return $is_ok;
}

/****************************************************************
* function czone_empty_validate(&$ld)                           *
****************************************************************/
function czone_empty_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['page_type_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        if (!is_numeric($ld['page_type_czone_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select page_type_czone_id from cms_page_type_czone where page_type_czone_id='".$ld['page_type_czone_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        return $is_ok;
}

}//end class
?>

