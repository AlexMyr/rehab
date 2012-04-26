<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "multiple_category.html"));
$ft->define_dynamic('category_row','main');

$dbu=new mysql_db;

if(!is_numeric($glob['programs_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else 
{
	$dbu->query("select programs_title from `programs` where programs_id='".$glob['programs_id']."'");
	if($dbu->move_next())
	{
		$product_name=$dbu->f('programs_title');
	}
}
$page_title="Copy  ".$product_name." to Other Categories ";
//$next_function='product-product2product_add';
$ft->assign('BACK_LINK','index.php?pag=programs_add&programs_id='.$glob['programs_id']);
$ft->assign('PRODUCT_NAME',$product_name);
$ft->assign("CATEGORY", build_category_list($glob['category_id']));


//*************Product Categories************************\\
    $i=0;
    $dbu->query("select programs_category.category_id, programs_category.category_name from programs_category
    			 inner join programs_in_category on programs_category.category_id=programs_in_category.category_id
    			 where programs_in_category.programs_id='".$glob['programs_id']."' 
    			 and programs_in_category.main!='1'
    			 order by programs_category.category_name
    ");
    while($dbu->move_next())
    {
//    	$ft->assign("CATEGORY_NAME", $dbu->f('category_name'));
    	$ft->assign("CATEGORY_NAME", get_admin_category_path($dbu->f('category_id')));
    	
    	$ft->assign("CATEGORY_DELETE_LINK", 'index.php?pag=multiple_category&programs_id='.$glob['programs_id'].'&act=programs-programs_category_delete&category_id='.$dbu->f('category_id'));
        if($i%2==1)
		{
			$ft->assign('BG_COLOR',"#F8F9FA");
		}
		else
		{
			$ft->assign('BG_COLOR',"#FFFFFF");
		}
	
    	$ft->parse('CATEGORY_ROW_OUT','.category_row');
    	$i++;
    }



$ft->assign('PAGE_TITLE',$page_title);
//$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('PRODUCT_ID',$glob['programs_id']);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','category_row');
return $ft->fetch('CONTENT');
?>
