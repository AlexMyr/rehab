<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "faq_list.html"));
$ft->define_dynamic('faq_row','main');

$l_r=ROW_PER_PAGE;

$dbu=new mysql_db;


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

$arguments="";
$arg="";
$filter='';

//************Building the search options********************************
	$ft->assign(array(
                          "CATEGORY_DD"      =>        build_faq_category_list($glob['faq_category_id'])
                     )
                );
    $ft->assign('SEARCH_KEY',$glob['search_key']);

//****************End Search Options*************************************


if($glob['faq_category_id'])
{
	$subcategories=get_faq_subcategory_comma_list($glob['faq_category_id']);
	
	$filter.=" and faq.faq_category_id in (".$subcategories.")";
	$arguments.="&faq_category_id=".$glob['faq_category_id'];
}

if($glob['search_key'])
{
	$filter.=" and ( faq.question LIKE '%".$glob['search_key']."%' 
					 or faq.answer LIKE '%".$glob['search_key']."%' 
				    ) ";
	$arguments.="&search_key=".$glob['search_key'];
}


$dbu->query("select faq.question, faq.sort_order, faq.faq_id, faq.faq_category_id, faq_category.name as category_name from faq 
			 inner join faq_category on faq_category.faq_category_id=faq.faq_category_id
			 where 1=1 ".$filter."
			 order by faq_category.sort_order, faq.sort_order");


$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;

while($dbu->move_next()&&$i<$l_r)
{
	$ft->assign('QUESTION',$dbu->f('question'));
	$ft->assign('SORT_ORDER',$dbu->f('sort_order'));
	$ft->assign('CATEGORY_NAME',$dbu->f('category_name'));
	$ft->assign('FAQ_ID',$dbu->f('faq_id'));

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
	
    $ft->assign('EDIT_LINK',"index.php?pag=faq_add&faq_id=".$dbu->f('faq_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=faq_list&act=faq-delete&faq_id=".$dbu->f('faq_id')."&offset=".$b_offset.$arguments);
    $ft->parse('faq_ROW_OUT','.faq_row');
    $i++;
}
/*              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no F.A.Q Entries in the database.");
}
*/
if($offset>=$l_r)
{
     $ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=faq_list&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
     $ft->assign('BACKLINK',''); 
}
if($offset+$l_r<$max_rows)
{
     $ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=faq_list&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
     $ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',"List Of F.A.Q Entries");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','faq_row');
return $ft->fetch('CONTENT');






?>
