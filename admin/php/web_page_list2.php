<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "web_page_list2.html"));
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

//************Building the Switch options********************************
$ft->assign('LINK_TYPE_CATEGORY_LIST', build_link_type_category_list($glob['pag']));

//****************End Search Options*************************************
$filter='';
$arguments='';



$dbu->query("select name, web_page_id from cms_web_page order by date DESC");

$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;

while($dbu->move_next()&&$i<$l_r)
{
	$ft->assign('NAME',$dbu->f('name'));
	$page_file_name=str_to_filename($dbu->f('name'));
	$ft->assign('URL',get_link('index.php?pag=cms&id='.$dbu->f('web_page_id').'&p='.$page_file_name));

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
	
    $ft->parse('web_page_ROW_OUT','.web_page_row');
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
$ft->assign('PAGE_TITLE',"List Of Web Pages");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','web_page_row');
return $ft->fetch('CONTENT');






?>
