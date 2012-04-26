<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "faq_add.html"));

if(!is_numeric($glob['faq_id']))
{
	$page_title="Add FAQ Entry";
	$next_function='faq-add';
	
    $ft->assign(array(
    					"CATEGORY"          =>        build_faq_category_list($glob['faq_category_id']),
    					"QUESTION"          =>        $glob['question'],
                        "ANSWER"            =>        $glob['answer'],
                        "SORT_ORDER"        =>        $glob['sort_order']
                     )
                );
}
else
{
    $page_title="Edit FAQ Entry";
    $next_function='faq-update';
    $dbu->query("select * from faq 
    			 where faq_id='".$glob['faq_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
    					"CATEGORY"         =>        build_faq_category_list($dbu->gf('faq_category_id')),
                        "FAQ_ID" 		   =>        $glob['faq_id'],
                        "QUESTION"         =>        $dbu->gf('question'),
                        "ANSWER"           =>        $dbu->gf('answer'),
                        "SORT_ORDER" 	   =>        $dbu->gf('sort_order'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('FAQ_ID',$glob['faq_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>