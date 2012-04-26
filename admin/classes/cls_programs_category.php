<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class programs_category
{
  var $dbu;

	function programs_category()
	{
	    $this->dbu=new mysql_db;
	}
	
	function add(&$ld)
	{
		if(!$this->add_validate($ld))
		{
			return false;
		}
				
		$ld['category_id']=$this->dbu->query_get_id("
								INSERT INTO 
											programs_category 
								SET 
											category_name = '".$ld['category_name']."', 
											active = '".$ld['active']."',
											sort_order ='".$ld['sort_order']."',
											category_level = '".$ld['category_level']."'
											");

		if(!$ld['parent_id'])
	    {
	    	$ld['parent_id'] = $ld['category_id'];
	    }

	    $this->dbu->query("INSERT INTO programs_category_subcategory (parent_id, category_id) VALUES ('".$ld['parent_id']."', '".$ld['category_id']."')");

	    $ld['error']="Category Created Successfully.";
	
	    return true;
	}
	
	function add_validate(&$ld)
	{
		$is_ok = true;
		
		if(!$ld['category_name'] )
		{
			$ld['error']="Please fill in the category first.";
			$is_ok = false;
		}
		if ($ld['sort_order'] && !is_numeric($ld['sort_order'])) 
		{
			$ld['error']="Please fill in just with numbers";
			$is_ok = false;
		}
		if ($ld['parent_id']) 
		{
			$ld['category_level']=1;	
		}
		else
		{
			$ld['parent_id']=0;
			$ld['category_level']=0;
		}
		
		return $is_ok;
	}
	
	function update(&$ld)
	{
		if(!$this->update_validate($ld))
		{
			return false;
		}
		
	    $this->dbu->query("
	    					UPDATE 
									programs_category 
	    					SET
									category_name='".$ld['category_name']."',
									active='".$ld['active']."',
									sort_order='".$ld['sort_order']."',
									category_level='".$ld['category_level']."'
							WHERE
									category_id='".$ld['category_id']."'
						");
	    if(!$ld['parent_id'])
	    {
	    	$ld['parent_id'] = $ld['category_id'];
	    }

	    $this->dbu->query("DELETE FROM programs_category_subcategory WHERE category_id='".$ld['category_id']."'");
	    $this->dbu->query("INSERT INTO programs_category_subcategory (parent_id, category_id) VALUES ('".$ld['parent_id']."', '".$ld['category_id']."')");
		
		$ld['error']="Category Succesfully Updated.";
	
	    return true;
	}
	
	function update_validate(&$ld)
	{
	       $is_ok=true;
	       if (!is_numeric($ld['category_id']))
	       {
	           $ld['error'].="Invalid ID.<br>";
	           return false;
	       }
	       $this->dbu->query("SELECT category_id FROM programs_category WHERE category_id='".$ld['category_id']."'");
	       if(!$this->dbu->move_next())
	       {
	           $ld['error'].="Invalid ID.<br>";
	           return false;
	       }
	       return $this->add_validate($ld);
	}
		
	function delete(&$ld)
	{
		global $script_path;
		if(!$this->delete_validate($ld))
		{
			return false;
		}
	        $this->dbu->query("DELETE FROM programs_category_subcategory WHERE category_id='".$ld['cid']."'");
	        $this->dbu->query("DELETE FROM programs_category WHERE category_id='".$ld['cid']."'");
	        $this->dbu->query("DELETE FROM programs WHERE category_id='".$ld['cid']."'");
	        $ld['error'].="Category succesfully deleted.";
	
	    return true;
	}
	
	function delete_validate(&$ld)
	{
		$is_ok = true;
	        if (!is_numeric($ld['cid']))
	        {
	            $ld['error'].="Id Invalid.<br>";
	            return false;
	        }
	        $this->dbu->query("SELECT category_id FROM programs_category WHERE category_id='".$ld['cid']."'");
	        if(!$this->dbu->move_next())
	        {
	            $ld['error'].="Id Invalid.<br>";
	            return false;
	        }

	        $this->dbu->query("SELECT category_id FROM programs_category_subcategory WHERE parent_id='".$ld['cid']."' AND category_id!='".$ld['cid']."'");
	        if($this->dbu->move_next())
	        {
	            $ld['error'].="This category has subcategories. Please delete them first.<br>";
	            return false;
	        }
		
		return $is_ok;
	}

	function sort_order_update(&$ld)
	{
	    if($ld['sort_order'])
	    {
			foreach ($ld['sort_order'] as $category_id => $sort_order)
			{
			    $this->dbu->query("UPDATE programs_category SET sort_order='".$sort_order."' WHERE category_id='".$category_id."'");
			}
	    }
	    return true;
	}

	function activate(&$ld)
	{
		if(!$this->activate_validate($ld))
		{
			return false;
		}

	    $this->dbu->query("UPDATE programs_category SET active='1' WHERE category_id='".$ld['cat_id']."'");
	    $ld['error'].="Category has been successfully Activated.";
	    return true;
	}

	function deactivate(&$ld)
	{
		if(!$this->activate_validate($ld))
		{
			return false;
		}

	    $this->dbu->query("UPDATE programs_category SET active='0' WHERE category_id='".$ld['cat_id']."'");
	    $ld['error'].="Category has been successfully Deactived.";
	    return true;
	}

	function activate_validate(&$ld)
	{
	   $is_ok=true;
	   if (!is_numeric($ld['cat_id']))
	   {
	     $ld['error'].="Id Invalid.<br>";
	     return false;
	   }
	     $this->dbu->query("SELECT category_id FROM programs_category WHERE category_id='".$ld['cat_id']."'");
	     if(!$this->dbu->move_next())
	     {
	        $ld['error'].="Id Invalid.<br>";
	        return false;
	     }
	  return $is_ok;
	}
	
}//end class

?>