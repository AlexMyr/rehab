<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class faq
{
  var $dbu;

function faq()
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
	$ld['faq_id']=$this->dbu->query_get_id("insert into faq (
                                                                       faq_category_id,
                                                                       question,
                                                                       answer,
                                                                       sort_order
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['faq_category_id']."',
                                                                        '".$ld['question']."',
                                                                        '".$ld['answer']."',
                                                                        '".$ld['sort_order']."'
                                                                        )
                                                                       ");
     
	
	$ld['error']="FAQ Entry Succesfylly added.";
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
	
       
    $this->dbu->query("update faq set
                       faq_category_id='".$ld['faq_category_id']."',
                       question='".$ld['question']."',
                       answer='".$ld['answer']."',
                       sort_order='".$ld['sort_order']."'
                       where
                       faq_id='".$ld['faq_id']."'"
                      );
                      
    $ld['error'].="FAQ Entry successfully updated.";
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
    $this->dbu->query("delete from faq where faq_id='".$ld['faq_id']."'");
    $ld['error'].="FAQ Entry successfully deleted.";
    return true;
}        

/****************************************************************
* function sort_order_update(&$ld)                              *
****************************************************************/
function sort_order_update(&$ld)
{
    if($ld['sort_order'])
		foreach ($ld['sort_order'] as $faq_id => $sort_order)
		{
		    $this->dbu->query("update faq set
		                       sort_order='".$sort_order."'
		                       where
		                       faq_id='".$faq_id."'"
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

    if(!is_numeric($ld['faq_category_id']))
    {
        $ld['error'].="Please select the Category."."<br>";
        $is_ok=false;
    }
    if(!$ld['question'])
    {
        $ld['error'].="Please fill in the Question Field."."<br>";
        $is_ok=false;
    }
    if(!$ld['answer'])
    {
        $ld['error'].="Please fill in the Answer Field."."<br>";
        $is_ok=false;
    }
    
    if($ld['sort_order'] && !is_numeric($ld['sort_order']))
    {
        $ld['error'].="Please fill in the Sort Order Field with a numeric value."."<br>";
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
    if (!is_numeric($ld['faq_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select faq_id from faq where faq_id='".$ld['faq_id']."'");
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
    if (!is_numeric($ld['faq_id']))
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
    $this->dbu->query("select faq_id from faq where faq_id='".$ld['faq_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid ID.<br>";
        return false;
    }
 
    return $is_ok;
}

}//end class
?>

