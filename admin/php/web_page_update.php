<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;
$dbu2=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "web_page_update.html"));
$ft->define_dynamic('tag_row','main');
$ft->define_dynamic('content_row','tag_row');

if(!is_numeric($glob['web_page_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

$page_title="Update Web Page";
$next_function='web_page-update';
$dbu->query("select cms_web_page.*, cms_page_type.name as page_type_name, cms_template.name as template_name,  cms_template.file_name as template_file_name
			 from cms_web_page
			 inner join cms_page_type on cms_web_page.page_type_id=cms_page_type.page_type_id
			 inner join cms_template on cms_template.template_id=cms_web_page.template_id
			 where web_page_id='".$glob['web_page_id']."'");

if(!$dbu->move_next())	
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

//get permanent link
$page_file_name=str_to_filename($dbu->f('name'));
$ft->assign('PERMANENT_LINK',get_link('index.php?pag=cms&id='.$glob['web_page_id'].'&p='.$page_file_name));
$ft->assign('PREVIEW_LINK',get_link('../index.php?pag=cms&id='.$glob['web_page_id'].'&p='.$page_file_name));
//end permanent link

$template_id=$dbu->f('template_id');
$page_type_id=$dbu->f('page_type_id');

$ft->assign(array(
                   "PAGE_FILE_NAME"    =>        $page_file_name,
                   "DESCRIPTION"       =>        $dbu->gf('description'),
                   "TITLE"             =>        $dbu->gf('title'),
                   "KEYWORDS"          =>        $dbu->gf('keywords'),
                   "PAGE_TYPE"         =>        $dbu->f('page_type_name'),
                   "TEMPLATE_NAME"     =>        $dbu->f('template_name'),
                   "TEMPLATE_FILE_NAME" =>        $dbu->f('template_file_name'),
                   "NAME"              =>        $dbu->gf('name'),
                   "EDIT_INFO_LINK"    =>        'index.php?pag=web_page_edit&web_page_id='.$glob['web_page_id'],
                   "ADVANCED_LINK"     =>        'index.php?pag=web_page_update&umode=1&web_page_id='.$glob['web_page_id'],
                  )
            );
if(!$glob['umode'])
{
	$ft->assign('ADVANCED_LINK','<a href="index.php?pag=web_page_update&umode=1&web_page_id='.$glob['web_page_id'].'" class="RedBoldLink">Advanced Mode</a>');
	$ft->assign('UMODE','');
	$filter=" and cms_page_type_czone.prefilled='0'";
	$args='';
}
else 
{
	$ft->assign('ADVANCED_LINK','<a href="index.php?pag=web_page_update&web_page_id='.$glob['web_page_id'].'" class="RedBoldLink">Basic Mode</a>');
	$ft->assign('UMODE','1');
	$filter='';
	$args='&umode=1';
}

$i=0;
$dbu->query("select * from cms_template_czone
			 inner join cms_page_type_czone on cms_template_czone.template_czone_id=cms_page_type_czone.template_czone_id
			 where cms_page_type_czone.page_type_id='".$page_type_id."'".$filter." order by prefilled");  
while($dbu->move_next())
{
	$dbu2->query("select title, web_page_content_id, sort_order  from cms_web_page_content 
				  where web_page_id='".$glob['web_page_id']."' and template_czone_id='".$dbu->f('template_czone_id')."'
				  order by sort_order ASC");
	while($dbu2->move_next())
	{
		$ft->assign(array(
                   "C_TITLE"           =>        ($dbu2->f('title')?$dbu2->f('title'):'Content '.$i),
                   "SORT_ORDER"        =>        $dbu2->f('sort_order'),
                   "SORT_ORDER"        =>        $dbu2->f('sort_order'),
                   "WEB_PAGE_CONTENT_ID" =>      $dbu2->f('web_page_content_id'),
                   "EDIT_C_LINK"       =>        'index.php?pag=web_page_content_add&web_page_id='.$glob['web_page_id'].'&web_page_content_id='.$dbu2->f('web_page_content_id').'&template_czone_id='.$dbu->f('template_czone_id').$args,
                   "DELETE_C_LINK"     =>        'index.php?pag=web_page_update&act=web_page_content-delete&web_page_id='.$glob['web_page_id'].'&web_page_content_id='.$dbu2->f('web_page_content_id').$args,
                  )
            );
		if($i%2==0)
	   	{
	   		$bgcolor="#FFFFFF";
	   	}
	   	else 
	   	{
	   		$bgcolor="#F5F5F5";
	   	}
		$ft->assign("BG_COLOR",$bgcolor);
		$i++;
		$ft->parse('CONTENT_ROW','.content_row');				
	}
	
	$ft->assign(array(
                   "WEB_PAGE_ID"       =>        $glob['web_page_id'],
                   "UMODE"             =>        $glob['umode'],
                   "TAG"               =>        $dbu->f('tag'),
                   "TEMPLATE_CZONE_ID" =>        $dbu->f('template_czone_id'),
                   "TAG_NAME"          =>        $dbu->f('name'),
                   "ADD_C_LINK"        =>        'index.php?pag=web_page_content_add&web_page_id='.$glob['web_page_id']."&template_czone_id=".$dbu->f('template_czone_id').$args,
                  )
            );
	$ft->parse('TAG_ROW','.tag_row');
	if($i!=0)
	{
		$ft->clear('CONTENT_ROW');
	}
	else 
	{
		$ft->clear_dynamic('TAG_ROW','content_row');
	}
	$i=0;
	
}          
            
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('WEB_PAGE_ID',$glob['web_page_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','tag_row');
return $ft->fetch('CONTENT');

?>