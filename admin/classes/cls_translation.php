<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class translation
{
  var $dbu;

function translation()
{
    $this->dbu=new mysql_db;
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
	
    $db=new mysql_db;
    foreach($ld['tag_text'] as $tag_id => $text){
        $this->dbu->query("UPDATE tmpl_translate_".$ld['lang']."
                            SET tag_text='".$text."'
                            WHERE tag_id='".$tag_id."'");
    }

	$ld['error']="Translation Was Succesfully updated.";
    return true;
}

function meta_update(&$ld)
{
	if(!$this->meta_update_validate($ld))
	{
		return false;
	}
	
    $db=new mysql_db;
    $this->dbu->query("UPDATE meta_translate_".$ld['lang']."
                        SET title='".$ld['title']."', keywords='".$ld['keywords']."', description='".$ld['description']."'
                        WHERE page_id='".$ld['page_id']."'");

	$ld['error']="Translation Meta Was Succesfully updated.";
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

/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
    if (count($ld['tag_text']) < 1)
    {
        $ld['error'].="There are no tags.<br>";
        return false;
    }
    if(!isset($ld['lang']) && $ld['lang'] == '' OR  !isset($ld['template_file']) && $ld['template_file'] == '')
    {
        $ld['error'].="Wrong parameters.<br>";
        return false;
    }
    return true;
}
function meta_update_validate(&$ld)
{
    if (!isset($ld['title']) || !isset($ld['keywords']) || !isset($ld['description']) || !isset($ld['page_id']))
    {
        $ld['error'].="Some fields are missing.<br>";
        return false;
    }
    if(!isset($ld['lang']) && $ld['lang'] == '' OR !is_numeric($ld['page_id']))
    {
        $ld['error'].="Wrong parameters.<br>";
        return false;
    }
    return true;
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

}//end class
?>

