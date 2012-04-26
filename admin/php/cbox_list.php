<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "cbox_list.html"));
$ft->define_dynamic('cbox_row','main');

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

//************Building the search options********************************
    $ft->assign('SEARCH_KEY',$glob['search_key']);

//****************End Search Options*************************************
$filter='';
$arguments='';

if($glob['search_key'])
{
	$arguments.='&search_key='.$glob['search_key'];
	$filter.=" and ( title LIKE '%".$glob['search_key']."%' 
					 or content LIKE '%".$glob['search_key']."%' 
				   ) ";
}

$dbu->query("select * from cms_content_box where 1=1 ".$filter." order by title");

$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;

while($dbu->move_next()&&$i<$l_r)
{
	$ft->assign('TITLE',$dbu->f('title'));
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
	
    $ft->assign('EDIT_LINK',"index.php?pag=cbox_add&content_box_id=".$dbu->f('content_box_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=cbox_list&act=cbox-delete&content_box_id=".$dbu->f('content_box_id')."&offset=".$b_offset.$arguments);
    $ft->parse('content_box_ROW_OUT','.cbox_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Content Boxes that match your search criteria.");
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
$ft->assign('PAGE_TITLE',"List Of Content Boxes");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','cbox_row');
return $ft->fetch('CONTENT');






?>
