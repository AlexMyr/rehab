<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class sys_message
{
  var $dbu;

function sys_message()
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
	$ld['sys_message_id']=$this->dbu->query_get_id("insert into sys_message (
                                                                      title,
                                                                      text,
                                                                      from_email,
                                                                      from_name,
                                                                      subject,
                                                                      name,
                                                                      description 
                                                                        )
                                                                        values
                                                                        (
                                                                        '".mysql_escape_string($ld['title'])."',
                                                                        '".mysql_escape_string($ld['text'])."',
                                                                        '".mysql_escape_string($ld['from_email'])."',                                                                        
                                                                        '".mysql_escape_string($ld['from_name'])."',
                                                                        '".mysql_escape_string($ld['subject'])."',
                                                                        '".mysql_escape_string($ld['sys_name'])."',
                                                                        '".mysql_escape_string($ld['description'])."'
                                                                        )
                                                                       ");
     
	
	$ld['error']="System Message Succesfylly added.";
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
	
       
    $this->dbu->query("update sys_message set
                       title='".mysql_escape_string($ld['title'])."',
                       text='".mysql_escape_string($ld['text'])."',
                       from_email='".mysql_escape_string($ld['from_email'])."',
                       from_name='".mysql_escape_string($ld['from_name'])."',
                       subject='".mysql_escape_string($ld['subject'])."'
                       where
                       sys_message_id='".$ld['sys_message_id']."'"
                      );                      
    $ld['error'].="System Message successfully updated.";
    return true;
}
/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;

    if(!$ld['title'])
    {
        $ld['error'].="Please fill in the Title Field."."<br>";
        $is_ok=false;
    }
    if(!$ld['text'])
    {
        $ld['error'].="Please fill in the Text Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['sys_name'])
    {
        $ld['error'].="Please fill in the Tag Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['description'])
    {
        $ld['error'].="Please fill in the Description Field."."<br>";
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
    if (!is_numeric($ld['sys_message_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select sys_message_id from sys_message where sys_message_id='".$ld['sys_message_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    
    if(!$ld['title'])
    {
        $ld['error'].="Please fill in the Title Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['text'])
    {
        $ld['error'].="Please fill in the Text Field."."<br>";
        $is_ok=false;
    }
    return $is_ok;
}

}//end class
?>

