<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "menu_link_add.html"));

if($glob['web_page_id'] && !$glob['url'])
{
	$glob['url']=get_link('index.php?pag=cms&id='.$glob['web_page_id'].'&p='.$glob['p']);
}
if(!is_numeric($glob['menu_link_id']))
{
    $page_title="Add Menu Link";
    $next_function='menu_link-add';
    
    $ft->assign(array(
                        "MENU_LIST"        =>        build_cms_menu_list($glob['menu_id']),
                        "MENU_LINK_LIST"   =>        build_menu_link_list($glob['menu_id'], $glob['parent_id']),
                        "NAME"             =>        $glob['name'],
                        "SORT_ORDER"       =>        $glob['sort_order'],
                        "URL" 			   =>        $glob['url'],
                        "TARGET"           =>        build_link_target_list($glob['target'])
                     )
                );

}
else
{
    $page_title="Edit Menu Link";
    $next_function='menu_link-update';
    $dbu->query("select cms_menu_link.*, cms_menu_submenu.parent_id from cms_menu_link 
    			 inner join cms_menu_submenu on cms_menu_submenu.menu_link_id = cms_menu_link.menu_link_id
    			 where cms_menu_link.menu_link_id='".$glob['menu_link_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "MENU_LIST"        =>        build_cms_menu_list($dbu->gf('menu_id')),
                        "MENU_LINK_LIST"   =>        build_menu_link_list($dbu->gf('menu_id'),$dbu->gf('parent_id'),$glob['menu_link_id']),
                        "NAME"             =>        $dbu->gf('name'),
                        "SORT_ORDER"       =>        $dbu->gf('sort_order'),
                        "URL"              =>        $dbu->gf('url'),
                        "TARGET"           =>        build_link_target_list($dbu->gf('target'))
                     )
                );

}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MENU_LINK_ID',$glob['menu_link_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>