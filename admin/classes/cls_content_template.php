<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class content_template
{
  var $dbu;

function content_template()
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
                
	$ld['content_template_id']=$this->dbu->query_get_id("insert into cms_content_template (
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
     
	$ld['error']="Content Box Template Succesfully added.";
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
       
    $this->dbu->query("update cms_content_template set
                       name='".$ld['name']."',
                       file_name='".$ld['file_name']."',
                       description='".$ld['description']."'
                       where
                       content_template_id='".$ld['content_template_id']."'"
                      );
                      
    $ld['error'].="Content Box Template has been successfully updated.";
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
    $this->dbu->query("delete from cms_web_page_content where content_template_id='".$ld['content_template_id']."'");
    $this->dbu->query("delete from cms_content_box where content_template_id='".$ld['content_template_id']."'");
    $this->dbu->query("delete from cms_content_template where content_template_id='".$ld['content_template_id']."'");
    $ld['error'].="Content Box Template has been successfully deleted.";
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
        if (!is_numeric($ld['content_template_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select content_template_id from cms_content_template where content_template_id='".$ld['content_template_id']."'");
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
        if (!is_numeric($ld['content_template_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select content_template_id from cms_content_template where content_template_id='".$ld['content_template_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }

        $this->dbu->query("select content_template_id from cms_web_page_content where content_template_id='".$ld['content_template_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Content Parts built with this Template. Please delete or edit them first.<br>";
            return false;
        }

        $this->dbu->query("select content_template_id from cms_content_box where content_template_id='".$ld['content_template_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Content Boxes built with this Template. Please delete or edit them first.<br>";
            return false;
        }

 
        return $is_ok;
}

}//end class
?>

