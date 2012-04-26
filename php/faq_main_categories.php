<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "faq_main_categories.html"));
$ft->define_dynamic('category_row','main');

$arguments='';

$dbu->query("select name, faq_category_id from faq_category where level='0' order by faq_category.sort_order");
$i=0;
while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('FAQ_CATEGORY_ID',$dbu->f('faq_category_id'));
	$ft->assign('LIST_LINK',get_link('index.php?pag=faq&id='.$dbu->f('faq_category_id').'&p='.str_to_filename($dbu->f('name'))));
	
    $ft->parse('category_row_OUT','.category_row');
  	$i++;
}
 if($i==0)
 {
 	unset($ft);
 	return get_error_message('There are no FAQs');
 }
$site_meta_title=$meta_title." - Frequently Asked Questions";
$site_meta_keywords=$meta_keywords.", Frequently Asked Questions";
$site_meta_description=$meta_description." Frequently Asked Questions";

$ft->assign('PAGE',$glob['pag']);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','category_row');

return $ft->fetch('content');

?>
