<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class template
{
  var $dbu;

function template()
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
                
	$ld['template_id']=$this->dbu->query_get_id("insert into cms_template (
                                                                       name,
                                                                       file_name,
                                                                       description
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['file_name']."',
                                                                        '".$ld['description']."'
                                                                        )
                                                                       ");
     
	$ld['error']="Web Page Template Succesfully added.";
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
       
    $this->dbu->query("update cms_template set
                       name='".$ld['name']."',
                       file_name='".$ld['file_name']."',
                       description='".$ld['description']."'
                       where
                       template_id='".$ld['template_id']."'"
                      );
                      
    $ld['error'].="Web Page Template has been successfully updated.";
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
	
		$db=new mysql_db;
        if(!$this->delete_validate($ld))
        {
                return false;
        }
        $this->dbu->query("select template_czone_id from cms_template_czone where template_id='".$ld['template_id']."'");
        while($this->dbu->move_next())
        {
        	$db->query("delete from cms_page_type_czone where template_czone_id='".$db->f('template_czone_id')."'");
        	$db->query("delete from cms_web_page_content where template_czone_id='".$db->f('template_czone_id')."'");
               
        }
        $this->dbu->query("delete from cms_template_czone where template_id='".$ld['template_id']."'");
        $this->dbu->query("delete from cms_page_type where template_id='".$ld['template_id']."'");
        $this->dbu->query("delete from cms_web_page where template_id='".$ld['template_id']."'");
        $this->dbu->query("delete from cms_template where template_id='".$ld['template_id']."'");
        $ld['error'].="Web Page Template has been successfully deleted.";
        return true;
}        

/****************************************************************
* function czone_add(&$ld)                                      *
****************************************************************/

function czone_add(&$ld)
{
	$db=new mysql_db;
	if(!$this->czone_add_validate($ld))
	{
		return false;
	}
                
	$ld['template_czone_id']=$this->dbu->query_get_id("insert into cms_template_czone (
                                                                       template_id,
                                                                       name,
                                                                       tag,
                                                                       description
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['template_id']."',
                                                                        '".$ld['name']."',
                                                                        '".$ld['tag']."',
                                                                        '".$ld['description']."'
                                                                        )
                                                                       ");
    $this->dbu->query("select * from cms_page_type where template_id='".$ld['template_id']."'");
    while($this->dbu->move_next())
    {
		$db->query("insert into cms_page_type_czone (
											page_type_id,
											template_czone_id,
											default_data,
											mode
											) values (
											'".$this->dbu->f('page_type_id')."',
											'".$ld['template_czone_id']."',
											'',
											'3'
											)
					");
    }
	$ld['error']="Web Page Template Content Zone Succesfully added.";
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
       
    $this->dbu->query("update cms_template_czone set
                       template_id='".$ld['template_id']."',
                       name='".$ld['name']."',
                       tag='".$ld['tag']."',
                       description='".$ld['description']."'
                       where
                       template_czone_id='".$ld['template_czone_id']."'"
                      );
                      
    $ld['error'].="Web Page Template Content Zone has been successfully updated.";
    return true;
}

/****************************************************************
* function czone_delete(&$ld)                                   *
****************************************************************/
function czone_delete(&$ld)
{
		$db=new mysql_db;
        if(!$this->czone_delete_validate($ld))
        {
                return false;
        }
        
       	$this->dbu->query("delete from cms_page_type_czone where template_czone_id='".$ld['template_czone_id']."'");
       	$this->dbu->query("delete from cms_web_page_content where template_czone_id='".$ld['template_czone_id']."'");
        $this->dbu->query("delete from cms_template_czone where template_czone_id='".$ld['template_czone_id']."'");

        $ld['error'].="Web Page Template Content Zone has been successfully deleted.";
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
    if(!$ld['file_name'])
    {
        $ld['error'].="Please fill in the File Name field."."<br>";
        $is_ok=false;
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
        if (!is_numeric($ld['template_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select template_id from cms_template where template_id='".$ld['template_id']."'");
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
        if (!is_numeric($ld['template_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select template_id from cms_template where template_id='".$ld['template_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }

        $this->dbu->query("select template_id from cms_page_type where template_id='".$ld['template_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Page Types defined with this Template.<br> Please delete or edit them first.<br>";
            return false;
        }

        $this->dbu->query("select template_id from cms_web_page where template_id='".$ld['template_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Web Pages built with this template.<br> Please delete or edit them first.<br>";
            return false;
        }

 
        return $is_ok;
}

/****************************************************************
* function czone_add_validate(&$ld)                             *
****************************************************************/

function czone_add_validate(&$ld)
{
    $is_ok=true;
    if (!is_numeric($ld['template_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    
    $this->dbu->query("select template_id from cms_template where template_id='".$ld['template_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    
    if(!$ld['name'])
    {
        $ld['error'].="Please fill in the Name field."."<br>";
        $is_ok=false;
    }
    if(!$ld['tag'])
    {
        $ld['error'].="Please fill in the Tag field."."<br>";
        $is_ok=false;
    }
    elseif (!secure_string_no_spaces($ld['tag']))
    {
        $ld['error'].="Please fill in the Tag field with valid data (no spaces allowed)."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Description field."."<br>";
        $is_ok=false;
    }
    return $is_ok;
}


/****************************************************************
* function czone_update_validate(&$ld)                          *
****************************************************************/
function czone_update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['template_id']))
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
        return $this->czone_add_validate($ld);
}


/****************************************************************
* function czone_delete_validate(&$ld)                         *
****************************************************************/
function czone_delete_validate(&$ld)
{
        $is_ok=true;
        /*if (!is_numeric($ld['template_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select template_id from cms_template where template_id='".$ld['template_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
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


        $this->dbu->query("select template_czone_id from cms_web_page_content where template_czone_id='".$ld['template_czone_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Web Pages built with this template that are using this Content Zone.<br> Please delete or edit them first.<br>";
            return false;
        }

 */
        return $is_ok;
}

}//end class
?>

