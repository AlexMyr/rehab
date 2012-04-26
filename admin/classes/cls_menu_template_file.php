<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class menu_template_file
{
  var $dbu;

function menu_template_file()
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
                
	$ld['menu_template_file_id']=$this->dbu->query_get_id("insert into cms_menu_template_file (
                                                                       name,
                                                                       file_name,
                                                                       type
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['file_name']."',
                                                                        '".$ld['type']."'
                                                                        )
                                                                       ");
     
	$ld['error']="Menu Template File Succesfully added.";
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
       
    $this->dbu->query("update cms_menu_template_file set
                       name='".$ld['name']."',
                       file_name='".$ld['file_name']."',
                       type='".$ld['type']."'
                       where
                       menu_template_file_id='".$ld['menu_template_file_id']."'"
                      );
                      
    $ld['error'].="Menu Template File has been successfully updated.";
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
        $this->dbu->query("delete from cms_menu_template_file where menu_template_file_id='".$ld['menu_template_file_id']."'");
        $ld['error'].="Menu Template File has been successfully deleted.";
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
    return $is_ok;
}

/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['menu_template_file_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select menu_template_file_id from cms_menu_template_file where menu_template_file_id='".$ld['menu_template_file_id']."'");
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
        if (!is_numeric($ld['menu_template_file_id']))
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
        $this->dbu->query("select menu_template_file_id from cms_menu_template_file where menu_template_file_id='".$ld['menu_template_file_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Id Invalid.<br>";
            return false;
        }
 
        return $is_ok;
}

}//end class
?>

