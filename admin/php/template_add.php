<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "template_add.html"));
$ft->define_dynamic('tag_row','main');

if(!is_numeric($glob['template_id']))
{
	$page_title="Web Page Template Add";
	$next_function='template-add';
	
    $ft->assign(array(
    					"S_EDIT_MODE"       =>        '<!--',
                        "E_EDIT_MODE"       =>        '-->',
                        "DESCRIPTION"       =>        $glob['description'],
                        "FILE_NAME"         =>        $glob['file_name'],
                        "NAME"              =>        $glob['name']
                     )
                );
}
else
{
	$active_checked='';
    $page_title="Web Page Template Edit";
    $next_function='template-update';
    $dbu->query("select * from cms_template 
    			 where template_id='".$glob['template_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TEMPLATE_ID"      =>        $glob['template_id'],
                        "DESCRIPTION"      =>        $dbu->gf('description'),
                        "FILE_NAME"        =>        $dbu->gf('file_name'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );
    $i=0;
	$dbu->query("select * from cms_template_czone where template_id='".$glob['template_id']."'");
	while($dbu->move_next())
	{
	    $ft->assign(array(
	                        "TEMPLATE_ID"      =>        $glob['template_id'],
	                        "TAG_NAME"         =>        $dbu->f('name'),
	                        "TAG"              =>        $dbu->f('tag'),
	                        "EDIT_LINK"        =>        'index.php?pag=template_czone_add&template_czone_id='.$dbu->f('template_czone_id').'&template_id='.$glob['template_id'],
	                        "DELETE_LINK"      =>        'index.php?pag=template_add&act=template-czone_delete&template_czone_id='.$dbu->f('template_czone_id').'&template_id='.$glob['template_id']
	                      )
	                );
		if($i%2==1)
		{
			$ft->assign('BG_COLOR',"#F8F9FA");
		}
		else
		{
			$ft->assign('BG_COLOR',"#FFFFFF");
		}
		$i++;
		$ft->parse('TAG_OUT','.tag_row');
	}

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('TEMPLATE_ID',$glob['template_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','tag_row');
return $ft->fetch('CONTENT');

?>