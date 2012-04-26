<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class faq_category
{
  var $dbu;

function faq_category()
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
                
	$ld['faq_category_id']=$this->dbu->query_get_id("insert into faq_category (
                                                                       name,
                                                                       active,
                                                                       sort_order,
                													   level
                														 )
                                                                        values
                                                                        (
                                                                        '".$ld['name']."',
                                                                        '".$ld['active']."',
                                                                        '".$ld['sort_order']."',
                                                                        '".$ld['level']."'
                                                                        )
                                                                       ");
     
	if(!$ld['parent_id'])
    {
    	$ld['parent_id'] = $ld['faq_category_id']; 	
    }                                             
    
    $this->dbu->query("insert into faq_category_subcategory (parent_id, faq_category_id) values ('".$ld['parent_id']."', '".$ld['faq_category_id']."')");          

    $ld['error']="Category Succesfully added.";
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
       
    $this->dbu->query("update faq_category set
                       name='".$ld['name']."',
                       active='".$ld['active']."',
                       sort_order='".$ld['sort_order']."',
                       level='".$ld['level']."'
                       where
                       faq_category_id='".$ld['faq_category_id']."'"
                      );
    if(!$ld['parent_id'])
    {
    	$ld['parent_id'] = $ld['faq_category_id']; 	
    }                                             
    
    $this->dbu->query("delete from faq_category_subcategory where faq_category_id='".$ld['faq_category_id']."'");          
    $this->dbu->query("insert into faq_category_subcategory (parent_id, faq_category_id) values ('".$ld['parent_id']."', '".$ld['faq_category_id']."')");          
    $ld['error'].="Category has been successfully updated.";
    return true;
}


/****************************************************************
* function sort_order_update(&$ld)                              *
****************************************************************/
function sort_order_update(&$ld)
{
    if($ld['sort_order'])
		foreach ($ld['sort_order'] as $faq_category_id => $sort_order)
		{
		    $this->dbu->query("update faq_category set
		                       sort_order='".$sort_order."'
		                       where
		                       faq_category_id='".$faq_category_id."'"
		                      );
		
		}
    return true;
}


/****************************************************************
* function delete(&$ld)                                         *
****************************************************************/
function delete(&$ld)
{
	global  $script_path;
	
        if(!$this->delete_validate($ld))
        {
                return false;
        }
        $this->dbu->query("delete from faq_category_subcategory where faq_category_id='".$ld['faq_category_id']."'");
        $this->dbu->query("delete from faq_category where faq_category_id='".$ld['faq_category_id']."'");
        $ld['error'].="Category has been successfully deleted.";
        return true;
}        

/****************************************************************
* function activate(&$ld)                                       *
****************************************************************/
function activate(&$ld)
{
	if(!$this->activate_validate($ld))
	{
		return false;
	}
       
    $this->dbu->query("update faq_category set
                       active='1'
                       where
                       faq_category_id='".$ld['cat_id']."'"
                      );
    //$ld['error'].="Category has been successfully Activated.";
    return true;
}

/****************************************************************
* function deactivate(&$ld)                                       *
****************************************************************/
function deactivate(&$ld)
{
	if(!$this->activate_validate($ld))
	{
		return false;
	}
       
    $this->dbu->query("update faq_category set
                       active='0'
                       where
                       faq_category_id='".$ld['cat_id']."'"
                      );
    //$ld['error'].="Category has been turned Inactive.";
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
    if($ld['sort_order']&& !is_numeric($ld['sort_order']))
    {
        $ld['error'].="Please fill in the Sort Order field with a numeric value."."<br>";
        $is_ok=false;
    }
	if($ld['parent_id'])
	{
		$ld['level']=1;
	}
	else 
	{
		$ld['parent_id']=0;
		$ld['level']=0;
	}
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['faq_category_id']))
        {
            $ld['error'].="Invalid ID.<br>";
            return false;
        }
        $this->dbu->query("select faq_category_id from faq_category where faq_category_id='".$ld['faq_category_id']."'");
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
        if (!is_numeric($ld['faq_category_id']))
        {
            $ld['error'].="Invalid Id.<br>";
            return false;
        }
        $this->dbu->query("select faq_category_id from faq_category where faq_category_id='".$ld['faq_category_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid Id.<br>";
            return false;
        }

        $this->dbu->query("select faq_category_id from faq_category_subcategory where parent_id='".$ld['faq_category_id']."' and faq_category_id!='".$ld['faq_category_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="This Category has Subcategories. Please delete them first.<br>";
            return false;
        }

        $this->dbu->query("select faq_id from faq where faq_category_id='".$ld['faq_category_id']."'");
        if($this->dbu->move_next())
        {
            $ld['error'].="There are Faq's from this Faq Category.<br> Please delete or edit them first.<br>";
            return false;
        }

 
        return $is_ok;
}

/****************************************************************
* function activate_validate(&$ld)                              *
****************************************************************/
function activate_validate(&$ld)
{
            $is_ok=true;
        if (!is_numeric($ld['cat_id']))
        {
            $ld['error'].="Invalid Id.<br>";
            return false;
        }
        $this->dbu->query("select faq_category_id from faq_category where faq_category_id='".$ld['cat_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="Invalid Id.<br>";
            return false;
        }
        return $is_ok;
}

}//end class
?>

