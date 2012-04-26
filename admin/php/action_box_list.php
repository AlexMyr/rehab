<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "action_box_list.html"));
$ft->define_dynamic('action_box_row','main');

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

$dbu->query("select * from cms_tag_library where type='3' order by name");

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
	
    $ft->assign('EDIT_LINK',"index.php?pag=action_box_add&tag_id=".$dbu->f('tag_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=action_box_list&act=tag-action_box_delete&tag_id=".$dbu->f('tag_id')."&offset=".$b_offset.$arguments);
    $ft->parse('action_box_ROW_OUT','.action_box_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Dynamic Boxes in database.");
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

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',"List Of Dynamic Boxes");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','action_box_row');
return $ft->fetch('CONTENT');






?>
