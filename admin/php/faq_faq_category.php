<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "faq_faq_category.html"));
$ft->define_dynamic('category_row','main');
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
        $ft->assign('OFFSET',$glob['offset']);
}

build_faq_category_list_blank();

$faq_category_array=$faq_categ_array;


$max_rows=count($faq_category_array);

$i=0;

for($i=$offset; $i<($offset+$l_r); $i++)
if($faq_category_array)
{
	$cat_array=$faq_category_array[$i];
	$ft->assign('NAME',$cat_array['name']);
	$ft->assign('SORT_ORDER',$cat_array['sort_order']);
	$ft->assign('FAQ_CATEGORY_ID',$cat_array['faq_category_id']);
	$ft->assign('SPACER',str_repeat("&nbsp;&nbsp;&nbsp;",$cat_array['level'])); 
	$ft->assign('FAQ_LIST_LINK','index.php?pag=faq_list&faq_category_id='.$cat_array['faq_category_id']);
	
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
	
    $ft->parse('faq_category_ROW_OUT','.category_row');
    if(!$faq_category_array[$i+1])
    {
    	$i++;
    	break;
    }
}

             
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Categories.");
}

if($offset>=$l_r)
{
     $ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=faq_faq_category&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
     $ft->assign('BACKLINK',''); 
}
if($offset+$l_r<$max_rows)
{
     $ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=faq_faq_category&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
     $ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',"List Of Categories");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','category_row');


return $ft->fetch('content');



?>
