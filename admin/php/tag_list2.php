<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "tag_list2.html"));
$ft->define_dynamic('tag_row','main');

if(!is_numeric($glob['type']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

switch ($glob['type'])
{
	case 1:
	{
		$page_term="Content Boxes";
		$edit_page="cbox_add";
		$id_term="content_box_id";
	} break;
	
	case 2:
	{
		$page_term="Site Menus";
		$edit_page="menu_add";
		$id_term="menu_id";
	} break;
	
	case 3:
	{
		$page_term="Dynamic Boxes";
		$edit_page="action_box_add";
		$id_term="tag_id";
	} break;
}
$ft->assign('TYPE_LIST', build_tag_type_list($glob['type']));

$dbu=new mysql_db;
$l_r=ROW_PER_PAGE;

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
}

$filter='';
$arguments='';

$arguments.='&type='.$glob['type'];

$dbu->query("select * from cms_tag_library where type='".$glob['type']."' order by name");

$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;

while($dbu->move_next()&&$i<$l_r)
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('TAG',$dbu->f('tag'));

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
	
	if($glob['type']!=3)
	{
    	$ft->assign('EDIT_LINK','<a href="'."index.php?pag=".$edit_page."&".$id_term."=".$dbu->f('id').'" class="verdanaSlimLinkRed">Update</a>');
	}
	else 
	{
		$ft->assign('EDIT_LINK',"-");
	}
	
    $ft->parse('tag_ROW_OUT','.tag_row');
    $i++;
}
              
if($offset>=$l_r)
{
     $ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index_blank.php?pag=".$glob['pag']."&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
     $ft->assign('BACKLINK',''); 
}
if($offset+$l_r<$max_rows)
{
     $ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index_blank.php?pag=".$glob['pag']."&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
     $ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',"List Of Alias Tags - ".$page_term);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','tag_row');
return $ft->fetch('CONTENT');






?>
