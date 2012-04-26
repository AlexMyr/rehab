<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "page_type_add.html"));
$ft->define_dynamic('tag_row','main');

if(!is_numeric($glob['page_type_id']))
{
	$page_title="Add Page Type";
	$next_function='page_type-add';
	
    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '<!--',
                        "E_EDIT_MODE"       =>        '-->',
                        "S_ADD_MODE"        =>        '',
                        "E_ADD_MODE"        =>        '',
                        "DESCRIPTION"       =>        $glob['description'],
                        "TEMPLATE_LIST"     =>        build_web_page_templates_radiobuttons($glob['template_id']),
                        "NAME"              =>        $glob['name']
                     )
                );
}
else
{
	$active_checked='';
    $page_title="Edit Page Type";
    $next_function='page_type-update';
    $dbu->query("select cms_page_type.page_type_id, cms_page_type.name, cms_page_type.description,
				 cms_template.name as template_name, cms_template.file_name
    			 from cms_page_type
    			 inner join cms_template on cms_page_type.template_id=cms_template.template_id
    			 where page_type_id='".$glob['page_type_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '',
                        "E_EDIT_MODE"       =>        '',
                        "S_ADD_MODE"        =>        '<!--',
                        "E_ADD_MODE"        =>        '-->',
                        "PAGE_TYPE_ID"      =>        $glob['page_type_id'],
                        "DESCRIPTION"       =>        $dbu->gf('description'),
                        "FILE_NAME"         =>        $dbu->f('file_name'),
                        "TEMPLATE_NAME"     =>        $dbu->f('template_name'),
                        "NAME"              =>        $dbu->gf('name')
                     )
                );
    $dbu->query("select cms_page_type_czone.*, cms_template_czone.*
				 from cms_page_type_czone
				 inner join cms_template_czone on cms_page_type_czone.template_czone_id=cms_template_czone.template_czone_id 
				 where page_type_id='".$glob['page_type_id']."'");
	while($dbu->move_next())
	{
	    $ft->assign(array(
	                        "PAGE_TYPE_ID"     =>        $glob['page_type_id'],
	                        "TAG_NAME"         =>        $dbu->f('name'),
	                        "TAG"              =>        $dbu->f('tag'),
	                        "TEMPLATE_CZONE_ID" =>        $dbu->f('template_czone_id'),
                        	"EDIT_LINK"        =>        'index.php?pag=page_type_czone_add&page_type_czone_id='.$dbu->f('page_type_czone_id').'&page_type_id='.$glob['page_type_id'],
                        	"EMPTY_LINK"       =>        'index.php?pag=page_type_add&act=page_type-czone_empty&page_type_czone_id='.$dbu->f('page_type_czone_id').'&page_type_id='.$glob['page_type_id'],
	                      )
	                );
		if($dbu->f('default_data'))
		{
			$ft->assign('BG_COLOR',"#FFFFCC");
		}
		else
		{
			$ft->assign('BG_COLOR',"#FFFFFF");
		}
		$ft->parse('TAG_OUT','.tag_row');
	}

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('PAGE_TYPE_ID',$glob['page_type_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','tag_row');
return $ft->fetch('CONTENT');

?>