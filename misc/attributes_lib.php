<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
//////////////////////////////////////////////////////////////////////////////////////
// Options
//////////////////////////////////////////////////////////////////////////////////////


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function build_option_type_list($selected)
{

	$opt=array(

            	1	=> 'Single Selection Allowed',
            	2	=> 'Multiple Selection Allowed'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.='<input name="type" type="radio" value="'.$key.'"';
		$out_str.=($key==$selected?" CHECKED ":"");//if selected
		$out_str.=">".$val."<br>";//options names
	}
	return $out_str;

}



//////////////////////////////////////////////////////////////////////////////////////
// Attributes
//////////////////////////////////////////////////////////////////////////////////////
/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function build_values_allowed_list($selected)
{

	$opt=array(

            	1	=> 'Characters',
            	2	=> 'Numeric'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.="<option value=\"".$key."\" ";//options values
		$out_str.=($key==$selected?" SELECTED ":"");//if selected
		$out_str.=">".$val."</option>";//options names
	}
	return $out_str;
}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
function get_values_allowed($selected)
{

	$opt=array(

            	1	=> 'Characters',
            	2	=> 'Numeric'
  			  );	
  	$out_str=$opt[$selected];		  		
	return $out_str;
}



/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function build_apply_to_list($selected)
{

	$opt=array(

            	1	=> 'All Products',
            	2	=> 'Several Categories (will select in the next step)'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.='<input name="apply_to" type="radio" value="'.$key.'"';
		$out_str.=($key==$selected?" CHECKED ":"");//if selected
		$out_str.=">".$val."<br>";//options names
	}
	return $out_str;

}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function build_where_show_list($selected)
{

	$opt=array(

            	1	=> 'Internal Note - for Admin Only',
            	2	=> 'Show Only on Product List Pages',
            	3	=> 'Show on Product List Pages and on Product Description Page',
            	4	=> 'Show Only on Product Description Page'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.='<input name="where_show" type="radio" value="'.$key.'"';
		$out_str.=($key==$selected?" CHECKED ":"");//if selected
		$out_str.=">".$val."<br>";//options names
	}
	return $out_str;

}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function build_mandatory_optional_list($selected)
{

	$opt=array(

            	2	=> 'Mandatory',
            	1	=> 'Optional'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.='<input name="mandatory" type="radio" value="'.$key.'"';
		$out_str.=($key==$selected?" CHECKED ":"");//if selected
		$out_str.=">".$val."<br>";//options names
	}
	return $out_str;

}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function get_mandatory_optional($selected)
{
	$out_str = '';
	$opt=array(

            	2	=> 'Mandatory',
            	1	=> 'Optional'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		if($key == $selected)
		{
			$out_str=$val;
		}
	}
	return $out_str;

}

/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function build_attribute_type_list($selected)
{

	$opt=array(

            	1	=> 'Single Checkbox',
            	2	=> 'Checkboxes Block',
            	3	=> 'Radio Buttons',
            	4	=> 'Drop Down Box',
            	5	=> 'Text Field',
            	6	=> 'Text Area Small (for short descriptions)', 
            	7	=> 'Text Area Big (for detailed descriptions)', 
            	8	=> 'HTML'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		$out_str.='<input name="type" type="radio" value="'.$key.'"';
		$out_str.=($key==$selected?" CHECKED ":"");//if selected
		$out_str.=">".$val."<br>";//options names
	}
	return $out_str;

}


/**
 * @return unknown
 * @param $selected unknown
 * @desc Enter description here...
 */
 
function get_attribute_type($selected)
{
	$out_str = '';
	$opt=array(

            	1	=> 'Single Checkbox',
            	2	=> 'Checkboxes Block',
            	3	=> 'Radio Buttons',
            	4	=> 'Drop Down Box',
            	5	=> 'Text Field',
            	6	=> 'Text Area', 
            	7	=> 'HTML'
  			  );			
    while (list ($key, $val) = each ($opt)) 
	{
		if($key == $selected)
		{
			$out_str=$val;
		}
	}
	return $out_str;

}


?>