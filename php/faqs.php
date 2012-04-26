<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/


$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "faqs.html"));
$ft->define_dynamic('faq_short_row','main');
$ft->define_dynamic('faq_detailed_row','main');

$dbu=new mysql_db;
$dbu->query("select name from faq_category where faq_category_id = '".$glob['id']."'");
$dbu->move_next();
$main_faq_category_name = $dbu->f('name');
//$ft->assign('MAIN_CATEGORY_NAME',$dbu->f('name'));
$ft->assign('MAIN_CATEGORY_NAME','FAQS');


$dbu->query("select question, sort_order, faq_id, answer from faq where faq_category_id = '".$glob['id']."' order by sort_order");

$i=0;

while($dbu->move_next())
{
	$ft->assign('QUESTION',$dbu->f('question'));
	$ft->assign('ANSWER',get_safe_text($dbu->f('answer')));
	$ft->assign('SORT_ORDER',$dbu->f('sort_order'));
	$ft->assign('FAQ_ID',$dbu->f('faq_id'));

    $ft->parse('faq_short_ROW_OUT','.faq_short_row');
    $ft->parse('faq_detailed_ROW_OUT','.faq_detailed_row');
    $i++;
}

if($i==0)
 {
 	unset($ft);
 	return get_error_message('There are no FAQ in this category.<br>
 	<a href="javascript:history.back()" class="faqStyle">Click here to go back</a>
 	
 	');
 }
$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>