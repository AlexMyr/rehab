<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "faq_subcategories.html"));
$ft->define_dynamic('category_row','main');

$arguments='';
$dbu->query("select name from faq_category where faq_category_id = '".$glob['id']."'");
$dbu->move_next();
$main_faq_category_name = $dbu->f('name');
//$ft->assign('MAIN_CATEGORY_NAME',$dbu->f('name'));
$ft->assign('MAIN_CATEGORY_NAME','FAQS');

$dbu->query("select faq_category.name, faq_category.faq_category_id from faq_category
			 inner join faq_category_subcategory on faq_category_subcategory.faq_category_id=faq_category.faq_category_id
			 where faq_category_subcategory.parent_id='".$glob['id']."' and faq_category_subcategory.faq_category_id != '".$glob['id']."'
			 order by faq_category.sort_order");
$i=0;
while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('FAQ_CATEGORY_ID',$dbu->f('faq_category_id'));
	$ft->assign('LIST_LINK',get_link('index.php?pag=faq&id='.$dbu->f('faq_category_id').'&p='.str_to_filename($dbu->f('name'))));
	
    $ft->parse('category_row_OUT','.category_row');
  	$i++;
}
 
$site_meta_title=$meta_title." - F.A.Q's - ".$main_faq_category_name;
$site_meta_keywords=$meta_keywords.", Frequently Asked Questions, ".$main_faq_category_name;
$site_meta_description=$meta_description." Frequently Asked Questions ".$main_faq_category_name;

$ft->assign('PAGE',$glob['pag']);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','category_row');

return $ft->fetch('content');

?>
