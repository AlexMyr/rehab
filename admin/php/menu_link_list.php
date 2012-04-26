<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "menu_link_list.html"));
$ft->define_dynamic('menu_link_row','main');
$l_r=ROW_PER_PAGE;

if(!is_numeric($glob['menu_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

$dbu=new mysql_db;
$dbu->query("select name from cms_menu where menu_id=".$glob['menu_id']);
if($dbu->move_next())
{
	$menu_name=$dbu->f('name');
}
else 
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

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

//************Building the search options********************************
	$ft->assign(array(
                          "MENU_LIST"      =>        build_cms_menu_list($glob['menu_id'])
                     )
                );
//****************End Search Options*************************************
$filter='';
$arguments='&menu_id='.$glob['menu_id'];

build_menu_link_blank_array($glob['menu_id']);

$max_rows=count($menu_link_array);

$i=0;

for($i=$offset; $i<($offset+$l_r); $i++)
if($menu_link_array)
{
	$men_array=$menu_link_array[$i];
	$ft->assign('NAME',$men_array['name']);
	$ft->assign('SORT_ORDER',$men_array['sort_order']);
	$ft->assign('MENU_LINK_ID',$men_array['menu_link_id']);
	$ft->assign('SPACER',str_repeat("&nbsp;&nbsp;&nbsp;",$men_array['level'])); 
	$ft->assign('FOLLOW_LINK',$men_array['url']);
	
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
	
    $ft->assign('EDIT_LINK',"index.php?pag=menu_link_add&menu_link_id=".$men_array['menu_link_id']);
    $ft->assign('DELETE_LINK',"index.php?pag=menu_link_list&act=menu_link-delete&menu_link_id=".$men_array['menu_link_id']."&offset=".$b_offset.$arguments);
    $ft->parse('link_ROW_OUT','.menu_link_row');
    if(!$menu_link_array[$i+1])
    {
    	$i++;
    	break;
    }
}

             
if($i==0)
{
    unset($ft);
	return get_error_message("There are no menu links in the database for this menu.");
}

if($offset>=$l_r)
{
     $ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=".$glob['pag']."&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
     $ft->assign('BACKLINK',''); 
}
if($offset+$l_r<$max_rows)
{
     $ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=".$glob['pag']."&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
     $ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('MENU_ID',$glob['menu_id']);
$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',"List Of Menu Links - ".$menu_name);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','menu_link_row');


return $ft->fetch('content');



?>
