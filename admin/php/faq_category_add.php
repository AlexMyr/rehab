<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;
$dbu2=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "faq_category_add.html"));


if(!is_numeric($glob['faq_category_id']))
{
	$active_checked='';
	if(!$glob['save'])
	{
		$glob['active']=1;
	}
	
	$page_title="Category Add";
	$next_function='faq_category-add';
	
	if($glob['active'])
		$active_checked='checked';
		
    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '<!--',
                        "E_EDIT_MODE"       =>        '-->',
                        "CATEGORY"          =>        build_faq_category_list($glob['parent_id'], 0),
                        "SORT_ORDER"        =>        $glob['sort_order'],
                        "ACTIVE_CHECKED"    => 		  $active_checked,
                        "NAME"              =>        $glob['name']
                                            )
                );
}
else
{
	$active_checked='';
    $page_title="Category Edit";
    $next_function='faq_category-update';
    $next_p_function='faq_category-upload_file';
    $dbu->query("select faq_category.*, faq_category_subcategory.parent_id from faq_category 
    			 inner join faq_category_subcategory on faq_category_subcategory.faq_category_id=faq_category.faq_category_id
    			 where faq_category.faq_category_id='".$glob['faq_category_id']."'");
    $dbu->move_next();
    
    if($dbu->gf('active'))
		$active_checked='checked';
		
    $ft->assign(array(
                        "FAQ_CATEGORY_ID"  =>        $glob['faq_category_id'],
                        "CATEGORY"         =>        build_faq_category_list($dbu->gf('parent_id'), $glob['faq_category_id']),
                        "SORT_ORDER"       =>        $dbu->gf('sort_order'),
                        "ACTIVE_CHECKED"   => 		 $active_checked,
                        "NAME"             =>        $dbu->gf('name')
                                          )
                );


}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('FAQ_CATEGORY_ID',$glob['faq_category_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','picture');
return $ft->fetch('CONTENT');

?>