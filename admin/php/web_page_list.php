<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "web_page_list.html"));
$ft->define_dynamic('web_page_row','main');

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
	$filter.=" and ( name LIKE '%".$glob['search_key']."%' 
					 or title LIKE '%".$glob['search_key']."%' 
					 or keywords LIKE '%".$glob['search_key']."%' 
					 or description LIKE '%".$glob['search_key']."%' ) ";
}

$dbu->query("select * from cms_web_page where 1=1 ".$filter." order by date DESC");

$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;
while($dbu->move_next()&&$i<$l_r)
{
	$ft->assign('NAME',$dbu->f('name'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
	if($dbu->f('web_page_id') == CMS_HOME_PAGE)
	{
		$ft->assign('BG_COLOR',"#FFFFCC");
	}
	
	if($offset+1==$max_rows)
	{
		$b_offset=$offset-$l_r;
	}
	else 
	{
		$b_offset=$offset;
	}
	
    $ft->assign('UPDATE_LINK',"index.php?pag=web_page_update&web_page_id=".$dbu->f('web_page_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=web_page_list&act=web_page-delete&web_page_id=".$dbu->f('web_page_id')."&offset=".$b_offset.$arguments);
    $ft->assign('SET_HOME',"index.php?pag=web_page_list&act=web_page-set_home&web_page_id=".$dbu->f('web_page_id')."&offset=".$b_offset.$arguments);
    $ft->parse('web_page_ROW_OUT','.web_page_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Web Pages that match your search criteria.");
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
$ft->assign('PAGE_TITLE',"List Of Web Pages");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','web_page_row');
return $ft->fetch('CONTENT');






?>
