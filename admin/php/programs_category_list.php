<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_category_list.html"));
$ft->define_dynamic('category_line','main');

$l_r = ROW_PER_PAGE;

$dbu = new mysql_db();
if(($glob['ofs']) || (is_numeric($glob['ofs'])))
{
	$glob['offset']=$glob['ofs'];
}
if((!$glob['offset']) || (!is_numeric($glob['offset'])))
{
        $offset=0;
}
else
{
        $offset=$glob['offset'];
        $ft->assign('OFFSET',$glob['offset']);
}
$filter='';
$arguments='';
//************Building the search options********************************
	$ft->assign(array(
                          "CATEGORY_DD" => build_category_list($glob['category_id'])
                     )
                );
    $ft->assign('SEARCH_KEY',$glob['search_key']);
//****************End Search Options*************************************
$filter='';
$arguments='';
if($glob['search1'])
{
	$arguments="&search1=".$glob['search1'];
	$ft->assign('SEARCH1',$glob['search1']);
		
	if($glob['category_id'])
	{
		$arguments.="&category_id=".$glob['category_id'];
		$ft->assign('CATEG_ID',$glob['category_id']);
		$category_array=get_subcategory_array($glob['category_id']);
	}
	else
	{
		$category_array=$categ_array;
	}
}
else
{
	$category_array=$categ_array;
}
$max_rows=count($category_array);
$i=0;
for($i=$offset; $i<($offset+$l_r); $i++)
{
	if($category_array)
	 {
	 	$cat_array=$category_array[$i];
		$ft->assign('CATEGORY_NAME',stripslashes($cat_array['category_name']));
		$ft->assign('SORT_ORDER',$cat_array['sort_order']);
		$ft->assign('CATEGORY_ID',$cat_array['category_id']);
		$ft->assign('SPACER',str_repeat("&nbsp;&nbsp;&nbsp;",$cat_array['category_level'])); 
		$ft->assign('PRODUCTS_LIST_LINK','index.php?pag=programs_list&category_id='.$cat_array['category_id']);
		
		if($cat_array['status'] == 1)
		{
			$ft->assign('STATUS_LINK','index.php?pag=programs_category_list&cat_id='.$cat_array['category_id'].'&act=programs_category-deactivate&offset='.$offset.$arguments);
			$ft->assign('ICON_STATUS','status_on.gif');
			$ft->assign('ICON_STATUS_ALT','Click to deactivate this Category');
			$ft->assign('JAVASCRIPT_MESSAGE','Are you sure you want to set this Category as Inactive?');
		}
		else 
		{
			$ft->assign('STATUS_LINK','index.php?pag=programs_category_list&cat_id='.$cat_array['category_id'].'&act=programs_category-activate&offset='.$offset.$arguments);
			$ft->assign('ICON_STATUS','status_off.gif');
			$ft->assign('ICON_STATUS_ALT','Click to activate this Category');
			$ft->assign('JAVASCRIPT_MESSAGE','Are you sure you want to Activate this Category?');
		}
		if($i%2==1)
		{
			$ft->assign('BG_COLOR',"#F8F9FA");
		}
		else
		{
			$ft->assign('BG_COLOR',"#FFFFFF");
		}
		
		if($offset+1==$max_rows)
		{
			$b_offset=$offset-$l_r;
		}
		else 
		{
			$b_offset=$offset;
		}
		
	    $ft->assign('EDIT_LINK','index.php?pag=programs_category_add&category_id='.$category_array[$i]['category_id'].'&offset='.$offset.$arguments);
	    $ft->assign('DELETE_LINK','index.php?pag=programs_category_list&cid='.$cat_array['category_id'].'&act=programs_category-delete&offset='.$offset.$arguments);;
		$ft->parse('CATEGORY_LINE_OUT','.category_line');
	  	if(!$category_array[$i+1])
	    {
	    	$i++;
	    	break;
	    }
	}	
}

if($i==0)
{
    unset($ft);
	return get_error_message("There are no categories in the database that match your search criteria.");
}

if($offset>=$l_r)
{
     $ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=programs_category_list&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
     $ft->assign('BACKLINK',''); 
}
if($offset+$l_r<$max_rows)
{
     $ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=programs_category_list&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
     $ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign(array(
	'MESSAGE' => $glob['error'],
	'PAG' => $glob['pag'],	
	'PAGE_TITLE' => 'Programs Category list',
));

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>