<?php

function get_faq_subcategory_comma_list($main_category_id)
{
	global $faq_categ_array;
	
	$m_cat_level=-2;
	$out=$main_category_id;
	if($faq_categ_array)
	foreach ($faq_categ_array as $key=>$cat_array)
	{   
		if($cat_array['faq_category_id']==$main_category_id) // we have found what we are looking for
	    {
	    	$m_cat_level=$cat_array['level'];
	    }
	    
	    if($m_cat_level != -2)
	    {
	    	if($m_cat_level < $cat_array['level'])
	    	{
	    		
	    		$out.=", ".$cat_array['faq_category_id'];
	    	}
	    	elseif ($cat_array['faq_category_id'] != $main_category_id)
	    	{
	    		$m_cat_level=-2;
	    	}
	    }
	}
        
    return $out;
}



//**Build Category Dropdown Functions****************************\\
//****************************************************************
//****************************************************************
//****************************************************************

function build_faq_category_list_blank() //use this to generate the global categ_array
{
	global $faq_categ_array;
	
	$old_faq_category_array=build_faq_categ_list_array($excluded);

	if($old_faq_category_array)
		$faq_category_array=sort_faq_categ_array($old_faq_category_array);
		
	$faq_categ_array=$faq_category_array;
	return true;
}

function build_faq_category_list($selected, $excluded=0)
{
	global $faq_categ_array;
	$old_faq_category_array=build_faq_categ_list_array($excluded);

	if($old_faq_category_array)
		$faq_category_array=sort_faq_categ_array($old_faq_category_array);
		
	$out_str="";
	if($faq_category_array)
	foreach ($faq_category_array as $key=>$faq_cat_array)
	{
	    $out_str.="<option value=\"".$faq_cat_array['faq_category_id']."\" ";//options values
        $out_str.=($faq_cat_array['faq_category_id']==$selected?" SELECTED ":"");//if selected
        $out_str.=">".str_repeat("&nbsp;&nbsp;",$faq_cat_array['level']).$faq_cat_array['name']."</option>";//options names
	}
	
	$faq_categ_array=$faq_category_array;
	return $out_str;
}

function build_faq_categ_list_array($excluded=0)
{
	
    $db=new mysql_db;
    $db->query("select faq_category.name, faq_category.faq_category_id, faq_category.active, faq_category.sort_order, faq_category_subcategory.parent_id from faq_category 
        			inner join faq_category_subcategory on faq_category.faq_category_id = faq_category_subcategory.faq_category_id
        		    where faq_category.faq_category_id!='".$excluded."' and faq_category_subcategory.parent_id!='".$excluded."'
        			order by faq_category.sort_order, faq_category.name ");
        
    $out=array();
    while($db->move_next())
    {
        if($db->f('parent_id') != $db->f('faq_category_id'))
        {
        	$parent=$db->f('parent_id');
        }
        else
        {
        	$parent=0;
        }
        
        	$out['faq_category_id'][$db->f('faq_category_id')]=$db->f('faq_category_id');
			$out[$db->f('faq_category_id')]['faq_category_id']=$db->f('faq_category_id');
			$out[$db->f('faq_category_id')]['parent']=$parent;
			$out[$db->f('faq_category_id')]['status']=$db->f('active');
			$out[$db->f('faq_category_id')]['sort_order']=$db->f('sort_order');
			$out[$db->f('faq_category_id')]['name']=$db->f('name');
    }
     
    return $out;
}

function sort_faq_categ_array($faq_category_array)
{
	
	$faq_category_level='';
	$level=0;
	$excluded='0';
	$out='';
	$i=0;
	foreach ($faq_category_array['faq_category_id'] as $faq_category_id => $categ_id)
	{
		if($faq_category_array[$faq_category_id]['parent']==0)
		{
			$out[$i]['faq_category_id']=$faq_category_id;
			$out[$i]['level']=$level;
			$out[$i]['name']=$faq_category_array[$faq_category_id]['name'];
			$out[$i]['status']=$faq_category_array[$faq_category_id]['status'];
			$out[$i]['sort_order']=$faq_category_array[$faq_category_id]['sort_order'];
			$out[$i]['parent']=$faq_category_array[$faq_category_id]['parent'];
			$i++;

			if(faq_subcateg_exist($faq_category_array, $faq_category_id, $excluded))
			{
				$faq_subcateg_exist=true;
			}
			else 
			{
				$faq_subcateg_exist=false;
			}
			
			$faq_category_level[$level]=$level;
			$parent_id=$faq_category_id;
			
			$excluded.=",".$parent_id;
			
			while($faq_subcateg_exist)
			{
				$result=get_faq_subcateg_array($faq_category_array, $parent_id, $excluded);
	            if($result)
	            {
	            	$faq_category_level[$level]=$parent_id;
	            	$level++;
	            		
					$out[$i]['faq_category_id']=$result['out_id'];
					$out[$i]['level']=$level;
					$out[$i]['name']=$faq_category_array[$result['out_id']]['name'];
					$out[$i]['status']=$faq_category_array[$result['out_id']]['status'];
					$out[$i]['sort_order']=$faq_category_array[$result['out_id']]['sort_order'];
					$out[$i]['parent']=$faq_category_array[$result['out_id']]['parent'];
					$i++;
			
	            	$parent_id=$result['next_parent'];
		           	$excluded.=",".$result['next_parent'];
	            }
		        else 
		        {
		           	$level--;
		           	$parent_id=$faq_category_level[$level];
		        }
		        if($level < 0)
		        {
		        	$faq_subcateg_exist=false;
		           	$level=0;
		        }
			}		
				
		}
	}
	
    return $out;
}


function get_faq_subcateg_array($faq_category_array, $parent_id, $excluded=0)
{
	$out_id="";
	$next_parent="";
	$prev_parent="";
	$exclude_keys = split(",",$excluded);
	foreach ($exclude_keys as $key=>$value)
	{
		$exclude[$value]=1;
	}
	foreach ($faq_category_array['faq_category_id'] as $c_key => $c_id)
	{
        if(!$exclude[$c_key] && ($faq_category_array[$c_id]['parent'] == $parent_id))
        {
        	$return['out_id']=$c_id;
			$return['next_parent']=$c_id;
			$return['prev_parent']=$parent_id;
			return $return;
        }
	}
		
	return false;
}


function faq_subcateg_exist($faq_category_array, $parent_id, $excluded=0)
{
	$exclude_keys = split(",",$excluded);
	foreach ($exclude_keys as $key=>$value)
	{
		$exclude[$value]=1;
	}

	foreach ($faq_category_array['faq_category_id'] as $c_key => $c_id)
	{
        if(!$exclude[$c_key] && ($faq_category_array[$c_id]['parent'] == $parent_id))
        {
        	
			return true;
        }
	}
	
	return false;
}


?>